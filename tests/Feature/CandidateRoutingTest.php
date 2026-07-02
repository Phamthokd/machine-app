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
}
