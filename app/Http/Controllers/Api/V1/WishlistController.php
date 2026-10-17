<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->user()->wishlists()->pluck('product_id');

        return response()->json(['ids' => $ids]);
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $user = $request->user();
        $existing = $user->wishlists()->where('product_id', $data['product_id'])->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['added' => false]);
        }

        $user->wishlists()->create(['product_id' => $data['product_id']]);

        return response()->json(['added' => true], 201);
    }

    public function sync(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        $user = $request->user();

        foreach ($data['ids'] as $pid) {
            $user->wishlists()->firstOrCreate(['product_id' => $pid]);
        }

        return response()->json(['ids' => $user->wishlists()->pluck('product_id')]);
    }
}
