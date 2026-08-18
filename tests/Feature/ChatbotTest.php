<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_chat_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/chatbot', [
            'message' => 'Hướng dẫn quy trình phiếu phỏng vấn ứng viên?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply', 'provider']);
        $this->assertStringContainsString('Ứng viên', $response->json('reply'));
    }
}
