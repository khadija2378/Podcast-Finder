<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([

                     'user' => ['id','name','email','role']
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'login@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'user' => ['id','name','email','role']
                 ]);
    }

    /** @test */
   public function admin_can_get_users()
{
    User::factory()->count(3)->create();

    $user = User::first();
    $response = $this->actingAs($user)->getJson('/api/users');

    $response->assertStatus(200)
             ->assertJsonCount(3);
}

}
