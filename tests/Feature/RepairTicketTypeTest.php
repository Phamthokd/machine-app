<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Machine;
use App\Models\User;
use App\Models\RepairTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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

    public function test_contractor_can_create_ticket_with_multiple_helpers(): void
    {
        $response = $this->actingAs($this->contractorUser)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Sự cố nhà xưởng',
                'noi_dung_sua_chua' => 'Khắc phục sự cố',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'contractor',
                'nguoi_ho_tro' => ['Người hỗ trợ 1', 'Người hỗ trợ 2']
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        
        $ticket = RepairTicket::where('machine_id', $this->machine->id)->first();
        $this->assertNotNull($ticket);
        $this->assertEquals($this->contractorUser->id, $ticket->mechanic_id);
        $this->assertEquals($ticket->created_at->toDateTimeString(), \Carbon\Carbon::parse($ticket->started_at)->toDateTimeString());
        $this->assertEquals('Người hỗ trợ 1, Người hỗ trợ 2', $ticket->nguoi_ho_tro);
    }

    public function test_standard_user_without_roles_can_create_repair_ticket(): void
    {
        $standardUser = User::factory()->create();

        $response = $this->actingAs($standardUser)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Lỗi màn hình cảm ứng',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'mechanic',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'mechanic',
            'status' => 'pending',
            'nguyen_nhan' => 'Lỗi màn hình cảm ứng',
            'created_by' => $standardUser->id,
            'mechanic_id' => null,
        ]);
    }

    public function test_audit_user_can_create_repair_ticket(): void
    {
        Role::firstOrCreate(['name' => 'audit']);
        $auditUser = User::factory()->create();
        $auditUser->assignRole('audit');

        $response = $this->actingAs($auditUser)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Kẹt dây đai máy',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'mechanic',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'mechanic',
            'status' => 'pending',
            'nguyen_nhan' => 'Kẹt dây đai máy',
            'created_by' => $auditUser->id,
            'mechanic_id' => null,
        ]);
    }

    public function test_standard_user_can_view_contractor_history(): void
    {
        $standardUser = User::factory()->create();

        $response = $this->actingAs($standardUser)
            ->get('/repairs/contractor');

        $response->assertOk();
    }

    public function test_senior_manager_rejects_and_deletes_ticket(): void
    {
        Role::firstOrCreate(['name' => 'senior_manager']);
        $seniorManager = User::factory()->create();
        $seniorManager->assignRole('senior_manager');

        $ticket = RepairTicket::create([
            'code' => 'RM-REJECT-9999',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'N/A',
            'nguyen_nhan' => 'Lỗi chập nguồn điện',
            'started_at' => now(),
            'status' => 'pending',
            'approval_status' => 'pending_approval',
            'type' => 'contractor',
            'created_by' => $this->teamLeader->id,
        ]);

        $response = $this->actingAs($seniorManager)
            ->post("/repairs/{$ticket->id}/reject", [
                'approval_note' => 'Thiếu mô tả sự cố chi tiết',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('repair_tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_supervisor_can_access_history_and_requests(): void
    {
        Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        // Can view repair history (repairs.view / /repairs)
        $response = $this->actingAs($supervisor)->get('/repairs');
        $response->assertOk();

        // Can view movement history (movement_history.view / /movement-history)
        $response = $this->actingAs($supervisor)->get('/movement-history');
        $response->assertOk();

        // Can view repair requests (repairs.manage / /repair-requests)
        $response = $this->actingAs($supervisor)->get('/repair-requests');
        $response->assertOk();

        // Create a repair ticket
        $ticket = RepairTicket::create([
            'code' => 'RM-TEST-SUPERVISOR',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'N/A',
            'nguyen_nhan' => 'Lỗi chập nguồn điện',
            'started_at' => now(),
            'status' => 'pending',
            'type' => 'contractor',
            'created_by' => $this->teamLeader->id,
        ]);

        // Supervisor cannot accept ticket (403)
        $response = $this->actingAs($supervisor)->post("/repairs/{$ticket->id}/accept");
        $response->assertStatus(403);

        // Supervisor cannot edit ticket (403)
        $response = $this->actingAs($supervisor)->get("/repairs/{$ticket->id}/edit");
        $response->assertStatus(403);

        // Supervisor cannot update ticket (403)
        $response = $this->actingAs($supervisor)->put("/repairs/{$ticket->id}", [
            'nguyen_nhan' => 'Lỗi khác',
            'noi_dung_sua_chua' => 'Sửa khác',
            'started_at' => now()->format('Y-m-d H:i:s'),
        ]);
        $response->assertStatus(403);
    }

    public function test_senior_manager_can_access_history_requests_seven_s_and_audit(): void
    {
        Role::firstOrCreate(['name' => 'senior_manager']);
        Role::firstOrCreate(['name' => '7s']);
        Role::firstOrCreate(['name' => 'audit']);

        $seniorManager = User::factory()->create();
        $seniorManager->assignRole('senior_manager');

        // Can view repair history (repairs.view / /repairs)
        $response = $this->actingAs($seniorManager)->get('/repairs');
        $response->assertOk();

        // Can view movement history (movement_history.view / /movement-history)
        $response = $this->actingAs($seniorManager)->get('/movement-history');
        $response->assertOk();

        // Can view repair requests (repairs.manage / /repair-requests)
        $response = $this->actingAs($seniorManager)->get('/repair-requests');
        $response->assertOk();

        // Create a repair ticket
        $ticket = RepairTicket::create([
            'code' => 'RM-TEST-SENIOR-MGR',
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'ma_hang' => 'N/A',
            'cong_doan' => 'N/A',
            'noi_dung_sua_chua' => 'N/A',
            'nguyen_nhan' => 'Lỗi chập nguồn điện',
            'started_at' => now(),
            'status' => 'pending',
            'type' => 'contractor',
            'created_by' => $this->teamLeader->id,
        ]);

        // Senior Manager cannot accept ticket (403)
        $response = $this->actingAs($seniorManager)->post("/repairs/{$ticket->id}/accept");
        $response->assertStatus(403);

        // Senior Manager cannot edit ticket (403)
        $response = $this->actingAs($seniorManager)->get("/repairs/{$ticket->id}/edit");
        $response->assertStatus(403);

        // Senior Manager cannot update ticket (403)
        $response = $this->actingAs($seniorManager)->put("/repairs/{$ticket->id}", [
            'nguyen_nhan' => 'Lỗi khác',
            'noi_dung_sua_chua' => 'Sửa khác',
            'started_at' => now()->format('Y-m-d H:i:s'),
        ]);
        $response->assertStatus(403);

        // Can access /seven-s list
        $response = $this->actingAs($seniorManager)->get('/seven-s');
        $response->assertOk();

        // Can access /seven-s/create
        $response = $this->actingAs($seniorManager)->get('/seven-s/create');
        $response->assertOk();

        // Can access /audits list
        $response = $this->actingAs($seniorManager)->get('/audits');
        $response->assertOk();
    }

    public function test_bok_repair_workflow(): void
    {
        Role::firstOrCreate(['name' => 'bok']);
        $bokUser = User::factory()->create();
        $bokUser->assignRole('bok');

        Permission::firstOrCreate(['name' => 'repairs.create_bok']);
        $this->teamLeader->givePermissionTo('repairs.create_bok');

        // 1. Team leader creates BOK ticket
        $response = $this->actingAs($this->teamLeader)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Báo sửa BOK lỗi xilanh',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'bok',
            ]);

        $response->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
        $this->assertDatabaseHas('repair_tickets', [
            'machine_id' => $this->machine->id,
            'type' => 'bok',
            'mo_ta_loi' => 'Báo sửa BOK lỗi xilanh',
            'nguyen_nhan' => 'N/A',
            'status' => 'pending',
        ]);

        $ticket = RepairTicket::where('machine_id', $this->machine->id)->where('type', 'bok')->first();

        // 2. BOK user accepts the ticket
        $response = $this->actingAs($bokUser)->post("/repairs/{$ticket->id}/accept");
        $response->assertRedirect("/repairs/{$ticket->id}/edit");

        $ticket->refresh();
        $this->assertEquals($bokUser->id, $ticket->mechanic_id);

        // 3. BOK user updates the ticket
        $response = $this->actingAs($bokUser)->put("/repairs/{$ticket->id}", [
            'nguyen_nhan' => 'Xilanh bị xì hơi nặng',
            'noi_dung_sua_chua' => 'Đã thay thế gioăng phớt mới',
            'started_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/repair-requests');
        $ticket->refresh();
        $this->assertEquals('Xilanh bị xì hơi nặng', $ticket->nguyen_nhan);
        $this->assertEquals('Đã thay thế gioăng phớt mới', $ticket->noi_dung_sua_chua);
        $this->assertEquals('submitted', $ticket->status);
    }

    public function test_multiple_bok_tickets_can_be_created(): void
    {
        Permission::firstOrCreate(['name' => 'repairs.create_bok']);
        $this->teamLeader->givePermissionTo('repairs.create_bok');

        // 1. Create first BOK ticket
        $response1 = $this->actingAs($this->teamLeader)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Lỗi xilanh',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'bok',
            ]);
        $response1->assertRedirect("/m/{$this->machine->ma_thiet_bi}");

        // 2. Create second BOK ticket while the first one is still pending (not ended)
        $response2 = $this->actingAs($this->teamLeader)
            ->post('/repairs', [
                'machine_id' => $this->machine->id,
                'department_id' => $this->department->id,
                'nguyen_nhan' => 'Lỗi cảm biến hành trình',
                'started_at' => now()->format('Y-m-d H:i:s'),
                'type' => 'bok',
            ]);
        $response2->assertRedirect("/m/{$this->machine->ma_thiet_bi}");

        $this->assertEquals(2, RepairTicket::where('machine_id', $this->machine->id)->where('type', 'bok')->count());
    }

    public function test_create_bok_ticket_form_accessible_when_pending_exists(): void
    {
        Permission::firstOrCreate(['name' => 'repairs.create_bok']);
        $this->teamLeader->givePermissionTo('repairs.create_bok');

        // 1. Create a pending mechanic ticket
        RepairTicket::create([
            'machine_id' => $this->machine->id,
            'department_id' => $this->department->id,
            'nguyen_nhan' => 'Lỗi kẹt kim cơ điện',
            'type' => 'mechanic',
            'status' => 'pending',
            'created_by' => $this->teamLeader->id,
        ]);

        // 2. Try to visit create page for BOK ticket
        $response = $this->actingAs($this->teamLeader)
            ->get("/repairs/create?machine={$this->machine->ma_thiet_bi}&type=bok");

        $response->assertOk();

        // 3. Try to visit create page for another mechanic ticket (should redirect)
        $response2 = $this->actingAs($this->teamLeader)
            ->get("/repairs/create?machine={$this->machine->ma_thiet_bi}&type=mechanic");

        $response2->assertRedirect("/m/{$this->machine->ma_thiet_bi}");
    }
}
