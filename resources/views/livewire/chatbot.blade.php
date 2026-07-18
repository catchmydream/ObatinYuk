<div 
    x-data="{ 
        open: false,
        scrollBot() {
            const el = document.getElementById('apotek-chat-msgs');
            if (el) el.scrollTop = el.scrollHeight;
        }
    }" 
    @open-chatbot.window="open = true; setTimeout(() => scrollBot(), 100)"
    x-init="$watch('open', value => { if(value) setTimeout(() => scrollBot(), 100) }); document.addEventListener('livewire:initialized', () => { Livewire.hook('morph.updated', (el, component) => { if(component.name === 'chatbot') scrollBot(); }) })"
    class="fixed z-[9999]"
    wire:ignore.self
>
    {{-- Floating Chat Window --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="fixed bottom-24 right-4 sm:right-6 w-[calc(100vw-32px)] sm:w-[380px] h-[560px] max-h-[calc(100vh-120px)] bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] border border-gray-100 flex flex-col overflow-hidden"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center border border-white/30 backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-[15px] leading-tight">Apoteker AI</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full {{ $isLoading ? 'bg-yellow-400 animate-pulse' : 'bg-green-400' }}"></span>
                        <span class="text-xs text-blue-100">{{ $isLoading ? 'Sedang mengetik...' : 'Online' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <button 
                    wire:click="$set('messages', [{role:'bot',content:'Riwayat dihapus. Ada yang bisa saya bantu?',time:'{{ now()->format('H:i') }}'}])"
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                    title="Hapus riwayat"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                </button>
                <button 
                    @click="open = false"
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="apotek-chat-msgs" class="flex-1 overflow-y-auto p-4 flex flex-col gap-4 bg-gray-50 scroll-smooth">
            
            {{-- Disclaimer --}}
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex gap-3 text-amber-800 text-xs shadow-sm">
                <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <p><strong>Disclaimer:</strong> Rekomendasi AI ini tidak menggantikan diagnosis dokter profesional.</p>
            </div>

            {{-- Messages --}}
            @foreach($messages as $msg)
                @if($msg['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="max-w-[85%]">
                            <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 text-[14px] leading-relaxed shadow-sm">
                                {{ $msg['content'] }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-1 text-right px-1">{{ $msg['time'] }}</div>
                        </div>
                    </div>
                @else
                    <div class="flex gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 mt-1">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                        </div>
                        <div class="max-w-[85%]">
                            <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 py-2.5 text-[14px] leading-relaxed shadow-sm prose prose-sm prose-p:my-1 prose-ul:my-1">
                                {!! nl2br(e($msg['content'])) !!}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-1 px-1">{{ $msg['time'] }}</div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Loading Indicator --}}
            @if($isLoading)
                <div class="flex gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 mt-1">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-sm px-4 py-3.5 shadow-sm flex items-center gap-1.5 h-10 w-16">
                        <div class="w-1.5 h-1.5 bg-blue-600/50 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-blue-600/50 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-1.5 h-1.5 bg-blue-600/50 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            @endif

            {{-- Suggestions --}}
            @if(count($messages) <= 1)
                <div class="flex flex-wrap gap-2 mt-2">
                    <button wire:click="sendMessage('Saya demam dan batuk kering')" class="px-3 py-1.5 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">🤒 Demam & Batuk</button>
                    <button wire:click="sendMessage('Saya sakit kepala dan pusing')" class="px-3 py-1.5 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">🤕 Sakit Kepala</button>
                    <button wire:click="sendMessage('Saya batuk berdahak dan pilek')" class="px-3 py-1.5 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">🤧 Batuk & Pilek</button>
                    <button wire:click="sendMessage('Saya mau cari vitamin')" class="px-3 py-1.5 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">💊 Cari Vitamin</button>
                </div>
            @endif
        </div>

        {{-- Input Area --}}
        <div class="bg-white border-t border-gray-100 p-3 flex gap-2 shrink-0">
            <input
                type="text"
                wire:model="inputMessage"
                wire:keydown.enter="sendMessage"
                class="flex-1 bg-gray-50 border border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 rounded-xl px-4 py-2.5 text-sm outline-none transition-all placeholder-gray-400"
                placeholder="Ketik keluhan Anda di sini..."
                {{ $isLoading ? 'disabled' : '' }}
            />
            <button
                wire:click="sendMessage"
                wire:loading.attr="disabled"
                class="w-11 h-11 bg-blue-600 hover:bg-blue-700 text-white rounded-xl flex items-center justify-center shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
            >
                <svg class="w-5 h-5 -rotate-45 -ml-1 mt-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Floating Toggle Button --}}
    <button 
        @click="open = !open" 
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-tr from-blue-600 to-blue-500 text-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center z-50 group"
    >
        <div x-show="!open" class="relative">
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
        </div>
        <div x-show="open" style="display:none;">
            <svg class="w-7 h-7 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    </button>
</div>
