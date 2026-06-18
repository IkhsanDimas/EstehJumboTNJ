@props([
    'product',
    'category' => null,
])

@php
    $catName = $category ?? ($product->category->name ?? 'Menu');
    $priceFormatted = number_format($product->price, 0, ',', '.');
    $detailUrl = route('menu.show', $product);
@endphp

<article class="group relative h-full">
    <div class="relative h-full flex flex-col bg-white rounded-3xl ring-1 ring-slate-100 hover:ring-sky-200 transition-all duration-300 overflow-hidden hover:-translate-y-1.5 shadow-[0_2px_10px_-4px_rgba(15,23,42,0.08)] hover:shadow-[0_24px_45px_-18px_rgba(14,165,233,0.35)]">

        {{-- ─── IMAGE STAGE (clickable, square, 3D effect) ─── --}}
        <a href="{{ $detailUrl }}" class="img-stage block m-3 rounded-2xl bg-gradient-to-br from-slate-50 via-white to-sky-50/20 border border-slate-100/50 overflow-hidden relative" style="perspective: 800px;">
            {{-- soft halo for depth --}}
            <span aria-hidden="true"
                  class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 rounded-full bg-sky-100/60 blur-2xl group-hover:bg-sky-200/70 transition-all duration-500"></span>

            {{-- 3D reflection glow --}}
            <span aria-hidden="true"
                  class="absolute top-[15%] left-1/2 -translate-x-1/2 w-1/2 h-[40%] bg-gradient-to-b from-white/40 to-transparent rounded-full blur-xl opacity-60"></span>

            {{-- enhanced ground shadow for 3D --}}
            <span aria-hidden="true"
                  class="absolute bottom-[10%] left-1/2 -translate-x-1/2 w-[55%] h-3 bg-slate-900/20 rounded-[100%] blur-lg group-hover:w-[60%] group-hover:bg-slate-900/25 transition-all duration-500"></span>

            {{-- Wishlist --}}
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation();"
                    aria-label="Simpan ke wishlist"
                    class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/90 backdrop-blur grid place-items-center text-slate-400 hover:text-rose-500 transition-all duration-300 opacity-0 group-hover:opacity-100 shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>

            {{-- product image: larger + 3D drop-shadow --}}
            <span class="relative z-10 block w-full h-full p-[8%]">
                <x-smart-image
                    :src="$product->image_path"
                    :alt="$product->name"
                    :transparent="true"
                    class="block w-full h-full"
                    imgClass="w-full h-full object-contain transition-all duration-500 group-hover:-translate-y-3 group-hover:scale-108 drop-shadow-[0_18px_25px_rgba(0,0,0,0.18)] group-hover:drop-shadow-[0_24px_35px_rgba(0,0,0,0.22)]" />
            </span>
        </a>

        {{-- ─── INFO ─── --}}
        <div class="flex flex-col flex-1 px-4 pb-4 pt-1">
            <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">{{ $catName }}</p>
            <a href="{{ $detailUrl }}" class="block mt-1">
                <h3 class="font-display font-semibold text-slate-800 text-[15px] leading-snug truncate group-hover:text-sky-600 transition-colors">
                    {{ $product->name }}
                </h3>
            </a>

            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <p class="font-display text-slate-900 leading-none">
                    <span class="text-[10px] font-semibold text-slate-400 align-middle mr-0.5">Rp</span>
                    <span class="text-lg font-bold tracking-tight text-slate-900">{{ $priceFormatted }}</span>
                </p>

                @if(\App\Models\StoreSetting::current()->is_open)
                    <form action="{{ route('cart.add') }}" method="POST" class="flex"
                          x-data
                          @submit.prevent="Alpine.store('customizer').show({{ $product->id }}, $el)">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit"
                                aria-label="Tambah {{ $product->name }} ke keranjang"
                                title="Pilih topping dan ukuran"
                                class="group/btn relative inline-flex items-center gap-1.5 bg-slate-900 hover:bg-sky-600 text-white pl-2.5 pr-2.5 py-2 rounded-full text-xs font-semibold transition-all duration-300 shadow-sm hover:shadow-md hover:shadow-sky-600/30">
                            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-300 group-hover/btn:rotate-90"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span class="max-w-0 overflow-hidden whitespace-nowrap opacity-0 transition-all duration-300 group-hover/btn:max-w-[80px] group-hover/btn:opacity-100 group-hover/btn:pr-1">
                                Tambah
                            </span>
                        </button>
                    </form>
                @else
                    <span class="px-3 py-1.5 rounded-full text-[10px] font-bold bg-slate-100 border border-slate-200 text-slate-400 uppercase tracking-wider">
                        Tutup
                    </span>
                @endif
            </div>
        </div>
    </div>
</article>
