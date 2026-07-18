<div class="px-4 py-8 max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs mb-8 text-base-content/60">
        <ul>
            <li><a href="/katalog"><x-icon name="o-home" class="w-4 h-4 mr-1"/> Katalog</a></li>
            <li class="font-bold text-base-content">Keranjang Belanja</li>
        </ul>
    </div>

    <div class="flex items-center gap-3 mb-8">
        <x-icon name="o-shopping-cart" class="w-8 h-8 text-primary" />
        <h1 class="text-3xl font-extrabold text-base-content">Keranjang Belanja</h1>
    </div>

    @if($items->isEmpty())
        <div class="bg-base-100 border border-base-200 rounded-3xl p-16 text-center shadow-sm">
            <x-icon name="o-shopping-bag" class="w-24 h-24 mx-auto mb-6 text-base-content/20" />
            <h3 class="text-2xl font-bold text-base-content mb-2">Keranjang Anda Kosong</h3>
            <p class="text-base-content/60 mb-8">Yuk, temukan obat yang Anda butuhkan di katalog kami!</p>
            <x-button link="/katalog" class="btn-primary rounded-2xl px-10 shadow-premium shadow-primary/30 text-lg h-14" label="Mulai Belanja" icon-right="o-arrow-right" />
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Cart Items List -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                @foreach($items as $item)
                    <x-card class="bg-base-100 border-base-200 shadow-sm overflow-hidden p-0">
                        <div class="flex flex-col sm:flex-row gap-6 items-center p-4">
                            <!-- Image -->
                            <div class="w-24 h-24 bg-gradient-to-br from-base-200 to-base-300 rounded-2xl flex-shrink-0 flex items-center justify-center p-2">
                                @if($item->obat->image)
                                    <img src="{{ Storage::url($item->obat->image) }}" alt="{{ $item->obat->name }}" class="w-full h-full object-contain filter drop-shadow-sm">
                                @else
                                    <x-icon name="o-beaker" class="w-10 h-10 text-base-content/20" />
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 text-center sm:text-left">
                                <h4 class="text-lg font-bold text-base-content mb-1 line-clamp-1">{{ $item->obat->name }}</h4>
                                <div class="text-primary font-black text-lg">Rp{{ number_format($item->obat->price, 0, ',', '.') }}</div>
                            </div>

                            <!-- Qty & Actions -->
                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="join bg-base-200 rounded-xl p-1 border border-base-300">
                                    <button wire:click="decrement({{ $item->id }})" class="btn btn-circle btn-sm btn-ghost join-item">
                                        <x-icon name="o-minus" class="w-4 h-4" />
                                    </button>
                                    <div class="px-4 font-bold text-base-content flex items-center justify-center min-w-[3rem] join-item">
                                        {{ $item->quantity }}
                                    </div>
                                    <button wire:click="increment({{ $item->id }})" class="btn btn-circle btn-sm btn-ghost join-item" {{ $item->quantity >= $item->obat->stock ? 'disabled' : '' }}>
                                        <x-icon name="o-plus" class="w-4 h-4" />
                                    </button>
                                </div>
                                
                                <div class="text-right flex flex-col items-end">
                                    <div class="text-lg font-extrabold text-base-content mb-1 hidden sm:block">Rp{{ number_format($item->obat->price * $item->quantity, 0, ',', '.') }}</div>
                                    <button wire:click="remove({{ $item->id }})" class="btn btn-sm btn-ghost text-error hover:bg-error/10 border-none rounded-xl">
                                        <x-icon name="o-trash" class="w-4 h-4 mr-1" /> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-4">
                <x-card class="bg-base-100 border-base-200 shadow-premium sticky top-24">
                    <h3 class="text-xl font-bold text-base-content mb-6 pb-4 border-b border-base-200 flex items-center gap-2">
                        <x-icon name="o-document-text" class="w-6 h-6 text-primary" /> Ringkasan Pesanan
                    </h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-base-content/70">
                            <span>Total Produk ({{ $items->count() }} item)</span>
                            <span class="font-semibold text-base-content">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base-content/70">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-success uppercase">Gratis</span>
                        </div>
                    </div>

                    <div class="border-t border-base-200 py-6 mb-6 flex justify-between items-center">
                        <span class="text-lg font-bold text-base-content">Total Bayar</span>
                        <span class="text-2xl font-black text-primary">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-warning/10 border border-warning/20 rounded-2xl p-4 mb-6 flex items-start gap-3">
                        <x-icon name="o-clock" class="w-5 h-5 text-warning shrink-0 mt-0.5" />
                        <p class="text-sm text-warning-content/80 leading-relaxed">
                            Setelah checkout, Anda memiliki <strong class="text-warning-content">1x24 jam</strong> untuk menyelesaikan pembayaran.
                        </p>
                    </div>

                    <x-button wire:click="checkout" label="Buat Pesanan" class="btn-primary w-full rounded-2xl h-14 text-lg shadow-premium shadow-primary/30" icon-right="o-arrow-right" spinner="checkout" />
                </x-card>
            </div>
        </div>
    @endif
</div>
