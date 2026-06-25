{{-- ════════════════════════════════════════════════════
     CART DRAWER  ·  slide-in dari kanan, di-power oleh Alpine
════════════════════════════════════════════════════ --}}
<div x-data
     x-show="$store.cart.open"
     x-cloak
     @keydown.escape.window="$store.cart.close()"
     class="fixed inset-0 z-[60]"
     style="display:none;">

    {{-- Backdrop --}}
    <div x-show="$store.cart.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.cart.close()"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
         aria-hidden="true"></div>

    {{-- Panel --}}
    <aside x-show="$store.cart.open"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="absolute top-0 right-0 h-full w-full sm:w-[420px] bg-white shadow-2xl flex flex-col"
           role="dialog"
           aria-label="Keranjang belanja">

        {{-- Header --}}
        <header class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-emerald-50 grid place-items-center text-emerald-600">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                    </svg>
                </span>
                <div>
                    <h2 class="font-display font-bold text-ink leading-tight">Keranjang</h2>
                    <p class="text-xs text-slate-500" x-text="$store.cart.count + ' item'"></p>
                </div>
            </div>
            <button type="button"
                    @click="$store.cart.close()"
                    class="w-9 h-9 rounded-full hover:bg-slate-100 grid place-items-center text-slate-500 hover:text-slate-900 transition"
                    aria-label="Tutup">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </header>

        {{-- Empty state --}}
        <template x-if="$store.cart.count === 0">
            <div class="flex-1 flex flex-col items-center justify-center px-8 text-center">
                <div class="w-20 h-20 rounded-full bg-slate-50 grid place-items-center text-slate-300 mb-5">
                    <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                    </svg>
                </div>
                <h3 class="font-display font-semibold text-ink text-lg">Keranjang kosong</h3>
                <p class="mt-1.5 text-sm text-slate-500 max-w-xs">Pilih minuman favoritmu, semua bisa di-tambahkan dari sini.</p>
                <button type="button"
                        @click="$store.cart.close()"
                        class="mt-6 inline-flex items-center gap-2 bg-slate-900 hover:bg-black text-white text-sm font-semibold px-6 py-3 rounded-full transition">
                    Mulai Belanja
                </button>
            </div>
        </template>

        {{-- Items --}}
        <template x-if="$store.cart.count > 0">
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                <template x-for="item in $store.cart.items" :key="item.key || item.id">
                    <div class="flex gap-3 bg-slate-50 rounded-2xl p-3 group">
                        <div class="w-20 h-20 rounded-xl bg-white grid place-items-center p-2 flex-shrink-0">
                            <img :src="$store.cart.imageUrl(item.image)"
                                 :alt="item.name"
                                 class="w-full h-full object-contain"
                                 loading="lazy">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-display font-semibold text-ink text-sm leading-tight truncate" x-text="item.name"></h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate"
                               x-show="item.options_summary"
                               x-text="item.options_summary"></p>
                            <p class="text-[11px] text-amber-600 mt-0.5 truncate italic"
                               x-show="item.notes"
                               :title="item.notes"
                               x-text="'« ' + item.notes + ' »'"></p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span class="text-[10px] text-slate-400 align-top">Rp</span>
                                <span x-text="$store.cart.format(item.price)" class="font-semibold"></span>
                            </p>

                            <div class="mt-2 flex items-center justify-between">
                                {{-- Qty controls --}}
                                <div class="inline-flex items-center bg-white rounded-full border border-slate-150 overflow-hidden">
                                    <button type="button"
                                            @click="if(item.quantity === 1) { if(confirm('Hapus ' + item.name + ' dari keranjang?')) $store.cart.remove(item.key || item.id) } else { $store.cart.update(item.key || item.id, 'decrease') }"
                                            class="w-7 h-7 grid place-items-center text-slate-600 hover:bg-slate-100 transition"
                                            aria-label="Kurangi">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"/></svg>
                                    </button>
                                    <span class="w-7 text-center text-sm font-bold text-slate-900" x-text="item.quantity"></span>
                                    <button type="button"
                                            @click="$store.cart.update(item.key || item.id, 'increase')"
                                            class="w-7 h-7 grid place-items-center text-slate-600 hover:bg-emerald-650 hover:text-white transition"
                                            aria-label="Tambah">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                </div>

                                {{-- Subtotal + remove --}}
                                <div class="flex items-center gap-3">
                                    <p class="font-display font-bold text-slate-900 text-sm">
                                        <span class="text-[10px] text-slate-400 font-semibold">Rp</span>
                                        <span x-text="$store.cart.format(item.subtotal)"></span>
                                    </p>
                                    <button type="button"
                                            @click="if(confirm('Hapus ' + item.name + ' dari keranjang?')) $store.cart.remove(item.key || item.id)"
                                            class="text-slate-400 hover:text-rose-500 transition"
                                            aria-label="Hapus">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Footer --}}
        <footer x-show="$store.cart.count > 0" class="border-t border-slate-100 px-6 py-5 bg-white">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-slate-500">Total</span>
                <p class="font-display font-extrabold text-slate-900 text-2xl">
                    <span class="text-sm font-semibold text-slate-400 align-top">Rp</span>
                    <span x-text="$store.cart.format($store.cart.total)"></span>
                </p>
            </div>
            <a href="{{ route('checkout.index') }}"
               class="block text-center bg-slate-900 hover:bg-black text-white font-semibold text-sm px-6 py-4 rounded-full transition shadow-lg shadow-slate-900/20">
                Checkout Sekarang
            </a>
            <button type="button"
                    @click="$store.cart.close()"
                    class="block w-full text-center mt-2 text-xs text-slate-500 hover:text-slate-900 py-2 transition">
                Lanjut Belanja
            </button>
        </footer>
    </aside>
</div>

{{-- Toast for "added to cart" feedback --}}
<div x-data
     x-show="$store.cart.toast.visible"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     x-cloak
     class="fixed bottom-24 right-5 z-[55] bg-slate-900 text-white text-sm font-medium px-5 py-3 rounded-full shadow-xl flex items-center gap-2"
     style="display:none;">
    <span class="w-5 h-5 rounded-full bg-emerald-500 grid place-items-center">
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
    </span>
    <span x-text="$store.cart.toast.message"></span>
</div>
