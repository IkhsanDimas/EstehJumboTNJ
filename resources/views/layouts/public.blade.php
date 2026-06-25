<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $store->store_name)</title>

    {{-- Fonts --}}
    @php
        $currentStore = $store ?? \App\Models\StoreSetting::current();
        $selectedFont = $currentStore->font_family ?? 'Plus Jakarta Sans';
        $isSerif = in_array($selectedFont, ['Playfair Display']);
        $bodyFont = $isSerif ? 'Plus Jakarta Sans' : $selectedFont;
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['"{{ $bodyFont }}"', '"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                        rounded: ['"{{ $selectedFont }}"', 'system-ui', 'sans-serif'],
                        display: ['"{{ $selectedFont }}"', 'system-ui', 'sans-serif'],
                    },
                    spacing: {
                        '18': '4.5rem',
                    },
                    colors: {
                        sky: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                    },
                },
            },
        }
    </script>

    {{-- Alpine global cart store (must register BEFORE Alpine init) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                open: false,
                items: [],
                count: 0,
                total: 0,
                loading: false,
                toast: { visible: false, message: '', timer: null },

                init() {
                    this.refresh();
                },

                async refresh() {
                    try {
                        const res = await fetch('{{ route('cart.data') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        this.items = data.items || [];
                        this.count = data.count || 0;
                        this.total = data.total || 0;
                    } catch (e) { /* silent */ }
                },

                show()   { this.open = true;  document.body.classList.add('overflow-hidden'); },
                close()  { this.open = false; document.body.classList.remove('overflow-hidden'); },
                toggle() { this.open ? this.close() : this.show(); },

                async add(productId) {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'product_id=' + encodeURIComponent(productId),
                        });
                        const data = await res.json();
                        if (!data.ok) {
                            this.showToast(data.message || 'Gagal menambahkan');
                            return;
                        }
                        this.items = data.items || [];
                        this.count = data.count || 0;
                        this.total = data.total || 0;
                        this.pulse();
                        this.showToast(data.message || 'Ditambahkan');
                        this.show();
                    } catch (e) {
                        this.showToast('Gagal terhubung. Coba lagi.');
                    } finally {
                        this.loading = false;
                    }
                },

                async update(productId, action) {
                    try {
                        const body = new URLSearchParams();
                        // Accept either composite key (string) or numeric product_id
                        if (typeof productId === 'string' && productId.startsWith('p')) {
                            body.append('key', productId);
                        } else {
                            body.append('product_id', productId);
                        }
                        body.append('action', action);
                        const res = await fetch('{{ route('cart.update') }}', {
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
                        if (!data.ok) { this.showToast(data.message || 'Gagal'); return; }
                        this.items = data.items || [];
                        this.count = data.count || 0;
                        this.total = data.total || 0;
                    } catch (e) { /* silent */ }
                },

                async remove(productId) {
                    try {
                        const body = new URLSearchParams();
                        if (typeof productId === 'string' && productId.startsWith('p')) {
                            body.append('key', productId);
                        } else {
                            body.append('product_id', productId);
                        }
                        const res = await fetch('{{ route('cart.remove') }}', {
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
                        if (data.ok) {
                            this.items = data.items || [];
                            this.count = data.count || 0;
                            this.total = data.total || 0;
                        }
                    } catch (e) { /* silent */ }
                },

                pulse() {
                    const el = document.getElementById('nav-cart-icon');
                    if (!el) return;
                    el.classList.remove('cart-pulse');
                    void el.offsetWidth;
                    el.classList.add('cart-pulse');
                },

                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.visible = true;
                    if (this.toast.timer) clearTimeout(this.toast.timer);
                    this.toast.timer = setTimeout(() => { this.toast.visible = false; }, 2200);
                },

                format(n) {
                    return Number(n || 0).toLocaleString('id-ID');
                },

                imageUrl(path) {
                    if (!path) return '';
                    // Try .png by default; smart-image fallback already handles server side
                    return '/' + String(path).replace(/^\/+/, '') + '.png';
                },
            });

            Alpine.store('customizer', {
                open: false,
                product: null,
                sizes: [],
                toppings: [],
                loading: false,
                size: 'reguler',
                ice: 'normal',
                sugar: 'normal',
                toppingIds: [],
                quantity: 1,
                notes: '',
                adding: false,
                sourceEl: null,

                iceLabels: {
                    less: 'Es sedikit',
                    normal: 'Es normal',
                    extra: 'Es banyak',
                    none: 'Tanpa es'
                },
                sugarLabels: {
                    less: 'Less sugar',
                    normal: 'Manis normal',
                    extra: 'Extra manis',
                    none: 'Tanpa gula'
                },

                async show(productId, triggerEl, initialQty = 1) {
                    this.sourceEl = triggerEl;
                    this.open = true;
                    this.loading = true;
                    this.product = null;
                    this.sizes = [];
                    this.toppings = [];
                    this.size = 'reguler';
                    this.ice = 'normal';
                    this.sugar = 'normal';
                    this.toppingIds = [];
                    this.quantity = initialQty;
                    this.notes = '';
                    document.body.classList.add('overflow-hidden');

                    try {
                        const res = await fetch('/menu/' + productId + '/data');
                        if (!res.ok) throw new Error();
                        const data = await res.json();
                        this.product = data.product;
                        this.toppings = data.toppings || [];
                        this.sizes = data.sizes || [];
                    } catch (e) {
                        this.close();
                        Alpine.store('cart').showToast('Gagal memuat detail produk');
                    } finally {
                        this.loading = false;
                    }
                },

                close() {
                    this.open = false;
                    document.body.classList.remove('overflow-hidden');
                },

                increment() { this.quantity++; },
                decrement() { if (this.quantity > 1) this.quantity--; },

                totalPrice() {
                    if (!this.product) return 0;
                    const sizeMod = (this.sizes.find(s => s.key === this.size) || { modifier: 0 }).modifier || 0;
                    const toppingsTotal = this.toppings
                        .filter(t => this.toppingIds.includes(t.id))
                        .reduce((sum, t) => sum + t.price, 0);
                    return Number(this.product.price) + sizeMod + toppingsTotal;
                },

                format(n) {
                    return Number(n || 0).toLocaleString('id-ID');
                },

                async addToCart() {
                    if (this.adding) return;
                    this.adding = true;
                    try {
                        const body = new URLSearchParams();
                        body.append('product_id', this.product.id);
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
                        // Sync global cart store
                        const store = Alpine.store('cart');
                        store.items = data.items || [];
                        store.count = data.count || 0;
                        store.total = data.total || 0;
                        
                        // Fly to cart animation
                        if (window.animateFlyToCart) {
                            window.animateFlyToCart(this.sourceEl);
                        } else {
                            store.pulse();
                        }
                        
                        this.close();
                        store.showToast(data.message || 'Ditambahkan');
                    } catch (e) {
                        Alpine.store('cart').showToast('Gagal terhubung. Coba lagi.');
                    } finally {
                        this.adding = false;
                    }
                }
            });
        });

        // Fly-to-cart animation function
        window.animateFlyToCart = function(sourceEl) {
            const targetEl = document.getElementById('nav-cart-icon');
            if (!sourceEl || !targetEl) return;
            
            // Find appropriate img inside container
            let imgEl = sourceEl.closest('article')?.querySelector('img') || 
                        sourceEl.closest('.img-stage')?.querySelector('img') ||
                        sourceEl.closest('.relative')?.querySelector('img') || 
                        sourceEl;
            
            const sourceRect = imgEl.getBoundingClientRect();
            const targetRect = targetEl.getBoundingClientRect();
            
            const clone = document.createElement('div');
            clone.style.position = 'fixed';
            clone.style.top = sourceRect.top + 'px';
            clone.style.left = sourceRect.left + 'px';
            clone.style.width = sourceRect.width + 'px';
            clone.style.height = sourceRect.height + 'px';
            clone.style.zIndex = '9999';
            clone.style.pointerEvents = 'none';
            clone.style.transition = 'all 0.8s cubic-bezier(0.25, 1, 0.5, 1)';
            
            if (imgEl.tagName === 'IMG') {
                const cloneImg = document.createElement('img');
                cloneImg.src = imgEl.src;
                cloneImg.style.width = '100%';
                cloneImg.style.height = '100%';
                cloneImg.style.objectFit = 'contain';
                cloneImg.style.borderRadius = '50%';
                cloneImg.style.filter = 'drop-shadow(0 8px 16px rgba(14, 165, 233, 0.45))';
                clone.appendChild(cloneImg);
            } else {
                clone.style.background = '#0ea5e9';
                clone.style.borderRadius = '50%';
                clone.style.boxShadow = '0 0 12px #0ea5e9';
            }
            
            document.body.appendChild(clone);
            void clone.offsetWidth;
            
            clone.style.top = (targetRect.top + targetRect.height / 2 - 15) + 'px';
            clone.style.left = (targetRect.left + targetRect.width / 2 - 15) + 'px';
            clone.style.width = '30px';
            clone.style.height = '30px';
            clone.style.opacity = '0.1';
            clone.style.transform = 'rotate(360deg)';
            
            setTimeout(() => {
                clone.remove();
                const cartStore = Alpine.store('cart');
                if (cartStore && typeof cartStore.pulse === 'function') {
                    cartStore.pulse();
                }
            }, 800);
        };
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: '{{ $bodyFont }}', 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; color: #475569; }
        ::selection { background: #bbf7d0; color: #042c16; }
        [x-cloak] { display: none !important; }

        /* ──────── Typography system ──────── */
        /* Headings: rounded display face, medium weight, soft deep-slate ink */
        .font-display { font-family: '{{ $selectedFont }}', system-ui, sans-serif; letter-spacing: -0.02em; font-weight: 800; }
        .text-ink   { color: #0f172a; }        /* heading ink — softer than pure black */
        .text-ink-2 { color: #334155; }
        .eyebrow {
            font-family: '{{ $selectedFont }}', 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #10b981;
            display: inline-flex;
            align-items: center;
        }
        .eyebrow::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 9999px;
            margin-right: 8px;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Util used in templates */
        .border-slate-150 { border-color: #e9edf2; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* ──────── Page backgrounds ──────── */
        /* Ambient tint that stays visible across the whole viewport (fixed wash) */
        .bg-app {
            background-color: #f8fafc;
            background-image:
                radial-gradient(1100px 650px at 100% -8%, rgba(14, 165, 233, 0.08), transparent 50%),
                radial-gradient(950px 650px at -12% 112%, rgba(14, 165, 233, 0.05), transparent 50%);
            background-attachment: fixed, fixed;
        }
        .bg-page-soft {
            background-color: #f8fafc;
            background-image:
                radial-gradient(950px 600px at 96% 0%, rgba(14, 165, 233, 0.08), transparent 50%),
                radial-gradient(850px 600px at 0% 100%, rgba(14, 165, 233, 0.05), transparent 50%);
            background-attachment: fixed, fixed;
        }
        .bg-page-cream {
            background-color: #fdfaf4;
            background-image:
                radial-gradient(800px 500px at 92% 6%, rgba(253, 230, 138, 0.30), transparent 58%),
                radial-gradient(800px 500px at 4% 96%, rgba(186, 230, 253, 0.26), transparent 58%);
            background-attachment: fixed, fixed;
        }
        /* Decorative blobs */
        .deco-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(70px);
            pointer-events: none;
            opacity: 0.5;
        }
        .deco-blob-sky    { background: #bae6fd; }
        .deco-blob-rose   { background: #fecdd3; }
        .deco-blob-amber  { background: #fde68a; }
        .deco-blob-emerald{ background: #bbf7d0; }

        /* Cart icon pulse on add */
        @keyframes cartPulse {
            0%   { transform: scale(1); }
            30%  { transform: scale(1.25) rotate(-8deg); }
            55%  { transform: scale(0.95) rotate(6deg); }
            100% { transform: scale(1) rotate(0); }
        }
        .cart-pulse { animation: cartPulse .6s cubic-bezier(.36,1.5,.4,1) both; }

        @keyframes badgeBump {
            0%   { transform: scale(0.6); opacity: 0; }
            60%  { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .badge-bump { animation: badgeBump .35s ease-out both; }

        /* ──────── "Alive" motion + gradient kit ──────── */
        @keyframes floaty {
            0%, 100% { transform: translateY(0) rotate(0); }
            50%      { transform: translateY(-16px) rotate(2deg); }
        }
        @keyframes floaty-2 {
            0%, 100% { transform: translateY(0) rotate(0); }
            50%      { transform: translateY(-12px) rotate(-3deg); }
        }
        @keyframes bobbing {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-10px); }
        }
        @keyframes bubbleRise {
            0%   { transform: translateY(0) scale(1);   opacity: 0; }
            15%  { opacity: .7; }
            100% { transform: translateY(-120px) scale(.5); opacity: 0; }
        }
        @keyframes sparkle {
            0%, 100% { transform: scale(.6) rotate(0);   opacity: .3; }
            50%      { transform: scale(1.1) rotate(90deg); opacity: 1; }
        }
        .anim-floaty   { animation: floaty 6s ease-in-out infinite; }
        .anim-floaty-2 { animation: floaty-2 7s ease-in-out infinite; }
        .anim-bob      { animation: bobbing 4.5s ease-in-out infinite; }
        .anim-sparkle  { animation: sparkle 3.5s ease-in-out infinite; }

        /* Gradient pill button with arrow chip (like the reference) */
        .btn-chip {
            display: inline-flex; align-items: center; gap: .75rem;
            padding: .35rem .35rem .35rem 1.5rem;
            border-radius: 9999px;
            font-weight: 700; font-size: .875rem; color: #fff;
            background: linear-gradient(100deg, #f59e0b 0%, #f97316 55%, #fb923c 100%);
            box-shadow: 0 12px 30px -8px rgba(249, 115, 22, .55);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .btn-chip:hover { transform: translateY(-2px); box-shadow: 0 18px 36px -8px rgba(249, 115, 22, .65); }
        .btn-chip .chip {
            width: 2.5rem; height: 2.5rem; border-radius: 9999px;
            background: rgba(255,255,255,.25); display: grid; place-items: center;
            backdrop-filter: blur(4px);
        }
        .btn-glass {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .85rem 1.75rem; border-radius: 9999px;
            font-weight: 600; font-size: .875rem; color: #fff;
            background: rgba(255,255,255,.12);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.30);
            backdrop-filter: blur(6px);
            transition: background .25s ease, transform .25s ease;
        }
        .btn-glass:hover { background: rgba(255,255,255,.22); transform: translateY(-2px); }

        /* Glassy floating info card */
        .float-card {
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(8px);
            border-radius: 1.25rem;
            box-shadow: 0 20px 45px -18px rgba(2, 132, 199, .45);
        }

        /* ──────── Product image stage ──────────
           Images are square 1024×1024 with the cup centered + transparent
           padding. We display them in a square stage with consistent inner
           padding so every product looks uniform and never cropped. */
        .img-stage {
            position: relative;
            aspect-ratio: 1 / 1;
            display: grid;
            place-items: center;
            overflow: hidden;
        }
        .img-stage > img,
        .img-stage > picture {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .img-stage > picture > img { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body class="bg-app text-slate-600 antialiased overflow-x-hidden">

@php
    $globalStore = \App\Models\StoreSetting::current();
@endphp

@if (!$globalStore->is_open)
    <div id="store-banner" class="bg-rose-600 text-white text-[11px] font-extrabold uppercase py-2.5 px-4 text-center tracking-wider relative z-[60] shadow-md flex items-center justify-center gap-2 select-none">
        SELAMAT DATANG! KEDAI KAMI SEDANG TUTUP SEMENTARA DAN BELUM MENERIMA PESANAN. KAMI AKAN SEGERA KEMBALI MELAYANI ANDA.
    </div>
@elseif ($globalStore->busy_mode)
    <div id="store-banner" class="bg-amber-500 text-slate-900 text-[11px] font-extrabold uppercase py-2.5 px-4 text-center tracking-wider relative z-[60] shadow-md flex items-center justify-center gap-2 select-none">
        {{ $globalStore->promo_banner_text }}
    </div>
@endif

@include('partials.nav')

<main>@yield('content')</main>

@include('partials.footer')

@include('partials.cart-drawer')
@include('partials.customizer-modal')

{{-- Floating WhatsApp button --}}
<a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo%20Es%20Teh%20Jumbo%2C%20saya%20mau%20pesan!"
   target="_blank" rel="noopener"
   aria-label="Chat WhatsApp"
   class="fixed bottom-5 right-5 z-50 inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1eb854] text-white text-sm font-semibold pl-3.5 pr-4 py-3 rounded-full shadow-lg hover:shadow-xl transition hover:-translate-y-0.5">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm5.82 14.12c-.25.7-1.47 1.35-2.02 1.4-.55.05-1.07.25-3.6-.75-3.06-1.2-5-4.35-5.15-4.55-.15-.2-1.22-1.62-1.22-3.1 0-1.47.77-2.2 1.05-2.5.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.85 2.07.92 2.22.07.15.12.32.02.52-.1.2-.15.32-.3.5-.15.17-.31.38-.45.51-.15.15-.3.3-.13.6.17.3.77 1.27 1.65 2.05 1.13 1 2.08 1.31 2.38 1.46.3.15.47.13.65-.07.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.75.82 2.05.97.3.15.5.22.57.35.07.12.07.72-.17 1.42z"/>
    </svg>
    <span class="hidden sm:inline">Chat WhatsApp</span>
</a>

</body>
</html>
