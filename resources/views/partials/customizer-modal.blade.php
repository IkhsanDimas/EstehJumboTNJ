<!-- Product Customization Modal -->
<div x-data
     x-show="$store.customizer.open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
     x-cloak
     @keydown.escape.window="$store.customizer.close()">
    
    <!-- Modal Backdrop Click -->
    <div class="absolute inset-0 cursor-pointer" @click="$store.customizer.close()"></div>

    <!-- Modal Content Card -->
    <div x-show="$store.customizer.open"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4 md:translate-y-0"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4 md:translate-y-0"
     class="relative z-10 w-full max-w-2xl max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 text-slate-850 dark:text-slate-100 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-850">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">Sesuaikan Pesanan</h3>
            <button @click="$store.customizer.close()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-550 dark:text-slate-400 transition grid place-items-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6" x-show="!$store.customizer.loading">
            <template x-if="$store.customizer.product">
                <div class="grid md:grid-cols-12 gap-6">
                    <!-- Product Image & Basic Info -->
                    <div class="md:col-span-5 flex flex-col items-center text-center">
                        <div class="relative w-44 h-44 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-850 p-6 flex items-center justify-center overflow-hidden">
                            <img :src="$store.cart.imageUrl($store.customizer.product.image_path)" :alt="$store.customizer.product.name" class="w-full h-full object-contain drop-shadow-[0_12px_20px_rgba(0,0,0,0.12)]">
                        </div>
                        <h4 class="font-display font-bold text-slate-900 dark:text-white text-lg mt-3" x-text="$store.customizer.product.name"></h4>
                        <p class="text-xs text-emerald-650 dark:text-emerald-400 font-semibold mt-1" x-text="$store.customizer.product.category.name"></p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3 text-left w-full px-2" x-text="$store.customizer.product.description"></p>
                    </div>

                    <!-- Customization Options -->
                    <div class="md:col-span-7 space-y-5">
                        
                        <!-- Sizes -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">Ukuran Gelas</label>
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="sz in $store.customizer.sizes" :key="sz.key">
                                    <label class="relative flex items-center justify-between p-3 rounded-2xl border cursor-pointer select-none transition-all duration-300"
                                           :class="$store.customizer.size === sz.key ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-bold ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                                        <input type="radio" name="modal_size" :value="sz.key" x-model="$store.customizer.size" class="sr-only">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold" x-text="sz.label"></span>
                                            <span class="text-[10px] mt-0.5" :class="$store.customizer.size === sz.key ? 'text-emerald-750 dark:text-emerald-400/80' : 'text-slate-400 dark:text-slate-500'" x-text="sz.volume || '500 ml'"></span>
                                        </div>
                                        <span class="text-xs font-bold" :class="$store.customizer.size === sz.key ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'" x-text="sz.modifier > 0 ? '+Rp ' + $store.customizer.format(sz.modifier) : 'Standar'"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Ice Levels -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">Tingkat Es Batu</label>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="(label, key) in $store.customizer.iceLabels" :key="key">
                                    <label class="relative flex flex-col items-center justify-center p-2.5 rounded-xl border text-center cursor-pointer select-none transition text-xs font-medium"
                                           :class="$store.customizer.ice === key ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-bold ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                                        <input type="radio" name="modal_ice" :value="key" x-model="$store.customizer.ice" class="sr-only">
                                        <span x-text="label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Sugar Levels -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">Tingkat Kemanisan</label>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="(label, key) in $store.customizer.sugarLabels" :key="key">
                                    <label class="relative flex flex-col items-center justify-center p-2.5 rounded-xl border text-center cursor-pointer select-none transition text-xs font-medium"
                                           :class="$store.customizer.sugar === key ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-bold ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                                        <input type="radio" name="modal_sugar" :value="key" x-model="$store.customizer.sugar" class="sr-only">
                                        <span x-text="label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Toppings -->
                        <div class="space-y-2" x-show="$store.customizer.toppings.length > 0">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">Tambahan Topping</label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="top in $store.customizer.toppings" :key="top.id">
                                    <label class="relative flex items-center justify-between p-2.5 rounded-xl border cursor-pointer select-none transition text-xs"
                                           :class="$store.customizer.toppingIds.includes(top.id) ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-bold ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :value="top.id" x-model="$store.customizer.toppingIds" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 w-3.5 h-3.5">
                                            <span x-text="top.name"></span>
                                        </div>
                                        <span class="text-[10px] font-semibold" :class="$store.customizer.toppingIds.includes(top.id) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'" x-text="'+Rp ' + $store.customizer.format(top.price)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">Catatan Khusus (Opsional)</label>
                            <textarea x-model="$store.customizer.notes" placeholder="Contoh: Gula cair dipisah, es batu sedikit saja..." maxlength="200" rows="2" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Skeleton Loading Loader -->
        <div class="flex-1 flex flex-col items-center justify-center p-12 space-y-4" x-show="$store.customizer.loading">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round"/>
            </svg>
            <p class="text-xs text-slate-400 dark:text-slate-550">Memuat data pilihan rasa...</p>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between gap-4">
            <!-- Price Display -->
            <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-semibold tracking-wider">Total Harga</span>
                <p class="font-display text-slate-900 dark:text-white leading-none mt-1">
                    <span class="text-[11px] font-semibold text-emerald-650 dark:text-emerald-400 align-top">Rp</span>
                    <span class="text-xl font-bold tracking-tight" x-text="$store.customizer.format($store.customizer.totalPrice() * $store.customizer.quantity)"></span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Quantity controls -->
                <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <button type="button" @click="$store.customizer.decrement()" :disabled="$store.customizer.quantity <= 1" class="w-9 h-9 grid place-items-center text-slate-650 dark:text-slate-350 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-40 transition">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                    </button>
                    <span class="w-8 text-center font-display font-bold text-slate-800 dark:text-white text-sm" x-text="$store.customizer.quantity"></span>
                    <button type="button" @click="$store.customizer.increment()" class="w-9 h-9 grid place-items-center text-slate-650 dark:text-slate-350 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                </div>

                <!-- Add button -->
                <button type="button"
                         @click="$store.customizer.addToCart()"
                         :disabled="$store.customizer.adding"
                         class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-full text-sm transition-all duration-300 shadow-md shadow-amber-500/20 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60">
                    <svg class="w-4 h-4 animate-spin" x-show="$store.customizer.adding" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                        <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round"/>
                    </svg>
                    <span x-text="$store.customizer.adding ? 'Menambahkan...' : 'Tambah ke Keranjang'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
