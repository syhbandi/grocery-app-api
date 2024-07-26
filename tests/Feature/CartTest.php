<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();

        Sanctum::actingAs($user, ['user']);

        $this->post('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 1
        ])->assertStatus(Response::HTTP_CREATED);
    }

    public function test_add_to_cart_failed(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();

        Sanctum::actingAs($user, ['user']);

        $this->post('/api/cart', [
            'product_id' => $product->id + 1,
            'quantity' => 1
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_add_to_cart_unauthorized(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();

        // Sanctum::actingAs($user, ['user']);

        $this->post('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 1
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_reduce_cart(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], ['quantity' => 1]);

        Sanctum::actingAs($user, ['user']);

        $this->post('/api/cart/reduce', [
            'product_id' => $product->id,
            'quantity' => 1
        ])->assertStatus(Response::HTTP_OK);
    }

    public function test_show_cart(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], ['quantity' => 1]);

        Sanctum::actingAs($user, ['user']);

        $this->get('/api/cart')->assertStatus(Response::HTTP_OK);
    }

    public function test_remove_cart_product(): void
    {
        $this->seed([UserSeeder::class, ProductSeeder::class]);

        $user = User::first();
        $product = Product::first();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], ['quantity' => 1]);

        Sanctum::actingAs($user, ['user']);

        $this->delete('/api/cart', [
            'product_id' => $product->id
        ])->assertStatus(Response::HTTP_OK);
    }
}
