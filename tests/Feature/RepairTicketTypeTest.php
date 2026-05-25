<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Machine;
use App\Models\User;
use App\Models\RepairTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RepairTicketTypeTest extends TestCase
{
    use RefreshDatabase;

    private User $teamLeader;
    private User $admin;
    private User $contractorUser;
    private Machine $machine;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'team_leader']);
        Role::firstOrCreate(['name' => 'contractor']);

        // Create Users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->teamLeader = User::factory()->create();
        $this->teamLeader->assignRole('team_leader');

        $this->contractorUser = User::factory()->create();
        $this->contractorUser->assignRole('contractor');

        // Create Department and Machine
        $this->department = Department::create([
            'code' => 'TO_01',
            'name' => 'Tổ 01',
            'type' => 'team',
        ]);

        $this->machine = Machine::create([
            'ma_thiet_bi' => 'MAY-001',
            'ten_thiet_bi' => 'Máy may JUKI DDL-8700',
            'current_department_id' => $this->department->id,
        ]);
    }

    public function test_team_leader_can_create_mechanic_repair_ticket(): void
    {
        $response = $this->actingAs($this->teamLeader)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Máy bị kẹt kim',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'mechanic',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'mechanic',
            'nguyen_nhan' => 'Máy bị kẹt kim',
        ]);
    }

    public function test_team_leader_can_create_contractor_repair_ticket(): void
    {
        $response = $this->actingAs($this->teamLeader)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Hệ thống điện chập cháy',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'contractor',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'contractor',
            'nguyen_nhan' => 'Hệ thống điện chập cháy',
        ]);
    }

    public function test_admin_can_create_mechanic_repair_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'ma_hang' => 'MH-01',
                'cong_doan' => 'CD-01',
                'nguyen_nhan' => 'Hư motor',
                'noi_dung_sua_chua' => 'Thay motor mới',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'mechanic',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'mechanic',
            'nguyen_nhan' => 'Hư motor',
        ]);
    }

    public function test_admin_can_create_contractor_repair_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'ma_hang' => 'MH-02',
                'cong_doan' => 'CD-02',
                'nguyen_nhan' => 'Bể đường ống nước',
                'noi_dung_sua_chua' => 'Hàn lại ống nước',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'contractor',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'contractor',
            'nguyen_nhan' => 'Bể đường ống nước',
        ]);
    }

    public function test_contractor_user_always_saves_as_contractor_type(): void
    {
        $response = $this->actingAs($this->contractorUser)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Sự cố nhà xưởng',
                'noi_dung_sua_chua' => 'Khắc phục sự cố',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'mechanic', // Send mechanic, but contractor role should force contractor type
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'contractor',
            'nguyen_nhan' => 'Sự cố nhà xưởng',
        ]);
    }

    public function test_admin_can_filter_repair_requests_by_type(): void
    {
        // Create a pending mechanic request
        $mechanicTicket = RepairTicket::create([
            'code' => 'RM-20260523-0001',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'N/A',
            'nguyen_nhan' => 'Máy hỏng cạch cạch',
            'started_at' => now(),
            'status' => 'pending',
            'type' => 'mechanic',
            'created_by' => $this->teamLeader->id,
        ]);

        // Create a pending contractor request
        $contractorTicket = RepairTicket::create([
            'code' => 'RM-20260523-0002',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'N/A',
            'nguyen_nhan' => 'Hư bóng đèn trần',
            'started_at' => now(),
            'status' => 'pending',
            'type' => 'contractor',
            'created_by' => $this->teamLeader->id,
        ]);

        // Filter by mechanic
        $response = $this->actingAs($this->admin)
            ->get('/repair-requests?type=mechanic');

        $response->assertOk();
        $response->assertViewHas('type', 'mechanic');
        $response->assertSee('Máy hỏng cạch cạch');
        $response->assertDontSee('Hư bóng đèn trần');

        // Filter by contractor
        $response = $this->actingAs($this->admin)
            ->get('/repair-requests?type=contractor');

        $response->assertOk();
        $response->assertViewHas('type', 'contractor');
        $response->assertSee('Hư bóng đèn trần');
        $response->assertDontSee('Máy hỏng cạch cạch');
    }

    public function test_contractor_history_shows_stt_and_repairer(): void
    {
        // Create a contractor ticket with an assigned mechanic
        $contractorTicket = RepairTicket::create([
            'code' => 'RM-20260523-9999',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'Đã sửa bóng đèn',
            'nguyen_nhan' => 'Hư bóng đèn trần',
            'started_at' => now(),
            'status' => 'done',
            'type' => 'contractor',
            'created_by' => $this->teamLeader->id,
            'mechanic_id' => $this->contractorUser->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/repairs/contractor');

        $response->assertOk();
        // Should show STT (1)
        $response->assertSee('1');
        // Should NOT show the code
        $response->assertDontSee('RM-20260523-9999');
        // Should show the mechanic's name
        $response->assertSee($this->contractorUser->name);
    }
}
