<div class="px-4 py-8 max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs mb-8 text-base-content/60">
        <ul>
            <li><a href="/keranjang"><x-icon name="o-shopping-cart" class="w-4 h-4 mr-1"/> Keranjang</a></li>
            <li class="font-bold text-base-content">Checkout</li>
        </ul>
    </div>

    <div class="flex items-center gap-3 mb-8">
        <x-icon name="o-credit-card" class="w-8 h-8 text-primary" />
        <h1 class="text-3xl font-extrabold text-base-content">Checkout</h1>
    </div>

    <x-form wire:submit="processCheckout" no-separator>
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Forms -->
            <div class="xl:col-span-8 flex flex-col gap-8">
                
                <!-- Shipping Info -->
                <x-card title="Informasi Pengiriman" class="bg-base-100 border-base-200 shadow-sm" icon="o-map-pin">
                    <div class="space-y-6 pt-2">
                        <x-input label="Nomor Telepon (WhatsApp)" wire:model="phone_number" placeholder="081234567890" icon="o-phone" />
                        <x-textarea label="Alamat Lengkap" wire:model="shipping_address" placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kota..." rows="3" />
                    </div>
                </x-card>

                <!-- Payment Method -->
                <x-card title="Metode Pembayaran" class="bg-base-100 border-base-200 shadow-sm" icon="o-banknotes">
                    @error('payment_method') <div class="text-error text-sm mb-4">{{ $message }}</div> @enderror

                    <div class="space-y-4 pt-2">
                        
                        <!-- Transfer Bank -->
                        <label class="block cursor-pointer">
                            <div class="border-2 rounded-2xl p-5 transition-all {{ $payment_method === 'transfer_bank' ? 'border-primary bg-primary/5' : 'border-base-200 hover:border-primary/50' }}">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" wire:model.live="payment_method" value="transfer_bank" class="radio radio-primary">
                                    <div class="flex-1">
                                        <div class="font-bold text-base-content text-lg">Transfer Bank</div>
                                        <div class="text-sm text-base-content/60">Transfer manual ke rekening bank apotek</div>
                                    </div>
                                    <div class="hidden sm:flex gap-2">
                                        <x-badge value="BCA" class="badge-info text-info-content font-bold" />
                                        <x-badge value="BRI" class="badge-info text-info-content font-bold" />
                                    </div>
                                </div>

                                <!-- Bank Selection -->
                                @if($payment_method === 'transfer_bank')
                                    <div class="mt-6 pt-6 border-t border-base-300 animate-fade-in-up">
                                        @error('selected_bank') <div class="text-error text-sm mb-3">{{ $message }}</div> @enderror
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                                            <!-- BCA -->
                                            <label class="cursor-pointer">
                                                <div class="border-2 rounded-xl p-4 text-center transition-all {{ $selected_bank === 'BCA' ? 'border-primary shadow-premium bg-base-100 ring-2 ring-primary/20' : 'border-base-200 bg-base-100/50 hover:border-primary/50' }}">
                                                    <input type="radio" name="selected_bank" wire:model.live="selected_bank" value="BCA" class="hidden">
                                                    <div class="font-extrabold text-info text-xl mb-2">BCA</div>
                                                    <div class="text-[10px] uppercase font-bold text-base-content/50 tracking-wider mb-1">No. Rekening</div>
                                                    <div class="text-lg text-base-content font-mono font-bold tracking-wide select-all">1234567890</div>
                                                    <div class="text-[0.7rem] font-semibold text-base-content/70 mt-2">a.n ObatinYuk</div>
                                                </div>
                                            </label>

                                            <!-- BRI -->
                                            <label class="cursor-pointer">
                                                <div class="border-2 rounded-xl p-4 text-center transition-all {{ $selected_bank === 'BRI' ? 'border-primary shadow-premium bg-base-100 ring-2 ring-primary/20' : 'border-base-200 bg-base-100/50 hover:border-primary/50' }}">
                                                    <input type="radio" name="selected_bank" wire:model.live="selected_bank" value="BRI" class="hidden">
                                                    <div class="font-extrabold text-info text-xl mb-2">BRI</div>
                                                    <div class="text-[10px] uppercase font-bold text-base-content/50 tracking-wider mb-1">No. Rekening</div>
                                                    <div class="text-lg text-base-content font-mono font-bold tracking-wide select-all">0987654321</div>
                                                    <div class="text-[0.7rem] font-semibold text-base-content/70 mt-2">a.n ObatinYuk</div>
                                                </div>
                                            </label>

                                            <!-- CIMB -->
                                            <label class="cursor-pointer">
                                                <div class="border-2 rounded-xl p-4 text-center transition-all {{ $selected_bank === 'CIMB Niaga' ? 'border-primary shadow-premium bg-base-100 ring-2 ring-primary/20' : 'border-base-200 bg-base-100/50 hover:border-primary/50' }}">
                                                    <input type="radio" name="selected_bank" wire:model.live="selected_bank" value="CIMB Niaga" class="hidden">
                                                    <div class="font-extrabold text-info text-xl mb-2">CIMB</div>
                                                    <div class="text-[10px] uppercase font-bold text-base-content/50 tracking-wider mb-1">No. Rekening</div>
                                                    <div class="text-lg text-base-content font-mono font-bold tracking-wide select-all">1122334455</div>
                                                    <div class="text-[0.7rem] font-semibold text-base-content/70 mt-2">a.n ObatinYuk</div>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Upload Bukti Pembayaran removed from here -->
                                    </div>
                                @endif
                            </div>
                        </label>

                        <!-- COD -->
                        <label class="block cursor-pointer">
                            <div class="border-2 rounded-2xl p-5 transition-all {{ $payment_method === 'cod' ? 'border-primary bg-primary/5' : 'border-base-200 hover:border-primary/50' }}">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" wire:model.live="payment_method" value="cod" class="radio radio-primary">
                                    <div class="flex-1">
                                        <div class="font-bold text-base-content text-lg">COD (Bayar di Tempat)</div>
                                        <div class="text-sm text-base-content/60">Bayar tunai langsung saat obat tiba di rumah Anda</div>
                                    </div>
                                    <x-badge value="Cash Only" class="badge-warning text-warning-content font-bold hidden sm:inline-flex" />
                                </div>
                            </div>
                        </label>

                    </div>
                </x-card>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="xl:col-span-4">
                <x-card class="bg-base-100 border-base-200 shadow-premium sticky top-24">
                    <h3 class="text-xl font-bold text-base-content mb-6 pb-4 border-b border-base-200 flex items-center gap-2">
                        <x-icon name="o-shopping-bag" class="w-6 h-6 text-primary" /> Pesanan Anda
                    </h3>

                    <!-- Mini Cart List -->
                    <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin">
                        @foreach($items as $item)
                            <div class="flex gap-4 items-center">
                                <div class="w-14 h-14 bg-base-200 rounded-xl flex items-center justify-center shrink-0 p-1">
                                    @if($item->obat->image)
                                        <img src="{{ Storage::url($item->obat->image) }}" class="w-full h-full object-contain filter drop-shadow-sm">
                                    @else
                                        <x-icon name="o-beaker" class="w-6 h-6 text-base-content/30" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-base-content text-sm line-clamp-1">{{ $item->obat->name }}</div>
                                    <div class="text-xs text-base-content/60 mt-0.5">{{ $item->quantity }} x Rp{{ number_format($item->obat->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="font-extrabold text-base-content text-sm shrink-0">
                                    Rp{{ number_format($item->obat->price * $item->quantity, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="border-t border-dashed border-base-300 pt-4 mb-6 space-y-3">
                        <div class="flex justify-between text-base-content/70 text-sm">
                            <span>Subtotal</span>
                            <span class="font-medium text-base-content">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base-content/70 text-sm">
                            <span>Biaya Layanan (Platform)</span>
                            <span class="font-medium text-base-content">Rp{{ number_format($service_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base-content/70 text-sm">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-success uppercase">Gratis</span>
                        </div>
                        @if($payment_method)
                            <div class="flex justify-between text-base-content/70 text-sm">
                                <span>Metode</span>
                                <span class="font-bold text-base-content">{{ $payment_method === 'cod' ? 'COD' : 'Transfer ' . ($selected_bank ?: '') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Grand Total -->
                    <div class="bg-gradient-to-r from-primary/10 to-info/10 rounded-2xl p-4 flex justify-between items-center mb-6 border border-primary/20">
                        <span class="font-bold text-base-content">Total Tagihan</span>
                        <span class="text-2xl font-black text-primary">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <x-slot:actions>
                        <x-button type="submit" label="Bayar Sekarang" class="btn-primary w-full rounded-2xl h-14 text-lg shadow-premium shadow-primary/30" icon-right="o-arrow-right" spinner="processCheckout" />
                    </x-slot:actions>
                </x-card>
            </div>

        </div>
    </x-form>
</div>
