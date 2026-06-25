@extends('layouts.admin')
@section('title', 'Dasbor Utama — estehjumboTNJ')

@section('content')
<div x-data="{ currentMenu: '{{ request()->query('menu', 'live_orders') }}', statusFilter: 'all', historyTab: 'all' }">

    {{-- ════════════════════════════════════════════════════
         OVERVIEW STATS CARDS GRID (Sleek SaaS Style)
         ════════════════════════════════════════════════════ --}}
    <div x-show="currentMenu !== 'settings'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
        {{-- Card 1: Pendapatan Hari Ini --}}
        <div class="bg-white rounded-2xl border border-slate-150 p-5 flex items-center justify-between shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Pendapatan Hari Ini</span>
                <h3 class="font-display font-extrabold text-lg text-slate-900 leading-none">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Pesanan selesai hari ini
                </span>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-xs">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>

        {{-- Card 2: Pendapatan Sebelumnya --}}
        <div class="bg-white rounded-2xl border border-slate-150 p-5 flex items-center justify-between shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Pendapatan Sebelumnya</span>
                <h3 class="font-display font-extrabold text-lg text-slate-900 leading-none">Rp {{ number_format($previousSales, 0, ',', '.') }}</h3>
                <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    Total sebelum hari ini
                </span>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 shadow-xs">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>

        {{-- Card 3: Total Pesanan --}}
        <div class="bg-white rounded-2xl border border-slate-150 p-5 flex items-center justify-between shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Total Pesanan</span>
                <h3 class="font-display font-extrabold text-lg text-slate-900 leading-none">{{ number_format($totalOrdersCount, 0, ',', '.') }}</h3>
                <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Aktif & Selesai
                </span>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-xs">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </span>
        </div>

        {{-- Card 4: Stok Daun Teh --}}
        @php
            $isTeaLow = $teaStock <= $teaMinStock;
            $teaPercent = min(100, max(0, ($teaStock / 15000) * 100)); // assume 15kg max stock for reference gauge
        @endphp
        <div class="bg-white rounded-2xl border border-slate-150 p-5 flex items-center justify-between shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="space-y-1.5 flex-1 min-w-0 pr-3">
                <span class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Stok Daun Teh</span>
                <h3 class="font-display font-extrabold text-lg text-slate-900 leading-none">{{ number_format($teaStock, 0, ',', '.') }} <span class="text-xs font-normal text-slate-450">g</span></h3>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $isTeaLow ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $teaPercent }}%"></div>
                </div>
            </div>
            <span class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-xs {{ $isTeaLow ? 'bg-rose-50 border border-rose-100 text-rose-600' : 'bg-emerald-50 border border-emerald-100 text-emerald-600' }}">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>

        {{-- Card 5: Bahan Baku Kritis --}}
        @php
            $lowIngredientsCount = $ingredients->filter(fn($i) => $i->isLowStock())->count();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-150 p-5 flex items-center justify-between shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200">
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-455 uppercase tracking-wider">Bahan Baku Kritis</span>
                <h3 class="font-display font-extrabold text-lg text-slate-900 leading-none">{{ $lowIngredientsCount }} <span class="text-xs font-normal text-slate-450">Bahan</span></h3>
                <span class="text-[10px] font-medium flex items-center gap-1 {{ $lowIngredientsCount > 0 ? 'text-rose-600 animate-pulse' : 'text-slate-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $lowIngredientsCount > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                    {{ $lowIngredientsCount > 0 ? 'Perlu tambah stok segera' : 'Semua bahan aman' }}
                </span>
            </div>
            <span class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-xs {{ $lowIngredientsCount > 0 ? 'bg-rose-50 border border-rose-100 text-rose-600' : 'bg-slate-50 border border-slate-100 text-slate-400' }}">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </span>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         LIVE ORDERS VIEW
         ════════════════════════════════════════════════════ --}}
    <div x-show="currentMenu === 'live_orders'">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">Pesanan Masuk</h1>
                <p class="text-xs text-slate-450 mt-1">Pantau dan kelola antrean pesanan pelanggan secara real-time.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm select-none">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                    Polling Real-Time Aktif
                </span>
            </div>
        </div>

        {{-- Order Status Sub-Tabs (Premium Pill Styles) --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100/70 p-1.5 rounded-2xl border border-slate-200/60 max-w-fit">
                <button @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                    Semua Aktif 
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="statusFilter === 'all' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $activeOrders->count() }}</span>
                </button>
                <button @click="statusFilter = 'pending'"
                        :class="statusFilter === 'pending' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                    Menunggu 
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="statusFilter === 'pending' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $activeOrders->where('status', 'pending')->count() }}</span>
                </button>
                <button @click="statusFilter = 'paid'"
                        :class="statusFilter === 'paid' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                    Sudah Bayar 
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="statusFilter === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $activeOrders->where('status', 'paid')->count() }}</span>
                </button>
                <button @click="statusFilter = 'preparing'"
                        :class="statusFilter === 'preparing' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                    Disiapkan 
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="statusFilter === 'preparing' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $activeOrders->where('status', 'preparing')->count() }}</span>
                </button>
                <button @click="statusFilter = 'ready'"
                        :class="statusFilter === 'ready' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                    Siap Saji 
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="statusFilter === 'ready' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $activeOrders->where('status', 'ready')->count() }}</span>
                </button>
            </div>
            
            <div class="hidden lg:flex items-center gap-2 bg-white border border-slate-200 rounded-2xl px-4 py-2.5 shadow-xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-bold text-slate-600">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        {{-- Order Card Rows --}}
        @if ($activeOrders->isNotEmpty())
            <div class="space-y-3.5">
                @foreach ($activeOrders as $order)
                    <div x-show="statusFilter === 'all' || statusFilter === '{{ $order->status }}'"
                         x-transition
                         class="bg-white rounded-2xl border border-slate-150 p-5 flex flex-col gap-4 hover:shadow-md transition duration-200">
                        
                        {{-- Top Meta Info Row --}}
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            {{-- ID --}}
                            <div class="lg:w-[10%] flex items-center gap-3">
                                <span class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-150 grid place-items-center text-[11px] font-extrabold text-slate-500 shadow-xs select-none">
                                    #{{ substr($order->order_number, -4) }}
                                </span>
                                <div class="lg:hidden">
                                    <h4 class="text-xs font-bold text-slate-900 capitalize">{{ $order->customer_name }}</h4>
                                    <p class="text-[9px] text-slate-400 font-medium">{{ $order->order_number }}</p>
                                </div>
                            </div>

                            {{-- Customer Name --}}
                            <div class="hidden lg:flex items-center gap-3 lg:w-[22%]">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold grid place-items-center text-xs select-none shadow-sm shadow-emerald-500/10">
                                    @php
                                        $initials = '';
                                        $words = explode(' ', $order->customer_name);
                                        foreach ($words as $w) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                        }
                                        echo substr($initials, 0, 2);
                                    @endphp
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 capitalize truncate">{{ $order->customer_name }}</h4>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $order->order_number }}</p>
                                </div>
                            </div>

                            {{-- Payment Status --}}
                            <div class="lg:w-[12%] text-left">
                                <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Metode</span>
                                <span class="text-xs font-semibold text-slate-700 capitalize">
                                    {{ $order->payment_method === 'online' ? 'Transfer' : 'Tunai (COD)' }}
                                </span>
                            </div>

                            {{-- Duration Elapsed --}}
                            <div class="lg:w-[15%] text-left">
                                <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Dipesan</span>
                                <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $order->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>

                            {{-- Delivery Type --}}
                            <div class="lg:w-[10%] text-left">
                                <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Tipe</span>
                                <span class="text-xs font-bold {{ $order->type === 'online_delivery' ? 'text-emerald-600' : 'text-slate-700' }} capitalize">
                                    {{ $order->type === 'online_delivery' ? 'Antar' : 'Ambil' }}
                                </span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="lg:w-[13%] text-left">
                                <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Status</span>
                                @php
                                    $colorMap = [
                                        'pending'   => 'bg-amber-50 border-amber-200 text-amber-700',
                                        'paid'      => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                        'preparing' => 'bg-teal-50 border-teal-200 text-teal-700',
                                        'ready'     => 'bg-purple-50 border-purple-200 text-purple-700',
                                        'completed' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                        'cancelled' => 'bg-rose-50 border-rose-200 text-rose-700'
                                    ];
                                    $statusLabel = [
                                        'pending'   => 'Menunggu',
                                        'paid'      => 'Dibayar',
                                        'preparing' => 'Disiapkan',
                                        'ready'     => 'Siap',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $col = $colorMap[$order->status] ?? 'bg-slate-50 border-slate-200 text-slate-700';
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $col }}">
                                    {{ $statusLabel[$order->status] ?? $order->status }}
                                </span>
                            </div>

                            {{-- Total Price --}}
                            <div class="lg:w-[12%] text-left">
                                <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Total</span>
                                <span class="text-xs font-extrabold text-slate-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>

                            {{-- Action Dropdown --}}
                            <div class="lg:w-[6%] flex items-center justify-end" x-data="{ open: false }" @click.outside="open = false">
                                <div class="relative">
                                    <button @click="open = !open" class="w-9 h-9 rounded-xl hover:bg-slate-100 border border-transparent hover:border-slate-200 flex items-center justify-center text-slate-500 font-extrabold focus:outline-none transition">
                                        ⋮
                                    </button>
                                    
                                    <div x-show="open" 
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         class="absolute right-0 mt-2 w-48 bg-slate-900 text-white rounded-xl shadow-xl z-50 overflow-hidden py-1 border border-slate-800">
                                        
                                        @if ($order->status === 'pending')
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-emerald-400 transition">Terima & Siapkan</button>
                                            </form>
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-rose-450 transition">Batalkan</button>
                                            </form>
                                        @elseif ($order->status === 'paid')
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-teal-400 transition">Siapkan Minuman</button>
                                            </form>
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-rose-450 transition">Batalkan</button>
                                            </form>
                                        @elseif ($order->status === 'preparing')
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="status" value="ready">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-purple-400 transition">Siap Saji</button>
                                            </form>
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-rose-450 transition">Batalkan</button>
                                            </form>
                                        @elseif ($order->status === 'ready')
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-emerald-400 transition">Selesaikan</button>
                                            </form>
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-rose-450 transition">Batalkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> {{-- Closes Top Meta Info Row --}}

                        {{-- Bottom Section: Inline Order Items --}}
                        <div class="pt-3.5 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Rincian Menu:</span>
                            @foreach ($order->items as $item)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-xs font-semibold text-slate-700 shadow-2xs">
                                    <span class="font-extrabold text-emerald-600">{{ $item->quantity }}x</span>
                                    <span class="font-bold text-slate-900">{{ $item->product_name_snapshot }}</span>
                                    @if ($item->notes)
                                        <span class="text-[10px] text-slate-400 font-normal">({{ $item->notes }})</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-150 shadow-xs">
                <svg class="w-12 h-12 mx-auto stroke-current opacity-30 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>
                <p class="text-sm font-semibold">Tidak ada pesanan aktif saat ini.</p>
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════
         ORDER HISTORY VIEW
         ════════════════════════════════════════════════════ --}}
    <div x-show="currentMenu === 'order_history'">
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">Riwayat Pesanan</h1>
                <p class="text-xs text-slate-450 mt-1">Daftar rekaman transaksi penjualan kedai serta laporan summary grafik.</p>
            </div>
            
            {{-- Date Picker Filter widget matching Bringova layout --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="menu" value="order_history">
                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2 shadow-xs">
                    <span class="text-slate-450 text-[11px] font-bold uppercase tracking-wider mr-1">Dari</span>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs font-semibold text-slate-700 bg-transparent focus:outline-none cursor-pointer">
                    <span class="text-slate-400 text-xs px-1.5">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs font-semibold text-slate-700 bg-transparent focus:outline-none cursor-pointer">
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-display font-bold text-xs tracking-wider uppercase px-5 py-3 rounded-xl shadow-md shadow-emerald-500/10 transition">
                    Filter
                </button>
            </form>
        </div>

        {{-- Sub-tabs (All Order, Summary, Completed, Cancelled) (Premium Pill Style) --}}
        <div class="flex flex-wrap items-center gap-1.5 bg-slate-100/70 p-1.5 rounded-2xl border border-slate-200/60 max-w-fit mb-6">
            <button @click="historyTab = 'all'"
                    :class="historyTab === 'all' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                Semua Pesanan
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="historyTab === 'all' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $pastOrders->count() }}</span>
            </button>
            <button @click="historyTab = 'summary'; initCharts()"
                    :class="historyTab === 'summary' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                Ringkasan Grafik
            </button>
            <button @click="historyTab = 'completed'"
                    :class="historyTab === 'completed' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                Selesai
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="historyTab === 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $pastOrders->where('status', 'completed')->count() }}</span>
            </button>
            <button @click="historyTab = 'cancelled'"
                    :class="historyTab === 'cancelled' ? 'bg-white text-emerald-600 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition duration-150 flex items-center gap-2 focus:outline-none">
                Dibatalkan
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-colors" :class="historyTab === 'cancelled' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-200/60 text-slate-500'">{{ $pastOrders->where('status', 'cancelled')->count() }}</span>
            </button>
        </div>

        {{-- Tab content 1: Order List (All, Completed, Cancelled) --}}
        <div x-show="historyTab !== 'summary'">
            @if ($pastOrders->isNotEmpty())
                <div class="space-y-3.5">
                    @foreach ($pastOrders as $order)
                        <div x-show="historyTab === 'all' || historyTab === '{{ $order->status }}'"
                             x-transition
                             class="bg-white rounded-2xl border border-slate-150 p-5 flex flex-col gap-4 hover:shadow-md transition duration-200">
                            
                            {{-- Top Meta Info Row --}}
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                {{-- ID --}}
                                <div class="lg:w-[10%] flex items-center gap-3">
                                    <span class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-150 grid place-items-center text-[11px] font-extrabold text-slate-500 shadow-xs select-none">
                                        #{{ substr($order->order_number, -4) }}
                                    </span>
                                    <div class="lg:hidden">
                                        <h4 class="text-xs font-bold text-slate-900 capitalize">{{ $order->customer_name }}</h4>
                                        <p class="text-[9px] text-slate-400 font-medium">{{ $order->order_number }}</p>
                                    </div>
                                </div>

                                {{-- Customer Profile --}}
                                <div class="hidden lg:flex items-center gap-3 lg:w-[22%]">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 font-bold grid place-items-center text-xs select-none">
                                        @php
                                            $initials = '';
                                            $words = explode(' ', $order->customer_name);
                                            foreach ($words as $w) {
                                                $initials .= strtoupper(substr($w, 0, 1));
                                            }
                                            echo substr($initials, 0, 2);
                                        @endphp
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 capitalize truncate">{{ $order->customer_name }}</h4>
                                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $order->order_number }}</p>
                                    </div>
                                </div>

                                {{-- Payment method --}}
                                <div class="lg:w-[12%] text-left">
                                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Metode</span>
                                    <span class="text-xs font-semibold text-slate-700 capitalize">
                                    {{ $order->payment_method === 'online' ? 'Transfer' : 'Tunai (COD)' }}
                                    </span>
                                </div>

                                {{-- Completed Time --}}
                                <div class="lg:w-[15%] text-left">
                                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Tanggal</span>
                                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ $order->created_at->translatedFormat('d M Y H:i') }} WIB
                                    </span>
                                </div>

                                {{-- Delivery Type --}}
                                <div class="lg:w-[10%] text-left">
                                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Tipe</span>
                                    <span class="text-xs font-bold {{ $order->type === 'online_delivery' ? 'text-blue-600' : 'text-slate-700' }} capitalize">
                                        {{ $order->type === 'online_delivery' ? 'Antar' : 'Ambil' }}
                                    </span>
                                </div>

                                {{-- Status Badge --}}
                                <div class="lg:w-[13%] text-left">
                                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Status</span>
                                    @php
                                        $histStatusLabel = [
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                        $col = $order->status === 'completed' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800';
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $col }}">
                                        {{ $histStatusLabel[$order->status] ?? $order->status }}
                                    </span>
                                </div>

                                {{-- Grand Total --}}
                                <div class="lg:w-[12%] text-left">
                                    <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider leading-none mb-1">Total</span>
                                    <span class="text-xs font-extrabold text-slate-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                </div>

                                {{-- Action --}}
                                <div class="lg:w-[6%] flex items-center justify-end" x-data="{ open: false }" @click.outside="open = false">
                                    <div class="relative">
                                        <button @click="open = !open" class="w-9 h-9 rounded-xl hover:bg-slate-100 border border-transparent hover:border-slate-200 flex items-center justify-center text-slate-500 font-extrabold focus:outline-none transition">
                                            ⋮
                                        </button>
                                        
                                        <div x-show="open" 
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             class="absolute right-0 mt-2 w-44 bg-slate-900 text-white rounded-xl shadow-xl z-50 overflow-hidden py-1 border border-slate-800">
                                            <span class="block px-4 py-2.5 text-[10px] text-slate-455 font-bold border-b border-slate-800">Tindakan</span>
                                            <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" target="_blank" class="block w-full text-left text-xs font-bold px-4 py-2.5 hover:bg-white/10 text-slate-350 transition">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div> {{-- Closes Top Meta Info Row --}}

                            {{-- Bottom Section: Inline Order Items --}}
                            <div class="pt-3.5 border-t border-slate-100 flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Rincian Menu:</span>
                                @foreach ($order->items as $item)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-xs font-semibold text-slate-700 shadow-2xs">
                                        <span class="font-extrabold text-emerald-600">{{ $item->quantity }}x</span>
                                        <span class="font-bold text-slate-900">{{ $item->product_name_snapshot }}</span>
                                        @if ($item->notes)
                                            <span class="text-[10px] text-slate-400 font-normal">({{ $item->notes }})</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-150 shadow-xs">
                    <svg class="w-12 h-12 mx-auto stroke-current opacity-30 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>
                    <p class="text-sm font-semibold">Tidak ada transaksi terdaftar pada rentang filter tanggal ini.</p>
                </div>
            @endif
        </div>

        {{-- Tab content 2: Laporan Summary Charts --}}
        <div x-show="historyTab === 'summary'">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                {{-- Chart 1: Sales Trend --}}
                <div class="bg-white rounded-3xl border border-slate-150 shadow-xs p-6 md:col-span-8">
                    <div class="mb-4">
                        <h3 class="font-display font-extrabold text-slate-900 text-sm">Tren Penjualan (7 Hari Terakhir)</h3>
                        <p class="text-[10px] text-slate-450 mt-0.5">Grafik nilai transaksi harian pesanan selesai.</p>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
                
                {{-- Chart 2: Top Products --}}
                <div class="bg-white rounded-3xl border border-slate-150 shadow-xs p-6 md:col-span-4 flex flex-col">
                    <div class="mb-4">
                        <h3 class="font-display font-extrabold text-slate-900 text-sm">Varian Minuman Terlaris</h3>
                        <p class="text-[10px] text-slate-450 mt-0.5">Perbandingan penjualan varian terpopuler.</p>
                    </div>
                    <div class="h-48 relative flex-1 flex items-center justify-center">
                        @if (count($topLabels) > 0)
                            <canvas id="topProductsChart"></canvas>
                        @else
                            <div class="text-slate-350 text-xs text-center">Belum ada data penjualan.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- ════════════════════════════════════════════════════
         VISUAL SETTINGS FORM VIEW
         ════════════════════════════════════════════════════ --}}
    <div x-show="currentMenu === 'settings'">
        <div class="bg-white rounded-3xl border border-slate-150 shadow-xs p-6 md:p-8">
            <div class="mb-6 pb-4 border-b border-slate-150">
                <h3 class="font-display font-extrabold text-slate-900 text-lg">Pengaturan Tampilan Website</h3>
                <p class="text-xs text-slate-450 mt-1">Sesuaikan font, teks utama, dan gambar banner yang ditampilkan di halaman depan pelanggan Anda secara dinamis.</p>
            </div>

            <form action="{{ route('admin.settings.visuals') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Left Column --}}
                    <div class="space-y-5">
                        {{-- Font --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Font Utama Website</label>
                            <select name="font_family" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-755 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                                <option value="Plus Jakarta Sans" {{ $store->font_family === 'Plus Jakarta Sans' ? 'selected' : '' }}>Plus Jakarta Sans (Default - Modern Bulat)</option>
                                <option value="Inter" {{ $store->font_family === 'Inter' ? 'selected' : '' }}>Inter (Sleek Minimalis)</option>
                                <option value="Outfit" {{ $store->font_family === 'Outfit' ? 'selected' : '' }}>Outfit (Geometris Premium)</option>
                                <option value="Poppins" {{ $store->font_family === 'Poppins' ? 'selected' : '' }}>Poppins (Friendly Populer)</option>
                                <option value="Montserrat" {{ $store->font_family === 'Montserrat' ? 'selected' : '' }}>Montserrat (Tegas Solid)</option>
                                <option value="Playfair Display" {{ $store->font_family === 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Klasik Serif)</option>
                            </select>
                            <p class="text-[10px] text-slate-400">Mengubah seluruh font teks pada halaman landing pembeli.</p>
                        </div>

                        {{-- Hero Title --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Judul Utama Promosi (Hero Title)</label>
                            <textarea name="hero_title" rows="3" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition" placeholder="Judul hero...">{{ $store->hero_title }}</textarea>
                            <p class="text-[10px] text-slate-400 leading-normal">Mendukung tag HTML. Gunakan <code>&lt;br&gt;</code> untuk baris baru, atau <code>&lt;span class="text-amber-300"&gt;kata&lt;/span&gt;</code> untuk sorotan warna kuning.</p>
                        </div>

                        {{-- Hero Subtitle --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Subjudul Promosi (Hero Subtitle)</label>
                            <textarea name="hero_subtitle" rows="3" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition" placeholder="Deskripsi pendek...">{{ $store->hero_subtitle }}</textarea>
                        </div>

                        {{-- About Text --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cerita Toko (About Us Text)</label>
                            <textarea name="about_text" rows="4" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition" placeholder="Teks cerita tentang kami...">{{ $store->about_text }}</textarea>
                        </div>

                        {{-- Promo Banner Text --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Banner Promosi (Promo Banner Text)</label>
                            <textarea name="promo_banner_text" rows="3" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition" placeholder="Isi teks promosi untuk banner di halaman depan...">{{ $store->promo_banner_text }}</textarea>
                            <p class="text-[10px] text-slate-400">Pesan promosi ini akan ditampilkan pada banner oranye di paling atas website ketika status "Banner Promosi" diaktifkan.</p>
                        </div>
                    </div>

                    {{-- Right Column: Upload Files --}}
                    <div class="space-y-5">
                        {{-- Hero Image --}}
                        <div class="space-y-2 bg-slate-50/50 p-5 rounded-2xl border border-slate-150">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Gambar Utama (Hero Image)</label>
                            <div class="flex flex-col gap-4 mt-3">
                                <div class="w-full h-44 rounded-xl bg-white border border-slate-150 p-4 flex items-center justify-center overflow-hidden shadow-xs">
                                    <img src="{{ asset($store->hero_image_path) }}" alt="Preview Hero" class="max-w-full max-h-full object-contain">
                                </div>
                                <div>
                                    <input type="file" name="hero_image" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                                    <p class="text-[10px] text-slate-455 mt-2">PNG, JPG, JPEG atau WEBP. Maks 2MB. Rekomendasi PNG transparan.</p>
                                </div>
                            </div>
                        </div>

                        {{-- About Image --}}
                        <div class="space-y-2 bg-slate-50/50 p-5 rounded-2xl border border-slate-150">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Gambar Cerita Kami (About Image)</label>
                            <div class="flex flex-col gap-4 mt-3">
                                <div class="w-full h-44 rounded-xl bg-white border border-slate-150 p-4 flex items-center justify-center overflow-hidden shadow-xs">
                                    <img src="{{ asset($store->about_image_path) }}" alt="Preview About" class="max-w-full max-h-full object-contain">
                                </div>
                                <div>
                                    <input type="file" name="about_image" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                                    <p class="text-[10px] text-slate-455 mt-2">PNG, JPG, JPEG atau WEBP. Maks 2MB. Rekomendasi PNG transparan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-4 border-t border-slate-150 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs tracking-wider uppercase px-8 py-3.5 rounded-xl transition shadow-md shadow-blue-500/10 hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Pengaturan Tampilan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Card (NEW) -->
        <div class="bg-white rounded-3xl border border-slate-150 shadow-xs p-6 md:p-8 mt-6">
            <div class="mb-6 pb-4 border-b border-slate-150">
                <h3 class="font-display font-extrabold text-slate-900 text-lg">Keamanan Akun</h3>
                <p class="text-xs text-slate-450 mt-1">Ubah kata sandi administrator akun pemilik toko Anda demi menjaga keamanan.</p>
            </div>

            <form action="{{ route('admin.change-password') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Current Password --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Saat Ini</label>
                        <input type="password" name="current_password" required 
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition"
                               placeholder="••••••••">
                        @error('current_password')
                            <p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Baru</label>
                        <input type="password" name="new_password" required 
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition"
                               placeholder="••••••••">
                        @error('new_password')
                            <p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm New Password --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" required 
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-4 border-t border-slate-150 flex justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white font-display font-bold text-xs tracking-wider uppercase px-8 py-3.5 rounded-xl transition shadow-md shadow-slate-900/10 hover:-translate-y-0.5 active:translate-y-0">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         INVENTORY & STOCK VIEW
         ════════════════════════════════════════════════════ --}}
    <div x-show="currentMenu === 'inventory'" x-data="{ 
        replenishOpen: false, 
        editOpen: false,
        selectedId: null,
        selectedName: '',
        selectedUnit: '',
        replenishQty: '',
        minStock: '',
        costPerUnit: '',
        
        openReplenish(id, name, unit) {
            this.selectedId = id;
            this.selectedName = name;
            this.selectedUnit = unit;
            this.replenishQty = '';
            this.replenishOpen = true;
        },
        
        openEdit(id, name, min, cost) {
            this.selectedId = id;
            this.selectedName = name;
            this.minStock = min;
            this.costPerUnit = cost;
            this.editOpen = true;
        }
    }">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">Inventaris & Stok Bahan Baku</h1>
                <p class="text-xs text-slate-450 mt-1">Kelola persediaan bahan baku minuman, modal beli, dan ambang batas minimum.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Left Column: Ingredients Table --}}
            <div class="bg-white rounded-3xl border border-slate-150 shadow-sm overflow-hidden lg:col-span-8">
                @if ($ingredients->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-150">
                                    <th class="px-6 py-4">Nama Bahan</th>
                                    <th class="px-6 py-4">Stok Saat Ini</th>
                                    <th class="px-6 py-4">Batas Minimum</th>
                                    <th class="px-6 py-4">Modal / Unit</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($ingredients as $ing)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        {{-- Name --}}
                                        <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                            {{ $ing->name }}
                                        </td>
                                        
                                        {{-- Current Stock --}}
                                        <td class="px-6 py-4 text-slate-700 font-semibold text-sm">
                                            {{ number_format($ing->current_stock, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">{{ $ing->unit }}</span>
                                        </td>
                                        
                                        {{-- Minimum Stock --}}
                                        <td class="px-6 py-4 text-slate-600 font-semibold">
                                            {{ number_format($ing->min_stock, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">{{ $ing->unit }}</span>
                                        </td>
                                        
                                        {{-- Cost per unit --}}
                                        <td class="px-6 py-4 font-semibold text-slate-900">
                                            Rp {{ number_format($ing->cost_per_unit, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">/ {{ $ing->unit }}</span>
                                        </td>
                                        
                                        {{-- Status badge --}}
                                        <td class="px-6 py-4">
                                            @if ($ing->isLowStock())
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 border border-rose-100 text-rose-700 capitalize">
                                                    Stok Rendah
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 capitalize">
                                                    Aman
                                                </span>
                                            @endif
                                        </td>
                                        
                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="openReplenish({{ $ing->id }}, '{{ $ing->name }}', '{{ $ing->unit }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] px-3 py-2 rounded-xl transition border border-transparent uppercase tracking-wide">
                                                    + Stok
                                                </button>
                                                
                                                <button type="button" @click="openEdit({{ $ing->id }}, '{{ $ing->name }}', {{ (float)$ing->min_stock }}, {{ (float)$ing->cost_per_unit }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] px-3 py-2 rounded-xl transition border border-slate-200 uppercase tracking-wide">
                                                    Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-16 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto stroke-current opacity-30 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>
                        <p class="text-sm">Tidak ada bahan baku terdaftar.</p>
                    </div>
                @endif
            </div>

            {{-- Right Column: Stock Movements log --}}
            <div class="bg-white rounded-3xl border border-slate-150 shadow-sm p-6 lg:col-span-4">
                <div class="mb-4 pb-3 border-b border-slate-150">
                    <h3 class="font-display font-extrabold text-slate-900 text-sm">Aktivitas Pergerakan Stok</h3>
                    <p class="text-[10px] text-slate-450 mt-0.5">Catatan terbaru penyesuaian, pengurangan & penambahan stok bahan baku.</p>
                </div>
                
                @if ($stockMovements->isNotEmpty())
                    <div class="space-y-4 max-h-[460px] overflow-y-auto pr-1">
                        @foreach ($stockMovements as $move)
                            <div class="flex items-start gap-3 text-xs leading-normal">
                                @if ($move->type === 'in')
                                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex-shrink-0 grid place-items-center">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    </span>
                                @else
                                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex-shrink-0 grid place-items-center">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    </span>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-slate-950 truncate capitalize">{{ $move->ingredient?->name ?? 'Bahan' }}</span>
                                        <span class="font-display font-extrabold {{ $move->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }} whitespace-nowrap">
                                            {{ $move->type === 'in' ? '+' : '-' }}{{ number_format($move->quantity, 0, ',', '.') }} {{ $move->ingredient?->unit }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">{{ $move->reason }}</p>
                                    <p class="text-[9px] text-slate-400 mt-1 font-medium">{{ $move->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-slate-350">
                        <p class="text-xs">Belum ada mutasi stok tercatat.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── MODAL REPLENISH ─── --}}
        <div x-show="replenishOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
             x-transition>
            <div class="bg-white rounded-3xl border border-slate-150 max-w-sm w-full p-6 shadow-2xl" @click.outside="replenishOpen = false">
                <h3 class="font-display font-extrabold text-slate-900 text-sm mb-1">Tambah Stok Bahan</h3>
                <p class="text-xs text-slate-500 mb-4">Menambah persediaan bahan baku secara manual.</p>
                
                <form :action="'/admin/ingredients/' + selectedId + '/replenish'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Bahan</label>
                        <input type="text" x-model="selectedName" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jumlah Ditambah</label>
                        <div class="relative">
                            <input type="number" name="quantity" step="0.001" required x-model="replenishQty" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                            <span class="absolute inset-y-0 right-3.5 flex items-center text-xs font-bold text-slate-400" x-text="selectedUnit"></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="replenishOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-4 py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-500/10">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ─── MODAL EDIT DETAILS ─── --}}
        <div x-show="editOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
             x-transition>
            <div class="bg-white rounded-3xl border border-slate-150 max-w-sm w-full p-6 shadow-2xl" @click.outside="editOpen = false">
                <h3 class="font-display font-extrabold text-slate-900 text-sm mb-1">Edit Pengaturan Bahan</h3>
                <p class="text-xs text-slate-500 mb-4">Ubah batas aman minimum dan estimasi harga modal.</p>
                
                <form :action="'/admin/ingredients/' + selectedId + '/update'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Bahan</label>
                        <input type="text" x-model="selectedName" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Batas Minimum (Min Stock)</label>
                        <input type="number" name="min_stock" step="0.001" required x-model="minStock" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Harga Modal / Unit (Rp)</label>
                        <input type="number" name="cost_per_unit" step="0.01" required x-model="costPerUnit" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="editOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-4 py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-500/10">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
    {{-- Chart Scripts --}}
    <script>
        let salesChartInstance = null;
        let topChartInstance = null;

        function initCharts() {
            // Give layout a tiny timeout to render elements inside hidden tabs first
            setTimeout(() => {
                const ctxSales = document.getElementById('salesChart')?.getContext('2d');
                if (ctxSales && !salesChartInstance) {
                    salesChartInstance = new Chart(ctxSales, {
                        type: 'line',
                        data: {
                            labels: @json($salesLabels ?? []),
                            datasets: [{
                                label: 'Penjualan (Rp)',
                                data: @json($salesValues ?? []),
                                borderColor: '#10b981', // emerald
                                backgroundColor: 'rgba(16, 185, 129, 0.04)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#10b981',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    grid: { color: 'rgba(226, 232, 240, 0.45)' },
                                    ticks: {
                                        font: { size: 10, family: 'Inter' },
                                        color: '#64748b',
                                        callback: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10, family: 'Inter' }, color: '#64748b' }
                                }
                            }
                        }
                    });
                }

                const ctxTop = document.getElementById('topProductsChart')?.getContext('2d');
                if (ctxTop && !topChartInstance) {
                    topChartInstance = new Chart(ctxTop, {
                        type: 'doughnut',
                        data: {
                            labels: @json($topLabels ?? []),
                            datasets: [{
                                data: @json($topValues ?? []),
                                backgroundColor: ['#10b981', '#f59e0b', '#059669', '#ea580c', '#14b8a6'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { size: 9, family: 'Inter' }, color: '#475569', boxWidth: 8, padding: 8 }
                                }
                            },
                            cutout: '68%'
                        }
                    });
                }
            }, 50);
        }

        // Trigger chart rendering initially if we start on the history page or summary
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const initialMenu = urlParams.get('menu') || 'live_orders';
            if (initialMenu === 'order_history') {
                // Pre-select summary or keep All Order
                // Trigger chart loading in case they click it
            }
        });

        // Real-time Audio alert & toaster notification
        let currentLatestOrderId = {{ \App\Models\Order::latest()->first() ? \App\Models\Order::latest()->first()->id : 0 }};

        function playNotificationSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                oscillator.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.15); // A5
                
                gainNode.gain.setValueAtTime(0.35, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.45);
                
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.45);
            } catch (e) {
                console.warn('Audio Context failed to initialize:', e);
            }
        }

        function showOrderToast() {
            const toast = document.createElement('div');
            toast.className = 'fixed top-24 right-6 z-[100] max-w-sm bg-slate-900 border border-slate-800 text-white rounded-2xl p-4 shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-4 opacity-0 scale-95';
            toast.innerHTML = `
                <span class="w-8 h-8 rounded-xl bg-emerald-600 flex-shrink-0 grid place-items-center text-white shadow-sm shadow-emerald-500/10">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </span>
                <div class="flex-1">
                    <h4 class="text-xs font-bold text-white leading-tight">Ada Pesanan Baru Masuk!</h4>
                    <p class="text-[10px] text-slate-400 mt-1 leading-snug">Sistem mendeteksi pesanan baru dari pelanggan. Silakan muat ulang halaman untuk memperbarui daftar.</p>
                    <div class="mt-2.5 flex items-center gap-2">
                        <button onclick="window.location.reload();" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[9px] px-3.5 py-1.5 rounded-lg uppercase tracking-wider transition">Muat Ulang</button>
                        <button onclick="this.closest('.fixed').remove();" class="text-slate-400 hover:text-white font-bold text-[9px] px-2 py-1.5 uppercase tracking-wider transition">Tutup</button>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
            }, 50);
        }

        // Poll endpoint every 15 seconds
        setInterval(async () => {
            try {
                const res = await fetch('{{ route('admin.orders.latest-id') }}');
                if (!res.ok) return;
                const data = await res.json();
                if (data.latest_id > currentLatestOrderId) {
                    currentLatestOrderId = data.latest_id;
                    playNotificationSound();
                    showOrderToast();
                }
            } catch (e) {
                console.error('Polling failed:', e);
            }
        }, 15000);
    </script>
@endsection
