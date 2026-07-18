<x-layouts.app>
    <x-slot:title>
        ObatinYuk - Sistem Pakar Kesehatan Digital
    </x-slot:title>

    {{-- Background Decorative Blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-[10%] -left-[10%] w-[500px] h-[500px] rounded-full bg-success/20 blur-[80px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[600px] h-[600px] rounded-full bg-info/20 blur-[100px]"></div>
    </div>

    <div class="container mx-auto px-4 py-20 lg:py-32 flex flex-col items-center justify-center text-center relative z-10">
        
        {{-- Hero Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-base-100/80 backdrop-blur border border-base-200 shadow-sm text-sm font-semibold text-primary mb-8 animate-fade-in-up">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-success"></span>
            </span>
            Sistem Pakar Forward Chaining + Gemini AI
        </div>

        {{-- Hero Headline --}}
        <h1 class="text-4xl lg:text-6xl font-extrabold text-base-content leading-tight mb-6 max-w-4xl tracking-tight">
            Cek Gejala Anda dengan AI dalam <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-info">30 Detik</span>
        </h1>

        {{-- Hero Description --}}
        <p class="text-lg lg:text-xl text-base-content/70 mb-10 max-w-2xl leading-relaxed">
            Konsultasikan kesehatan Anda secara personal dengan Asisten Apoteker Virtual. Teknologi AI Hybrid kami menjamin rekomendasi obat yang akurat, aman, dan dapat dipercaya.
        </p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <x-button link="/konsultasi" label="Mulai Konsultasi AI" class="btn-primary rounded-xl px-8 shadow-premium shadow-primary/30" icon-right="o-sparkles" />
            <x-button link="/katalog" label="Beli Obat" class="btn-ghost bg-base-100/50 backdrop-blur rounded-xl px-8 border border-base-200" icon-right="o-shopping-bag" />
        </div>

        {{-- Feature Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 max-w-6xl w-full">
            
            {{-- Feature 1 --}}
            <div class="glass-effect p-8 rounded-3xl text-left hover:-translate-y-2 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-success/10 flex items-center justify-center text-success mb-6">
                    <x-icon name="o-check-badge" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-base-content mb-3">Sistem Pakar Akurat</h3>
                <p class="text-base-content/70 leading-relaxed">
                    Menggunakan algoritma Forward Chaining untuk memastikan setiap rekomendasi obat 100% presisi sesuai database gejala medis.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="glass-effect p-8 rounded-3xl text-left hover:-translate-y-2 transition-all duration-300 delay-100">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6">
                    <x-icon name="o-chat-bubble-left-right" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-base-content mb-3">Interaksi Natural</h3>
                <p class="text-base-content/70 leading-relaxed">
                    Didukung oleh Gemini LLM, mengobrol dengan AI kami terasa hangat dan personal seperti berkonsultasi langsung dengan apoteker.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="glass-effect p-8 rounded-3xl text-left hover:-translate-y-2 transition-all duration-300 delay-200">
                <div class="w-14 h-14 rounded-2xl bg-info/10 flex items-center justify-center text-info mb-6">
                    <x-icon name="o-shopping-bag" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-base-content mb-3">Beli Instan</h3>
                <p class="text-base-content/70 leading-relaxed">
                    Langsung masukkan rekomendasi obat ke keranjang dan proses checkout dengan cepat dalam satu platform yang terintegrasi.
                </p>
            </div>

        </div>
    </div>
</x-layouts.app>
