<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    private function createUser()
    {
        return  User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }

    public function test_store_success(): void
    {
        $user = $this->createUser();
        Sanctum::actingAs($user, ['admin']);

        $response = $this->post('/api/products', [
            'name' => 'Kangkung',
            'description' => '',
            'price' => 1000,
            'stock' => 10,
            'image' => '',
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_store_unauthorized(): void
    {
        $response = $this->post('/api/products', [
            'name' => 'Kangkung',
            'description' => '',
            'price' => 1000,
            'stock' => 10,
            'image' => '',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_store_validation_error(): void
    {
        $user = $this->createUser();
        Sanctum::actingAs($user, ['admin']);
        $response = $this->post('/api/products', [
            'name' => 'Kangkung',
            'description' => '',
            'price' => 1000,
            'stock' => '',
            'image' => '',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_get_paginated_products(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);
        Sanctum::actingAs($user, ['user']);
        $response = $this->get('/api/products');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_get_product(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        $product = Product::first();
        Sanctum::actingAs($user, ['admin']);
        $response = $this->get('/api/products/' . $product->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_get_product_failed(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        $product = Product::first();
        Sanctum::actingAs($user, ['admin']);
        $response = $this->get('/api/products/' . $product->id + 1);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_product(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        Sanctum::actingAs($user, ['admin']);
        $product = Product::first();
        $response = $this->put('/api/products/' . $product->id, ['name' => 'kungkang']);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_update_product_failed(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        Sanctum::actingAs($user, ['admin']);
        $product = Product::first();
        $response = $this->put('/api/products/' . $product->id + 1, ['name' => 'kungkang']);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_product_unauthorized(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['user']);
        $response = $this->put('/api/products/1', ['name' => 'kungkang']);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_delete_product(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        $product = Product::first();
        Sanctum::actingAs($user, ['admin']);
        $response = $this->delete('/api/products/' . $product->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_delete_product_failed(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = $this->createUser();
        $product = Product::first();
        Sanctum::actingAs($user, ['admin']);
        $response = $this->delete('/api/products/' . $product->id + 1);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_product_unauthorized(): void
    {
        $this->seed([ProductSeeder::class]);
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['user']);
        $response = $this->delete('/api/products/1');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
