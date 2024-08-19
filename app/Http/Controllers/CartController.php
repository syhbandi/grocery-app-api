<?php

namespace App\Http\Controllers;

use App\Http\Requests\cart\AddToCartRequest;
use App\Http\Requests\cart\removeProductRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function add(AddToCartRequest $addToCartRequest): CartResource
    {
        $user = Auth::user();
        $data = $addToCartRequest->validated();

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $data['product_id']], ['quantity' => DB::raw('quantity+' . $data['quantity'])]);

        return new CartResource($cart);
    }

    public function reduce(AddToCartRequest $addToCartRequest)
    {
        $user = Auth::user();
        $data = $addToCartRequest->validated();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            $this->notFound('Cart is empty');
        }

        $cartItem = $cart->items()->where('product_id', $data['product_id'])->first();
        if (!$cartItem) {
            $this->notFound('Product not found');
        }

        $newQuantity = $cartItem->quantity - $data['quantity'];
        if ($newQuantity > 0) {
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem->delete();
        }

        return new CartResource($cart);
    }

    public function remove(removeProductRequest $removeProductRequest): CartResource
    {
        $user = Auth::user();
        $data = $removeProductRequest->validated();

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            $this->notFound('Cart is Empty');
        }

        $cart->items()->where('product_id', $data['product_id'])->delete();

        return new CartResource($cart);
    }

    public function show(Request $request)
    {
        $pageSize = $request->input('size', 10);
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['data' => [], 'message' => 'Cart is empty'], Response::HTTP_OK);
        }

        $cartItems = $cart->items()->with('product')->paginate($pageSize);

        return CartItemResource::collection($cartItems);
    }

    private function notFound($msg = '')
    {
        throw new HttpResponseException(response()->json([
            'message' => $msg,
        ], Response::HTTP_NOT_FOUND));
    }
}
