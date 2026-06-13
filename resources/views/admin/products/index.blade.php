@extends('layouts.admin')
@section('title', 'Kelola Menu — estehjumboTNJ')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-blue-600">Manajemen Produk</span>
            <h1 class="font-display font-extrabold text-2xl md:text-3xl text-slate-900 mt-1">Kelola Menu Minuman</h1>
            <p class="text-xs text-slate-450 mt-1">Kelola data menu, harga, ketersediaan, serta gambar minuman.</p>
        </div>
        
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs tracking-wider uppercase px-6 py-3.5 rounded-xl transition shadow-md shadow-blue-500/10 hover:-translate-y-0.5 inline-flex items-center gap-2 w-fit">
            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg> Tambah Menu
        </a>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white rounded-2xl border border-slate-150 shadow-sm p-4 mb-6">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama menu..." class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-xs font-semibold placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
            </div>

            <div class="w-full sm:w-48">
                <select name="category_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (isset($filters['category_id']) && $filters['category_id'] == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-slate-900 hover:bg-black text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition">
                    Cari
                </button>
                @if (!empty($filters))
                    <a href="{{ route('admin.products.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-5 py-2.5 rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Products Grid/Table --}}
    <div class="bg-white rounded-3xl border border-slate-150 shadow-sm overflow-hidden">
        @if ($products->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-150">
                            <th class="px-6 py-4">Foto</th>
                            <th class="px-6 py-4">Nama Menu</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            <tr class="hover:bg-slate-50/50 transition">
                                {{-- Photo --}}
                                <td class="px-6 py-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-150 p-1 flex items-center justify-center overflow-hidden">
                                        <picture>
                                            <img src="{{ asset(str_contains($product->image_path, '.') ? $product->image_path : $product->image_path . '.png') }}" alt="{{ $product->name }}" class="w-full h-full object-contain drop-shadow-md">
                                        </picture>
                                    </div>
                                </td>
                                
                                {{-- Name --}}
                                <td class="px-6 py-3">
                                    <div class="font-bold text-slate-900 text-sm">{{ $product->name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 line-clamp-1 max-w-[250px] font-normal">{{ $product->description ?? 'Tidak ada deskripsi.' }}</div>
                                </td>
                                
                                {{-- Category --}}
                                <td class="px-6 py-3 text-slate-600 font-semibold">
                                    {{ $product->category->name }}
                                </td>
                                
                                {{-- Price --}}
                                <td class="px-6 py-3 font-bold text-slate-900">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                
                                {{-- Availability status --}}
                                <td class="px-6 py-3">
                                    @if ($product->is_available)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 capitalize">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 border border-rose-100 text-rose-700 capitalize">
                                            Habis
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Actions --}}
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[10px] px-3.5 py-2 rounded-xl transition border border-blue-100 uppercase tracking-wide">
                                            Edit
                                        </a>
                                        
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[10px] px-3.5 py-2 rounded-xl transition border border-rose-100 uppercase tracking-wide">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination links --}}
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto stroke-current opacity-30 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>
                <p class="text-sm">Tidak ada menu minuman ditemukan.</p>
            </div>
        @endif
    </div>
@endsection
