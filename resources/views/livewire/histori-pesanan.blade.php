<div class="px-4 py-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="/katalog" class="text-base-content/60 hover:text-primary transition-colors text-sm font-medium flex items-center gap-1 mb-4">
            <x-icon name="o-arrow-left" class="w-4 h-4" /> Kembali ke Katalog
        </a>
        <h1 class="text-3xl font-extrabold text-base-content flex items-center gap-3">
            <x-icon name="o-archive-box" class="w-8 h-8 text-primary" />
            Pesanan Saya
        </h1>
        <p class="text-base-content/70 mt-2">Lacak status pesanan dan riwayat belanja Anda.</p>
    </div>

    @if($groupedOrders->isEmpty())
        <div class="bg-base-100 border border-base-200 rounded-3xl p-12 text-center shadow-sm">
            <div class="w-24 h-24 bg-base-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-icon name="o-archive-box-x-mark" class="w-12 h-12 text-base-content/30" />
            </div>
            <h3 class="text-2xl font-bold text-base-content mb-2">Belum Ada Pesanan</h3>
            <p class="text-base-content/60 mb-8 max-w-md mx-auto">Anda belum membuat pesanan apapun. Yuk, mulai cari obat atau produk kesehatan yang Anda butuhkan di katalog kami!</p>
            <x-button link="/katalog" class="btn-primary rounded-xl shadow-premium shadow-primary/20" icon="o-building-storefront" label="Mulai Belanja" />
        </div>
    @else
        <div class="flex flex-col gap-6">
            @foreach($groupedOrders as $checkoutId => $items)
                @php
                    $firstOrder = $items->first();
                    $totalPrice = $items->sum('total_price');
                    $serviceFee = $items->sum('service_fee');
                    
                    $statusConfig = [
                        'Menunggu Pembayaran' => ['icon' => 'o-clock', 'color' => 'warning', 'badge' => 'badge-warning'],
                        'Menunggu Verifikasi' => ['icon' => 'o-magnifying-glass', 'color' => 'info', 'badge' => 'badge-info'],
                        'Diproses'           => ['icon' => 'o-cog-6-tooth', 'color' => 'primary', 'badge' => 'badge-primary'],
                        'Dikirim'            => ['icon' => 'o-truck', 'color' => 'secondary', 'badge' => 'badge-secondary'],
                        'Selesai'            => ['icon' => 'o-check-circle', 'color' => 'success', 'badge' => 'badge-success'],
                        'Dibatalkan'         => ['icon' => 'o-x-circle', 'color' => 'error', 'badge' => 'badge-error'],
                    ];
                    $cfg = $statusConfig[$firstOrder->status] ?? ['icon'=>'o-archive-box', 'color'=>'neutral', 'badge'=>'badge-neutral'];
                    $displayId = str_starts_with($checkoutId, 'SINGLE-') ? '#' . str_pad(str_replace('SINGLE-', '', $checkoutId), 5, '0', STR_PAD_LEFT) : $checkoutId;
                @endphp

                <x-card class="bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all overflow-hidden !p-0">
                    <!-- Status Header -->
                    <div class="bg-{{ $cfg['color'] }}/10 border-b border-{{ $cfg['color'] }}/20 px-6 py-4 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <x-icon name="{{ $cfg['icon'] }}" class="w-5 h-5 text-{{ $cfg['color'] }}" />
                            <span class="font-bold text-{{ $cfg['color'] }}">{{ $firstOrder->status }}</span>
                            <span class="text-base-content/40 hidden sm:inline">•</span>
                            <span class="text-sm text-base-content/60 font-medium">{{ $firstOrder->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="text-sm font-semibold text-base-content/70">
                            Invoice: <span class="text-base-content font-mono font-bold">{{ $displayId }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Left: List of Items in this checkout -->
                            <div class="flex-1 space-y-4 divide-y divide-base-100">
                                @foreach($items as $index => $order)
                                    <div class="flex gap-4 {{ $index > 0 ? 'pt-4 border-t border-base-100' : '' }} items-center">
                                        <!-- Product Image -->
                                        <div class="w-16 h-16 bg-base-200 rounded-xl flex-shrink-0 flex items-center justify-center p-2 border border-base-200">
                                            @if($order->obat->image)
                                                <img src="{{ Storage::url($order->obat->image) }}" alt="{{ $order->obat->name }}" class="w-full h-full object-contain filter drop-shadow-sm">
                                            @else
                                                <x-icon name="o-beaker" class="w-6 h-6 text-base-content/20" />
                                            @endif
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-base-content text-sm sm:text-base truncate">{{ $order->obat->name }}</h4>
                                            <p class="text-xs sm:text-sm text-base-content/60 mt-0.5">{{ $order->quantity }} x Rp{{ number_format($order->obat->price, 0, ',', '.') }}</p>
                                        </div>

                                        <!-- Subtotal price for this item -->
                                        <div class="text-sm font-bold text-base-content shrink-0">
                                            Rp{{ number_format($order->obat->price * $order->quantity, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Meta Info Footer inside Card -->
                                <div class="pt-4 border-t border-base-200 flex flex-wrap items-center gap-2">
                                    @if($firstOrder->payment_method)
                                        <x-badge value="{{ $firstOrder->payment_method }}" class="badge-ghost font-semibold text-xs" icon="o-credit-card" />
                                    @endif
                                    
                                    @if($firstOrder->status === 'Menunggu Pembayaran' && $firstOrder->payment_deadline)
                                        <div class="flex items-center gap-1 text-xs font-semibold text-warning bg-warning/10 px-3 py-1 rounded-full border border-warning/20">
                                            <x-icon name="o-clock" class="w-4 h-4" />
                                            Batas waktu: {{ $firstOrder->payment_deadline->format('d M Y, H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Total Pricing & Unified Action -->
                            <div class="md:text-right flex flex-col justify-between items-start md:items-end min-w-[200px] border-t md:border-t-0 md:border-l border-base-200 pt-6 md:pt-0 md:pl-6">
                                <div class="mb-6">
                                    <p class="text-sm text-base-content/60 font-medium mb-1">Total Tagihan ({{ $items->count() }} Produk)</p>
                                    <div class="text-3xl font-black text-primary">Rp{{ number_format($totalPrice, 0, ',', '.') }}</div>
                                    @if($serviceFee > 0)
                                        <p class="text-[11px] text-base-content/50 mt-1">Termasuk biaya layanan Rp{{ number_format($serviceFee, 0, ',', '.') }}</p>
                                    @endif
                                </div>

                                <div class="w-full sm:w-auto">
                                    @if(!str_contains($firstOrder->payment_method, 'COD') && $firstOrder->status === 'Menunggu Pembayaran')
                                        @if($selectedCheckoutId === $checkoutId)
                                            <x-button wire:click="$set('selectedCheckoutId', null)" class="btn-ghost btn-sm w-full sm:w-auto" label="Batal" />
                                        @else
                                            <x-button wire:click="uploadBukti('{{ $checkoutId }}')" class="btn-primary rounded-xl shadow-premium shadow-primary/20 w-full sm:w-auto text-white" icon="o-arrow-up-tray" label="Upload Bukti Transfer" />
                                        @endif
                                    @elseif($firstOrder->payment_proof)
                                        <a href="{{ Storage::url($firstOrder->payment_proof) }}" target="_blank" class="btn btn-outline btn-sm rounded-xl border-base-300 hover:border-primary hover:bg-primary/5 hover:text-primary w-full sm:w-auto">
                                            <x-icon name="o-document-text" class="w-4 h-4" /> Lihat Bukti
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Upload Panel (inline expansion per checkout group) -->
                        @if($selectedCheckoutId === $checkoutId)
                            <div class="mt-6 border border-base-200 bg-base-200/30 rounded-2xl p-6 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
                                
                                <h4 class="font-bold text-lg text-base-content mb-4 flex items-center gap-2">
                                    <x-icon name="o-credit-card" class="w-5 h-5 text-primary" />
                                    Instruksi Pembayaran
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="bg-base-100 p-4 rounded-xl border border-base-200 shadow-sm">
                                        @if(str_contains($firstOrder->payment_method, 'Transfer Bank'))
                                            <p class="text-sm text-base-content/60 mb-2">Transfer ke Rekening:</p>
                                            <div class="flex items-center justify-between mb-2">
                                                <strong class="text-lg text-primary">
                                                    @if(str_contains($firstOrder->payment_method, 'BCA')) BCA
                                                    @elseif(str_contains($firstOrder->payment_method, 'BRI')) BRI
                                                    @elseif(str_contains($firstOrder->payment_method, 'CIMB')) CIMB Niaga
                                                    @else Bank Transfer
                                                    @endif
                                                </strong>
                                                <div class="bg-base-200 px-3 py-1 rounded-lg font-mono font-bold text-lg text-base-content">
                                                    @if(str_contains($firstOrder->payment_method, 'BCA')) 1234567890
                                                    @elseif(str_contains($firstOrder->payment_method, 'BRI')) 0987654321
                                                    @elseif(str_contains($firstOrder->payment_method, 'CIMB')) 1122334455
                                                    @else 9999888877
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-sm text-base-content/80">a.n. <strong class="text-base-content">ObatinYuk</strong></p>
                                        @endif
                                    </div>
                                    
                                    <div class="bg-base-100 p-4 rounded-xl border border-base-200 shadow-sm flex flex-col justify-center">
                                        <p class="text-sm text-base-content/60 mb-1">Total yang harus ditransfer:</p>
                                        <div class="text-3xl font-black text-primary mb-1">Rp{{ number_format($totalPrice, 0, ',', '.') }}</div>
                                        <p class="text-xs text-base-content/50">Harap transfer nominal pas agar proses verifikasi lebih cepat.</p>
                                    </div>
                                </div>

                                <form wire:submit.prevent="submitBukti" class="bg-base-100 p-5 rounded-xl border border-base-200 shadow-sm">
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-base-content mb-2">Upload Bukti Transfer</label>
                                        <x-file wire:model="payment_proof" accept="image/*" class="file-input-bordered file-input-primary w-full" />
                                        @error('payment_proof') <p class="text-error text-xs mt-2 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <x-button type="button" wire:click="$set('selectedCheckoutId', null)" class="btn-ghost" label="Batal" />
                                        <x-button type="submit" wire:loading.attr="disabled" class="btn-primary rounded-xl shadow-premium shadow-primary/20 text-white font-bold" icon="o-paper-airplane" label="Kirim Bukti" spinner="submitBukti" />
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
