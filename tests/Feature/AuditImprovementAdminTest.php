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

class AuditImprovementAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $deptManager;
    private User $otherManager;
    private User $standardUser;
    private AuditRecord $auditRecord;
    private AuditResult $failedResult;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles and Permissions
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'audit']);
        Permission::firstOrCreate(['name' => 'audits.access']);

        // Create Users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->deptManager = User::factory()->create([
            'name' => 'Manager BTP',
            'managed_departments' => ['BTP'],
        ]);
        $this->deptManager->givePermissionTo('audits.access');

        $this->otherManager = User::factory()->create([
            'name' => 'Manager IE',
            'managed_departments' => ['IE'],
        ]);
        $this->otherManager->givePermissionTo('audits.access');

        $this->standardUser = User::factory()->create();

        // Create Audit Template
        $template = AuditTemplate::create([
            'name' => 'Template BTP',
            'department_name' => 'BTP',
            'is_active' => true,
        ]);

        // Create Criterion
        $criterion = AuditCriterion::create([
            'audit_template_id' => $template->id,
            'content' => 'Câu hỏi kiểm tra BTP',
            'order_num' => 1,
        ]);

        // Create Audit Record
        $this->auditRecord = AuditRecord::create([
            'audit_template_id' => $template->id,
            'auditor_id' => $this->admin->id,
            'status' => 'completed',
        ]);

        // Create failed result which is agreed by department (making it improvable)
        $this->failedResult = AuditResult::create([
            'audit_record_id' => $this->auditRecord->id,
            'audit_criterion_id' => $criterion->id,
            'is_passed' => false,
            'note' => 'Lỗi phát hiện tại BTP',
            'department_agreement' => true,
        ]);
    }

    public function test_department_manager_can_add_improvement_plan(): void
    {
        $response = $this->actingAs($this->deptManager)
            ->post(route('audits.improvements', $this->auditRecord->id), [
                'improvements' => [
                    $this->failedResult->id => [
                        'root_cause' => 'Nguyên nhân BTP',
                        'corrective_action' => 'Biện pháp khắc phục BTP',
                        'improvement_deadline' => '2026-06-30',
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('audit_results', [
            'id' => $this->failedResult->id,
            'root_cause' => 'Nguyên nhân BTP',
            'corrective_action' => 'Biện pháp khắc phục BTP',
            'improvement_deadline' => '2026-06-30',
            'improver_name' => 'Manager BTP',
        ]);
    }

    public function test_other_department_manager_cannot_add_improvement_plan(): void
    {
        $response = $this->actingAs($this->otherManager)
            ->post(route('audits.improvements', $this->auditRecord->id), [
                'improvements' => [
                    $this->failedResult->id => [
                        'root_cause' => 'Nguyên nhân sai',
                        'corrective_action' => 'Biện pháp sai',
                        'improvement_deadline' => '2026-06-30',
                    ]
                ]
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_add_improvement_plan_to_any_department(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('audits.improvements', $this->auditRecord->id), [
                'improvements' => [
                    $this->failedResult->id => [
                        'root_cause' => 'Admin sửa lỗi',
                        'corrective_action' => 'Admin khắc phục',
                        'improvement_deadline' => '2026-06-25',
                    ]
                ]
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_results', [
            'id' => $this->failedResult->id,
            'root_cause' => 'Admin sửa lỗi',
            'corrective_action' => 'Admin khắc phục',
            'improvement_deadline' => '2026-06-25',
            'improver_name' => $this->admin->name,
        ]);
    }

    public function test_admin_can_edit_existing_improvement_plan_preserving_original_improver(): void
    {
        // First set up an existing improvement plan by the department manager
        $this->failedResult->update([
            'root_cause' => 'Nguyên nhân ban đầu',
            'corrective_action' => 'Biện pháp ban đầu',
            'improvement_deadline' => '2026-06-20',
            'improver_name' => 'Manager BTP',
        ]);

        // Admin updates it
        $response = $this->actingAs($this->admin)
            ->post(route('audits.improvements', $this->auditRecord->id), [
                'improvements' => [
                    $this->failedResult->id => [
                        'root_cause' => 'Admin đã sửa đổi',
                        'corrective_action' => 'Biện pháp cập nhật bởi Admin',
                        'improvement_deadline' => '2026-06-28',
                    ]
                ]
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_results', [
            'id' => $this->failedResult->id,
            'root_cause' => 'Admin đã sửa đổi',
            'corrective_action' => 'Biện pháp cập nhật bởi Admin',
            'improvement_deadline' => '2026-06-28',
            'improver_name' => 'Manager BTP', // Preserved!
        ]);
    }

    public function test_non_admin_cannot_edit_existing_improvement_plan(): void
    {
        // Set up existing improvement plan
        $this->failedResult->update([
            'root_cause' => 'Nguyên nhân ban đầu',
            'corrective_action' => 'Biện pháp ban đầu',
            'improvement_deadline' => '2026-06-20',
            'improver_name' => 'Manager BTP',
        ]);

        // Department manager tries to update it again (should bypass / not update if already has improver_name, or only allowed if admin)
        // Wait, the controller has:
        // if (!empty($result->improver_name) && !$user->isAdminUser()) { continue; }
        // So it continues without updating. Let's assert database remains unchanged.
        $response = $this->actingAs($this->deptManager)
            ->post(route('audits.improvements', $this->auditRecord->id), [
                'improvements' => [
                    $this->failedResult->id => [
                        'root_cause' => 'Thay đổi mới bởi Manager',
                        'corrective_action' => 'Khắc phục mới bởi Manager',
                        'improvement_deadline' => '2026-06-22',
                    ]
                ]
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_results', [
            'id' => $this->failedResult->id,
            'root_cause' => 'Nguyên nhân ban đầu',
            'corrective_action' => 'Biện pháp ban đầu',
            'improvement_deadline' => '2026-06-20',
            'improver_name' => 'Manager BTP',
        ]);
    }
}
