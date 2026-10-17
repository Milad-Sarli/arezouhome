<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function showForUser(string $ref): View
    {
        $order = auth()->user()->orders()->where('ref_code', $ref)->firstOrFail();
        $order->loadMissing([
            'user', 'address.province', 'address.city',
            'shipment', 'discountCode', 'items.product',
        ]);

        return view('invoices.sales', ['order' => $order]);
    }

    public function invoiceForApi(string $ref): View
    {
        $order = request()->user()->orders()->where('ref_code', $ref)->firstOrFail();
        $order->loadMissing([
            'user', 'address.province', 'address.city',
            'shipment', 'discountCode', 'items.product',
        ]);

        return view('invoices.sales', ['order' => $order]);
    }

    public function show(Order $order): View
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['super-admin', 'manager', 'seller', 'warehouse']),
            403
        );

        $order->loadMissing([
            'user', 'address.province', 'address.city',
            'shipment', 'discountCode', 'items.product',
        ]);

        return view('invoices.sales', ['order' => $order]);
    }
}
