<div class="w-full max-w-7xl mx-auto px-4 py-6">
    
    {{-- Header Banner --}}
    <div class="mb-6 p-6 sm:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6" style="background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #3b82f6 100%); color: #ffffff;">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-3" style="background-color: rgba(255, 255, 255, 0.2); color: #ffffff;">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                Sistem Pakar Forward Chaining Active
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-2">Konsultasi Kesehatan AI</h1>
            <p class="text-blue-100 text-sm sm:text-base leading-relaxed" style="color: #e0effe;">
                Konsultasikan keluhan atau gejala penyakit Anda. Sistem Pakar kami akan menganalisis kecocokan fakta gejala dengan database obat secara real-time dan transparan.
            </p>
        </div>

        <div class="relative z-10 flex items-center gap-3 shrink-0">
            <button wire:click="clearChat" class="btn btn-ghost text-white border border-white/30 rounded-xl" style="background-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                <x-icon name="o-arrow-path" class="w-5 h-5" />
                <span>Reset Konsultasi</span>
            </button>
        </div>
    </div>

    {{-- Bulletproof Dual-Panel Flex Layout --}}
    <div class="flex flex-col lg:flex-row gap-6 w-full items-start">
        
        {{-- LEFT PANEL: CHAT INTERFACE (60% Desktop Width) --}}
        <div class="w-full lg:w-[60%] xl:w-[65%] bg-white rounded-3xl shadow-xl border border-gray-200 flex flex-col h-[650px] overflow-hidden shrink-0">
            
            {{-- Chat Header --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-md" style="background: linear-gradient(135deg, #2563eb, #4f46e5); color: #ffffff;">
                        <x-icon name="o-sparkles" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base leading-tight">Asisten Apoteker AI</h3>
                        <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                            <span class="w-2.5 h-2.5 rounded-full {{ $isLoading ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                            <span class="font-medium">{{ $isLoading ? 'Sedang menganalisis gejala...' : 'Online & Ready' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages Body --}}
            <div id="konsultasi-chat-box" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50 scroll-smooth">
                @foreach($messages as $msg)
                    @if($msg['role'] === 'user')
                        <div class="flex justify-end">
                            <div class="max-w-[85%]">
                                <div class="text-white rounded-2xl rounded-tr-xs p-4 text-sm leading-relaxed shadow-sm" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #ffffff;">
                                    {{ $msg['content'] }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1 text-right px-1">{{ $msg['time'] }}</div>
                            </div>
                        </div>
                    @else
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 border border-blue-200 text-blue-700 flex items-center justify-center shrink-0 mt-1 shadow-xs">
                                <x-icon name="o-heart" class="w-4 h-4 text-blue-600" />
                            </div>
                            <div class="max-w-[85%]">
                                <div class="bg-white border border-gray-200 text-gray-900 rounded-2xl rounded-tl-xs p-4 text-sm leading-relaxed shadow-sm">
                                    {!! nl2br(e($msg['content'])) !!}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1 px-1">{{ $msg['time'] }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Loading State --}}
                @if($isLoading)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-1 shadow-xs">
                            <x-icon name="o-sparkles" class="w-4 h-4 animate-spin" />
                        </div>
                        <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-xs px-5 py-3 shadow-sm flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-600">Menganalisis matriks gejala & obat...</span>
                            <div class="flex gap-1">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-bounce"></span>
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick Prompts & Input Area --}}
            <div class="p-4 bg-white border-t border-gray-200 space-y-3">
                @if(count($messages) <= 2)
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-gray-400 self-center mr-1">Contoh Keluhan:</span>
                        <button wire:click="sendMessage('Saya merasa demam tinggi, pusing, dan sakit kepala')" class="btn btn-xs btn-outline btn-primary rounded-full">🤒 Demam & Sakit Kepala</button>
                        <button wire:click="sendMessage('Saya batuk berdahak dan hidung tersumbat')" class="btn btn-xs btn-outline btn-primary rounded-full">🤧 Batuk & Hidung Tersumbat</button>
                        <button wire:click="sendMessage('Rekomendasikan suplemen vitamin C untuk daya tahan tubuh')" class="btn btn-xs btn-outline btn-primary rounded-full">💊 Vitamin Daya Tahan</button>
                    </div>
                @endif

                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <input 
                        type="text" 
                        wire:model="inputMessage" 
                        placeholder="Tuliskan keluhan atau gejala yang Anda rasakan di sini..."
                        class="flex-1 bg-gray-50 border border-gray-300 focus:border-blue-600 focus:bg-white rounded-2xl px-4 py-3 text-sm outline-none transition-all text-gray-900"
                        {{ $isLoading ? 'disabled' : '' }}
                    />
                    <button 
                        type="submit" 
                        class="btn btn-primary text-white rounded-2xl px-6 shadow-md flex items-center gap-2"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                        <span>Kirim</span>
                        <svg class="w-4 h-4 -rotate-45" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- RIGHT PANEL: EXPERT SYSTEM DASHBOARD (40% Desktop Width) --}}
        <div class="w-full lg:w-[40%] xl:w-[35%] space-y-6 shrink-0">
            
            {{-- Dashboard Container --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 p-6 space-y-6">
                
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                            <x-icon name="o-cpu-chip" class="w-6 h-6 text-indigo-600" />
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-900 text-lg leading-tight">Panel Sistem Pakar</h3>
                            <p class="text-xs text-gray-500">Transparansi Algoritma Forward Chaining</p>
                        </div>
                    </div>
                    <span class="badge badge-primary badge-outline font-mono text-xs">Engine v2.0</span>
                </div>

                @if(!$hasAnalyzed)
                    <div class="py-10 text-center space-y-3">
                        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
                            <x-icon name="o-magnifying-glass-circle" class="w-10 h-10" />
                        </div>
                        <h4 class="font-bold text-gray-800 text-base">Belum Ada Analisis</h4>
                        <p class="text-xs text-gray-500 max-w-xs mx-auto leading-relaxed">
                            Silakan tuliskan keluhan Anda di obrolan sebelah kiri. Hasil identifikasi gejala dan skor kecocokan obat akan langsung muncul di panel ini.
                        </p>
                    </div>
                @else
                    
                    {{-- 1. Detected Symptoms Section --}}
                    <div class="space-y-2.5">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <x-icon name="o-funnel" class="w-4 h-4 text-blue-600" />
                            Fakta Gejala Terdeteksi
                        </h4>

                        @if(empty($detectedGejalas))
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-center gap-2">
                                <x-icon name="o-exclamation-triangle" class="w-4 h-4 shrink-0 text-amber-600" />
                                <span>Tidak ada gejala medis spesifik yang cocok dengan fakta di database.</span>
                            </div>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($detectedGejalas as $g)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold shadow-xs">
                                        <x-icon name="o-check-circle" class="w-3.5 h-3.5 text-blue-600" />
                                        {{ $g }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- 2. Recommendations & Matching Score Section --}}
                    <div class="space-y-3 pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <x-icon name="o-chart-bar" class="w-4 h-4 text-emerald-600" />
                            Hasil Perhitungan Kecocokan Obat
                        </h4>

                        @if(empty($recommendations))
                            <p class="text-xs text-gray-500 italic">Tidak ada obat yang memenuhi ambang skor kecocokan.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($recommendations as $rec)
                                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-3">
                                        
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h5 class="font-bold text-gray-900 text-sm leading-snug">{{ $rec['name'] }}</h5>
                                                <div class="flex items-center gap-2 mt-1 text-xs">
                                                    <span class="badge badge-sm {{ $rec['category'] === 'herbal' ? 'badge-success' : 'badge-ghost' }}">
                                                        {{ ucfirst($rec['category']) }}
                                                    </span>
                                                    <span class="text-gray-600 font-bold">Rp {{ number_format($rec['price'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>

                                            {{-- Score Badge --}}
                                            <div class="text-right shrink-0">
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg {{ $rec['score'] >= 75 ? 'bg-emerald-100 text-emerald-900' : 'bg-blue-100 text-blue-900' }} font-extrabold text-xs">
                                                    <span>{{ $rec['score'] }}%</span>
                                                    <span class="text-[10px] opacity-75">Match</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Reason & Formula --}}
                                        <div class="text-xs text-gray-700 bg-white p-2.5 rounded-xl border border-gray-200 space-y-1">
                                            <p class="font-medium"><strong>Alasan:</strong> {{ $rec['reason'] }}</p>
                                            @if(!empty($rec['matched_gejala']))
                                                <p class="text-[11px] text-gray-500"><strong>Rumus FC:</strong> ({{ count($rec['matched_gejala']) }} cocok / total gejala obat) &times; 100%</p>
                                            @endif
                                        </div>

                                        {{-- Action Button --}}
                                        <a 
                                            href="{{ route('produk.detail', $rec['id']) }}" 
                                            class="w-full btn btn-sm btn-primary rounded-xl text-white flex items-center justify-center gap-2 shadow-xs"
                                            wire:navigate
                                        >
                                            <x-icon name="o-eye" class="w-4 h-4" />
                                            <span>Lihat Detail Produk</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- 3. Execution Log Trace --}}
                    <div class="space-y-2 pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <x-icon name="o-command-line" class="w-4 h-4 text-indigo-600" />
                            Jejak Evaluasi Aturan (Rule Trace)
                        </h4>
                        <div class="p-3 bg-slate-900 text-slate-200 rounded-xl font-mono text-[11px] space-y-1 max-h-40 overflow-y-auto leading-relaxed">
                            @foreach($evaluationSteps as $step)
                                <div class="flex items-start gap-1.5">
                                    <span class="text-emerald-400">&gt;</span>
                                    <span>{{ $step }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                @endif
            </div>

            {{-- Academic Information Box --}}
            <div class="p-5 rounded-3xl text-xs space-y-2 shadow-xs" style="background-color: #f0f3ff; border: 1px solid #c7d2fe; color: #1e1b4b;">
                <div class="flex items-center gap-2 font-bold" style="color: #1e1b4b;">
                    <x-icon name="o-academic-cap" class="w-5 h-5 text-indigo-600" />
                    <span class="text-sm">Metode Sistem Pakar Forward Chaining</span>
                </div>
                <p class="leading-relaxed" style="color: #312e81;">
                    Algoritma pencarian dimulai dari sekumpulan fakta gejala yang diberikan oleh pengguna, kemudian mengevaluasi aturan-aturan (rules) medis hingga menghasilkan kesimpulan berupa dosis obat dan persentase kepastian rekomendasi.
                </p>
            </div>
        </div>

    </div>
</div>
