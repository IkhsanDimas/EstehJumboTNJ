@props([
    'product',
    'category' => null,
])

@php
    $catName = $category ?? ($product->category->name ?? 'Menu');
    $priceFormatted = number_format($product->price, 0, ',', '.');
    $detailUrl = route('menu.show', $product);
@endphp

<article class="group relative h-full bg-white rounded-xl border border-slate-200/80 hover:border-slate-350/80 transition-all duration-300 flex flex-col overflow-hidden hover:shadow-[0_8px_35px_rgba(0,0,0,0.05)] hover:-translate-y-1"
         x-data="{ qty: 1, wishlisted: false }">
    

    {{-- ─── Wishlist Button (Top-Right) ─── --}}
    <button type="button"
            @click="wishlisted = !wishlisted"
            aria-label="Simpan ke wishlist"
            class="absolute top-2.5 right-2.5 z-20 w-7.5 h-7.5 rounded-full bg-white/90 backdrop-blur grid place-items-center shadow-xs transition-all duration-300 focus:outline-none">
        <svg class="w-3.5 h-3.5 transition-colors duration-300"
             :class="wishlisted ? 'fill-rose-500 text-rose-500' : 'text-slate-350 hover:text-rose-500'"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
    </button>

    {{-- ─── Product Image ─── --}}
    <a href="{{ $detailUrl }}" class="block aspect-square relative p-3 flex items-center justify-center bg-white">
        <x-smart-image
            :src="$product->image_path"
            :alt="$product->name"
            :transparent="true"
            class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105"
            imgClass="w-full h-full object-contain" />
    </a>

    {{-- ─── Product Info ─── --}}
    <div class="flex flex-col flex-1 p-3 pt-2 bg-white">
        {{-- Category --}}
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">{{ $catName }}</p>
        
        {{-- Name --}}
        <a href="{{ $detailUrl }}" class="block mt-0.5 flex-1">
            <h3 class="font-display font-bold text-slate-800 text-sm leading-snug line-clamp-2 group-hover:text-sky-600 transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Price --}}
        <div class="mt-1.5 flex items-baseline">
            <p class="font-display text-orange-500 leading-none">
                <span class="text-[10px] font-bold align-middle mr-0.5">Rp</span>
                <span class="text-base font-extrabold tracking-tight">{{ $priceFormatted }}</span>
            </p>
        </div>

        {{-- Bottom Actions (Friendly Row) --}}
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2.5 bg-white">
            @if(\App\Models\StoreSetting::current()->is_open)
                {{-- Friendly Qty Selector --}}
                <div class="inline-flex items-center bg-white border border-slate-200 rounded-lg shadow-sm">
                    <button type="button"
                            @click="if (qty > 1) qty--"
                            class="w-7 h-7 grid place-items-center text-slate-400 hover:text-slate-650 active:bg-slate-50 transition focus:outline-none"
                            aria-label="Kurangi jumlah">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                        </svg>
                    </button>
                    <span class="w-6 text-center font-display font-extrabold text-xs text-slate-700" x-text="qty"></span>
                    <button type="button"
                            @click="qty++"
                            class="w-7 h-7 grid place-items-center text-slate-400 hover:text-slate-650 active:bg-slate-50 transition focus:outline-none"
                            aria-label="Tambah jumlah">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </button>
                </div>

                {{-- Add to Cart Button (Green circular button) --}}
                <button type="button"
                        @click="Alpine.store('customizer').show({{ $product->id }}, $el, qty)"
                        aria-label="Tambah {{ $product->name }} ke keranjang"
                        title="Pilih topping dan ukuran"
                        class="w-8 h-8 rounded-full bg-[#25D366] hover:bg-[#20b857] active:bg-[#1b9e4a] text-white flex items-center justify-center transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none hover:scale-105 active:scale-95">
                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                    </svg>
                </button>
            @else
                <span class="w-full text-center py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 border border-slate-200 text-slate-400 uppercase tracking-wider">
                    Tutup
                </span>
            @endif
        </div>
    </div>
</article>
