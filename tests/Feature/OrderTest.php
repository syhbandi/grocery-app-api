<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], ['quantity' => 1]);

        Sanctum::actingAs($user, ['user']);

        $this->post('/api/order')->assertStatus(Response::HTTP_CREATED);
    }

    public function test_create_order_no_cart(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();

        Sanctum::actingAs($user, ['user']);

        $this->post('/api/order')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_show_order(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();

        Sanctum::actingAs($user, ['user']);

        $this->get('/api/order')->assertStatus(Response::HTTP_OK);
    }
}
