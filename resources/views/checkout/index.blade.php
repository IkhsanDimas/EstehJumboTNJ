@extends('layouts.public')
@section('title', 'Checkout — ' . $store->store_name)

@section('content')
<!-- Load Leaflet Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .leaflet-container img.leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
</style>

<section class="relative bg-page-soft min-h-screen pt-10 pb-24 overflow-hidden">
    {{-- Decorative blobs --}}
    <div aria-hidden="true" class="deco-blob deco-blob-sky w-[28rem] h-[28rem] -top-32 -right-32"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-amber w-72 h-72 top-1/3 -left-20 opacity-40"></div>
    <div aria-hidden="true" class="deco-blob deco-blob-emerald w-72 h-72 bottom-32 -right-20 opacity-40"></div>

    <div class="relative max-w-7xl mx-auto px-6">
        {{-- Header --}}
        <div class="mb-10">
            <p class="text-xs font-semibold tracking-[0.18em] uppercase text-sky-600">Checkout</p>
            <h1 class="mt-2 font-display font-semibold text-3xl md:text-4xl text-ink tracking-tight">
                Selesaikan Pesanan
            </h1>
            <p class="mt-2 text-slate-500">Isi data pengantaran. Kami konfirmasi via WhatsApp.</p>
        </div>

        @php $subtotal = 0; @endphp
        @foreach ($cart as $item)
            @php
                $unit = (int) ($item['unit_price'] ?? $item['price'] ?? 0);
                $subtotal += $unit * (int) $item['quantity'];
            @endphp
        @endforeach

        <div x-data="{
            deliveryType: 'pickup',
            customerName: '',
            address: '',
            latitude: '',
            longitude: '',
            distance: 0,
            map: null,
            marker: null,
            storeMarker: null,
            subtotal: {{ (int) $subtotal }},
            minOrderForDelivery: {{ (int) ($store->min_order_for_delivery ?? 30000) }},
            baseFare: {{ (int) ($store->base_fare ?? 5000) }},
            perKmRate: {{ (int) ($store->per_km_rate ?? 2000) }},
            maxRadius: {{ (float) ($store->max_radius_km ?? 5.0) }},
            storeLat: {{ (float) ($store->store_lat ?? -6.2434) }},
            storeLng: {{ (float) ($store->store_lng ?? 106.9871) }},
            addressLoading: false,
            mapExpanded: true,
            paymentMethod: 'cash',
            scheduleType: 'now',
            scheduleDate: '',
            scheduleTime: '',
            isSubmitting: false,

            init() {
                const profile = JSON.parse(localStorage.getItem('esteh_jumbo_profile') || '{}');
                this.customerName = profile.name || '';
                this.address = profile.address || '';
                this.latitude = profile.lat || '';
                this.longitude = profile.lng || '';
                
                // Initialize default schedule date and time
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                this.scheduleDate = `${yyyy}-${mm}-${dd}`;

                now.setMinutes(now.getMinutes() + 30);
                const hh = String(now.getHours()).padStart(2, '0');
                const minStr = String(now.getMinutes()).padStart(2, '0');
                this.scheduleTime = `${hh}:${minStr}`;
                
                if (this.address && this.isEligible()) {
                    this.deliveryType = 'delivery';
                    this.initMap();
                }
            },
            saveProfile() {
                localStorage.setItem('esteh_jumbo_profile', JSON.stringify({
                    name: this.customerName,
                    address: this.address,
                    lat: this.latitude,
                    lng: this.longitude
                }));
            },
            initMap() {
                if (this.map) return;
                this.$nextTick(() => {
                    const el = this.$refs.mapContainer;
                    if (!el) return;
                    
                    const centerLat = this.latitude || this.storeLat;
                    const centerLng = this.longitude || this.storeLng;

                    this.map = L.map(el).setView([centerLat, centerLng], 14);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);
                    
                    this.storeMarker = L.marker([this.storeLat, this.storeLng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(this.map).bindPopup('<b>Outlet Es Teh Jumbo</b><br>Galaxy, Bekasi.');

                    this.marker = L.marker([centerLat, centerLng], {
                        draggable: true,
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(this.map);

                    this.marker.on('dragend', () => {
                        const latlng = this.marker.getLatLng();
                        this.updateLocation(latlng.lat, latlng.lng);
                    });

                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.updateLocation(e.latlng.lat, e.latlng.lng);
                    });
                    
                    if (this.latitude && this.longitude) {
                        this.updateLocation(this.latitude, this.longitude);
                    } else {
                        this.updateLocation(this.storeLat, this.storeLng);
                    }
                });
            },
            updateLocation(lat, lng) {
                this.latitude = lat;
                this.longitude = lng;
                this.calculateDistance();
                this.reverseGeocode(lat, lng);
            },
            calculateDistance() {
                const R = 6371;
                const dLat = (this.latitude - this.storeLat) * Math.PI / 180;
                const dLng = (this.longitude - this.storeLng) * Math.PI / 180;
                const a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(this.storeLat * Math.PI / 180) * Math.cos(this.latitude * Math.PI / 180) * 
                    Math.sin(dLng/2) * Math.sin(dLng/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                this.distance = R * c;
            },
            isEligibleDistance() {
                if (this.deliveryType !== 'delivery') return true;
                return this.distance <= this.maxRadius;
            },
            distanceText() {
                if (this.distance === 0) return '-';
                return this.distance.toFixed(2) + ' km';
            },
            selectDelivery() {
                this.deliveryType = 'delivery';
                this.initMap();
                this.getCurrentLocation(true);
            },
            getCurrentLocation(isAuto = false) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        this.latitude = lat;
                        this.longitude = lng;
                        
                        if (this.map && this.marker) {
                            this.marker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng], 16);
                            this.updateLocation(lat, lng);
                        } else {
                            this.updateLocation(lat, lng);
                            this.$nextTick(() => {
                                if (this.map && this.marker) {
                                    this.marker.setLatLng([lat, lng]);
                                    this.map.setView([lat, lng], 16);
                                }
                            });
                        }
                    }, (err) => {
                        console.warn('Geolocation error:', err);
                        if (!isAuto) {
                            if (err.code === 1) { // PERMISSION_DENIED
                                alert('Akses lokasi ditolak atau diblokir. Silakan aktifkan kembali izin lokasi di pengaturan browser Anda (klik ikon gembok/pengaturan di sebelah alamat URL) agar dapat membagikan lokasi otomatis.');
                            } else {
                                alert('Gagal mendapatkan lokasi GPS Anda. Silakan pilih titik manual di peta.');
                            }
                        }
                    });
                } else {
                    if (!isAuto) {
                        alert('Browser Anda tidak mendukung Geolocation.');
                    }
                }
            },
            async reverseGeocode(lat, lng) {
                this.addressLoading = true;
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.display_name) {
                            this.address = data.display_name;
                        }
                    }
                } catch (e) {
                    console.warn('Geocoding error:', e);
                } finally {
                    this.addressLoading = false;
                }
            },
            toggleMapSize() {
                this.mapExpanded = !this.mapExpanded;
                this.$nextTick(() => {
                    if (this.map) {
                        setTimeout(() => {
                            this.map.invalidateSize({ animate: true });
                        }, 350);
                    }
                });
            },
            isEligible() {
                return true;
            },
            getDeliveryFare() {
                if (this.deliveryType !== 'delivery') return 0;
                return this.baseFare + Math.round(this.distance * this.perKmRate);
            },
            getGrandTotal() {
                return this.deliveryType === 'delivery'
                    ? this.subtotal + this.getDeliveryFare()
                    : this.subtotal;
            },
            format(n) {
                return Number(n || 0).toLocaleString('id-ID');
            },
            validateForm(e) {
                if (this.scheduleType === 'later') {
                    if (this.scheduleTime < '09:00' || this.scheduleTime > '22:00') {
                        alert('Jam operasional pengantaran/pengambilan adalah pukul 09:00 s/d 22:00 WIB.');
                        e.preventDefault();
                        return false;
                    }

                    const todayStr = new Date().toISOString().split('T')[0];
                    if (this.scheduleDate < todayStr) {
                        alert('Tanggal pengantaran/pengambilan tidak boleh di masa lalu.');
                        e.preventDefault();
                        return false;
                    }

                    if (this.scheduleDate === todayStr) {
                        const now = new Date();
                        const currentHHMM = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                        if (this.scheduleTime < currentHHMM) {
                            alert('Jam pengantaran/pengambilan tidak boleh di masa lalu.');
                            e.preventDefault();
                            return false;
                        }
                    }
                }
                return true;
            }
        }" class="grid lg:grid-cols-3 gap-8">
            {{-- ─── FORM ─── --}}
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.store') }}" method="POST"
                      @submit="if (!validateForm($event)) return; if (isSubmitting) { $event.preventDefault(); return; } isSubmitting = true; saveProfile()"
                      class="bg-white/85 backdrop-blur-sm rounded-3xl border border-white shadow-[0_20px_40px_-20px_rgba(2,132,199,0.20)] p-7 md:p-8">
                    @csrf
                    <input type="hidden" name="latitude" x-model="latitude">
                    <input type="hidden" name="longitude" x-model="longitude">

                    {{-- ─── DELIVERY METHOD ─── --}}
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-600 grid place-items-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m18 14-1-3" />
                                <path d="m3 9 6 2a2 2 0 0 1 2-2h2a2 2 0 0 1 1.99 1.81" />
                                <path d="M8 17h3a1 1 0 0 0 1-1 6 6 0 0 1 6-6 1 1 0 0 0 1-1v-.75A5 5 0 0 0 17 5" />
                                <circle cx="19" cy="17" r="3" />
                                <circle cx="5" cy="17" r="3" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-display font-semibold text-ink">Metode Pengiriman</p>
                            <p class="text-xs text-slate-500">Pilih bagaimana pesanan Anda ingin diterima.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        {{-- Pickup option --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_type" value="pickup" x-model="deliveryType" class="sr-only">
                            <div class="relative rounded-2xl border-2 px-4 py-4 transition-all duration-300"
                                 :class="deliveryType === 'pickup' ? 'border-sky-500 bg-sky-50/60' : 'border-slate-200 bg-white/70 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-display font-bold text-slate-900 text-sm">Ambil Sendiri</p>
                                        <p class="text-xs text-slate-500 mt-1">Ambil langsung di toko</p>
                                    </div>
                                </div>
                                <span class="absolute top-2.5 right-2.5 w-4.5 h-4.5 rounded-full border border-slate-300 flex items-center justify-center transition-colors"
                                      :class="deliveryType === 'pickup' ? 'border-sky-500 bg-sky-500 text-white' : ''">
                                    <template x-if="deliveryType === 'pickup'">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </template>
                                </span>
                            </div>
                        </label>

                        {{-- Delivery option --}}
                        <label class="relative block" :class="!isEligible() ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'" @click="if (isEligible()) { selectDelivery(); }">
                            <input type="radio" name="delivery_type" value="delivery" x-model="deliveryType" :disabled="!isEligible()" class="sr-only">
                            <div class="relative rounded-2xl border-2 px-4 py-4 transition-all duration-300"
                                 :class="deliveryType === 'delivery' ? 'border-sky-500 bg-sky-50/60' : 'border-slate-200 bg-white/70 hover:border-slate-300'">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-display font-bold text-slate-900 text-sm">Kirim ke Alamat</p>
                                        <p class="text-xs text-slate-500 mt-1" x-text="'+Rp ' + format(baseFare)"></p>
                                    </div>
                                </div>
                                <span class="absolute top-2.5 right-2.5 w-4.5 h-4.5 rounded-full border border-slate-300 flex items-center justify-center transition-colors"
                                      :class="deliveryType === 'delivery' ? 'border-sky-500 bg-sky-500 text-white' : ''">
                                    <template x-if="deliveryType === 'delivery'">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </template>
                                </span>
                            </div>
                        </label>
                    </div>

                    {{-- Minimum Delivery Alert --}}
                    <div x-show="!isEligible()" x-cloak class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-800 flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-slate-900">Peringatan Pengantaran</p>
                            <p class="mt-1 leading-relaxed">
                                Total belanja Anda (<strong class="text-amber-900" x-text="'Rp ' + format(subtotal)"></strong>) kurang dari batas minimal pengantaran (<strong class="text-amber-900" x-text="'Rp ' + format(minOrderForDelivery)"></strong>). Silakan tambahkan item ke keranjang atau pilih Ambil Sendiri.
                            </p>
                        </div>
                    </div>

                    {{-- ─── DELIVERY/PICKUP SCHEDULE ─── --}}
                    <div class="mt-6 space-y-3">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Waktu Pemesanan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="schedule_type" value="now" x-model="scheduleType" class="sr-only">
                                <div class="rounded-2xl border-2 px-4 py-3.5 text-center transition text-xs font-bold"
                                     :class="scheduleType === 'now' ? 'border-sky-500 bg-sky-50/60 text-sky-700 ring-1 ring-sky-500' : 'border-slate-200 bg-white/70 text-slate-600 hover:border-slate-300'">
                                    <span x-text="deliveryType === 'delivery' ? 'Kirim Sekarang (Instan)' : 'Ambil Sekarang (Instan)'"></span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="schedule_type" value="later" x-model="scheduleType" class="sr-only">
                                <div class="rounded-2xl border-2 px-4 py-3.5 text-center transition text-xs font-bold"
                                     :class="scheduleType === 'later' ? 'border-sky-500 bg-sky-50/60 text-sky-700 ring-1 ring-sky-500' : 'border-slate-200 bg-white/70 text-slate-600 hover:border-slate-300'">
                                    <span>Jadwalkan Waktu</span>
                                </div>
                            </label>
                        </div>

                        {{-- Date & Time Inputs --}}
                        <div x-show="scheduleType === 'later'" x-cloak x-transition class="grid grid-cols-2 gap-4 mt-3 p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                                <input type="date" name="schedule_date" x-model="scheduleDate" :required="scheduleType === 'later'"
                                       :min="new Date().toISOString().split('T')[0]"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam Operasional (09.00 - 22.00)</label>
                                <input type="time" name="schedule_time" x-model="scheduleTime" :required="scheduleType === 'later'"
                                       min="09:00" max="22:00"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400">
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100 my-6">

                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-600 grid place-items-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-display font-semibold text-ink">Informasi Pemesan</p>
                            <p class="text-xs text-slate-500">Isi nama Anda untuk dicantumkan pada rincian pesanan.</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- Name --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wider">Nama</label>
                            <input type="text" name="customer_name" required x-model="customerName"
                                   placeholder="Nama lengkap"
                                   class="w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 focus:bg-white transition">
                        </div>



                        {{-- Address field (only for Delivery) --}}
                        <div x-show="deliveryType === 'delivery'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wider">Alamat Lengkap Pengiriman</label>
                                <div class="relative">
                                    <textarea name="address" :required="deliveryType === 'delivery'" x-model="address" rows="3"
                                              :placeholder="addressLoading ? 'Sedang mendeteksi alamat Anda dari koordinat peta...' : 'Masukkan alamat pengiriman lengkap (Nama jalan, RT/RW, nomor rumah, kelurahan, kecamatan, patokan)...'"
                                              :disabled="addressLoading"
                                              class="w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3.5 pr-32 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 focus:bg-white transition resize-none disabled:opacity-75 disabled:cursor-not-allowed"></textarea>
                                    <div x-show="addressLoading" x-cloak class="absolute top-3.5 right-3.5 flex items-center gap-1.5 text-xs font-semibold text-sky-600 bg-white/90 px-2.5 py-1 rounded-xl border border-sky-100 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="9" stroke-opacity="0.25"/>
                                            <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round"/>
                                        </svg>
                                        Mendeteksi...
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leaflet Map Container & Geolocation -->
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Tandai Lokasi Rumah di Peta</label>
                                     <button type="button" @click="getCurrentLocation(false)" class="text-xs text-sky-600 hover:text-sky-700 font-semibold flex items-center gap-1 transition">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                                        </svg>
                                        Gunakan Lokasi Saya
                                    </button>
                                </div>
                                <div id="checkout-map" 
                                     class="w-full rounded-2xl border border-slate-200 shadow-inner z-10 transition-all duration-300"
                                     :class="mapExpanded ? 'h-[280px]' : 'h-[140px]'"
                                     x-ref="mapContainer"></div>
                                <div class="flex items-center justify-end">
                                    <button type="button" @click="toggleMapSize()" class="md:hidden text-[10px] text-slate-500 hover:text-sky-600 font-semibold flex items-center gap-1 transition mt-1 bg-slate-100 hover:bg-sky-50 px-2 py-1.5 rounded-xl border border-slate-200/60">
                                        <span x-text="mapExpanded ? 'Sembunyikan Peta (Minimize)' : 'Tampilkan Peta (Maximize)'"></span>
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="mapExpanded ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                                    </button>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs p-3 rounded-xl bg-slate-50 border border-slate-100 mt-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full" :class="isEligibleDistance() ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                        <span class="font-medium text-slate-700">Jarak: <span class="font-bold text-slate-900" x-text="distanceText()"></span></span>
                                    </div>
                                    <span class="text-slate-400" x-text="isEligibleDistance() ? 'Dalam jangkauan kurir' : 'Di luar jangkauan (Maks ' + maxRadius + ' km)'"></span>
                                </div>

                                <div x-show="!isEligibleDistance() && distance > 0" x-cloak class="p-3.5 rounded-xl bg-rose-50 border border-rose-150 text-xs text-rose-800 flex items-start gap-2.5 mt-2">
                                    <svg class="w-4.5 h-4.5 mt-0.5 text-rose-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-rose-900">Lokasi Terlalu Jauh</p>
                                        <p class="mt-0.5">Jarak pengantaran melebihi batas maksimal <span x-text="maxRadius"></span> km dari outlet kami. Silakan pilih opsi Ambil Sendiri.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─── PAYMENT METHOD ─── --}}
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-600 grid place-items-center">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                                        <line x1="12" y1="4" x2="12" y2="20"/>
                                        <line x1="2" y1="12" x2="22" y2="12"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-display font-semibold text-ink">Metode Pembayaran</p>
                                    <p class="text-xs text-slate-500">Pembayaran dilakukan secara tunai saat pesanan diterima.</p>
                                </div>
                            </div>

                            {{-- COD only — auto-selected --}}
                            <input type="hidden" name="payment_method" value="cash">
                            <div class="rounded-2xl border-2 border-sky-500 bg-sky-50/60 ring-1 ring-sky-500 px-4 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-display font-bold text-slate-900 text-sm">Bayar di Tempat (COD)</p>
                                        <p class="text-[10px] text-slate-500 mt-1">Bayar tunai saat pesanan diterima</p>
                                    </div>
                                    <span class="w-4.5 h-4.5 rounded-full border border-sky-500 bg-sky-500 text-white flex items-center justify-center">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wider">Catatan Pesanan <span class="text-slate-400 normal-case">(opsional)</span></label>
                            <textarea name="notes" rows="3"
                                      placeholder="Contoh: titip ke pos satpam, minta plastik tambahan, dll..."
                                      class="w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 focus:bg-white transition resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            :disabled="(deliveryType === 'delivery' && !isEligibleDistance()) || isSubmitting"
                            class="mt-7 w-full inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-black text-white font-semibold text-sm px-6 py-4 rounded-full transition shadow-lg shadow-slate-900/20 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <!-- Loading State -->
                        <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                            <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="9" stroke-opacity="0.25" stroke="currentColor"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round" stroke="currentColor"/>
                            </svg>
                            Memproses Pesanan...
                        </span>
                        <!-- Default State -->
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/>
                            </svg>
                            Pesan via WhatsApp
                        </span>
                    </button>
                    <p class="mt-3 text-xs text-slate-400 text-center">Klik untuk lanjut ke WhatsApp dengan rincian pesanan otomatis.</p>
                </form>
            </div>

            {{-- ─── ORDER SUMMARY ─── --}}
            <aside>
                <div class="sticky top-24 relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-500 via-sky-600 to-sky-700 text-white p-7 shadow-[0_25px_50px_-15px_rgba(2,132,199,0.45)]">
                    {{-- Pattern --}}
                    <div aria-hidden="true" class="absolute inset-0 opacity-15"
                         style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 50px 50px, 70px 70px;"></div>
                    <div aria-hidden="true" class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-white/15 blur-2xl"></div>

                    <div class="relative">
                        <p class="text-[10px] font-semibold tracking-[0.22em] uppercase text-sky-100/80">Pesanan Anda</p>
                        <h2 class="mt-2 font-display font-extrabold text-2xl">Ringkasan</h2>

                        <ul class="mt-6 space-y-4">
                            @foreach ($cart as $item)
                                @php $unit = (int) ($item['unit_price'] ?? $item['price'] ?? 0); @endphp
                                <li class="flex items-start justify-between gap-3 text-sm">
                                    <div class="min-w-0">
                                        <p class="font-semibold leading-tight truncate">{{ $item['name'] }}</p>
                                        @if (! empty($item['options_summary']))
                                            <p class="text-[11px] text-sky-100/80 mt-0.5 truncate">{{ $item['options_summary'] }}</p>
                                        @endif
                                        @if (! empty($item['notes']))
                                            <p class="text-[11px] text-amber-200 mt-0.5 truncate italic font-medium">« {{ $item['notes'] }} »</p>
                                        @endif
                                        <p class="text-[11px] text-sky-100/70 mt-0.5">{{ $item['quantity'] }} × Rp {{ number_format($unit, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-display font-bold whitespace-nowrap">
                                        Rp {{ number_format($unit * (int) $item['quantity'], 0, ',', '.') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6 space-y-3 pt-5 border-t border-white/20 text-sm">
                            <div class="flex justify-between text-sky-100/90">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="'Rp ' + format(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sky-100/90" x-show="deliveryType === 'delivery'" x-cloak>
                                <span>Biaya Pengiriman (<span x-text="distanceText()"></span>)</span>
                                <span class="font-semibold" x-text="'Rp ' + format(getDeliveryFare())"></span>
                            </div>
                            <div class="flex justify-between text-sky-100/90" x-show="deliveryType === 'pickup'" x-cloak>
                                <span>Biaya Pengiriman</span>
                                <span class="font-semibold text-emerald-200">Gratis (Pickup)</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-white/20 text-white">
                                <span class="font-semibold">Total Tagihan</span>
                                <span class="font-display font-extrabold text-2xl" x-text="'Rp ' + format(getGrandTotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
