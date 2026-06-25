@extends('layouts.public')
@section('title', 'Menu Lengkap — ' . $store->store_name)

@php
    $allProducts = $categories->flatMap->products;
    $activeCategory = request('category', 'all');
    $filteredProducts = $activeCategory === 'all'
        ? $allProducts
        : $allProducts->where('category_id', (int) $activeCategory);

    $activeCategoryName = $activeCategory === 'all'
        ? 'Semua Menu'
        : ($categories->firstWhere('id', (int) $activeCategory)->name ?? 'Menu');

    $query = $query ?? '';
@endphp

@section('content')
<div x-data="menuFilter({{ $filteredProducts->count() }})" class="bg-page-soft text-slate-800 relative overflow-hidden">
    {{-- Decorative blobs --}}
    <div aria-hidden="true" class="deco-blob deco-blob-sky w-[28rem] h-[28rem] -top-32 -right-32"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-amber w-80 h-80 top-1/3 -left-24 opacity-40"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-rose w-72 h-72 bottom-32 -right-16 opacity-40"></div>

    {{-- ════════════════════════════════════════════════════
         PAGE HEADER  ·  with search bar
    ════════════════════════════════════════════════════ --}}
    <section class="relative bg-gradient-to-b from-sky-100/70 via-sky-50/50 to-transparent border-b border-sky-100/60 pt-28 pb-14">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-5" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-sky-600 transition">Beranda</a>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                <span class="text-slate-700 font-medium">Menu</span>
                @if ($query !== '')
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    <span class="text-slate-500">Pencarian: "{{ $query }}"</span>
                @endif
            </nav>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="min-w-0">
                    <p class="eyebrow">Katalog</p>
                    <h1 class="mt-2.5 font-display font-extrabold text-3xl md:text-[2.75rem] text-ink tracking-tight leading-tight">
                        {{ $query !== '' ? 'Hasil Pencarian' : $activeCategoryName }}
                    </h1>
                    <p class="mt-3 text-slate-600 max-w-lg">
                        <span x-show="!filtering" x-cloak>{{ $filteredProducts->count() }} pilihan minuman segar siap diantar dalam radius {{ (int) $store->max_radius_km }} km.</span>
                        <span x-show="filtering" x-text="visibleCount + ' menu cocok dengan pencarian Anda.'" x-cloak></span>
                    </p>
                </div>

                {{-- Search bar --}}
                <form id="search" action="{{ route('menu') }}" method="GET" class="md:w-96 w-full scroll-mt-32">
                    @if ($activeCategory !== 'all')
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif
                    <label class="relative block">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                        <input type="search"
                               name="q"
                               x-model="liveQuery"
                               @input.debounce.150ms="applyFilter()"
                               value="{{ $query }}"
                               placeholder="Cari menu favoritmu..."
                               autocomplete="off"
                               class="w-full bg-white border border-slate-200 rounded-full pl-11 pr-12 py-3.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition">
                        <button type="button"
                                x-show="liveQuery.length > 0"
                                @click="clearSearch()"
                                x-cloak
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-700"
                                aria-label="Bersihkan pencarian">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </label>
                </form>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         CATEGORY TABS  ·  sticky
    ════════════════════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white/85 backdrop-blur-md border-b border-sky-100/80 shadow-[0_4px_20px_-12px_rgba(14,165,233,0.15)]">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex items-center gap-2 overflow-x-auto py-4 scrollbar-hide" aria-label="Filter kategori">
                <a href="{{ route('menu', $query !== '' ? ['q' => $query] : []) }}"
                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition
                          {{ $activeCategory === 'all'
                                ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20'
                                : 'bg-white text-slate-600 hover:bg-sky-50 hover:text-sky-700 border border-slate-150' }}">
                    Semua
                    <span class="ml-1 opacity-70 text-xs">{{ $allProducts->count() }}</span>
                </a>
                @foreach ($categories as $cat)
                    @if ($cat->products->isEmpty()) @continue @endif
                    <a href="{{ route('menu', array_filter(['category' => $cat->id, 'q' => $query !== '' ? $query : null])) }}"
                       class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition
                              {{ (int) $activeCategory === $cat->id
                                    ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20'
                                    : 'bg-white text-slate-600 hover:bg-sky-50 hover:text-sky-700 border border-slate-150' }}">
                        {{ $cat->name }}
                        <span class="ml-1 opacity-70 text-xs">{{ $cat->products->count() }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         PRODUCT GRID
    ════════════════════════════════════════════════════ --}}
    <section class="relative max-w-7xl mx-auto px-6 py-16">
        @if ($filteredProducts->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 lg:gap-5" id="product-grid">
                @foreach ($filteredProducts as $product)
                    <div class="product-cell"
                         data-name="{{ Str::lower($product->name) }}"
                         data-category="{{ Str::lower($product->category->name) }}">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            {{-- Empty filter state --}}
            <div x-show="visibleCount === 0 && filtering" x-cloak
                 class="mt-10 bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-white grid place-items-center text-slate-400">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                </div>
                <h3 class="mt-4 font-display font-semibold text-ink text-lg">Tidak ada menu yang cocok</h3>
                <p class="mt-1 text-sm text-slate-500">Coba kata kunci lain atau ubah kategori.</p>
                <button type="button" @click="clearSearch()" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-sky-600 hover:text-sky-700">
                    Bersihkan pencarian →
                </button>
            </div>
        @else
            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-14 text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-white grid place-items-center text-slate-400">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                </div>
                <h3 class="mt-4 font-display font-semibold text-ink text-lg">
                    @if ($query !== '')
                        Tidak ada hasil untuk "{{ $query }}"
                    @else
                        Tidak ada produk di kategori ini
                    @endif
                </h3>
                <p class="mt-1 text-sm text-slate-500">Coba kata kunci atau kategori lain.</p>
                <a href="{{ route('menu') }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-sky-600 hover:text-sky-700">
                    Lihat semua menu →
                </a>
            </div>
        @endif
    </section>

    {{-- ════════════════════════════════════════════════════
         CTA STRIP
    ════════════════════════════════════════════════════ --}}
    <section class="relative max-w-7xl mx-auto px-6 pb-24">
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-sky-900 px-8 py-10 md:px-12 md:py-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div aria-hidden="true" class="absolute -top-16 -right-16 w-72 h-72 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div aria-hidden="true" class="absolute -bottom-20 -left-10 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="relative">
                <h2 class="font-display font-semibold text-white text-2xl md:text-3xl tracking-tight">Butuh bantuan memilih?</h2>
                <p class="mt-2 text-slate-300 max-w-md">Tanya rekomendasi varian terlaris langsung lewat WhatsApp.</p>
            </div>
            <a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank" rel="noopener"
               class="relative inline-flex items-center gap-2 bg-white text-slate-900 hover:bg-sky-50 text-sm font-semibold px-6 py-3.5 rounded-full transition shadow-lg w-fit">
                Chat Sekarang
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </section>
</div>

<script>
    function menuFilter(initialCount) {
        return {
            liveQuery:    @js($query),
            visibleCount: initialCount,
            filtering:    false,

            applyFilter() {
                const q = (this.liveQuery || '').trim().toLowerCase();
                this.filtering = q.length > 0;
                let visible = 0;
                document.querySelectorAll('.product-cell').forEach(cell => {
                    const name = cell.dataset.name || '';
                    const cat  = cell.dataset.category || '';
                    const match = !q || name.includes(q) || cat.includes(q);
                    cell.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                this.visibleCount = visible;
            },

            clearSearch() {
                this.liveQuery = '';
                this.filtering = false;
                this.applyFilter();
                // Also strip ?q= from the URL without a reload
                const url = new URL(window.location.href);
                url.searchParams.delete('q');
                window.history.replaceState({}, '', url.toString());
            },

            init() {
                if (this.liveQuery) this.applyFilter();
            },
        };
    }
</script>
@endsection
