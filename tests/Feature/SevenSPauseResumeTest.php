<?php

namespace Tests\Feature;

use App\Models\SevenSChecklist;
use App\Models\SevenSRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SevenSPauseResumeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $inspector;
    private User $deptUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => '7s']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->inspector = User::factory()->create();
        $this->inspector->assignRole('7s');

        $this->deptUser = User::factory()->create([
            'managed_departments' => ['Test Dept Pause'],
        ]);

        // Seed 3 checklist items for Test Dept Pause
        SevenSChecklist::create([
            'department' => 'Test Dept Pause',
            'section' => 'S1 & S2',
            'content' => 'Item 1 content',
            'sort_order' => 1,
        ]);
        SevenSChecklist::create([
            'department' => 'Test Dept Pause',
            'section' => 'S1 & S2',
            'content' => 'Item 2 content',
            'sort_order' => 2,
        ]);
        SevenSChecklist::create([
            'department' => 'Test Dept Pause',
            'section' => 'S1 & S2',
            'content' => 'Item 3 content',
            'sort_order' => 3,
        ]);
    }

    public function test_inspector_can_pause_seven_s_inspection_during_creation(): void
    {
        $items = SevenSChecklist::where('department', 'Test Dept Pause')->get();

        // Pause with only 1 item graded (partial)
        $response = $this->actingAs($this->inspector)->post('/seven-s', [
            'department' => 'Test Dept Pause',
            'action' => 'pause',
            'grades' => [
                $items[0]->id => 'B',
            ],
            'notes' => [
                $items[0]->id => 'Good',
            ],
        ]);

        $record = SevenSRecord::where('department', 'Test Dept Pause')->first();
        $this->assertNotNull($record);
        $this->assertEquals('paused', $record->status);
        $this->assertTrue($record->isPaused());
        $this->assertEquals(2, $record->score); // B=2
        $this->assertEquals(2, $record->max_score);
        $this->assertEquals(1, $record->results()->count());

        $response->assertRedirect(route('seven-s.show', $record->id));
        $response->assertSessionHas('success');
    }

    public function test_visiting_create_resumes_existing_paused_inspection(): void
    {
        $record = SevenSRecord::create([
            'department' => 'Test Dept Pause',
            'inspector_id' => $this->inspector->id,
            'score' => 2,
            'max_score' => 2,
            'status' => 'paused',
        ]);

        $response = $this->actingAs($this->inspector)->get('/seven-s/create?department=Test Dept Pause');
        $response->assertRedirect(route('seven-s.edit', $record->id));
    }

    public function test_completing_inspection_requires_all_items(): void
    {
        $items = SevenSChecklist::where('department', 'Test Dept Pause')->get();

        // Try to complete with only 1 of 3 items
        $response = $this->actingAs($this->inspector)->post('/seven-s', [
            'department' => 'Test Dept Pause',
            'action' => 'complete',
            'grades' => [
                $items[0]->id => 'B',
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('seven_s_records', ['status' => 'completed']);
    }

    public function test_inspector_can_resume_and_complete_inspection(): void
    {
        $items = SevenSChecklist::where('department', 'Test Dept Pause')->get();

        $record = SevenSRecord::create([
            'department' => 'Test Dept Pause',
            'inspector_id' => $this->inspector->id,
            'score' => 2,
            'max_score' => 2,
            'status' => 'paused',
        ]);

        // Complete all 3 items
        $response = $this->actingAs($this->inspector)->put('/seven-s/' . $record->id, [
            'action' => 'complete',
            'grades' => [
                $items[0]->id => 'B',
                $items[1]->id => 'C',
                $items[2]->id => 'B',
            ],
            'notes' => [
                $items[1]->id => 'Note for C grade item',
            ],
        ]);

        $record->refresh();
        $this->assertEquals('completed', $record->status);
        $this->assertTrue($record->isCompleted());
        $this->assertEquals(5, $record->score); // B(2) + C(1) + B(2) = 5
        $this->assertEquals(6, $record->max_score); // 3 items * 2 = 6
        $this->assertEquals(3, $record->results()->count());

        $response->assertRedirect(route('seven-s.show', $record->id));
    }

    public function test_inspector_can_delete_their_own_paused_record(): void
    {
        $record = SevenSRecord::create([
            'department' => 'Test Dept Pause',
            'inspector_id' => $this->inspector->id,
            'score' => 2,
            'max_score' => 2,
            'status' => 'paused',
        ]);

        $response = $this->actingAs($this->inspector)->delete('/seven-s/' . $record->id);
        $response->assertRedirect(route('seven-s.index'));
        $this->assertDatabaseMissing('seven_s_records', ['id' => $record->id]);
    }
}
