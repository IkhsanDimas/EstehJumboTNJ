@extends('layouts.admin')
@section('title', 'Edit Menu — ' . $product->name)

@section('content')
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-5" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700 transition">Dasbor</a>
        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
        <a href="{{ route('admin.products.index') }}" class="hover:text-slate-700 transition">Manajemen Menu</a>
        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
        <span class="text-slate-600 font-medium">Edit Menu</span>
    </nav>

    {{-- Form Card --}}
    <div class="bg-white rounded-3xl border border-slate-150 shadow-sm p-6 md:p-8 max-w-4xl">
        <div class="mb-6 pb-4 border-b border-slate-150">
            <h1 class="font-display font-extrabold text-slate-900 text-xl">Edit Menu Minuman</h1>
            <p class="text-xs text-slate-450 mt-1">Ubah rincian informasi dan foto untuk menu <strong>{{ $product->name }}</strong>.</p>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                {{-- Form Fields --}}
                <div class="md:col-span-8 space-y-5">
                    
                    {{-- Name --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Menu Minuman</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required placeholder="Contoh: Es Teh Original Premium" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Category --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori</label>
                            <select name="category_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-650 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Harga (Rupiah)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" name="price" value="{{ old('price', (int) $product->price) }}" required min="0" placeholder="5000" class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan kesegaran menu ini..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Availability Status --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Status Ketersediaan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="radio" name="is_available" value="1" {{ old('is_available', $product->is_available ? '1' : '0') == '1' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-semibold text-slate-700">Tersedia (Bisa Dipesan)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="radio" name="is_available" value="0" {{ old('is_available', $product->is_available ? '1' : '0') == '0' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-semibold text-slate-700">Habis / Tidak Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Image Upload & Live Preview --}}
                <div class="md:col-span-4 space-y-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Produk</label>
                    
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-full aspect-square rounded-2xl bg-slate-50 border border-slate-200 p-4 flex items-center justify-center overflow-hidden relative">
                            @php
                                $hasDot = str_contains($product->image_path, '.');
                                $imgPath = $hasDot ? $product->image_path : $product->image_path . '.png';
                            @endphp
                            <img id="preview-image" src="{{ asset($imgPath) }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain drop-shadow-md">
                        </div>
                        
                        <div class="w-full">
                            <input type="file" name="image" id="image-input" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                            <p class="text-[9px] text-slate-400 mt-2 leading-relaxed">PNG, JPG, JPEG, atau WEBP. Maksimal 2MB. Disarankan PNG transparan.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="pt-5 border-t border-slate-150 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="bg-slate-150 hover:bg-slate-250 text-slate-700 font-semibold text-xs px-6 py-3.5 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs tracking-wider uppercase px-8 py-3.5 rounded-xl transition shadow-md shadow-blue-500/10 hover:-translate-y-0.5 active:translate-y-0">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    {{-- Javascript for Image Live Preview --}}
    <script>
        const imageInput = document.getElementById('image-input');
        const previewImage = document.getElementById('preview-image');
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    previewImage.setAttribute('src', this.result);
                });
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
