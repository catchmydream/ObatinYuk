<div class="bg-gray-50 min-h-screen pb-12" 
    x-data="{ filterOpen: false, showToast: false }" 
    @scroll-to-products.window="document.getElementById('section-produk').scrollIntoView({behavior:'smooth'})"
    @cart-updated.window="showToast = true; setTimeout(() => showToast = false, 3000)"
>
    {{-- Toast Notification --}}
    <div 
        x-show="showToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-10"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-10"
        class="fixed top-20 right-6 z-[9999] flex items-center gap-3 bg-green-50 border border-green-100 text-green-700 px-5 py-4 rounded-xl shadow-lg pointer-events-none"
        style="display: none;"
    >
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-sm">Produk berhasil ditambahkan ke keranjang</span>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="section-produk">
        
        {{-- Combined Search & Filter Card --}}
        <div class="mb-10 p-6 sm:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #3b82f6 100%); color: #ffffff;">
            <div class="relative z-10 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Temukan Obat & Produk Kesehatan</h2>
                <p class="text-blue-100 text-sm leading-relaxed mb-6" style="color: #e0effe;">
                    Cari berdasarkan nama obat, keluhan, kategori, atau filter ketersediaan produk secara langsung.
                </p>

                {{-- Grid Pencarian dan Filter --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    {{-- Input Search --}}
                    <div class="relative md:col-span-6 lg:col-span-7">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            wire:model.live.debounce.500ms="q"
                            type="search" 
                            placeholder="Cari obat, suplemen, atau produk kesehatan..." 
                            class="block w-full pl-11 pr-10 py-3 border-0 rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-300 sm:text-sm shadow-sm transition-all duration-200"
                        >
                        @if(!empty($q))
                            <button wire:click="clearSearch" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Kategori Select --}}
                    <div class="md:col-span-3 lg:col-span-3">
                        <select wire:model.live="category" class="block w-full py-3 px-4 border-0 rounded-xl bg-white text-gray-700 font-medium focus:ring-2 focus:ring-blue-300 sm:text-sm shadow-sm cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="obat">Obat Medis</option>
                            <option value="herbal">Herbal</option>
                            <option value="vitamin">Vitamin</option>
                        </select>
                    </div>

                    {{-- Checkbox Tersedia --}}
                    <div class="md:col-span-3 lg:col-span-2 flex items-center justify-start md:justify-center">
                        <label class="flex items-center gap-2 text-sm font-semibold text-white cursor-pointer select-none">
                            <input type="checkbox" wire:model.live="in_stock" class="rounded border-white/20 text-blue-600 focus:ring-blue-300 bg-white/10 w-5 h-5">
                            <span>Hanya Tersedia</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-900">
                @if(!empty($q)) 
                    Hasil untuk "{{ $q }}"
                @elseif(!empty($symptom)) 
                    Produk untuk Gejala Terpilih
                @else 
                    Katalog Seluruh Obat
                @endif
            </h3>
            
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 font-medium">{{ $obats->total() }} produk ditemukan</span>
                <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-xs">
                    <span>Tampilkan:</span>
                    <select wire:model.live="perPage" class="font-bold text-blue-600 bg-transparent border-none p-0 focus:ring-0 cursor-pointer">
                        <option value="8">8</option>
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                        <option value="100">Semua</option>
                    </select>
                </div>
            </div>
        </div>


        {{-- Skeleton Loading --}}
        <div wire:loading wire:target="q, category, in_stock, symptom, perPage, clearSearch, searchBySymptom">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 w-full mb-8">
                @for ($i = 0; $i < 8; $i++)
                    <div wire:key="skeleton-{{ $i }}" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-pulse flex flex-col h-[320px]">
                    <div class="w-full aspect-[4/3] bg-gray-100 rounded-xl mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-100 rounded w-full mb-1"></div>
                    <div class="h-4 bg-gray-100 rounded w-2/3 mb-auto"></div>
                    <div class="mt-4 flex justify-between items-end">
                        <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-8 w-8 bg-gray-200 rounded-lg"></div>
                    </div>
                </div>
            @endfor
            </div>
        </div>

        {{-- Product Grid --}}
        <div wire:loading.remove wire:target="q, category, in_stock, symptom, perPage, clearSearch, searchBySymptom" wire:key="product-grid-wrapper">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
                @forelse($obats as $obat)
                <div wire:key="obat-{{ $obat->id }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col cursor-pointer" wire:click="goToProduct({{ $obat->id }})">
                    
                    {{-- Image Container (80-90% visual space) --}}
                    <div class="relative aspect-[4/3] w-full bg-white p-4 flex items-center justify-center border-b border-gray-50">
                        @if($obat->image)
                            <img src="{{ Storage::url($obat->image) }}" alt="{{ $obat->name }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                        @else
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            @if($obat->category)
                                <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-semibold px-2 py-0.5 rounded-md uppercase tracking-wide">
                                    {{ $obat->category }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute top-3 right-3">
                            @if($obat->stock == 0)
                                <span class="bg-red-50 text-red-600 border border-red-100 text-[10px] font-semibold px-2 py-0.5 rounded-md">Habis</span>
                            @elseif($obat->stock < 10)
                                <span class="bg-orange-50 text-orange-600 border border-orange-100 text-[10px] font-semibold px-2 py-0.5 rounded-md">Stok Terbatas</span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 sm:p-5 flex flex-col flex-1">
                        @if($obat->gejalas->count() > 0)
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($obat->gejalas->take(2) as $g)
                                    <span class="text-[11px] font-medium text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded">{{ $g->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        <h4 class="text-[15px] font-semibold text-gray-900 leading-tight mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $obat->name }}</h4>
                        
                        <p class="text-[13px] text-gray-500 line-clamp-2 leading-relaxed mb-4">{{ $obat->description }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-end justify-between">
                            <div>
                                <span class="block text-[11px] text-gray-400 mb-0.5">Mulai dari</span>
                                <div class="text-base font-bold text-gray-900">
                                    Rp{{ number_format($obat->price, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            @auth
                                @if($obat->stock > 0)
                                    <button 
                                        wire:click.stop="addToCart({{ $obat->id }})" 
                                        class="h-9 w-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors duration-200"
                                        title="Tambah ke Keranjang"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                @endif
                            @else
                                <a href="/login" class="text-[12px] font-medium text-blue-600 hover:text-blue-700">Masuk</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="col-span-full py-16 px-4 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Produk tidak ditemukan</h3>
                    <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">
                        Maaf, kami tidak dapat menemukan produk yang sesuai dengan pencarian atau filter Anda. Coba gunakan kata kunci yang berbeda.
                    </p>
                    <button wire:click="clearSearch" class="inline-flex items-center justify-center px-6 py-2.5 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                        Hapus Filter
                    </button>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8" wire:key="pagination-wrapper">
            {{ $obats->links('vendor.pagination.apotek') }}
        </div>
        
    </div>
</div>
