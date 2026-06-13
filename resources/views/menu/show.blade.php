@extends('layouts.public')
@section('title', $product->name . ' — ' . $store->store_name)

@section('content')
<div class="bg-page-soft text-slate-800 relative overflow-hidden">
    {{-- Page-level decorative blobs (subtle, behind everything) --}}
    <div aria-hidden="true" class="deco-blob deco-blob-sky w-96 h-96 -top-20 -right-32"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-rose w-80 h-80 top-[40%] -left-24"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-amber w-72 h-72 bottom-20 -right-20"></div>

    {{-- ──────────────────────────────────────────────
         BREADCRUMB
    ────────────────────────────────────────────── --}}
    <section class="relative bg-gradient-to-b from-sky-50/80 to-transparent border-b border-sky-100/60 pt-24 pb-5">
        <div class="max-w-5xl mx-auto px-6">
            <nav class="flex items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-sky-600 transition">Beranda</a>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                <a href="{{ route('menu') }}" class="hover:text-sky-600 transition">Menu</a>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                <a href="{{ route('menu', ['category' => $product->category_id]) }}" class="hover:text-sky-600 transition">{{ $product->category->name }}</a>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                <span class="text-slate-700 font-medium truncate">{{ $product->name }}</span>
            </nav>
        </div>
    </section>

    {{-- ──────────────────────────────────────────────
         PRODUCT DETAIL
    ────────────────────────────────────────────── --}}
    <section
        x-data="productDetail({
            basePrice: {{ (int) $product->price }},
            sizes: @js($sizes),
            toppings: @js($toppings->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'price' => (int) $t->price])->values()),
            productId: {{ $product->id }},
        })"
        class="relative max-w-5xl mx-auto px-6 py-12 md:py-16">

        <div class="grid md:grid-cols-12 gap-10 lg:gap-16 items-start">

            {{-- LEFT: image stage --}}
            <div class="md:col-span-6">
                <div class="relative aspect-square rounded-[32px] bg-gradient-to-br from-sky-100 via-white to-rose-50 overflow-hidden border border-white shadow-[0_30px_60px_-30px_rgba(14,165,233,0.35)]">
                    {{-- decorative blobs --}}
                    <div aria-hidden="true" class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-sky-200 blur-3xl opacity-70"></div>
                    <div aria-hidden="true" class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full bg-rose-200/60 blur-3xl"></div>

                    {{-- ground shadow --}}
                    <div aria-hidden="true" class="absolute bottom-10 left-1/2 -translate-x-1/2 w-3/5 h-5 bg-slate-900/15 rounded-full blur-lg"></div>

                    {{-- product image --}}
                    <div class="absolute inset-0 flex items-center justify-center p-12">
                        <x-smart-image :src="$product->image_path" :alt="$product->name" :transparent="true"
                                        class="relative z-10 w-full h-full object-contain drop-shadow-[0_25px_30px_rgba(0,0,0,0.15)] hover-tilt" />
                    </div>

                    {{-- volume badge --}}
                    <div class="absolute top-5 left-5 inline-flex items-center gap-2 bg-white/90 backdrop-blur rounded-full pl-3 pr-4 py-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-slate-700" x-text="currentSize().volume"></span>
                    </div>

                    {{-- floating dot pattern --}}
                    <div aria-hidden="true" class="absolute bottom-6 right-6 grid grid-cols-3 gap-1.5 opacity-50">
                        @for ($i = 0; $i < 9; $i++)
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-300"></span>
                        @endfor
                    </div>
                </div>

                {{-- ingredients (if any) --}}
                @if ($product->ingredients->isNotEmpty())
                    <div class="mt-6 bg-white/70 backdrop-blur rounded-2xl p-5 border border-sky-100 shadow-sm">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-sky-600 font-semibold mb-3">Komposisi</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->ingredients as $ing)
                                <span class="inline-flex items-center gap-1.5 bg-white border border-sky-100 rounded-full px-3 py-1.5 text-xs text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                    {{ $ing->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT: options panel (now in a soft card) --}}
            <div class="md:col-span-6">
                <div class="relative bg-white/80 backdrop-blur-sm rounded-[28px] p-7 md:p-8 border border-white shadow-[0_20px_50px_-20px_rgba(2,132,199,0.20)]">
                <p class="text-[11px] uppercase tracking-[0.2em] text-sky-600 font-semibold">{{ $product->category->name }}</p>
                <h1 class="mt-2 font-display font-semibold text-ink text-3xl md:text-4xl tracking-tight leading-tight">{{ $product->name }}</h1>

                {{-- rating hint --}}
                <div class="mt-3 flex items-center gap-3 text-sm">
                    <div class="flex items-center gap-0.5 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 5.82 21.02 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <span class="text-slate-500 text-xs">4.9 · 200+ pesanan bulan ini</span>
                </div>

                @if ($product->description)
                    <p class="mt-5 text-slate-600 leading-relaxed">{{ $product->description }}</p>
                @else
                    <p class="mt-5 text-slate-600 leading-relaxed">
                        Diracik segar dengan bahan pilihan. Sempurna untuk melepas dahaga di cuaca panas.
                    </p>
                @endif

                {{-- live price --}}
                <div class="mt-7 flex items-baseline gap-3">
                    <p class="font-display text-ink">
                        <span class="text-base font-medium text-slate-400 align-top">Rp</span>
                        <span class="text-4xl font-bold tracking-tight" x-text="format(unitPrice())"></span>
                    </p>
                    <span class="text-xs text-slate-400" x-show="modifiersTotal() > 0" x-cloak>
                        (+Rp <span x-text="format(modifiersTotal())"></span> opsi)
                    </span>
                </div>

                {{-- ─── SIZE ─── --}}
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-slate-900">Ukuran</p>
                        <p class="text-xs text-slate-500">Wajib pilih</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($sizes as $size)
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="{{ $size['key'] }}" x-model="size" class="sr-only peer">
                                <div class="relative rounded-2xl border-2 px-4 py-3.5 transition-all
                                            peer-checked:border-sky-500 peer-checked:bg-sky-50/60
                                            border-slate-200 hover:border-slate-300">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-display font-bold text-slate-900 text-sm">{{ $size['label'] }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $size['volume'] }}</p>
                                        </div>
                                        <p class="font-display font-bold text-sm
                                                  {{ $size['modifier'] > 0 ? 'text-sky-600' : 'text-slate-400' }}">
                                            {{ $size['modifier'] > 0 ? '+' . number_format($size['modifier'], 0, ',', '.') : 'Standar' }}
                                        </p>
                                    </div>
                                    <span class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:after:content-['✓'] after:text-white after:text-[10px] after:font-bold after:flex after:items-center after:justify-center after:w-full after:h-full hidden peer-checked:block"></span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ─── ICE LEVEL ─── --}}
                <div class="mt-7">
                    <p class="text-sm font-semibold text-slate-900 mb-3">Level Es</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'less' => 'Sedikit',
                            'normal' => 'Normal',
                            'extra' => 'Banyak',
                            'none' => 'Tanpa Es',
                        ] as $key => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="ice" value="{{ $key }}" x-model="ice" class="sr-only peer">
                                <span class="inline-block px-4 py-2 rounded-full text-sm font-medium border transition
                                             border-slate-200 text-slate-600 hover:border-slate-300
                                             peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ─── SUGAR LEVEL ─── --}}
                <div class="mt-7">
                    <p class="text-sm font-semibold text-slate-900 mb-3">Level Manis</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'less' => 'Less Sugar',
                            'normal' => 'Normal',
                            'extra' => 'Extra Manis',
                            'none' => 'Tanpa Gula',
                        ] as $key => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="sugar" value="{{ $key }}" x-model="sugar" class="sr-only peer">
                                <span class="inline-block px-4 py-2 rounded-full text-sm font-medium border transition
                                             border-slate-200 text-slate-600 hover:border-slate-300
                                             peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ─── TOPPINGS ─── --}}
                @if ($toppings->isNotEmpty())
                    <div class="mt-7">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-slate-900">Tambahan Topping</p>
                            <p class="text-xs text-slate-500">Opsional</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach ($toppings as $topping)
                                <label class="cursor-pointer">
                                    <input type="checkbox" value="{{ $topping->id }}" x-model="toppingIds" class="sr-only peer">
                                    <div class="relative flex items-center gap-3 rounded-2xl border-2 px-3 py-3 transition
                                                border-slate-200 hover:border-slate-300
                                                peer-checked:border-sky-500 peer-checked:bg-sky-50/60">
                                        <div class="w-12 h-12 rounded-xl bg-slate-50 grid place-items-center flex-shrink-0 p-1">
                                            <x-smart-image :src="$topping->image_path" :alt="$topping->name" :transparent="true"
                                                            class="w-full h-full object-contain" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-display font-bold text-slate-900 text-sm truncate">{{ $topping->name }}</p>
                                            <p class="text-xs text-sky-600 font-semibold mt-0.5">+Rp {{ number_format($topping->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ─── PER-ITEM NOTES ─── --}}
                <div class="mt-7">
                    <div class="flex items-center justify-between mb-3">
                        <label for="item-notes" class="text-sm font-semibold text-slate-900">Catatan untuk Barista</label>
                        <span class="text-xs text-slate-400" x-text="(notes.length || 0) + '/200'"></span>
                    </div>
                    <textarea
                        id="item-notes"
                        x-model="notes"
                        maxlength="200"
                        rows="2"
                        placeholder="Contoh: tolong jangan terlalu manis, minum di tempat..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 focus:bg-white transition resize-none"></textarea>
                </div>

                {{-- ─── QUANTITY + ADD ─── --}}
                <div class="mt-9 flex items-center gap-4">
                    {{-- qty --}}
                    <div class="inline-flex items-center bg-slate-50 rounded-full border border-slate-150 overflow-hidden">
                        <button type="button"
                                @click="decrement()"
                                :disabled="quantity <= 1"
                                class="w-11 h-11 grid place-items-center text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition"
                                aria-label="Kurangi jumlah">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <span class="w-10 text-center font-display font-bold text-slate-900" x-text="quantity"></span>
                        <button type="button"
                                @click="increment()"
                                class="w-11 h-11 grid place-items-center text-slate-600 hover:bg-sky-500 hover:text-white transition"
                                aria-label="Tambah jumlah">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>

                    {{-- main CTA --}}
                    @if(\App\Models\StoreSetting::current()->is_open)
                        <button type="button"
                                @click="addToCart()"
                                :disabled="adding"
                                class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-black text-white font-semibold px-7 py-3.5 rounded-full transition shadow-lg shadow-slate-900/20 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" x-show="!adding" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                            </svg>
                            <svg class="w-5 h-5 animate-spin" x-show="adding" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round"/>
                            </svg>
                            <span x-text="adding ? 'Menambahkan...' : 'Tambahkan · Rp ' + format(unitPrice() * quantity)"></span>
                        </button>
                    @else
                        <button type="button"
                                disabled
                                class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-100 border border-slate-200 text-slate-400 font-semibold px-7 py-3.5 rounded-full cursor-not-allowed">
                            <span>Kedai Sedang Tutup</span>
                        </button>
                    @endif
                </div>

                <p class="mt-4 text-xs text-slate-400 text-center">Stok tersedia · siap diantar dalam radius {{ (int) $store->max_radius_km }} km</p>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────
             RELATED PRODUCTS
        ────────────────────────────────────────────── --}}
        @if ($related->isNotEmpty())
            <div class="mt-20 pt-14 border-t border-sky-100/80">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.18em] uppercase text-sky-600">Sering Dipesan Bersama</p>
                        <h2 class="mt-2 font-display font-semibold text-2xl md:text-3xl text-ink tracking-tight">Mungkin Anda suka</h2>
                    </div>
                    <a href="{{ route('menu', ['category' => $product->category_id]) }}"
                       class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 hover:text-sky-600 transition">
                        Lihat semua →
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach ($related as $rel)
                        <a href="{{ route('menu.show', $rel) }}" class="group block">
                            <div class="relative aspect-square rounded-2xl bg-gradient-to-br from-sky-50 via-white to-sky-50/40 overflow-hidden border border-slate-150 group-hover:border-sky-200 group-hover:-translate-y-1 transition-all duration-300">
                                <div aria-hidden="true" class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-sky-100/70 blur-2xl"></div>
                                <div aria-hidden="true" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-2/3 h-3 bg-slate-900/15 rounded-full blur-md"></div>
                                <div class="absolute inset-0 flex items-center justify-center p-6">
                                    <x-smart-image :src="$rel->image_path" :alt="$rel->name" :transparent="true"
                                                    class="w-full h-full object-contain group-hover:-translate-y-2 group-hover:scale-105 transition-transform duration-500" />
                                </div>
                            </div>
                            <div class="px-2 mt-3">
                                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">{{ $rel->category->name }}</p>
                                <h3 class="font-display font-semibold text-ink text-sm leading-snug truncate mt-0.5">{{ $rel->name }}</h3>
                                <p class="font-display font-bold text-ink text-base mt-1.5">
                                    <span class="text-[10px] font-medium text-slate-400 align-top">Rp</span>
                                    {{ number_format($rel->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>

<style>
    .hover-tilt {
        animation: detailFloat 5s ease-in-out infinite;
    }
    @keyframes detailFloat {
        0%, 100% { transform: translateY(0) rotate(0); }
        50%      { transform: translateY(-10px) rotate(-1deg); }
    }
</style>

<script>
    function productDetail(config) {
        return {
            basePrice: config.basePrice,
            sizes:     config.sizes,
            toppings:  config.toppings,
            productId: config.productId,
            size:      'reguler',
            ice:       'normal',
            sugar:     'normal',
            toppingIds: [],
            quantity:  1,
            notes:     '',
            adding:    false,

            currentSize() {
                return this.sizes.find(s => s.key === this.size) || this.sizes[0];
            },
            modifiersTotal() {
                const sizeMod = this.currentSize().modifier || 0;
                const tops    = this.toppings
                    .filter(t => this.toppingIds.includes(t.id))
                    .reduce((sum, t) => sum + t.price, 0);
                return sizeMod + tops;
            },
            unitPrice() {
                return this.basePrice + this.modifiersTotal();
            },
            format(n) {
                return Number(n || 0).toLocaleString('id-ID');
            },
            increment() { this.quantity++; },
            decrement() { if (this.quantity > 1) this.quantity--; },

            async addToCart() {
                if (this.adding) return;
                this.adding = true;
                try {
                    const body = new URLSearchParams();
                    body.append('product_id', this.productId);
                    body.append('size', this.size);
                    body.append('ice_level', this.ice);
                    body.append('sugar_level', this.sugar);
                    body.append('quantity', this.quantity);
                    if (this.notes && this.notes.trim()) {
                        body.append('notes', this.notes.trim());
                    }
                    this.toppingIds.forEach(id => body.append('toppings[]', id));

                    const res = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: body.toString(),
                    });
                    const data = await res.json();
                    if (!data.ok) {
                        Alpine.store('cart').showToast(data.message || 'Gagal menambahkan');
                        return;
                    }
                    // Sync the global cart store
                    const store = Alpine.store('cart');
                    store.items = data.items || [];
                    store.count = data.count || 0;
                    store.total = data.total || 0;
                    store.pulse();
                    store.showToast(data.message || 'Ditambahkan');
                    store.show();
                } catch (e) {
                    Alpine.store('cart').showToast('Gagal terhubung. Coba lagi.');
                } finally {
                    this.adding = false;
                }
            },
        };
    }
</script>
@endsection
