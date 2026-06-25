@php
    $store      = $store ?? \App\Models\StoreSetting::current();
    $cart       = session('cart', []);
    $cartCount  = collect($cart)->sum('quantity');
    $cartTotal  = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    $isHome     = Route::is('home') || request()->is('/');
    $isMenu     = Route::is('menu');
    $isCart     = Route::is('cart.*');
@endphp

<header
    x-data="{ scrolled: false, mobile: false, bannerHeight: 0 }"
    x-init="
        const banner = document.getElementById('store-banner');
        if (banner) {
            bannerHeight = banner.offsetHeight;
            new ResizeObserver(() => {
                bannerHeight = banner.offsetHeight;
            }).observe(banner);
        }
        window.addEventListener('scroll', () => {
            scrolled = window.scrollY > (bannerHeight > 0 ? bannerHeight : 20);
        });
    "
    class="fixed left-0 right-0 z-50 transition-all duration-300"
    :style="scrolled ? 'top: 0px' : `top: ${bannerHeight}px`"
    :class="scrolled || mobile || {{ $isHome ? 'false' : 'true' }}
                ? 'bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-[0_1px_3px_rgba(0,0,0,0.05)]'
                : 'bg-white/85 backdrop-blur-md border-b border-slate-200/40 shadow-sm'">
    <div class="max-w-7xl mx-auto px-6 h-16 md:h-18 flex items-center justify-between gap-6">

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 group flex-shrink-0">
            <span class="w-9 h-9 rounded-xl grid place-items-center shadow-sm bg-emerald-500 text-white">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 8h14l-1.5 11a2 2 0 0 1-2 1.8h-7a2 2 0 0 1-2-1.8L5 8z"/>
                    <path d="M9 8V5a3 3 0 0 1 6 0v3"/>
                </svg>
            </span>
            <span class="font-display font-extrabold text-lg tracking-tight text-slate-950">
                Es Teh Jumbo
            </span>
        </a>
 
        {{-- Desktop nav --}}
        <nav class="hidden md:flex items-center gap-1">
            @php
                $links = [
                    ['label' => 'Beranda', 'href' => route('home'),  'active' => $isHome],
                    ['label' => 'Menu',    'href' => route('menu'),  'active' => $isMenu],
                    ['label' => 'Tentang', 'href' => route('home') . '#tentang', 'active' => false],
                    ['label' => 'Kontak',  'href' => '#contact',     'active' => false],
                ];
            @endphp
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="px-4 py-2 text-sm font-semibold rounded-full transition-colors duration-300 {{ $link['active'] ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
 
        {{-- Right actions --}}
        <div class="flex items-center gap-3">
            {{-- Search shortcut → menu page with focus --}}
            <a href="{{ route('menu') }}#search"
               class="hidden sm:inline-flex w-10 h-10 rounded-full items-center justify-center text-slate-700 hover:bg-slate-100 transition-colors duration-300"
               aria-label="Cari menu">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>
                </svg>
            </a>
 
            {{-- Cart (opens drawer) --}}
            <button type="button"
                    @click="$store.cart.show()"
                    class="relative inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors duration-300"
                    aria-label="Buka keranjang">
                <span id="nav-cart-icon" class="relative inline-block">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                    </svg>
                    <template x-if="$store.cart.count > 0">
                        <span :key="$store.cart.count"
                               class="badge-bump absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold grid place-items-center ring-2 ring-white"
                               x-text="$store.cart.count">
                        </span>
                    </template>
                </span>
                <template x-if="$store.cart.total > 0">
                    <span class="hidden lg:inline" x-text="'Rp ' + $store.cart.format($store.cart.total)"></span>
                </template>
            </button>
 
            {{-- Mobile toggle --}}
            <button type="button"
                    @click="mobile = !mobile"
                    class="md:hidden w-10 h-10 rounded-full grid place-items-center text-slate-700 hover:bg-slate-100 transition-colors"
                    aria-label="Buka menu">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
 
    {{-- Mobile drawer --}}
    <div x-show="mobile" x-transition.opacity
         class="md:hidden bg-white border-t border-slate-100"
         style="display: none;">
        <nav class="max-w-7xl mx-auto px-6 py-3 flex flex-col">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="px-3 py-3 text-sm font-medium rounded-lg transition
                          {{ $link['active'] ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700 hover:bg-slate-50' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

{{-- Spacer for non-home pages so content doesn't sit under the fixed nav --}}
@unless ($isHome)
    <div class="h-16 md:h-18"></div>
@endunless
