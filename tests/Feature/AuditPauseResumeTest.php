<?php

namespace Tests\Feature;

use App\Models\AuditCriterion;
use App\Models\AuditRecord;
use App\Models\AuditResult;
use App\Models\AuditTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditPauseResumeTest extends TestCase
{
    use RefreshDatabase;

    private User $auditor;
    private User $deptManager;
    private AuditTemplate $template;
    private AuditCriterion $criterion1;
    private AuditCriterion $criterion2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'audit']);
        Permission::firstOrCreate(['name' => 'audits.access']);

        $this->auditor = User::factory()->create([
            'name' => 'Auditor User',
            'managed_departments' => [],
        ]);
        $this->auditor->assignRole('audit');
        $this->auditor->givePermissionTo('audits.access');

        $this->deptManager = User::factory()->create([
            'name' => 'Dept Manager',
            'managed_departments' => ['BTP'],
        ]);
        $this->deptManager->givePermissionTo('audits.access');

        $this->template = AuditTemplate::create([
            'name' => 'Đánh giá BTP',
            'department_name' => 'BTP',
            'is_active' => true,
        ]);

        $this->criterion1 = AuditCriterion::create([
            'audit_template_id' => $this->template->id,
            'content' => 'Tiêu chí 1: Sạch sẽ',
            'order_num' => 1,
        ]);

        $this->criterion2 = AuditCriterion::create([
            'audit_template_id' => $this->template->id,
            'content' => 'Tiêu chí 2: Ngăn nắp',
            'order_num' => 2,
        ]);
    }

    public function test_auditor_can_pause_audit_during_creation(): void
    {
        $response = $this->actingAs($this->auditor)->post('/audits', [
            'action' => 'pause',
            'audit_template_id' => $this->template->id,
            'results' => [
                0 => [
                    'audit_criterion_id' => $this->criterion1->id,
                    'is_passed' => 1,
                ],
            ],
        ]);

        $response->assertRedirect('/audits');

        $record = AuditRecord::where('audit_template_id', $this->template->id)
            ->where('auditor_id', $this->auditor->id)
            ->first();

        $this->assertNotNull($record);
        $this->assertEquals('paused', $record->status);
        $this->assertTrue($record->isPaused());
        $this->assertFalse($record->isCompleted());
        $this->assertCount(1, $record->results);
    }

    public function test_visiting_create_redirects_to_edit_if_paused_audit_exists(): void
    {
        $record = AuditRecord::create([
            'audit_template_id' => $this->template->id,
            'auditor_id' => $this->auditor->id,
            'status' => 'paused',
        ]);

        $response = $this->actingAs($this->auditor)->get('/audits/create?template_id=' . $this->template->id);

        $response->assertRedirect(route('audits.edit', $record->id));
    }

    public function test_completing_audit_requires_all_criteria(): void
    {
        $response = $this->actingAs($this->auditor)->post('/audits', [
            'action' => 'complete',
            'audit_template_id' => $this->template->id,
            'results' => [
                0 => [
                    'audit_criterion_id' => $this->criterion1->id,
                    'is_passed' => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['criteria']);
        $this->assertDatabaseMissing('audit_records', [
            'audit_template_id' => $this->template->id,
            'status' => 'completed',
        ]);
    }

    public function test_auditor_can_resume_and_complete_audit(): void
    {
        $record = AuditRecord::create([
            'audit_template_id' => $this->template->id,
            'auditor_id' => $this->auditor->id,
            'status' => 'paused',
        ]);

        AuditResult::create([
            'audit_record_id' => $record->id,
            'audit_criterion_id' => $this->criterion1->id,
            'is_passed' => true,
        ]);

        $response = $this->actingAs($this->auditor)->post(route('audits.update', $record->id), [
            'action' => 'complete',
            'results' => [
                $this->criterion1->id => [
                    'audit_criterion_id' => $this->criterion1->id,
                    'is_passed' => '1',
                ],
                $this->criterion2->id => [
                    'audit_criterion_id' => $this->criterion2->id,
                    'is_passed' => '0',
                    'note' => 'Chưa dọn dẹp gọn gàng',
                ],
            ],
        ]);

        $response->assertRedirect(route('audits.show', $record->id));

        $record->refresh();
        $this->assertEquals('completed', $record->status);
        $this->assertTrue($record->isCompleted());
        $this->assertFalse($record->isPaused());
        $this->assertCount(2, $record->results);
    }

    public function test_auditor_can_delete_their_own_paused_record(): void
    {
        $record = AuditRecord::create([
            'audit_template_id' => $this->template->id,
            'auditor_id' => $this->auditor->id,
            'status' => 'paused',
        ]);

        $response = $this->actingAs($this->auditor)->delete(route('audits.destroy', $record->id));

        $response->assertRedirect(route('audits.index'));
        $this->assertDatabaseMissing('audit_records', ['id' => $record->id]);
    }

    public function test_department_manager_does_not_see_paused_audits(): void
    {
        AuditRecord::create([
            'audit_template_id' => $this->template->id,
            'auditor_id' => $this->auditor->id,
            'status' => 'paused',
        ]);

        $completed = AuditRecord::create([
            'audit_template_id' => $this->template->id,
            'auditor_id' => $this->auditor->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->deptManager)->get('/audits');

        $response->assertOk();
        $audits = $response->viewData('audits');
        $this->assertEquals(1, $audits->total());
        $this->assertEquals($completed->id, $audits->first()->id);
    }
}
