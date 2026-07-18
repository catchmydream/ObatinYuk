<div class="px-4 py-8 max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs mb-8 text-base-content/60">
        <ul>
            <li><a href="/"><x-icon name="o-home" class="w-4 h-4 mr-1"/> Beranda</a></li>
            <li class="font-bold text-base-content">Profil Saya</li>
        </ul>
    </div>

    <div class="flex items-center gap-3 mb-8">
        <x-icon name="o-user-circle" class="w-8 h-8 text-primary" />
        <h1 class="text-3xl font-extrabold text-base-content font-sans tracking-tight">Pengaturan Profil</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Sidebar Profile Card -->
        <div class="md:col-span-4">
            <x-card class="bg-base-100 border border-base-200 shadow-premium text-center pt-6 pb-4">
                <div class="avatar placeholder mx-auto mb-4">
                    <div class="bg-primary/10 text-primary rounded-full w-24 h-24 ring-4 ring-primary/20">
                        <span class="text-3xl font-black uppercase">{{ substr($name, 0, 1) }}</span>
                    </div>
                </div>
                <h3 class="text-xl font-extrabold text-base-content leading-tight mb-1">{{ $name }}</h3>
                <p class="text-sm text-base-content/60 mb-4">{{ $email }}</p>
                <div class="badge badge-primary badge-outline font-semibold capitalize">{{ Auth::user()->role }}</div>

                <div class="border-t border-dashed border-base-200 mt-6 pt-6 text-left text-xs space-y-2 text-base-content/70">
                    <div class="flex items-center gap-2">
                        <x-icon name="o-envelope" class="w-4 h-4 text-base-content/40" />
                        <span class="truncate">{{ $email }}</span>
                    </div>
                    @if($phone_number)
                        <div class="flex items-center gap-2">
                            <x-icon name="o-phone" class="w-4 h-4 text-base-content/40" />
                            <span>{{ $phone_number }}</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        <!-- Forms Column -->
        <div class="md:col-span-8 space-y-6">
            <x-form wire:submit="save" no-separator>
                <div class="space-y-6">
                    <!-- Personal Info Card -->
                    <x-card title="Informasi Pribadi" class="bg-base-100 border border-base-200 shadow-sm" icon="o-identification">
                        <div class="space-y-6 pt-4">
                            <!-- Nama Lengkap -->
                            <div class="form-control w-full">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">NAMA LENGKAP</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-user" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        placeholder="Nama lengkap Anda" 
                                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                                        style="padding-left: 3.75rem;"
                                    />
                                </div>
                                @error('name')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <!-- Alamat Email -->
                            <div class="form-control w-full">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">ALAMAT EMAIL</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/50 h-6">
                                        <x-icon name="o-envelope" class="w-5 h-5 text-base-content/30" />
                                    </div>
                                    <input 
                                        type="email" 
                                        wire:model="email" 
                                        disabled 
                                        class="input input-bordered w-full rounded-2xl bg-base-200/50 border-2 border-base-300/50 text-base-content/50 text-sm h-12 cursor-not-allowed"
                                        style="padding-left: 3.75rem;"
                                    />
                                </div>
                                <span class="text-[11px] text-base-content/50 mt-1.5 pl-1">Alamat email tidak dapat diubah</span>
                            </div>
                            
                            <!-- Nomor Telepon -->
                            <div class="form-control w-full">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">NOMOR TELEPON (WHATSAPP)</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-phone" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <input 
                                        type="text" 
                                        wire:model="phone_number" 
                                        placeholder="081234567890" 
                                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                                        style="padding-left: 3.75rem;"
                                    />
                                </div>
                                @error('phone_number')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <!-- Alamat Lengkap -->
                            <div class="form-control w-full">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">ALAMAT LENGKAP</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute left-0 top-3 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-map-pin" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <textarea 
                                        wire:model="address" 
                                        placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kota, Provinsi..." 
                                        rows="3" 
                                        class="textarea textarea-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm pt-3"
                                        style="padding-left: 3.75rem;"
                                    ></textarea>
                                </div>
                                @error('address')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </x-card>

                    <!-- Security Card -->
                    <x-card title="Ubah Password" class="bg-base-100 border border-base-200 shadow-sm" icon="o-lock-closed" subtitle="Biarkan kosong jika tidak ingin mengubah password">
                        <div class="space-y-6 pt-4">
                            <!-- Password Saat Ini -->
                            <div class="form-control w-full" x-data="{ show: false }">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">PASSWORD SAAT INI</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <input 
                                        :type="show ? 'text' : 'password'" 
                                        wire:model="current_password" 
                                        placeholder="Password saat ini" 
                                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                                        style="padding-left: 3.75rem; padding-right: 5.5rem;"
                                    />
                                    <button 
                                        type="button" 
                                        @click="show = !show" 
                                        class="absolute right-4 text-sm font-semibold text-primary hover:text-primary-focus select-none focus:outline-none"
                                        x-text="show ? 'Sembunyikan' : 'Tampilkan'"
                                    ></button>
                                </div>
                                @error('current_password')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Password Baru -->
                            <div class="form-control w-full" x-data="{ show: false }">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">PASSWORD BARU</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <input 
                                        :type="show ? 'text' : 'password'" 
                                        wire:model="new_password" 
                                        placeholder="Minimal 6 karakter" 
                                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                                        style="padding-left: 3.75rem; padding-right: 5.5rem;"
                                    />
                                    <button 
                                        type="button" 
                                        @click="show = !show" 
                                        class="absolute right-4 text-sm font-semibold text-primary hover:text-primary-focus select-none focus:outline-none"
                                        x-text="show ? 'Sembunyikan' : 'Tampilkan'"
                                    ></button>
                                </div>
                                @error('new_password')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password Baru -->
                            <div class="form-control w-full" x-data="{ show: false }">
                                <label class="label pb-1.5">
                                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">KONFIRMASI PASSWORD BARU</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                                        <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/40" />
                                    </div>
                                    <input 
                                        :type="show ? 'text' : 'password'" 
                                        wire:model="new_password_confirmation" 
                                        placeholder="Ulangi password baru" 
                                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                                        style="padding-left: 3.75rem; padding-right: 5.5rem;"
                                    />
                                    <button 
                                        type="button" 
                                        @click="show = !show" 
                                        class="absolute right-4 text-sm font-semibold text-primary hover:text-primary-focus select-none focus:outline-none"
                                        x-text="show ? 'Sembunyikan' : 'Tampilkan'"
                                    ></button>
                                </div>
                                @error('new_password_confirmation')
                                    <label class="label pt-1.5">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </x-card>

                    <div class="flex justify-end pt-2">
                        <x-button type="submit" label="Simpan Perubahan" class="btn-primary w-full sm:w-auto px-8 rounded-xl shadow-md shadow-primary/20 text-white font-bold" icon="o-check" spinner="save" />
                    </div>
                </div>
            </x-form>
        </div>
    </div>
</div>
