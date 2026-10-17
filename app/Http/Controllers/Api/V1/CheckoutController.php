<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Services\Order\CheckoutService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use RuntimeException;

class CheckoutController extends Controller
{
    public function checkout(Request $request, CheckoutService $checkout, PaymentService $payments)
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
            'shipment_id' => ['nullable', 'integer'],
            'discount_code' => ['nullable', 'string'],
            'installment_plan_id' => ['nullable', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $order = $checkout->checkout($request->user(), $data);
            $request->user()->abandonedCart()->delete();
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        try {
            $payment = $payments->start($order);
            $paymentUrl = $payment['url'];
            $paymentForm = ['method' => $payment['method'], 'inputs' => $payment['inputs']];
        } catch (\Throwable) {
            $paymentUrl = null;
            $paymentForm = null;
        }

        return response()->json([
            'order' => new OrderResource($order),
            'payment_url' => $paymentUrl,
            'payment_form' => $paymentForm,
        ], 201);
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with(['items.product'])->latest()->paginate(15);

        return OrderResource::collection($orders);
    }

    public function order(Request $request, string $ref)
    {
        $order = $request->user()->orders()->with(['items.product', 'shipment'])->where('ref_code', $ref)->firstOrFail();

        return new OrderResource($order);
    }

    public function track(string $ref)
    {
        $order = Order::with(['items.product', 'shipment'])->where('ref_code', $ref)->firstOrFail();

        return new OrderResource($order);
    }

    public function cancel(Request $request, string $ref)
    {
        $order = $request->user()->orders()->where('ref_code', $ref)->firstOrFail();

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'فقط سفارش‌های در انتظار پرداخت قابل لغو هستند.'], 422);
        }

        $order->update(['status' => 'canceled']);

        return new OrderResource($order->fresh(['items', 'shipment']));
    }
}
