@extends('layouts.public')
@section('title', 'Keranjang Belanja')

@section('content')


    <div class="relative max-w-6xl mx-auto px-6">
        <div class="mb-10">
            <p class="text-xs font-semibold tracking-[0.18em] uppercase text-emerald-600">Keranjang</p>
            <h1 class="mt-2 font-display font-semibold text-3xl md:text-4xl text-ink tracking-tight">
                Pesanan Anda
            </h1>
            <p class="mt-2 text-slate-500">Tinjau pilihan minuman sebelum lanjut ke checkout.</p>
        </div>

        @if (count($cart))
            @php $subtotal = 0; @endphp

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cart as $item)
                        @php
                            $unit = (int) ($item['unit_price'] ?? $item['price'] ?? 0);
                            $qty  = (int) ($item['quantity'] ?? 1);
                            $line = $unit * $qty;
                            $subtotal += $line;
                            $key = $item['key'] ?? ($item['id'] ?? '');
                        @endphp
                        <div class="bg-white/85 backdrop-blur-sm rounded-2xl border border-white shadow-[0_15px_30px_-15px_rgba(16,185,129,0.10)] hover:shadow-[0_20px_40px_-15px_rgba(16,185,129,0.15)] transition-shadow p-5 flex gap-4">
                            <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-emerald-50 via-white to-emerald-50/40 grid place-items-center p-2 flex-shrink-0 border border-emerald-100/60">
                                <x-smart-image :src="$item['image']" :alt="$item['name']" :transparent="true"
                                                class="w-full h-full object-contain" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-display font-semibold text-ink leading-tight">{{ $item['name'] }}</h3>
                                @if (! empty($item['options_summary']))
                                    <p class="text-xs text-slate-500 mt-1">{{ $item['options_summary'] }}</p>
                                @endif
                                @if (! empty($item['notes']))
                                    <p class="text-xs text-amber-600 italic mt-0.5">« {{ $item['notes'] }} »</p>
                                @endif
                                <p class="text-sm text-slate-600 mt-2">
                                    <span class="text-[10px] text-slate-400 align-top">Rp</span>
                                    <span class="font-semibold">{{ number_format($unit, 0, ',', '.') }}</span>
                                </p>

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <div class="inline-flex items-center bg-emerald-50/80 rounded-full border border-emerald-100 overflow-hidden">
                                        <form action="{{ route('cart.update') }}" method="POST"
                                              onsubmit="return {{ $qty }} > 1 || confirm('Hapus {{ $item['name'] }} dari keranjang?')">
                                            @csrf
                                            <input type="hidden" name="key" value="{{ $key }}">
                                            <input type="hidden" name="action" value="decrease">
                                            <button class="w-8 h-8 grid place-items-center text-slate-600 hover:bg-white transition">−</button>
                                        </form>
                                        <span class="w-8 text-center font-bold">{{ $qty }}</span>
                                        <form action="{{ route('cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="key" value="{{ $key }}">
                                            <input type="hidden" name="action" value="increase">
                                            <button class="w-8 h-8 grid place-items-center text-slate-600 hover:bg-emerald-500 hover:text-white transition">+</button>
                                        </form>
                                    </div>
 
                                    <div class="flex items-center gap-3">
                                        <p class="font-display font-extrabold text-slate-900">
                                            <span class="text-xs font-semibold text-slate-400">Rp</span>
                                            {{ number_format($line, 0, ',', '.') }}
                                        </p>
                                        <form action="{{ route('cart.remove') }}" method="POST"
                                              onsubmit="return confirm('Hapus {{ $item['name'] }} dari keranjang?')">
                                            @csrf
                                            <input type="hidden" name="key" value="{{ $key }}">
                                            <button class="text-rose-400 hover:text-rose-600 text-sm font-semibold">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside>
                    <div class="sticky top-24 relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-800 text-white p-7 shadow-[0_25px_50px_-15px_rgba(16,185,129,0.25)]">
                        {{-- Decorative pattern --}}
                        <div aria-hidden="true" class="absolute inset-0 opacity-15"
                             style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 50px 50px, 70px 70px;"></div>
                        <div aria-hidden="true" class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-white/15 blur-2xl"></div>

                        <div class="relative">
                            <p class="text-[10px] font-semibold tracking-[0.22em] uppercase text-emerald-100/80">Ringkasan</p>
                            <h2 class="mt-2 font-display font-extrabold text-2xl">Total Pesanan</h2>

                            <dl class="mt-6 space-y-3 text-sm">
                                <div class="flex justify-between text-emerald-100/90">
                                    <dt>Subtotal</dt>
                                    <dd>Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between text-emerald-100/90">
                                    <dt>Pengiriman</dt>
                                    <dd class="font-semibold text-emerald-100/80">Dihitung di checkout</dd>
                                </div>
                                <div class="flex justify-between pt-3 border-t border-white/20 text-white">
                                    <dt class="font-semibold">Total Tagihan</dt>
                                    <dd class="font-display font-extrabold text-2xl">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                                </div>
                            </dl>

                            <a href="{{ route('checkout.index') }}"
                               class="block text-center mt-7 bg-white text-slate-900 hover:bg-emerald-50 text-sm font-semibold py-3.5 rounded-full transition shadow-lg">
                                Checkout Sekarang
                            </a>
                            <a href="{{ route('home') }}" class="block text-center mt-2 text-xs text-emerald-100 hover:text-white py-2 transition">
                                ← Lanjut belanja
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        @else
            <div class="relative bg-white/80 backdrop-blur-sm border border-white shadow-[0_20px_40px_-20px_rgba(16,185,129,0.12)] rounded-3xl p-14 text-center overflow-hidden">
                <div aria-hidden="true" class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-emerald-100 blur-3xl"></div>
                <div aria-hidden="true" class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-emerald-50/30 blur-3xl"></div>
                <div class="relative">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-emerald-100 to-emerald-50 grid place-items-center text-emerald-500 shadow-inner">
                        <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                    </div>
                    <h2 class="mt-5 font-display font-semibold text-ink text-2xl">Keranjang masih kosong</h2>
                    <p class="mt-2 text-slate-500">Yuk pilih minuman favoritmu dulu.</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-flex bg-slate-900 hover:bg-black text-white text-sm font-semibold px-7 py-3.5 rounded-full transition shadow-lg shadow-slate-900/20 hover:-translate-y-0.5">
                        Lihat Menu
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
