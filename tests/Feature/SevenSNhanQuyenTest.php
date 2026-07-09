<?php

namespace Tests\Feature;

use App\Models\SevenSChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SevenSNhanQuyenTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        Role::firstOrCreate(['name' => 'admin']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_nhan_quyen_checklist_seeding(): void
    {
        // Run the seeder
        $this->seed(\Database\Seeders\SevenSNhanQuyenSeeder::class);

        // Assert 18 items are created
        $this->assertEquals(18, SevenSChecklist::where('department', 'Nhân quyền')->count());

        // Check a specific item is present
        $this->assertDatabaseHas('seven_s_checklists', [
            'department' => 'Nhân quyền',
            'sort_order' => 1,
            'content' => 'messages.seven_s_nhan_quyen_q1',
        ]);
    }

    public function test_admin_can_access_nhan_quyen_seven_s_create_form(): void
    {
        // Run the seeder
        $this->seed(\Database\Seeders\SevenSNhanQuyenSeeder::class);

        $response = $this->actingAs($this->admin)
            ->get('/seven-s/create?department=Nhân quyền');

        $response->assertStatus(200);
        
        // Assert translation string is rendered on the page
        $response->assertSee('Hồ sơ, giấy tờ được phân loại');
    }
}
