@extends('layouts.public')
@section('title', $store->store_name . ' — Es Teh Jumbo Asli, Diracik Segar Harian')

@php
    $allProducts = $categories->flatMap->products;

    $heroTeh    = $allProducts->firstWhere('name', 'Teh Jumbo');
    $heroLemon  = $allProducts->firstWhere('name', 'Lemon Tea');
    $heroCoklat = $allProducts->firstWhere('name', 'Es Coklat');

    $heroTehImg    = $heroTeh    ? $heroTeh->image_path    : 'images/es-teh-jumbo';
    $heroLemonImg  = $heroLemon  ? $heroLemon->image_path  : 'images/lemon-tea-new';
    $heroCoklatImg = $heroCoklat ? $heroCoklat->image_path : 'images/es-coklat';

    // Build a lookup map so we don't trigger N+1 queries on $p->category
    $catSortMap = $categories->pluck('sort_order', 'id');

    $featuredProducts = $allProducts
        ->sortBy(fn($p) => $catSortMap[$p->category_id] ?? 99)
        ->take(8);
@endphp


@section('content')
<div class="bg-app text-slate-600">

    {{-- ════════════════════════════════════════════════════
         HERO  ·  vibrant, alive
    ════════════════════════════════════════════════════ --}}
    <section class="relative w-full overflow-hidden pt-28 pb-32 md:pt-32 md:pb-44"
             style="background:
                    radial-gradient(1200px 600px at 85% -5%, #38bdf8 0%, transparent 55%),
                    radial-gradient(900px 600px at 5% 110%, #0369a1 0%, transparent 55%),
                    linear-gradient(135deg, #0ea5e9 0%, #0284c7 55%, #075985 100%);">

        {{-- ambient glows --}}
        <div aria-hidden="true" class="absolute -top-24 right-1/4 w-[28rem] h-[28rem] rounded-full bg-cyan-300/30 blur-[120px]"></div>
        <div aria-hidden="true" class="absolute bottom-0 left-1/3 w-96 h-96 rounded-full bg-sky-400/30 blur-[100px]"></div>




        <div class="relative z-10 max-w-5xl mx-auto px-6 grid md:grid-cols-12 gap-8 items-center">
            {{-- Headline --}}
            <div class="md:col-span-6 text-left">
                @php
                    $hour = now()->timezone('Asia/Jakarta')->hour;
                    $isOpen = $hour >= 9 && $hour < 22;
                @endphp
                <span class="inline-flex items-center gap-2 bg-white/15 text-white text-[11px] font-semibold tracking-wide px-3.5 py-1.5 rounded-full backdrop-blur ring-1 ring-white/25">
                    <span class="w-1.5 h-1.5 rounded-full {{ $isOpen ? 'bg-emerald-300' : 'bg-rose-400' }} {{ $isOpen ? 'animate-pulse' : '' }}"></span>
                    {{ $isOpen ? 'Buka sekarang' : 'Tutup · Buka 09.00 WIB' }} · Galaxy, Bekasi
                </span>
                <h1 class="mt-6 font-display font-extrabold text-white leading-[0.95] tracking-tight text-5xl md:text-6xl lg:text-[72px] drop-shadow-[0_4px_20px_rgba(0,0,0,0.15)]">
                    {!! $store->hero_title !!}
                </h1>
                <p class="mt-6 text-sky-50/90 text-base md:text-lg leading-relaxed max-w-md">
                    {{ $store->hero_subtitle }}
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="{{ route('menu') }}" class="btn-chip">
                        PESAN SEKARANG
                        <span class="chip">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </a>
                    <a href="#menu" class="btn-glass">Lihat Menu</a>
                </div>

                <div class="mt-9 flex items-center gap-5 text-sky-50/80 text-xs">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 5.82 21.02 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <strong class="font-semibold text-white">4.9</strong> rating
                    </span>
                    <span class="w-px h-3 bg-white/30"></span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <strong class="font-semibold text-white">5.000+</strong> pelanggan
                    </span>
                    <span class="w-px h-3 bg-white/30"></span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        antar <strong class="font-semibold text-white">cepat</strong>
                    </span>
                </div>
            </div>

            {{-- Hero stage --}}
            <div class="md:col-span-6 relative min-h-[420px] md:min-h-[560px] flex items-center justify-center select-none">
                {{-- pedestal glow --}}
                <div aria-hidden="true" class="absolute bottom-10 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full bg-amber-300/35 blur-3xl"></div>
                <div aria-hidden="true" class="absolute bottom-16 left-1/2 -translate-x-1/2 w-80 h-14 bg-sky-900/30 rounded-[100%] blur-xl"></div>

                {{-- 3D storefront image (no card box border) --}}
                <div class="relative z-20 w-[90%] md:w-[95%] lg:w-[102%] lg:-mr-4 anim-bob">
                    <img src="{{ asset($store->hero_image_path) }}" alt="Kedai Es Teh Jumbo 3D" class="w-full h-auto object-contain drop-shadow-[0_25px_40px_rgba(0,0,0,0.35)]">
                </div>
            </div>
        </div>

        {{-- wave --}}
        <div class="absolute left-0 right-0 bottom-0 z-[5] pointer-events-none">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" class="w-full h-16 md:h-24 block" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,70 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#eef4fb"/>
            </svg>
        </div>
    </section>



    {{-- ════════════════════════════════════════════════════
         MENU FAVORIT  ·  white section
    ════════════════════════════════════════════════════ --}}
    <section id="menu" class="max-w-5xl mx-auto px-6 pt-20 pb-16 md:pt-24">
        <div class="flex items-end justify-between mb-12">
            <div>
                <p class="eyebrow">Pilihan Hari Ini</p>
                <h2 class="mt-3 font-display font-extrabold text-3xl md:text-[2.75rem] text-ink leading-tight tracking-tight">Menu Favorit</h2>
                <p class="mt-3 text-slate-500 max-w-md text-[15px]">Paling sering dipesan, paling cepat habis. Coba salah satu di bawah ini.</p>
            </div>
            <a href="{{ route('menu') }}"
               class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-sky-600 hover:text-sky-700 transition group">
                Semua menu
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-10 text-center sm:hidden">
            <a href="{{ route('menu') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-sky-600 hover:text-sky-700">
                Lihat semua menu →
            </a>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         CERITA  ·  Elegant Warm Sand/Cream Section (No curves, no bobbing)
     ════════════════════════════════════════════════════ --}}
    <section id="tentang" class="relative overflow-hidden bg-stone-50 border-y border-stone-200/60">
        {{-- Soft subtle dot pattern --}}
        <div aria-hidden="true" class="absolute inset-0 opacity-[0.03]"
             style="background-image: radial-gradient(circle at 1px 1px, #1c1917 1px, transparent 0); background-size: 40px 40px;"></div>

        <div class="relative max-w-5xl mx-auto px-6 py-20 md:py-24 grid md:grid-cols-12 gap-10 lg:gap-14 items-center">
            {{-- Image Column --}}
            <div class="md:col-span-5 relative flex items-center justify-center">
                {{-- Clean image container with rounded corners and subtle shadow --}}
                <div class="w-full max-w-sm md:max-w-none rounded-3xl overflow-hidden shadow-lg border border-stone-200 bg-white p-2">
                    <img src="{{ asset($store->about_image_path) }}" alt="Tentang Es Teh Jumbo" class="w-full h-auto object-cover rounded-2xl">
                </div>
            </div>

            {{-- Content Column --}}
            <div class="md:col-span-7 text-left">
                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-[10px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Cerita Kami
                </span>
                <h2 class="mt-4 font-display font-extrabold text-stone-900 text-3xl md:text-4xl leading-tight tracking-tight">
                    Mulai dari satu gelas<br class="hidden md:inline"> untuk tetangga sebelah.
                </h2>
                <p class="mt-4 text-stone-600 text-sm md:text-base leading-relaxed">
                    {{ $store->about_text }}
                </p>

                {{-- Stats --}}
                <div class="mt-8 grid grid-cols-3 gap-4">
                    @php
                        $stats = [
                            ['val' => '30+', 'label' => 'Varian menu'],
                            ['val' => '5K+', 'label' => 'Pelanggan setia'],
                            ['val' => '4.9★','label' => 'Rata-rata rating'],
                        ];
                    @endphp
                    @foreach ($stats as $s)
                        <div class="bg-white rounded-2xl p-4 border border-stone-200/80 text-center shadow-sm hover:shadow transition-shadow duration-300">
                            <p class="font-display font-extrabold text-stone-900 text-2xl md:text-3xl tracking-tight">{{ $s['val'] }}</p>
                            <p class="text-[10px] text-stone-500 font-bold mt-1 uppercase tracking-wider">{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         JELAJAHI KATEGORI  ·  colorful gradient cards
    ════════════════════════════════════════════════════ --}}
    @php
        $catStyles = [
            ['grad' => 'from-sky-400 to-cyan-500',      'shadow' => 'rgba(14,165,233,.45)'],
            ['grad' => 'from-rose-400 to-pink-500',     'shadow' => 'rgba(244,63,94,.40)'],
            ['grad' => 'from-amber-400 to-orange-500',  'shadow' => 'rgba(249,115,22,.40)'],
            ['grad' => 'from-emerald-400 to-teal-500',  'shadow' => 'rgba(16,185,129,.40)'],
        ];
    @endphp
    <section class="max-w-5xl mx-auto px-6 pt-4 pb-16">
        <div class="text-center max-w-xl mx-auto mb-12">
            <p class="eyebrow">Jelajahi</p>
            <h2 class="mt-3 font-display font-extrabold text-3xl md:text-[2.75rem] text-ink leading-tight tracking-tight">Pilih kategori favoritmu</h2>
            <p class="mt-3 text-slate-500 text-[15px]">Temukan minuman terbaik sesuai selera kamu.</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($categories->take(4) as $i => $category)
                @php
                    $style = $catStyles[$i % 4];
                    $thumb = $category->products->first();
                @endphp
                <a href="#cat-{{ Str::slug($category->name) }}"
                   class="group relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $style['grad'] }} p-6 min-h-[200px] flex flex-col justify-between transition-transform duration-300 hover:-translate-y-1.5"
                   style="box-shadow: 0 22px 45px -20px {{ $style['shadow'] }};">
                    <div aria-hidden="true" class="absolute -top-8 -right-8 w-28 h-28 rounded-full bg-white/20 blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="relative">
                        <p class="text-white/80 text-[11px] font-semibold uppercase tracking-wider">{{ $category->products->count() }} menu</p>
                        <h3 class="mt-1 font-display font-bold text-white text-xl leading-tight drop-shadow-sm">{{ $category->name }}</h3>
                    </div>
                    @if ($thumb)
                        <div class="relative self-end w-28 h-28 -mb-4 -mr-3.5 anim-bob" style="animation-delay:{{ $i * 0.3 }}s">
                            <x-smart-image :src="$thumb->image_path" :alt="$thumb->name" :transparent="true"
                                            class="w-full h-full object-contain drop-shadow-[0_15px_22px_rgba(0,0,0,0.28)] group-hover:scale-112 group-hover:rotate-3 transition-transform duration-500" />
                        </div>
                    @endif
                    <span class="absolute bottom-5 left-6 inline-flex items-center gap-1 text-white text-xs font-semibold opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                        Lihat menu
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         KATEGORI LENGKAP  ·  white, alternating subtle tint
    ════════════════════════════════════════════════════ --}}
    @foreach ($categories as $loopCat => $category)
        @if ($category->products->isEmpty()) @continue @endif
        <section id="cat-{{ Str::slug($category->name) }}"
                 class="{{ $loop->iteration % 2 === 0 ? 'bg-app border-y border-slate-100' : '' }} scroll-mt-24">
            <div class="max-w-5xl mx-auto px-6 py-16 md:py-20">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="eyebrow">Kategori</p>
                        <h2 class="mt-2.5 font-display font-extrabold text-2xl md:text-[2rem] text-ink leading-tight tracking-tight">{{ $category->name }}</h2>
                    </div>
                    @if ($category->products->count() > 4)
                        <a href="{{ route('menu', ['category' => $category->id]) }}"
                           class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-sky-600 hover:text-sky-700 transition group">
                            Lihat semua
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach ($category->products->take(4) as $product)
                        <x-product-card :product="$product" :category="$category->name" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    {{-- ════════════════════════════════════════════════════
         TESTIMONI  ·  clean white with vibrant cards
    ════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden">
        <div class="relative max-w-5xl mx-auto px-6 py-20 md:py-28">
            <div class="text-center max-w-xl mx-auto">
                <p class="eyebrow">Testimoni</p>
                <h2 class="mt-3 font-display font-extrabold text-3xl md:text-[2.75rem] text-ink leading-tight tracking-tight">Apa kata pelanggan</h2>
                <p class="mt-3 text-slate-500 text-[15px]">Cerita nyata dari yang sudah mencoba dan ketagihan.</p>
            </div>

            @php
                $testimonials = [
                    ['n' => 'Aditya W.', 'r' => 'Mahasiswa', 't' => 'Porsi jumbo, harganya bersahabat. Wajib pesan kalau lagi panas-panasnya.', 'color' => 'from-sky-500 to-cyan-500'],
                    ['n' => 'Siti R.',   'r' => 'Karyawan',  't' => 'Es coklatnya kental, manisnya pas. Es batu tidak berlebihan jadi rasa tetap enak.', 'color' => 'from-amber-500 to-orange-500'],
                    ['n' => 'Budi S.',   'r' => 'Kurir',     't' => 'Pesan, langsung diantar cepat. Harga masuk akal, jadi langganan tiap lewat Galaxy.', 'color' => 'from-emerald-500 to-teal-500'],
                ];
            @endphp
            <div class="mt-14 grid md:grid-cols-3 gap-7">
                @foreach ($testimonials as $t)
                    <figure class="group relative bg-white rounded-3xl p-8 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.12)] hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.15)] ring-1 ring-slate-100 hover:ring-slate-200 transition-all duration-300 hover:-translate-y-1">
                        {{-- Color accent top strip --}}
                        <div class="absolute top-0 left-8 right-8 h-1 rounded-b-full bg-gradient-to-r {{ $t['color'] }} opacity-60 group-hover:opacity-100 transition-opacity"></div>

                        {{-- Stars --}}
                        <div class="flex gap-0.5 text-amber-400">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 5.82 21.02 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                        </div>

                        {{-- Quote --}}
                        <blockquote class="mt-5 text-slate-600 leading-relaxed text-[15px]">
                            <svg class="w-6 h-6 text-slate-200 mb-2" viewBox="0 0 24 24" fill="currentColor"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg>
                            {{ $t['t'] }}
                        </blockquote>

                        {{-- Author --}}
                        <figcaption class="mt-7 flex items-center gap-3 pt-5 border-t border-slate-100">
                            <span class="w-11 h-11 rounded-full bg-gradient-to-br {{ $t['color'] }} text-white grid place-items-center font-display font-bold text-sm shadow-md">{{ substr($t['n'], 0, 1) }}</span>
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ $t['n'] }}</p>
                                <p class="text-xs text-slate-500">{{ $t['r'] }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         CTA AKHIR  ·  blue gradient with polished typography
    ════════════════════════════════════════════════════ --}}
    <section class="max-w-5xl mx-auto px-6 py-20 md:py-24">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-sky-500 via-sky-600 to-sky-700 px-8 md:px-14 py-14 md:py-16 text-center">
            <div aria-hidden="true" class="absolute inset-0 opacity-[0.12]" style="background-image:radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size:60px 60px, 80px 80px;"></div>
            <div aria-hidden="true" class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/15 blur-3xl"></div>
            <div aria-hidden="true" class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-cyan-300/20 blur-3xl"></div>
            <div class="relative">
                <span class="inline-flex items-center gap-2 bg-white/15 text-white/90 text-[11px] font-semibold tracking-[0.18em] uppercase px-4 py-1.5 rounded-full ring-1 ring-white/20 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Siap Antar
                </span>
                <h2 class="font-display font-extrabold text-white text-3xl md:text-[2.75rem] tracking-tight max-w-xl mx-auto leading-tight">
                    Lagi haus? Tinggal sebut alamat.
                </h2>
                <p class="mt-4 text-sky-100/80 max-w-md mx-auto text-[15px] leading-relaxed">
                    Mau pesan banyak untuk acara, atau satu untuk sore-sore — kami siap. Pilih menu di sini, atau langsung WhatsApp.
                </p>
                <div class="mt-9 flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-white text-sky-700 hover:bg-sky-50 text-sm font-bold px-8 py-4 rounded-full transition shadow-lg shadow-sky-900/20 hover:-translate-y-0.5">
                        Pesan Lewat Web
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-8 py-4 rounded-full backdrop-blur ring-1 ring-white/25 transition hover:-translate-y-0.5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/></svg>
                        Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .hero-cup-main { animation: cupBob 4.5s ease-in-out infinite; }
    .hero-cup-side { animation: cupBob 5.2s ease-in-out infinite; animation-delay:.3s; }
    @keyframes cupBob {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-10px); }
    }
</style>
@endsection

