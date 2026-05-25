<?php

namespace Tests\Feature;

use App\Models\SevenSChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SevenSPhongMauTest extends TestCase
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

    public function test_phong_mau_checklist_seeding(): void
    {
        // Run the seeder
        $this->seed(\Database\Seeders\SevenSPhongMauSeeder::class);

        // Assert 28 items are created
        $this->assertEquals(28, SevenSChecklist::where('department', 'Phòng mẫu')->count());

        // Check a specific item is present
        $this->assertDatabaseHas('seven_s_checklists', [
            'department' => 'Phòng mẫu',
            'sort_order' => 1,
            'content' => 'messages.seven_s_phong_mau_q1',
        ]);
    }

    public function test_admin_can_access_phong_mau_seven_s_create_form(): void
    {
        // Run the seeder
        $this->seed(\Database\Seeders\SevenSPhongMauSeeder::class);

        $response = $this->actingAs($this->admin)
            ->get('/seven-s/create?department=Phòng mẫu');

        $response->assertStatus(200);
        
        // Assert translation string is rendered on the page
        $response->assertSee('Số liệu trên báo cáo chính xác');
    }
}
