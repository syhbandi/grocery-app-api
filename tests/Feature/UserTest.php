<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_register_success(): void
    {
        $this->post('/api/user', [
            'username' => 'admin', 'full_name' => 'Administrator', 'password' => '123456', 'email' => 'admin@grocery.com'
        ])->assertStatus(Response::HTTP_CREATED);
    }

    public function test_register_password_weak(): void
    {
        $this->post('/api/user', [
            'username' => 'admin', 'full_name' => 'Administrator', 'password' => '123', 'email' => 'admin@grocery.com'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_register_failed(): void
    {
        $this->post('/api/user', [
            'username' => ''
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_register_username_email_taken(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/user', [
            'username' => 'admin', 'full_name' => 'Administrator', 'password' => '123456', 'email' => 'admin@grocery.com'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_login_success(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/user/login', ['email' => 'admin@grocery.com', 'password' => '123456'])->assertStatus(Response::HTTP_OK);
    }

    public function test_login_failed(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/user/login', ['email' => 'admin@grocery.com', 'password' => '123457'])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_login_validation_error(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/user/login', ['email' => 'admin@grocery.com', 'password' => ''])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_logout_success(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($user, ['*']);
        $response = $this->delete('/api/user');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, $user->tokens);
    }

    public function test_get_user_success(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($user, ['*']);
        $response = $this->get('/api/user');
        $response->assertStatus(Response::HTTP_OK);
    }
}
