@extends('layouts.public')
@section('title', 'Pesanan Berhasil')

@section('content')


    <div class="relative max-w-2xl w-full">
        <div class="relative bg-white/85 backdrop-blur-sm rounded-[36px] p-10 md:p-14 shadow-[0_30px_60px_-20px_rgba(16,185,129,0.15)] border border-white text-center overflow-hidden">
            {{-- Inner soft glow --}}
            <div aria-hidden="true" class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-emerald-100 blur-3xl"></div>
            <div aria-hidden="true" class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full bg-emerald-50/30 blur-3xl"></div>

            <div class="relative">
                {{-- Success icon with halo --}}
                <div class="relative w-28 h-28 mx-auto">
                    <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping"></div>
                    <div class="relative w-28 h-28 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 grid place-items-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-14 h-14 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <h1 class="mt-8 font-display font-semibold text-ink text-3xl md:text-4xl tracking-tight">
                    Pesanan Berhasil!
                </h1>
                <p class="mt-3 text-slate-600 leading-relaxed">
                    Terima kasih sudah memesan di
                    <span class="font-semibold text-emerald-600">{{ $store->store_name }}</span>.
                </p>

                {{-- Order ID --}}
                <div>
                    <div class="mt-7 inline-flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-3">
                        <span class="text-[10px] uppercase tracking-[0.2em] font-semibold text-emerald-700">ID Pesanan</span>
                        <span class="font-display font-extrabold text-slate-900 text-lg">{{ $order->order_number }}</span>
                    </div>
                    <div class="mt-2 text-xs">
                        <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="text-emerald-600 hover:text-emerald-800 font-bold hover:underline transition">
                            Lacak Status Pesanan Real-time →
                        </a>
                    </div>
                </div>

                {{-- Total --}}
                <div class="mt-7">
                    <p class="text-xs uppercase tracking-[0.18em] font-semibold text-slate-400">Total Pembayaran</p>
                    <h2 class="mt-2 font-display font-extrabold text-slate-900 text-4xl md:text-5xl">
                        <span class="text-base font-semibold text-slate-400 align-top">Rp</span>
                        {{ number_format($order->grand_total, 0, ',', '.') }}
                    </h2>
                </div>

                {{-- Redirect notice --}}
                @if (session('whatsapp_url'))
                    <div class="mt-9 bg-gradient-to-br from-emerald-50/50 to-emerald-50/10 border border-emerald-100 rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/>
                            </svg>
                            <p class="font-display font-semibold text-slate-900">Membuka WhatsApp...</p>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Anda akan diarahkan dalam <span id="countdown" class="font-bold text-emerald-600">3</span> detik</p>
                    </div>
                @endif

                {{-- CTA --}}
                <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center items-center">
                    <a href="{{ session('whatsapp_url') ?: 'https://wa.me/' . config('services.whatsapp.number') }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1eb854] text-white font-semibold text-sm px-7 py-3.5 rounded-full transition shadow-lg shadow-emerald-500/20 hover:-translate-y-0.5 w-full sm:w-auto">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/>
                        </svg>
                        Buka WhatsApp Sekarang
                    </a>
                    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}"
                       class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm px-7 py-3.5 rounded-full transition hover:-translate-y-0.5 w-full sm:w-auto">
                        Lacak Status Pesanan
                    </a>
                </div>

                <div class="mt-5">
                    <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-emerald-600 transition">
                        ← Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    @if (session('whatsapp_url'))
        let countdown = 3;
        const el = document.getElementById('countdown');
        const timer = setInterval(() => {
            countdown--;
            if (el) el.innerText = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = "{{ session('whatsapp_url') }}";
            }
        }, 1000);
    @endif
</script>
@endsection
