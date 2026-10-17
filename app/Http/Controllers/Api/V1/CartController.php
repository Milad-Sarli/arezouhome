<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->abandonedCart()->first();

        return response()->json(['items' => $cart->items ?? []]);
    }

    public function sync(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        $user->abandonedCart()->updateOrCreate(
            ['user_id' => $user->id],
            ['items' => $data['items'], 'notified_at' => null]
        );

        return response()->json(['message' => 'ذخیره شد']);
    }

    public function clear(Request $request)
    {
        $request->user()->abandonedCart()->delete();

        return response()->json(['message' => 'پاک شد']);
    }
}
