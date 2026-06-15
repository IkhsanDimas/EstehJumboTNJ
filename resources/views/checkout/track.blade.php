@extends('layouts.public')
@section('title', 'Lacak Pesanan — ' . $store->store_name)

@section('content')
<section class="relative bg-page-soft min-h-screen pt-10 pb-24 overflow-hidden">
    {{-- Decorative blobs --}}
    <div aria-hidden="true" class="deco-blob deco-blob-sky w-[28rem] h-[28rem] -top-32 -right-32"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-amber w-72 h-72 top-1/3 -left-20 opacity-40"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-emerald w-72 h-72 bottom-32 -right-20 opacity-40"></div>

    <div class="relative max-w-4xl mx-auto px-6">
        {{-- Header --}}
        <div class="mb-10 text-center md:text-left">
            <p class="text-xs font-semibold tracking-[0.18em] uppercase text-sky-600">Pelacakan Mandiri</p>
            <h1 class="mt-2 font-display font-semibold text-3xl md:text-4xl text-ink tracking-tight">
                Lacak Pesanan
            </h1>
            <p class="mt-2 text-slate-500">Pantau status pembuatan dan pengantaran pesanan Anda secara real-time.</p>
        </div>

        {{-- ─── SEARCH FORM CARD ─── --}}
        <div class="bg-white/85 backdrop-blur-sm rounded-3xl border border-white shadow-[0_20px_40px_-20px_rgba(2,132,199,0.15)] p-6 md:p-8 mb-8">
            <h2 class="font-display font-bold text-slate-900 text-lg mb-3">Masukkan Nomor Pesanan</h2>
            <form action="{{ route('order.track') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" name="order_number" value="{{ $orderNumber ?? '' }}" required
                           placeholder="Contoh: ETJ-20260531-1234"
                           style="text-transform: uppercase;"
                           oninput="this.value = this.value.toUpperCase()"
                           class="w-full rounded-2xl border border-slate-200 bg-white/70 pl-11 pr-4 py-3.5 text-sm font-semibold placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 focus:bg-white transition">
                </div>
                <button type="submit" class="bg-slate-900 hover:bg-black text-white font-semibold text-sm px-7 py-3.5 rounded-full transition shadow-lg shadow-slate-900/10 hover:-translate-y-0.5 whitespace-nowrap">
                    Cari Pesanan
                </button>
            </form>

            @if ($error)
                <div class="mt-4 bg-rose-50 border border-rose-150 text-xs text-rose-800 p-4 rounded-2xl flex items-start gap-2.5">
                    <svg class="w-4.5 h-4.5 text-rose-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <p class="font-bold text-rose-900">Pesanan Tidak Ditemukan</p>
                        <p class="mt-0.5 leading-relaxed">{{ $error }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ─── ORDER DETAILS ─── --}}
        @if ($order)
            @php
                $statusSteps = [
                    'pending' => 1,
                    'paid' => 2,
                    'preparing' => 2,
                    'ready' => 3,
                    'completed' => 4,
                    'cancelled' => 0
                ];
                $currentStepIndex = $statusSteps[$order->status] ?? 1;
                $isCancelled = $order->status === 'cancelled';
            @endphp

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Left Side: Stepper Progress --}}
                <div class="md:col-span-1 bg-white/85 backdrop-blur-sm rounded-3xl border border-white shadow-[0_20px_40px_-20px_rgba(2,132,199,0.15)] p-6 md:p-8 flex flex-col">
                    <h3 class="font-display font-bold text-slate-900 text-base mb-6 pb-3 border-b border-slate-100">Status Pengiriman</h3>
                    
                    @if ($isCancelled)
                        {{-- Cancelled state --}}
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <span class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </span>
                            <h4 class="font-display font-extrabold text-rose-700 text-base">Pesanan Dibatalkan</h4>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Pemesanan ini telah dibatalkan. Silakan hubungi admin outlet untuk bantuan lebih lanjut.</p>
                        </div>
                    @else
                        {{-- Stepper Progress --}}
                        <div class="flex-1 relative pl-8 space-y-8 before:absolute before:inset-y-2 before:left-[11px] before:w-[2px] before:bg-slate-200">
                            {{-- Step 1 --}}
                            <div class="relative">
                                {{-- Step circle indicator --}}
                                <span class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                    {{ $currentStepIndex > 1 ? 'bg-emerald-500 border-emerald-500 text-white' : ($currentStepIndex === 1 ? 'bg-sky-500 border-sky-500 text-white shadow-[0_0_0_4px_rgba(14,165,233,0.25)]' : 'bg-white border-slate-350') }}">
                                    @if ($currentStepIndex > 1)
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <span class="text-[10px] font-bold">1</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-display font-bold text-sm leading-tight {{ $currentStepIndex >= 1 ? 'text-slate-900' : 'text-slate-400' }}">Pesanan Diterima</p>
                                    <p class="text-xs mt-1 leading-relaxed {{ $currentStepIndex === 1 ? 'text-slate-600' : 'text-slate-400' }}">Menunggu konfirmasi oleh pihak Toko.</p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="relative">
                                <span class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                    {{ $currentStepIndex > 2 ? 'bg-emerald-500 border-emerald-500 text-white' : ($currentStepIndex === 2 ? 'bg-sky-500 border-sky-500 text-white shadow-[0_0_0_4px_rgba(14,165,233,0.25)]' : 'bg-white border-slate-350') }}">
                                    @if ($currentStepIndex > 2)
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <span class="text-[10px] font-bold">2</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-display font-bold text-sm leading-tight {{ $currentStepIndex >= 2 ? 'text-slate-900' : 'text-slate-400' }}">Sedang Diracik</p>
                                    <p class="text-xs mt-1 leading-relaxed {{ $currentStepIndex === 2 ? 'text-slate-600' : 'text-slate-400' }}">Minuman segar Anda sedang disiapkan secara higienis.</p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="relative">
                                <span class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                    {{ $currentStepIndex > 3 ? 'bg-emerald-500 border-emerald-500 text-white' : ($currentStepIndex === 3 ? 'bg-sky-500 border-sky-500 text-white shadow-[0_0_0_4px_rgba(14,165,233,0.25)]' : 'bg-white border-slate-350') }}">
                                    @if ($currentStepIndex > 3)
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <span class="text-[10px] font-bold">3</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-display font-bold text-sm leading-tight {{ $currentStepIndex >= 3 ? 'text-slate-900' : 'text-slate-400' }}">
                                        {{ $order->type === 'online_delivery' ? 'Sedang Diantar' : 'Siap Diambil' }}
                                    </p>
                                    <p class="text-xs mt-1 leading-relaxed {{ $currentStepIndex === 3 ? 'text-slate-600' : 'text-slate-400' }}">
                                        {{ $order->type === 'online_delivery' ? 'Kurir kami sedang meluncur mengantarkan pesanan.' : 'Pesanan selesai diracik, siap untuk Anda ambil.' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="relative">
                                <span class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                    {{ $currentStepIndex === 4 ? 'bg-emerald-500 border-emerald-500 text-white shadow-[0_0_0_4px_rgba(16,185,129,0.25)]' : 'bg-white border-slate-350' }}">
                                    @if ($currentStepIndex === 4)
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <span class="text-[10px] font-bold">4</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-display font-bold text-sm leading-tight {{ $currentStepIndex === 4 ? 'text-slate-900' : 'text-slate-400' }}">Selesai</p>
                                    <p class="text-xs mt-1 leading-relaxed {{ $currentStepIndex === 4 ? 'text-slate-600' : 'text-slate-400' }}">Pemesanan telah selesai dilakukan. Selamat menikmati!</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Side: Order Summaries and Details --}}
                <div class="md:col-span-2 space-y-6">
                    {{-- Detail Card --}}
                    <div class="bg-white/85 backdrop-blur-sm rounded-3xl border border-white shadow-[0_20px_40px_-20px_rgba(2,132,199,0.15)] p-6 md:p-8">
                        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Rincian Transaksi</p>
                                <p class="font-display font-extrabold text-slate-950 text-xl mt-1">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Tipe Pengiriman</p>
                                <p class="font-display font-bold text-sky-700 text-sm mt-1">
                                    {{ $order->type === 'online_delivery' ? 'Kirim ke Alamat' : 'Ambil Sendiri' }}
                                </p>
                            </div>
                        </div>

                        {{-- Metadata details --}}
                        <div class="grid sm:grid-cols-2 gap-4 py-6 border-b border-slate-100 text-xs text-slate-600">
                            <div>
                                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-1">Informasi Pemesan</h4>
                                <p class="font-medium text-slate-800 text-sm">{{ $order->customer_name ?? '-' }}</p>
                                
                                @php
                                    // Parse notes for address and coordinate details
                                    $notesLines = explode("\n", $order->notes);
                                    $addressText = '';
                                    $pinpointLink = '';
                                    $scheduleText = '';
                                    
                                    $capturingAddress = false;
                                    
                                    foreach ($notesLines as $line) {
                                        if (str_contains($line, 'Alamat Pengiriman:')) {
                                            $capturingAddress = true;
                                            continue;
                                        }
                                        if (str_contains($line, 'Catatan:')) {
                                            $capturingAddress = false;
                                            continue;
                                        }
                                        if (str_contains($line, 'Pinpoint Peta:')) {
                                            $capturingAddress = false;
                                            $pinpointLink = str_replace('Pinpoint Peta:', '', $line);
                                            continue;
                                        }
                                        if (str_contains($line, 'Jadwal Pengantaran/Pengambilan:')) {
                                            $scheduleText = str_replace('Jadwal Pengantaran/Pengambilan:', '', $line);
                                            continue;
                                        }
                                        
                                        if ($capturingAddress) {
                                            $addressText .= $line . "\n";
                                        }
                                    }
                                    
                                    $addressText = trim($addressText);
                                    $pinpointLink = trim($pinpointLink);
                                    $scheduleText = trim($scheduleText);
                                @endphp

                                @if ($order->type === 'online_delivery' && $addressText)
                                    <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-1 mt-3">Alamat Pengantaran</h4>
                                    <p class="leading-relaxed text-slate-700 bg-slate-50 border border-slate-100 p-2.5 rounded-xl mt-1.5 whitespace-pre-line">{{ $addressText }}</p>
                                    
                                    @if ($pinpointLink)
                                        <a href="{{ $pinpointLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-700 font-semibold mt-2 transition">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            Buka Koordinat Pinpoint Peta
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-1">Jadwal Waktu</h4>
                                <p class="font-semibold text-slate-800 text-xs">
                                    @if ($scheduleText)
                                        <span class="text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-full text-[10px] font-bold inline-block mb-1">Terjadwal</span><br>
                                        {{ $scheduleText }}
                                    @else
                                        <span class="text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full text-[10px] font-bold inline-block mb-1">Instan / Sekarang</span><br>
                                        {{ $order->type === 'online_delivery' ? 'Kirim segera setelah siap' : 'Ambil segera setelah siap' }}
                                    @endif
                                </p>

                                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-1 mt-4">Metode Pembayaran</h4>
                                <p class="font-semibold text-slate-700">
                                    {{ $order->payment_method === 'online' ? 'Transfer' : 'Bayar di Tempat (COD)' }}
                                </p>
                            </div>
                        </div>

                        {{-- Order Items Table --}}
                        <div class="py-6">
                            <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Pesanan Anda</h4>
                            <div class="overflow-hidden border border-slate-100 rounded-2xl bg-slate-50/50">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-100">
                                            <th class="px-4 py-3">Menu</th>
                                            <th class="px-4 py-3 text-center">Qty</th>
                                            <th class="px-4 py-3 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($order->items as $item)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="px-4 py-3">
                                                    <p class="font-bold text-slate-900">{{ $item->product_name_snapshot }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-center font-medium text-slate-700">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($item->line_total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Total Calculation Block --}}
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-xs space-y-2.5">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if ($order->type === 'online_delivery')
                                <div class="flex justify-between text-slate-600">
                                    <span>Biaya Pengiriman</span>
                                    <span class="font-semibold text-slate-900">Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2.5 border-t border-slate-200 text-slate-900 font-bold text-sm">
                                <span>Total Tagihan</span>
                                <span class="text-sky-600 font-display font-extrabold text-base">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Quick CTAs --}}
                        @php
                            $waText = "Halo kak, saya ingin menanyakan perkembangan pesanan saya di Es Teh Jumbo dengan ID: " . $order->order_number;
                            $waUrl = "https://wa.me/" . config('services.whatsapp.number') . "?text=" . urlencode($waText);
                        @endphp
                        
                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1eb854] text-white font-semibold text-sm px-6 py-4 rounded-full transition shadow-md shadow-emerald-500/10 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/>
                                </svg>
                                Hubungi Admin via WhatsApp
                            </a>
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm px-6 py-4 rounded-full transition whitespace-nowrap">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
