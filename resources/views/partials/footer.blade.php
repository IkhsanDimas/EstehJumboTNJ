<footer id="contact" class="relative text-white overflow-hidden border-t border-white/10"
        style="background: linear-gradient(135deg, #10b981 0%, #059669 55%, #064e3b 100%);">
    
    {{-- Ambient glows matching the Hero section --}}
    <div aria-hidden="true" class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-emerald-300/30 blur-3xl"></div>
    <div aria-hidden="true" class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-green-450/30 blur-3xl"></div>
    <div aria-hidden="true" class="absolute inset-0 opacity-[0.03]"
         style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>

    <div class="relative max-w-7xl mx-auto px-6 pt-16 pb-10">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
            {{-- Brand column --}}
            <div class="md:col-span-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-white grid place-items-center text-emerald-600 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 8h14l-1.5 11a2 2 0 0 1-2 1.8h-7a2 2 0 0 1-2-1.8L5 8z"/>
                            <path d="M9 8V5a3 3 0 0 1 6 0v3"/>
                        </svg>
                    </span>
                    <span class="font-display font-bold text-lg tracking-tight text-white">Es Teh Jumbo</span>
                </a>
                <p class="mt-4 text-sm leading-relaxed text-emerald-100/90 max-w-sm">
                    Minuman jumbo segar diracik harian. Antar cepat di Bekasi dan sekitarnya.
                </p>
                <div class="mt-6 flex items-center gap-2.5">
                    @foreach ([
                        ['label' => 'Instagram', 'href' => 'https://www.instagram.com/estehjumbotnj?igsh=dndnd21tZzEydzFq', 'd' => 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm-5 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM17.5 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z'],
                        ['label' => 'TikTok',    'href' => '#', 'd' => 'M16 3v3.5a4.5 4.5 0 0 0 4.5 4.5V14a7.5 7.5 0 0 1-4.5-1.5V17a5 5 0 1 1-5-5v3a2 2 0 1 0 2 2V3h3z'],
                        ['label' => 'WhatsApp',  'href' => 'https://wa.me/' . config('services.whatsapp.number'), 'd' => 'M12 2a10 10 0 0 0-8.7 14.94L2 22l5.2-1.36A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.08-1.12l-.29-.17-3.08.81.82-3-.19-.31A8 8 0 1 1 12 20zm4.59-5.91c-.25-.13-1.47-.73-1.7-.81-.23-.08-.39-.13-.56.13s-.64.81-.79.97-.29.2-.54.07a6.55 6.55 0 0 1-3.27-2.86c-.25-.43.25-.4.7-1.34a.45.45 0 0 0 0-.42c-.06-.13-.56-1.36-.77-1.86s-.41-.43-.56-.43h-.48a.92.92 0 0 0-.67.31 2.81 2.81 0 0 0-.88 2.09 4.88 4.88 0 0 0 1 2.62 11.18 11.18 0 0 0 4.27 3.78c2.55 1 2.55.66 3 .62a2.55 2.55 0 0 0 1.7-1.2 2.1 2.1 0 0 0 .15-1.2c-.06-.11-.23-.17-.48-.29z'],
                    ] as $s)
                        <a href="{{ $s['href'] }}" target="_blank" rel="noopener" aria-label="{{ $s['label'] }}"
                           class="w-9 h-9 rounded-full bg-white/10 hover:bg-white text-white hover:text-emerald-700 grid place-items-center transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $s['d'] }}"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Navigation column --}}
            <div class="md:col-span-3">
                <h4 class="text-sm font-display font-bold text-white mb-4 uppercase tracking-wider text-[11px]">Jelajahi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-emerald-100/80 hover:text-white transition">Beranda</a></li>
                    <li><a href="{{ route('menu') }}" class="text-emerald-100/80 hover:text-white transition">Menu Lengkap</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-emerald-100/80 hover:text-white transition">Keranjang</a></li>
                    <li><a href="{{ route('home') }}#tentang" class="text-emerald-100/80 hover:text-white transition">Tentang Kami</a></li>
                </ul>
            </div>

            {{-- Contact column --}}
            <div class="md:col-span-4">
                <h4 class="text-sm font-display font-bold text-white mb-4 uppercase tracking-wider text-[11px]">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm text-emerald-100/80">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-white flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="leading-relaxed">Perumahan Permata Galaxy, Bekasi</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-white flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.81.36 1.6.68 2.34a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.74.32 1.53.55 2.34.68a2 2 0 0 1 1.72 2.03z"/></svg>
                        <a href="https://wa.me/{{ config('services.whatsapp.number') }}" class="hover:text-white transition">{{ config('services.whatsapp.formatted') }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-white flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                        <a href="mailto:halo@estehjumbo.test" class="hover:text-white transition">halo@estehjumbo.test</a>
                    </li>
                    <li class="flex items-center gap-3">
                        @php
                            $hour = now()->timezone('Asia/Jakarta')->hour;
                            $isOpen = $hour >= 9 && $hour < 22;
                        @endphp
                        <svg class="w-4 h-4 text-white flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <span>Buka 09.00 – 22.00 WIB <span class="ml-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider {{ $isOpen ? 'bg-white text-emerald-600' : 'bg-rose-500/20 text-rose-200' }}">{{ $isOpen ? 'BUKA' : 'TUTUP' }}</span></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-emerald-100/60">
            <p>&copy; {{ date('Y') }} Es Teh Jumbo. Semua hak dilindungi.</p>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
