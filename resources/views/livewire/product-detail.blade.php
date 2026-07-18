<div class="px-4 py-8 max-w-7xl mx-auto" x-data="{ imgZoom: false }">
    
    @if(session('cart_success'))
        <x-toast />
        <script>
            document.addEventListener('livewire:initialized', () => {
                Toast.success("{{ session('cart_success') }}");
            });
        </script>
    @endif

    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs mb-8 text-base-content/60">
        <ul>
            <li><a href="/katalog"><x-icon name="o-home" class="w-4 h-4 mr-1"/> Katalog</a></li>
            <li class="font-bold text-base-content">{{ $obat->name }}</li>
        </ul>
    </div>

    <!-- Product Detail Container -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16 items-start">
        
        <!-- Image Section -->
        <div class="relative bg-gradient-to-br from-base-200 to-base-300 rounded-3xl p-8 flex items-center justify-center min-h-[400px] lg:min-h-[500px] group cursor-pointer" @click="imgZoom = true" title="Klik untuk memperbesar">
            <!-- Zoom Icon Helper -->
            <div class="absolute top-4 right-4 bg-base-100/50 backdrop-blur rounded-full p-2 text-base-content/50 group-hover:text-primary transition-colors z-10">
                <x-icon name="o-arrows-pointing-out" class="w-6 h-6" />
            </div>

            @if($obat->image)
                <img src="{{ Storage::url($obat->image) }}" alt="{{ $obat->name }}" class="w-full max-h-[400px] object-contain filter drop-shadow-xl group-hover:scale-105 transition-transform duration-500">
            @else
                <x-icon name="o-beaker" class="w-40 h-40 text-base-content/20" />
            @endif

            <div class="absolute bottom-6 left-6 flex flex-col gap-2">
                @if($obat->stock < 10 && $obat->stock > 0)
                    <x-badge value="Stok Terbatas" class="badge-warning text-warning-content shadow-lg" />
                @elseif($obat->stock == 0)
                    <x-badge value="Habis" class="badge-error text-error-content shadow-lg" />
                @endif
            </div>
        </div>

        <!-- Image Zoom Modal (For Accessibility) -->
        <x-modal wire:model="imgZoom" class="backdrop-blur-sm">
            <div class="p-4 bg-white rounded-3xl relative flex flex-col items-center">
                @if($obat->image)
                    <img src="{{ Storage::url($obat->image) }}" alt="{{ $obat->name }}" class="w-full max-h-[80vh] object-contain mb-4">
                @else
                    <x-icon name="o-beaker" class="w-64 h-64 text-base-content/20 mb-4" />
                @endif
                <x-button label="Tutup Gambar" @click="imgZoom = false" class="btn-primary w-full rounded-xl" />
            </div>
        </x-modal>

        <!-- Info Section -->
        <div>
            <!-- Gejala Tags -->
            @if($obat->gejalas->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($obat->gejalas as $gejala)
                        <span class="badge badge-success badge-outline font-bold uppercase tracking-wider text-xs">{{ $gejala->name }}</span>
                    @endforeach
                </div>
            @endif

            <h1 class="text-4xl md:text-5xl font-extrabold text-base-content leading-tight mb-2">{{ $obat->name }}</h1>
            
            <div class="text-4xl font-black text-primary mb-6">
                Rp{{ number_format($obat->price, 0, ',', '.') }}
            </div>

            <div class="flex flex-wrap gap-2 mb-8">
                <x-badge value="{{ $obat->category === 'herbal' ? 'Obat Herbal' : ucfirst($obat->category ?: 'Obat') }}" class="badge-ghost shadow-sm uppercase font-bold" />
                @if($obat->classification === 'keras' && $obat->category === 'obat')
                    <x-badge value="Obat Keras" class="badge-error text-error-content shadow-sm uppercase font-bold" />
                @endif
            </div>

            <!-- Description -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-base-content mb-2 flex items-center gap-2">
                    <x-icon name="o-information-circle" class="w-5 h-5 text-primary" /> Deskripsi Produk
                </h3>
                <p class="text-base-content/70 leading-relaxed text-lg">
                    {{ $obat->description ?: 'Tidak ada deskripsi.' }}
                </p>
            </div>

            <!-- Dosage -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-base-content mb-2 flex items-center gap-2">
                    <x-icon name="o-clipboard-document-list" class="w-5 h-5 text-primary" /> Dosis & Penggunaan
                </h3>
                <p class="text-base-content/70 leading-relaxed text-lg whitespace-pre-line">
                    {{ $obat->dosage ?: 'Tidak ada informasi dosis.' }}
                </p>
            </div>

            <!-- Side Effects -->
            @if($obat->side_effects)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-base-content mb-2 flex items-center gap-2">
                    <x-icon name="o-exclamation-circle" class="w-5 h-5 text-warning" /> Efek Samping
                </h3>
                <p class="text-base-content/70 leading-relaxed text-lg whitespace-pre-line">
                    {{ $obat->side_effects }}
                </p>
            </div>
            @endif

            <!-- Warnings -->
            @if($obat->warnings)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-base-content mb-2 flex items-center gap-2">
                    <x-icon name="o-shield-exclamation" class="w-5 h-5 text-error" /> Peringatan & Perhatian
                </h3>
                <p class="text-base-content/70 leading-relaxed text-lg whitespace-pre-line">
                    {{ $obat->warnings }}
                </p>
            </div>
            @endif

            <!-- Aturan Pakai Box -->
            @if($obat->aturan_pakai)
            <div class="bg-base-200/50 border border-base-300 rounded-3xl p-6 mb-8 flex items-center gap-6">
                <div class="w-16 h-16 bg-base-100 rounded-2xl flex items-center justify-center text-3xl shadow-sm shrink-0">
                    @if($obat->aturan_pakai === 'sebelum_makan') 🍽️ @elseif($obat->aturan_pakai === 'sesudah_makan') 🍕 @elseif($obat->aturan_pakai === 'saat_makan') 🍲 @else ✅ @endif
                </div>
                <div>
                    <h4 class="font-bold text-base-content text-lg mb-1">
                        @if($obat->aturan_pakai === 'sebelum_makan') Sebelum Makan @elseif($obat->aturan_pakai === 'sesudah_makan') Sesudah Makan @elseif($obat->aturan_pakai === 'saat_makan') Bersamaan Makan @else Bebas / Tidak Terikat Makan @endif
                    </h4>
                    <p class="text-base-content/60 text-sm">
                        @if($obat->aturan_pakai === 'sebelum_makan') Diminum minimal 30-60 menit sebelum makan. @elseif($obat->aturan_pakai === 'sesudah_makan') Diminum setelah makan untuk penyerapan optimal/mencegah iritasi. @elseif($obat->aturan_pakai === 'saat_makan') Diminum bersamaan dengan suapan makanan. @else Dapat diminum kapan saja (sebelum/sesudah makan). @endif
                    </p>
                </div>
            </div>
            @endif

            <hr class="border-base-300 my-8" />

            <!-- Add to Cart Actions -->
            @auth
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-base-content mb-1">Jumlah Pembelian</div>
                        <div class="text-sm text-base-content/60">Tersedia: {{ $obat->stock }}</div>
                    </div>
                    
                    <div class="join bg-base-200 p-1 rounded-2xl">
                        <button type="button" wire:click="decrementQty" class="btn btn-circle btn-sm btn-ghost join-item" {{ $quantity <= 1 ? 'disabled' : '' }}>
                            <x-icon name="o-minus" class="w-4 h-4" />
                        </button>
                        <input type="number" wire:model.live="quantity" class="input input-sm w-16 text-center font-bold bg-transparent join-item pointer-events-none" readonly />
                        <button type="button" wire:click="incrementQty" class="btn btn-circle btn-sm btn-ghost join-item" {{ $quantity >= $obat->stock ? 'disabled' : '' }}>
                            <x-icon name="o-plus" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="flex gap-4">
                    @if($obat->stock > 0)
                        <x-button wire:click="addToCart" class="btn-primary flex-1 rounded-2xl shadow-premium shadow-primary/30 h-14 text-lg" icon="o-shopping-cart" spinner="addToCart">
                            Tambahkan ke Keranjang
                        </x-button>
                    @else
                        <x-button disabled class="btn-disabled flex-1 rounded-2xl h-14 text-lg">Stok Habis</x-button>
                    @endif
                </div>
            @else
                <div class="flex gap-4">
                    <x-button link="/login" class="btn-primary flex-1 rounded-2xl shadow-premium shadow-primary/30 h-14 text-lg" icon="o-lock-closed">
                        Login untuk Membeli
                    </x-button>
                </div>
            @endauth
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedObats->count() > 0)
        <div class="mt-24">
            <h2 class="text-2xl font-bold text-base-content mb-6 flex items-center gap-2">
                <x-icon name="o-rectangle-group" class="w-6 h-6 text-primary" /> Produk Terkait
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedObats as $related)
                    <x-card class="bg-base-100 border-base-200 shadow-sm hover:shadow-premium hover:-translate-y-1 transition-all overflow-hidden cursor-pointer" onclick="window.location='/produk/{{ $related->id }}'">
                        <x-slot:figure>
                            <div class="w-full h-40 bg-gradient-to-br from-base-200 to-base-300 flex items-center justify-center p-4">
                                @if($related->image)
                                    <img src="{{ Storage::url($related->image) }}" alt="{{ $related->name }}" class="h-full object-contain filter drop-shadow-md" />
                                @else
                                    <x-icon name="o-beaker" class="w-16 h-16 text-base-content/20" />
                                @endif
                            </div>
                        </x-slot:figure>
                        <div class="p-2">
                            <h3 class="font-bold text-base-content line-clamp-1 mb-1">{{ $related->name }}</h3>
                            <div class="font-extrabold text-primary">Rp{{ number_format($related->price, 0, ',', '.') }}</div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif
</div>
