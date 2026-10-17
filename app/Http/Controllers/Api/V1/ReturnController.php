<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $rows = $request->user()->returnRequests()->with(['order', 'product'])->latest()->paginate(10);

        return $rows->through(fn ($r) => $this->row($r));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $order = $request->user()->orders()->findOrFail($data['order_id']);

        if (! in_array($order->status, ['paid', 'processing', 'shipped', 'delivered'])) {
            return response()->json(['message' => 'این سفارش قابل مرجوع نیست.'], 422);
        }

        $row = $request->user()->returnRequests()->create([
            'order_id' => $order->id,
            'product_id' => $data['product_id'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($this->row($row), 201);
    }

    private function row(ReturnRequest $r): array
    {
        return [
            'id' => $r->id,
            'order_ref' => $r->order?->ref_code,
            'product_name' => $r->product?->name,
            'quantity' => $r->quantity,
            'reason' => $r->reason,
            'status' => $r->status,
            'status_label' => $r->statusLabel(),
            'created_at' => $r->created_at?->toIso8601String(),
        ];
    }
}
