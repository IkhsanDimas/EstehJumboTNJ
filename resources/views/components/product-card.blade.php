@props([
    'product',
    'category' => null,
])

@php
    $catName = $category ?? ($product->category->name ?? 'Menu');
    $priceFormatted = number_format($product->price, 0, ',', '.');
    $detailUrl = route('menu.show', $product);
    $discountPercent = (($product->id % 3) + 2) * 5; // e.g. 10%, 15%, 20%
    $originalPrice = round($product->price / (1 - ($discountPercent / 100)));
    $originalPriceFormatted = number_format($originalPrice, 0, ',', '.');
@endphp

<article class="group relative h-full" x-data="{ qty: 1, wishlisted: false }">
    <div class="relative h-full flex flex-col bg-white rounded-3xl border border-slate-100 hover:border-sky-200 transition-all duration-300 overflow-hidden hover:-translate-y-1.5 shadow-[0_2px_10px_-4px_rgba(15,23,42,0.08)] hover:shadow-[0_24px_45px_-18px_rgba(14,165,233,0.25)]">

        {{-- ─── BADGES (Promo & Wishlist) ─── --}}
        <div class="absolute top-3.5 left-3.5 z-20">
            <span class="bg-orange-500 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg shadow-sm">
                -{{ $discountPercent }}%
            </span>
        </div>

        <button type="button"
                @click="wishlisted = !wishlisted"
                aria-label="Simpan ke wishlist"
                class="absolute top-3 right-3 z-20 w-8.5 h-8.5 rounded-full bg-white/90 backdrop-blur grid place-items-center shadow-sm transition-all duration-300 focus:outline-none">
            <svg class="w-4.5 h-4.5 transition-colors duration-300"
                 :class="wishlisted ? 'fill-rose-500 text-rose-500' : 'text-slate-400 hover:text-rose-500'"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>

        {{-- ─── IMAGE STAGE (clickable, square) ─── --}}
        <a href="{{ $detailUrl }}" class="img-stage block m-3.5 rounded-2xl bg-slate-50/60 border border-slate-100/50 overflow-hidden relative">
            <span aria-hidden="true"
                  class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 rounded-full bg-sky-100/20 blur-2xl group-hover:bg-sky-200/30 transition-all duration-500"></span>

            <span class="relative z-10 block w-full h-full p-[8%]">
                <x-smart-image
                    :src="$product->image_path"
                    :alt="$product->name"
                    :transparent="true"
                    class="block w-full h-full"
                    imgClass="w-full h-full object-contain transition-all duration-500 group-hover:scale-105 drop-shadow-[0_12px_20px_rgba(0,0,0,0.12)] group-hover:drop-shadow-[0_18px_25px_rgba(0,0,0,0.18)]" />
            </span>
        </a>

        {{-- ─── INFO & ACTIONS ─── --}}
        <div class="flex flex-col flex-1 px-4.5 pb-4.5 pt-1">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $catName }}</p>
            <a href="{{ $detailUrl }}" class="block mt-1">
                <h3 class="font-display font-bold text-slate-800 text-[15px] leading-snug truncate group-hover:text-sky-600 transition-colors">
                    {{ $product->name }}
                </h3>
            </a>

            {{-- Price with original price mock --}}
            <div class="mt-2.5 flex items-baseline gap-2">
                <p class="font-display text-slate-900 leading-none">
                    <span class="text-[10px] font-bold text-slate-400 align-middle mr-0.5">Rp</span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-950">{{ $priceFormatted }}</span>
                </p>
                <p class="text-xs text-slate-400 line-through">Rp {{ $originalPriceFormatted }}</p>
            </div>

            {{-- Bottom row actions --}}
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                @if(\App\Models\StoreSetting::current()->is_open)
                    {{-- Friendly Qty Selector --}}
                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-full overflow-hidden">
                        <button type="button"
                                @click="if (qty > 1) qty--"
                                class="w-8.5 h-8.5 grid place-items-center text-slate-500 hover:bg-slate-200/60 active:bg-slate-200 transition focus:outline-none"
                                aria-label="Kurangi jumlah">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                            </svg>
                        </button>
                        <span class="w-6.5 text-center font-display font-extrabold text-xs text-slate-850" x-text="qty"></span>
                        <button type="button"
                                @click="qty++"
                                class="w-8.5 h-8.5 grid place-items-center text-slate-500 hover:bg-slate-200/60 active:bg-slate-200 transition focus:outline-none"
                                aria-label="Tambah jumlah">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Add to Cart Button (Green circular button) --}}
                    <button type="button"
                            @click="Alpine.store('customizer').show({{ $product->id }}, $el, qty)"
                            aria-label="Tambah {{ $product->name }} ke keranjang"
                            title="Pilih topping dan ukuran"
                            class="w-9 h-9 rounded-full bg-[#25D366] hover:bg-[#1eb854] active:bg-[#199d46] text-white grid place-items-center transition-all duration-300 shadow-sm shadow-emerald-500/10 hover:shadow-md hover:shadow-emerald-500/20 focus:outline-none">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                        </svg>
                    </button>
                @else
                    <span class="w-full text-center py-2 rounded-full text-[10px] font-extrabold bg-slate-100 border border-slate-200 text-slate-400 uppercase tracking-wider">
                        Tutup
                    </span>
                @endif
            </div>
        </div>
    </div>
</article>
