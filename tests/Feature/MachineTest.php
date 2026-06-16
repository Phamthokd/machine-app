<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MachineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $warehouse;
    private User $standardUser;
    private Machine $machine;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'warehouse']);

        // Create users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->warehouse = User::factory()->create();
        $this->warehouse->assignRole('warehouse');

        $this->standardUser = User::factory()->create();

        // Create Department and Machine
        $this->department = Department::create([
            'code' => 'TEST_DEPT',
            'name' => 'Test Department',
            'type' => 'team',
        ]);

        $this->machine = Machine::create([
            'ma_thiet_bi' => 'MAY-001',
            'ten_thiet_bi' => 'Test Machine',
            'current_department_id' => $this->department->id,
        ]);
    }

    public function test_admin_can_delete_machine(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete("/machines/{$this->machine->id}");

        $response->assertRedirect('/machines');
        $this->assertDatabaseMissing('machines', [
            'id' => $this->machine->id,
        ]);
    }

    public function test_warehouse_user_can_delete_machine(): void
    {
        $response = $this->actingAs($this->warehouse)
            ->delete("/machines/{$this->machine->id}");

        $response->assertRedirect('/machines');
        $this->assertDatabaseMissing('machines', [
            'id' => $this->machine->id,
        ]);
    }

    public function test_standard_user_cannot_delete_machine(): void
    {
        $response = $this->actingAs($this->standardUser)
            ->delete("/machines/{$this->machine->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('machines', [
            'id' => $this->machine->id,
        ]);
    }
}
