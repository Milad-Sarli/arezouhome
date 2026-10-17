<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8"/>
<title>فاکتور فروش {{ $order->ref_code }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">
<style>
*{box-sizing:border-box}
body{font-family:'Vazirmatn',Tahoma,sans-serif;color:#000;margin:0;background:#fff}
.sheet{max-width:820px;margin:0 auto;padding:1rem}
h1{font-size:1.25rem;text-align:center;margin:.2rem 0}
.meta{text-align:center;font-size:.85rem;margin-bottom:.6rem}
table{width:100%;border-collapse:collapse;font-size:.82rem}
th,td{border:1px solid #000;padding:.4rem .5rem;text-align:right}
thead th{background:#eee}
.box{border:1.5px solid #000;border-radius:6px;padding:.5rem .7rem;margin-bottom:.6rem;font-size:.82rem}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}
.totals{width:60%;margin-right:auto;margin-top:.6rem}
.signs{display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-top:1rem}
.sign{border:1.5px solid #000;border-radius:6px;min-height:110px;padding:.5rem;font-size:.82rem}
.words{margin-top:.6rem;font-size:.85rem;font-weight:700}
@page{size:A4;margin:10mm}
@media print{.no-print{display:none!important} body{margin:0} .sheet{max-width:100%;width:100%;margin:0} table,.signs{break-inside:avoid}}
</style>
</head>
<body>
<div class="sheet">
<div class="no-print" style="text-align:center;margin-bottom:.6rem"><button onclick="window.print()" style="font-family:inherit;border:2px solid #000;background:#fff;padding:.3rem 2rem;font-weight:700;cursor:pointer">چاپ فاکتور</button></div>

<h1>فاکتور فروش کالا</h1>
<div class="meta">شماره: <b>{{ $order->ref_code }}</b> &nbsp;—&nbsp; تاریخ صدور: <b>{{ verta($order->created_at)->format('Y/m/d') }}</b> &nbsp;—&nbsp; وضعیت پرداخت: <b>{{ $order->paidTransaction ? 'پرداخت شده' : $order->statusLabel() }}</b></div>

<div class="grid2">
<div class="box">
<b>فروشنده:</b> {{ \App\Models\Setting::get('store_name', config('app.name')) }}<br>
نشانی: {{ \App\Models\Setting::get('contact_address', '—') }}<br>
تلفن: {{ \App\Models\Setting::get('contact_phone', '—') }}
</div>
<div class="box">
<b>خریدار:</b> {{ $order->user?->name ?? '—' }}<br>
موبایل: {{ $order->user?->phone ?? '—' }} &nbsp; کد ملی: {{ $order->user?->national_id ?? '—' }}<br>
نشانی: @if($order->address){{ $order->address->province?->name }}، {{ $order->address->city?->name }}، {{ $order->address->address }} — کدپستی: {{ $order->address->postal_code ?? '—' }}@else — @endif
</div>
</div>

<table>
<thead><tr><th style="width:36px">ردیف</th><th>شرح کالا</th><th style="width:60px">تعداد</th><th>مبلغ واحد (تومان)</th><th>مبلغ کل (تومان)</th></tr></thead>
<tbody>
@php($fa = \App\Support\PersianNumbers::class)
@foreach ($order->items as $i => $item)
<tr>
<td>{{ $fa::fa($i + 1) }}</td>
<td>{{ $item->product_name }}</td>
<td>{{ $fa::fa($item->quantity) }}</td>
<td>{{ $fa::fa(number_format($item->unit_price_rial / 10)) }}</td>
<td>{{ $fa::fa(number_format($item->total_rial / 10)) }}</td>
</tr>
@endforeach
</tbody>
</table>

<table class="totals">
<tr><td>جمع اقلام (تومان)</td><td>{{ $fa::fa(number_format(($order->final_price_rial - $order->post_price_rial + $order->discount_price_rial) / 10)) }}</td></tr>
<tr><td>تخفیف (تومان)</td><td>{{ $fa::fa(number_format($order->discount_price_rial / 10)) }}</td></tr>
<tr><td>هزینه ارسال (تومان)</td><td>{{ $fa::fa(number_format($order->post_price_rial / 10)) }}</td></tr>
<tr><td><b>جمع کل (تومان)</b></td><td><b>{{ $fa::fa(number_format($order->final_price_rial / 10)) }}</b></td></tr>
</table>

<div class="words">مبلغ به حروف: {{ \App\Support\PersianNumbers::tomanInWords($order->final_price_rial) }}</div>

<div class="signs">
<div class="sign">مهر و امضای فروشنده</div>
<div class="sign">امضای خریدار</div>
</div>
</div>
</body>
</html>
