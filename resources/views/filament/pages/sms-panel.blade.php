<x-filament-panels::page>
    @if ($serviceError)
        <div class="rounded-xl border border-dashed p-6 text-center text-sm">
            {{ $serviceError }}
            <div class="mt-1 text-xs opacity-70">کلید و شماره فرستنده را از بخش تنظیمات فروشگاه وارد کنید.</div>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border p-4">
                <div class="font-bold">اعتبار باقی‌مانده</div>
                <div class="mt-2 text-2xl">{{ $credit['sms_credit'] ?? $credit['credit'] ?? '—' }}</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="font-bold">شماره‌های فرستنده</div>
                <ul class="mt-2 space-y-1 text-sm">
                    @forelse ($numbers as $number)
                        <li dir="ltr" class="text-left">{{ is_array($number) ? ($number['number'] ?? json_encode($number)) : $number }}</li>
                    @empty
                        <li>موردی یافت نشد.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
</x-filament-panels::page>
