<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_candidate_routing_and_access_scoping()
    {
        // 1. Create users with roles
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $hr = User::factory()->create();
        $hr->assignRole('hr');

        $manager1 = User::factory()->create();
        $manager1->assignRole('senior_manager');

        $manager2 = User::factory()->create();
        $manager2->assignRole('senior_manager');

        // 2. Create Candidate
        $candidate = Candidate::create([
            'full_name' => 'Nguyen Van A',
            'gender' => 'male',
            'phone' => '0987654321',
            'position_applied' => 'Developer',
        ]);

        // 3. Manager 1 and Manager 2 cannot see the candidate initially
        $this->actingAs($manager1);
        $response = $this->get('/candidates');
        $response->assertStatus(200);
        $response->assertDontSee('Nguyen Van A');

        $responseDetail = $this->get("/candidates/{$candidate->id}");
        $responseDetail->assertStatus(403);

        // 4. Admin / HR can see the candidate and route it
        $this->actingAs($hr);
        $response = $this->get('/candidates');
        $response->assertStatus(200);
        $response->assertSee('Nguyen Van A');

        $responseDetail = $this->get("/candidates/{$candidate->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Nguyen Van A');

        // Route candidate to manager 1
        $routeResponse = $this->post("/candidates/{$candidate->id}/route", [
            'senior_manager_ids' => [$manager1->id],
        ]);
        $routeResponse->assertRedirect();
        $this->assertTrue($candidate->fresh()->seniorManagers->contains($manager1->id));
        $this->assertFalse($candidate->fresh()->seniorManagers->contains($manager2->id));

        // 5. Manager 1 can now view the candidate
        $this->actingAs($manager1);
        $response = $this->get('/candidates');
        $response->assertStatus(200);
        $response->assertSee('Nguyen Van A');

        $responseDetail = $this->get("/candidates/{$candidate->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Nguyen Van A');

        // Manager 2 still cannot view the candidate
        $this->actingAs($manager2);
        $response = $this->get('/candidates');
        $response->assertStatus(200);
        $response->assertDontSee('Nguyen Van A');

        $responseDetail = $this->get("/candidates/{$candidate->id}");
        $responseDetail->assertStatus(403);
    }

    public function test_user_with_candidates_create_permission_can_access_candidates()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('candidates.create');

        $this->actingAs($user);

        $response = $this->get('/candidates');
        $response->assertStatus(200);

        $responseCreate = $this->get('/candidates/create');
        $responseCreate->assertStatus(200);

        $responseStore = $this->post('/candidates', [
            'full_name' => 'Test Store Candidate',
            'gender' => 'male',
            'phone' => '0999888777',
            'position_applied' => 'Developer',
            'marital_status' => 'single',
        ]);

        $candidate = Candidate::where('full_name', 'Test Store Candidate')->first();
        $this->assertNotNull($candidate);
        $responseStore->assertRedirect(route('candidates.show', $candidate->id));
    }

    public function test_candidates_create_permission_user_cannot_see_candidates_older_than_1_hour()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('candidates.create');

        $recentCandidate = Candidate::create([
            'full_name' => 'Recent Candidate',
            'gender' => 'male',
            'phone' => '0911111111',
            'position_applied' => 'Operator',
            'created_at' => now()->subMinutes(30),
        ]);

        $oldCandidate = Candidate::create([
            'full_name' => 'Old Candidate',
            'gender' => 'female',
            'phone' => '0922222222',
            'position_applied' => 'Technician',
        ]);
        Candidate::where('id', $oldCandidate->id)->update(['created_at' => now()->subHours(2)]);
        $oldCandidate->refresh();

        $this->actingAs($user);

        // Index page should show recent candidate but hide old candidate
        $response = $this->get('/candidates');
        $response->assertStatus(200);
        $response->assertSee('Recent Candidate');
        $response->assertDontSee('Old Candidate');

        // Show page for recent candidate works
        $responseRecent = $this->get("/candidates/{$recentCandidate->id}");
        $responseRecent->assertStatus(200);

        // Show page for old candidate returns 403
        $responseOld = $this->get("/candidates/{$oldCandidate->id}");
        $responseOld->assertStatus(403);
    }

    public function test_candidate_edit_and_update_before_routing()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('candidates.create');

        $candidate = Candidate::create([
            'full_name' => 'Original Candidate Name',
            'gender' => 'male',
            'phone' => '0988776655',
            'position_applied' => 'Developer',
        ]);

        $this->actingAs($user);

        // Edit page accessible before HR routing
        $responseEdit = $this->get("/candidates/{$candidate->id}/edit");
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Original Candidate Name');

        // Update candidate
        $responseUpdate = $this->put("/candidates/{$candidate->id}", [
            'full_name' => 'Updated Candidate Name',
            'gender' => 'male',
            'phone' => '0988776655',
            'position_applied' => 'Senior Developer',
            'marital_status' => 'single',
        ]);

        $responseUpdate->assertRedirect(route('candidates.show', $candidate->id));
        $this->assertEquals('Updated Candidate Name', $candidate->fresh()->full_name);
        $this->assertEquals('Senior Developer', $candidate->fresh()->position_applied);

        // Once routed to senior manager, edit is blocked for non-admins
        $seniorManager = User::factory()->create();
        $seniorManager->assignRole('senior_manager');
        $candidate->seniorManagers()->attach($seniorManager->id);

        $responseEditBlocked = $this->get("/candidates/{$candidate->id}/edit");
        $responseEditBlocked->assertStatus(403);
    }

    public function test_candidate_edit_is_blocked_after_30_minutes()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('candidates.create');

        $candidate = Candidate::create([
            'full_name' => 'Candidate 35 mins old',
            'gender' => 'male',
            'phone' => '0977665544',
            'position_applied' => 'Operator',
        ]);
        Candidate::where('id', $candidate->id)->update(['created_at' => now()->subMinutes(35)]);

        $this->actingAs($user);

        // Edit page is blocked after 30 minutes
        $responseEdit = $this->get("/candidates/{$candidate->id}/edit");
        $responseEdit->assertStatus(403);

        // Update action is also blocked after 30 minutes
        $responseUpdate = $this->put("/candidates/{$candidate->id}", [
            'full_name' => 'Should Not Update',
            'gender' => 'male',
            'phone' => '0977665544',
            'position_applied' => 'Operator',
            'marital_status' => 'single',
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_phone_and_cccd_numeric_and_length_validation()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('candidates.create');
        $this->actingAs($user);

        // Invalid phone (> 10 digits)
        $responseInvalidPhone = $this->post('/candidates', [
            'full_name' => 'Invalid Phone Candidate',
            'gender' => 'male',
            'phone' => '098877665544', // 12 digits
            'position_applied' => 'Staff',
            'marital_status' => 'single',
        ]);
        $responseInvalidPhone->assertSessionHasErrors(['phone']);

        // Invalid CCCD (> 12 digits)
        $responseInvalidCccd = $this->post('/candidates', [
            'full_name' => 'Invalid CCCD Candidate',
            'gender' => 'male',
            'phone' => '0988776655',
            'id_number' => '1234567890123', // 13 digits
            'position_applied' => 'Staff',
            'marital_status' => 'single',
        ]);
        $responseInvalidCccd->assertSessionHasErrors(['id_number']);

        // Valid phone (10 digits) and valid CCCD (12 digits)
        $responseValid = $this->post('/candidates', [
            'full_name' => 'Valid Candidate',
            'gender' => 'male',
            'phone' => '0988776655',
            'id_number' => '012345678901', // 12 digits
            'position_applied' => 'Staff',
            'marital_status' => 'single',
        ]);
        $responseValid->assertSessionHasNoErrors();
    }

    public function test_hr_can_add_optional_notes_and_photo_when_routing_candidate()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $hr = User::factory()->create();
        $hr->assignRole('hr');

        $manager = User::factory()->create();
        $manager->assignRole('senior_manager');

        $candidate = Candidate::create([
            'full_name' => 'Candidate for HR Review',
            'gender' => 'female',
            'phone' => '0912345678',
            'position_applied' => 'QA Inspector',
        ]);

        $this->actingAs($hr);

        $fakePhoto = \Illuminate\Http\UploadedFile::fake()->image('hr_assessment.jpg');

        $response = $this->post("/candidates/{$candidate->id}/route", [
            'senior_manager_ids' => [$manager->id],
            'hr_notes' => 'Ứng viên có kỹ năng giao tiếp tốt, tác phong chuyên nghiệp, phù hợp với QA.',
            'hr_photo' => $fakePhoto,
        ]);

        $response->assertRedirect();
        $candidate->refresh();

        $this->assertEquals('Ứng viên có kỹ năng giao tiếp tốt, tác phong chuyên nghiệp, phù hợp với QA.', $candidate->hr_notes);
        $this->assertNotNull($candidate->hr_photo_path);

        $storedPath = str_replace('storage/', '', $candidate->hr_photo_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($storedPath);

        // Check manager can view the HR review note
        $this->actingAs($manager);
        $showResponse = $this->get("/candidates/{$candidate->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Ứng viên có kỹ năng giao tiếp tốt, tác phong chuyên nghiệp, phù hợp với QA.');

        // Test HR can remove the photo
        $this->actingAs($hr);
        $this->post("/candidates/{$candidate->id}/route", [
            'senior_manager_ids' => [$manager->id],
            'hr_notes' => 'Cập nhật nhận xét: đã hoàn thành bài test.',
            'remove_hr_photo' => '1',
        ]);

        $candidate->refresh();
        $this->assertEquals('Cập nhật nhận xét: đã hoàn thành bài test.', $candidate->hr_notes);
        $this->assertNull($candidate->hr_photo_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($storedPath);
    }
}

