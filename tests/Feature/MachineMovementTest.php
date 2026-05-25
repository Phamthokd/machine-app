<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Machine;
use App\Models\MachineMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MachineMovementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Department $deptA;
    private Department $deptB;
    private Department $deptC;
    private Machine $machine;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        Role::firstOrCreate(['name' => 'admin']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create Departments
        $this->deptA = Department::create([
            'code' => 'DEPT_A',
            'name' => 'Department A',
            'type' => 'team',
        ]);

        $this->deptB = Department::create([
            'code' => 'DEPT_B',
            'name' => 'Department B',
            'type' => 'team',
        ]);

        $this->deptC = Department::create([
            'code' => 'DEPT_C',
            'name' => 'Department C',
            'type' => 'team',
        ]);

        // Create Machine
        $this->machine = Machine::create([
            'ma_thiet_bi' => 'MAY-001',
            'ten_thiet_bi' => 'Test Machine',
            'current_department_id' => $this->deptA->id,
        ]);
    }

    public function test_admin_can_view_movement_history(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/movement-history');

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_movement_history_by_department(): void
    {
        // Movement 1: deptA -> deptB
        MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptA->id,
            'to_department_id' => $this->deptB->id,
            'user_id' => $this->admin->id,
            'note' => 'Move to B',
        ]);

        // Movement 2: deptB -> deptC
        MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptB->id,
            'to_department_id' => $this->deptC->id,
            'user_id' => $this->admin->id,
            'note' => 'Move to C',
        ]);

        // Filter by deptC - should only see Move to C
        $response = $this->actingAs($this->admin)
            ->get('/movement-history?department_id=' . $this->deptC->id);

        $response->assertStatus(200);
        $response->assertSee('Move to C');
        $response->assertDontSee('Move to B');

        // Filter by deptB - should see both (since deptB is 'to' in first movement and 'from' in second movement)
        $response = $this->actingAs($this->admin)
            ->get('/movement-history?department_id=' . $this->deptB->id);

        $response->assertStatus(200);
        $response->assertSee('Move to C');
        $response->assertSee('Move to B');
    }

    public function test_admin_can_filter_movement_history_by_dates(): void
    {
        // Create movement today
        MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptA->id,
            'to_department_id' => $this->deptB->id,
            'user_id' => $this->admin->id,
            'note' => 'Today Movement',
        ]);

        // Create movement in the past
        $movPast = MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptA->id,
            'to_department_id' => $this->deptB->id,
            'user_id' => $this->admin->id,
            'note' => 'Past Movement',
        ]);
        $movPast->created_at = now()->subDays(5);
        $movPast->save();

        // Filter for last 2 days
        $response = $this->actingAs($this->admin)
            ->get('/movement-history?start_date=' . now()->subDays(2)->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertSee('Today Movement');
        $response->assertDontSee('Past Movement');
    }

    public function test_admin_can_export_filtered_movement_history(): void
    {
        // Movement 1
        MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptA->id,
            'to_department_id' => $this->deptB->id,
            'user_id' => $this->admin->id,
            'note' => 'Move to B',
        ]);

        // Movement 2
        MachineMovement::create([
            'machine_id' => $this->machine->id,
            'from_department_id' => $this->deptB->id,
            'to_department_id' => $this->deptC->id,
            'user_id' => $this->admin->id,
            'note' => 'Move to C',
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/movement-history/export?department_id=' . $this->deptC->id);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Move to C', $content);
        $this->assertStringNotContainsString('Move to B', $content);
    }
}
