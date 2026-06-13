<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin — estehjumboTNJ')</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@550;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Chart.js & Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['Inter', 'system-ui', 'sans-serif'],
                        rounded: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
            background-color: #f8fafc;
            background-image: 
                radial-gradient(1000px 600px at 90% 0%, rgba(186, 230, 253, 0.15) 0%, transparent 60%),
                radial-gradient(1000px 600px at 10% 100%, rgba(59, 130, 246, 0.03) 0%, transparent 60%);
            background-attachment: fixed;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @yield('head')
</head>
<body class="min-h-screen font-sans text-slate-800 flex" x-data="adminLayoutState()">    {{-- Sidebar Kiri --}}
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col fixed inset-y-0 z-40 transition-transform duration-300 md:translate-x-0"
           :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-0.5 max-md:-translate-x-full'">
        
        {{-- Logo Brand --}}
        <div class="h-20 px-6 border-b border-slate-800 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 grid place-items-center text-white shadow-md shadow-blue-500/10 select-none">
                <svg class="w-5.5 h-5.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </span>
            <div class="flex flex-col">
                <span class="font-display font-extrabold text-[15px] tracking-tight text-white leading-none">estehjumbo</span>
                <span class="font-display font-bold text-[10px] text-blue-400 tracking-wider uppercase mt-1">TNJ Bekasi</span>
            </div>
        </div>

        {{-- Sidebar Links --}}
        <nav class="flex-1 py-6 flex flex-col gap-6 overflow-y-auto">
            @php
                $route = Route::currentRouteName();
                $currentMenu = request()->query('menu', 'live_orders');
                $activeMenu = str_contains($route, 'products') ? 'products' : $currentMenu;
            @endphp
            
            {{-- Menu Group: Pesanan --}}
            <div class="space-y-1">
                <div class="px-6 mb-2 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Pesanan</div>
                
                {{-- Live Orders --}}
                <a href="{{ route('admin.dashboard', ['menu' => 'live_orders']) }}" 
                   class="relative flex items-center gap-3.5 px-6 py-3 text-[13px] font-bold tracking-wide transition-all group {{ $activeMenu === 'live_orders' ? 'text-white bg-blue-600/10 border-l-4 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                    <span class="w-5 h-5 flex items-center justify-center rounded-lg {{ $activeMenu === 'live_orders' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }} transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </span>
                    Pesanan Masuk
                </a>
                
                {{-- Order History --}}
                <a href="{{ route('admin.dashboard', ['menu' => 'order_history']) }}" 
                   class="relative flex items-center gap-3.5 px-6 py-3 text-[13px] font-bold tracking-wide transition-all group {{ $activeMenu === 'order_history' ? 'text-white bg-blue-600/10 border-l-4 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                    <span class="w-5 h-5 flex items-center justify-center rounded-lg {{ $activeMenu === 'order_history' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }} transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </span>
                    Riwayat Pesanan
                </a>
            </div>

            {{-- Menu Group: Manajemen --}}
            <div class="space-y-1">
                <div class="px-6 mb-2 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Manajemen</div>
                
                {{-- Products CRUD --}}
                <a href="{{ route('admin.products.index') }}" 
                   class="relative flex items-center gap-3.5 px-6 py-3 text-[13px] font-bold tracking-wide transition-all group {{ $activeMenu === 'products' ? 'text-white bg-blue-600/10 border-l-4 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                    <span class="w-5 h-5 flex items-center justify-center rounded-lg {{ $activeMenu === 'products' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }} transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </span>
                    Menu & Produk
                </a>

                {{-- Inventory (Stok & Bahan) --}}
                <a href="{{ route('admin.dashboard', ['menu' => 'inventory']) }}" 
                   class="relative flex items-center gap-3.5 px-6 py-3 text-[13px] font-bold tracking-wide transition-all group {{ $activeMenu === 'inventory' ? 'text-white bg-blue-600/10 border-l-4 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                    <span class="w-5 h-5 flex items-center justify-center rounded-lg {{ $activeMenu === 'inventory' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }} transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </span>
                    Inventaris & Stok
                </a>
            </div>

            {{-- Menu Group: Sistem --}}
            <div class="space-y-1">
                <div class="px-6 mb-2 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Sistem</div>
                
                {{-- Settings / Tampilan Website --}}
                <a href="{{ route('admin.dashboard', ['menu' => 'settings']) }}" 
                   class="relative flex items-center gap-3.5 px-6 py-3 text-[13px] font-bold tracking-wide transition-all group {{ $activeMenu === 'settings' ? 'text-white bg-blue-600/10 border-l-4 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                    <span class="w-5 h-5 flex items-center justify-center rounded-lg {{ $activeMenu === 'settings' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }} transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" /></svg>
                    </span>
                    Pengaturan
                </a>
            </div>

            <div class="mt-auto pt-6 px-4 pb-2 border-t border-slate-800">
                <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-500/10 transition border border-transparent hover:border-rose-950">
                        <span class="w-5 h-5 flex items-center justify-center rounded-lg bg-rose-500/10 text-rose-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </span>
                        Keluar Akun
                    </button>
                </form>
            </div>
        </nav>

        {{-- Sidebar Bottom: Busy Mode Toggle --}}
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center justify-between bg-slate-800/40 border border-slate-800 px-4 py-3.5 rounded-xl shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Banner Promosi</span>
                
                {{-- Toggle switch --}}
                <button type="button" 
                        @click="toggleSetting('busy_mode')"
                        :class="busyMode ? 'bg-blue-600' : 'bg-slate-700'"
                        class="w-10 h-6 flex items-center rounded-full p-0.5 transition-colors duration-300 focus:outline-none relative">
                    <span :class="busyMode ? 'translate-x-4' : 'translate-x-0'"
                          class="bg-white w-4 h-4 rounded-full shadow-md transform transition-transform duration-300"></span>
                </button>
            </div>
        </div>
    </aside>e>

    {{-- Overlays on mobile --}}
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false" 
         x-cloak
         class="fixed inset-0 bg-black/25 backdrop-blur-xs z-35 md:hidden"></div>

    {{-- Right Content Container --}}
    <div class="flex-1 md:pl-64 min-h-screen flex flex-col">
        
        {{-- Top Header Bar --}}
        <header class="h-20 bg-white/90 backdrop-blur-md border-b border-slate-150 sticky top-0 z-30 flex items-center justify-between px-6 md:px-8">
            {{-- Left: Search Bar & Hamburger --}}
            <div class="flex items-center gap-4 flex-1">
                <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                        class="md:hidden w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                </button>

            </div>

            {{-- Right: Toggles, Notification, User --}}
            <div class="flex items-center gap-6">
                
                {{-- Open For Order Toggle --}}
                <div class="flex items-center gap-3 border-r border-slate-150 pr-5">
                    <span class="text-xs font-bold text-slate-500 tracking-tight">Buka Pesanan</span>
                    
                    {{-- Toggle Switch --}}
                    <button type="button" 
                            @click="toggleSetting('is_open')"
                            :class="isOpen ? 'bg-emerald-500' : 'bg-slate-200'"
                            class="w-10 h-6 flex items-center rounded-full p-0.5 transition-colors duration-300 focus:outline-none relative">
                        <span :class="isOpen ? 'translate-x-4' : 'translate-x-0'"
                              class="bg-white w-4 h-4 rounded-full shadow-md transform transition-transform duration-300"></span>
                    </button>
                </div>


                {{-- User Avatar Profile --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-bold border border-blue-100 grid place-items-center text-sm shadow-xs select-none">
                        @php
                            $user = Auth::user();
                            $words = explode(' ', $user->name);
                            $initials = '';
                            foreach($words as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                            }
                            echo substr($initials, 0, 2);
                        @endphp
                    </div>
                    <div class="hidden lg:flex flex-col">
                        <span class="text-xs font-bold text-slate-900 leading-none">{{ $user->name }}</span>
                        <span class="text-[10px] text-slate-400 font-medium capitalize mt-1.5">{{ $user->roles->first()?->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Success/Error Alerts --}}
        <div class="max-w-7xl w-full mx-auto px-6 md:px-8 mt-6">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs font-semibold text-emerald-800 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-150 rounded-2xl p-4 text-xs font-semibold text-rose-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Main Page Content --}}
        <main class="flex-1 max-w-7xl w-full mx-auto px-6 md:px-8 py-6">
            @yield('content')
        </main>
    </div>

    {{-- Script for Toggle logic --}}
    <script>
        function adminLayoutState() {
            @php
                $store = \App\Models\StoreSetting::current();
            @endphp
            return {
                mobileSidebarOpen: false,
                isOpen: {{ $store->is_open ? 'true' : 'false' }},
                busyMode: {{ $store->busy_mode ? 'true' : 'false' }},

                async toggleSetting(name) {
                    try {
                        const res = await fetch('{{ route('admin.settings.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ setting: name })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            if (name === 'is_open') this.isOpen = data.value;
                            if (name === 'busy_mode') this.busyMode = data.value;
                            
                            // Show floating success message
                            this.showToast(data.message);
                        }
                    } catch (e) {
                        console.error('Failed to toggle settings:', e);
                    }
                },

                showToast(message) {
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-6 right-6 z-[100] bg-slate-900 text-white text-xs font-bold px-4 py-3 rounded-xl shadow-lg transition-opacity duration-300 opacity-0';
                    toast.innerText = message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.classList.remove('opacity-0'), 50);
                    setTimeout(() => {
                        toast.classList.add('opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                }
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
