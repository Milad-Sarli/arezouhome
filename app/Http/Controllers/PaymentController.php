<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function callback(Request $request, PaymentService $payments): RedirectResponse
    {
        $result = $payments->verify($request);

        $base = rtrim((string) config('app.frontend_url'), '/');
        $ref = (string) (Transaction::where('authority', $request->get('Authority'))->latest()->first()?->order?->ref_code ?? '');

        return redirect()->away($base.'/account/orders/'.$ref.'?'.http_build_query([
            'pay' => $result['ok'] ? 'ok' : 'fail',
            'ref_id' => $result['ref_id'],
            'msg' => $result['ok'] ? 'پرداخت موفق بود. کد پیگیری: '.$result['ref_id'] : $result['message'],
        ]));
    }

    public function local(Request $request): View
    {
        return view('payment.local', [
            'authority' => (string) $request->get('Authority', ''),
            'callback' => route('payment.callback'),
        ]);
    }
}
