<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $search = $request->input('search');
        $user = Auth::user();
        $query = Order::query();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $orders = $query->whereHas('user', function ($query) use ($search) {
            if ($search) {
                $query->where('full_name', 'like', '%' . $search . '%');
            }
        })->with('items.product')->paginate($size);

        return OrderResource::collection($orders);
    }

    private function notFound($msg = '')
    {
        throw new HttpResponseException(response()->json([
            'message' => $msg,
        ], Response::HTTP_NOT_FOUND));
    }

    public function store(): OrderResource
    {
        $user = Auth::user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            $this->notFound('Cart is empty');
        }

        $order = new Order();
        $order->user_id = $user->id;
        $order->total_price = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        $order->status = 'pending';
        $order->save();

        foreach ($cart->items as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $cartItem->product_id;
            $orderItem->quantity = $cartItem->quantity;
            $orderItem->price = $cartItem->product->price;
            $orderItem->save();
        }

        $cart->items()->delete();
        return new OrderResource($order);
    }

    public function get($id)
    {
        $user = Auth::user();
        $query = Order::query();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $order = $query->with('items.product.images')->where('id', $id)->first();

        if (!$order) {
            $this->notFound('Order not found');
        }

        return new OrderResource($order);
    }
}
