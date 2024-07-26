<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_store_category(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);
        $this->post('/api/categories', [
            'name' => 'fruits'
        ])->assertStatus(Response::HTTP_CREATED);
    }

    public function test_get_categories(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);
        $this->get('/api/categories')->assertStatus(Response::HTTP_OK);
    }

    public function test_get_category(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);

        $category = Category::create(['name' => 'fruits']);

        $this->get('/api/categories/' . $category->id)->assertStatus(Response::HTTP_OK);
    }

    public function test_get_category_failed(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);

        $category = Category::create(['name' => 'fruits']);

        $this->get('/api/categories/' . $category->id + 1)->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_category(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);

        $category = Category::create(['name' => 'fruits']);

        $this->delete('/api/categories/' . $category->id)->assertStatus(Response::HTTP_OK);
    }

    public function test_delete_category_failed(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);

        $category = Category::create(['name' => 'fruits']);

        $this->delete('/api/categories/' . $category->id + 1)->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_get_category_products()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        Sanctum::actingAs($user, ['admin']);

        $category = Category::create(['name' => 'fruits']);
        $product =  Product::create([
            'name' => 'Kangkung',
            'description' => 'Fresh Kangkung',
            'price' => 1000,
            'stock' => 100,
        ]);
        $product->categories()->sync([$category->id]);

        $this->get('/api/categories/' . $category->id . '/products')->assertStatus(Response::HTTP_OK);
    }
}
