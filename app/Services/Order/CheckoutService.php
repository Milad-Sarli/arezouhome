<?php

namespace App\Services\Order;

use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    /**
     * @param  array{address_id: int, shipment_id?: int, discount_code?: string, installment_plan_id?: int, items: array<int, array{product_id: int, variant_id?: int, quantity: int}>}  $data
     */
    public function checkout(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $address = $user->addresses()->findOrFail($data['address_id']);
            $shipment = isset($data['shipment_id'])
                ? Shipment::where('status', true)->findOrFail($data['shipment_id'])
                : null;

            $itemsSum = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('status', true)->findOrFail($item['product_id']);
                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                $unitPrice = $product->finalPrice();
                $available = $product->quantity;
                $variantId = $item['variant_id'] ?? null;

                if ($variantId) {
                    $variant = $product->variants()->where('status', true)->findOrFail($variantId);
                    $available = $variant->quantity;

                    if ((int) $variant->price_rial > 0) {
                        $unitPrice = (int) $variant->price_rial;
                    }
                }

                if ($available < $quantity) {
                    throw new RuntimeException('موجودی «'.$product->name.'» کافی نیست.');
                }

                $itemsSum += $unitPrice * $quantity;
                $lines[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variantId,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price_rial' => $unitPrice,
                    'total_rial' => $unitPrice * $quantity,
                ];
            }

            $discountCode = null;
            $discount = 0;

            if (! empty($data['discount_code'])) {
                $discountCode = DiscountCode::where('code', $data['discount_code'])->first();

                if (! $discountCode || ! $discountCode->isUsable() || $itemsSum < $discountCode->min_order_rial) {
                    throw new RuntimeException('کد تخفیف معتبر نیست.');
                }

                $discount = $discountCode->type === 'percent'
                    ? (int) ($itemsSum * $discountCode->value_rial / 100)
                    : min($discountCode->value_rial, $itemsSum);
            }

            $post = $shipment ? (int) $shipment->default_price_rial : 0;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'shipment_id' => $shipment?->id,
                'discount_code_id' => $discountCode?->id,
                'installment_plan_id' => $data['installment_plan_id'] ?? null,
                'ref_code' => $this->refCode(),
                'status' => 'pending',
                'post_price_rial' => $post,
                'discount_price_rial' => $discount,
                'final_price_rial' => max(0, $itemsSum + $post - $discount),
            ]);

            $order->items()->createMany($lines);

            return $order->fresh(['items']);
        });
    }

    private function refCode(): string
    {
        do {
            $code = 'AH-'.now()->format('Ymd').'-'.random_int(1000, 9999);
        } while (Order::where('ref_code', $code)->exists());

        return $code;
    }
}
