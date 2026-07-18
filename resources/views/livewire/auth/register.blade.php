<div class="flex items-center justify-center min-h-[80vh] px-4 py-12">
    <x-card class="w-full max-w-md shadow-premium border-base-200" title="Daftar Akun Baru">
        <div class="text-sm text-base-content/70 mb-6">Buat akun gratis untuk mulai berkonsultasi.</div>

        <x-form wire:submit="register" class="space-y-6">
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
                        placeholder="Nama Anda" 
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

            <!-- Email -->
            <div class="form-control w-full">
                <label class="label pb-1.5">
                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">ALAMAT EMAIL</span>
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                        <x-icon name="o-envelope" class="w-5 h-5 text-base-content/40" />
                    </div>
                    <input 
                        type="email" 
                        wire:model="email" 
                        placeholder="contoh@email.com" 
                        class="input input-bordered w-full rounded-2xl bg-base-100 border-2 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all text-sm h-12"
                        style="padding-left: 3.75rem;"
                    />
                </div>
                @error('email')
                    <label class="label pt-1.5">
                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                    </label>
                @enderror
            </div>
            
            <!-- Password -->
            <div class="form-control w-full" x-data="{ show: false }">
                <label class="label pb-1.5">
                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">PASSWORD</span>
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                        <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/40" />
                    </div>
                    <input 
                        :type="show ? 'text' : 'password'" 
                        wire:model="password" 
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
                @error('password')
                    <label class="label pt-1.5">
                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-control w-full" x-data="{ show: false }">
                <label class="label pb-1.5">
                    <span class="label-text uppercase text-[11px] font-extrabold tracking-wider text-base-content/60">KONFIRMASI PASSWORD</span>
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-0 flex items-center justify-center pl-4 pr-3 border-r border-base-300/80 h-6">
                        <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/40" />
                    </div>
                    <input 
                        :type="show ? 'text' : 'password'" 
                        wire:model="password_confirmation" 
                        placeholder="Ulangi password" 
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
                @error('password_confirmation')
                    <label class="label pt-1.5">
                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <x-slot:actions>
                <x-button label="Daftar Sekarang" type="submit" class="btn-primary w-full rounded-xl mt-2" icon-right="o-arrow-right" spinner="register" />
            </x-slot:actions>
        </x-form>

        <div class="text-center mt-6 text-sm text-base-content/70">
            Sudah punya akun? <a href="/login" class="text-primary font-bold hover:underline">Masuk di sini</a>
        </div>
    </x-card>
</div>
