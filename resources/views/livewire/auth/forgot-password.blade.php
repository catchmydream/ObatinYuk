<div class="flex items-center justify-center min-h-[80vh] px-4 py-12">
    <x-card class="w-full max-w-md shadow-premium border-base-200" title="Lupa Password">
        <div class="text-sm text-base-content/70 mb-6">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mereset password.</div>

        <x-form wire:submit="sendResetLink">
            <x-input label="Email" wire:model="email" type="email" placeholder="contoh@email.com" icon="o-envelope" />
            
            <x-slot:actions>
                <x-button label="Kirim Tautan" type="submit" class="btn-primary w-full rounded-xl mt-2" icon-right="o-paper-airplane" spinner="sendResetLink" />
            </x-slot:actions>
        </x-form>

        <div class="text-center mt-6 text-sm text-base-content/70">
            Ingat password Anda? <a href="/login" class="text-primary font-bold hover:underline">Masuk di sini</a>
        </div>
    </x-card>
</div>
