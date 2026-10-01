<div x-data="{ isOpen: false, isMinimized: false, isExpanded: false }" x-cloak>

    {{-- ===== Floating Action Button (FAB) ===== --}}
    <button 
        x-show="!isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        @click="isOpen = true; isMinimized = false; isExpanded = false"
        class="fixed bottom-6 right-6 z-[9999] w-16 h-16 bg-[#1565C0] text-white rounded-full shadow-[0_6px_24px_rgba(21,101,192,0.45)] flex items-center justify-center hover:scale-110 hover:shadow-[0_8px_32px_rgba(21,101,192,0.6)] active:scale-95 transition-all duration-300 cursor-pointer group"
        aria-label="Buka Chat"
    >
        {{-- Chat icon --}}
        <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        {{-- Pulse ring --}}
        <span class="absolute inset-0 rounded-full bg-[#1565C0] animate-ping opacity-30"></span>
    </button>

    {{-- ===== Chat Widget Window ===== --}}
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 right-6 z-[9998] flex flex-col bg-white rounded-3xl shadow-[0_16px_60px_rgba(0,0,0,0.18)] border border-gray-100 overflow-hidden"
        :class="{
            'w-[calc(100vw-3rem)] sm:w-[380px]': !isExpanded,
            'w-[calc(100vw-3rem)] sm:w-[640px]': isExpanded,
            'h-auto': isMinimized,
            'h-[600px]': !isMinimized
        }"
        :style="isMinimized ? '' : 'max-height: calc(100vh - 48px);'"
        style="transition: height 0.3s ease, width 0.3s ease;"
    >
        
        {{-- ===== Header ===== --}}
        <div 
            class="bg-[#1565C0] text-white px-5 py-4 flex items-center justify-between shadow-md z-10 flex-shrink-0 cursor-pointer select-none"
            :class="isMinimized ? 'rounded-3xl' : 'rounded-t-3xl'"
            @click="if(isMinimized) { isMinimized = false }"
        >
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full p-1 shadow-inner flex items-center justify-center relative flex-shrink-0">
                    <img src="{{ asset('img/icon.png') }}" class="w-6 h-6 object-contain" alt="Bot">
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></div>
                </div>
                <div>
                    <h2 class="font-bold text-[15px] leading-tight tracking-wide">TirtAssistant</h2>
                    <p class="text-[11px] text-blue-100/90 font-medium tracking-wide">Asisten Virtual Tirta Kepri</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                {{-- Resize button --}}
                <button
                    @click.stop="isExpanded = !isExpanded; isMinimized = false"
                    class="hover:bg-white/20 p-2 rounded-full transition-colors text-white/90"
                    :title="isExpanded ? 'Ukuran normal' : 'Perluas widget'"
                    :aria-label="isExpanded ? 'Kembalikan ukuran normal' : 'Perluas widget'"
                >
                    <svg x-show="!isExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6v6m0-6-7 7M9 21H3v-6m0 6 7-7" /></svg>
                    <svg x-show="isExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H3v6m0-6 7 7m5 11h6v-6m0 6-7-7" /></svg>
                </button>
                {{-- Minimize button --}}
                <button 
                    @click.stop="isMinimized = !isMinimized" 
                    class="hover:bg-white/20 p-2 rounded-full transition-colors text-white/90"
                    :title="isMinimized ? 'Expand' : 'Minimize'"
                    :aria-label="isMinimized ? 'Buka widget' : 'Minimalkan widget'"
                >
                    <svg x-show="!isMinimized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                    <svg x-show="isMinimized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                </button>
                {{-- Close button --}}
                <button 
                    @click.stop="isOpen = false" 
                    class="hover:bg-white/20 p-2 rounded-full transition-colors text-white/90"
                    title="Tutup"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        {{-- ===== Chat Body (hidden when minimized) ===== --}}
        <div x-show="!isMinimized" class="flex-1 flex flex-col overflow-hidden">

                {{-- Chat Area --}}
                <div class="flex-1 p-4 overflow-y-auto bg-[#F8FAFC] flex flex-col gap-4 scroll-smooth" id="chat-container" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                    
                    {{-- Welcome Message --}}
                    @if(count($messages) === 0)
                        <div class="flex justify-start items-end gap-2.5 group">
                            <div class="w-8 h-8 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-0.5 shadow-sm mb-5 flex items-center justify-center">
                                <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                            </div>
                            <div class="flex flex-col items-start max-w-[85%]">
                                <div class="bg-[#F0F4F8] text-gray-700 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm text-[13px] leading-relaxed border border-gray-100/50">
                                    <p class="mb-1.5">Halo! 👋</p>
                                    <p>Saya <span class="font-bold text-[#1565C0]">TirtAssistant</span>, asisten virtual dari Perumda Air Minum Tirta Kepri.</p>
                                    <p class="mt-1.5">Ada yang bisa saya bantu?</p>
                                </div>
                                <span class="text-[10px] font-medium text-gray-400 mt-1 ml-1">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="flex flex-wrap gap-1.5 pl-10">
                            <button wire:click="$set('userMessage', 'Cek Tagihan'); sendMessage()" class="bg-white border border-gray-200 hover:border-[#1565C0] hover:text-[#1565C0] text-gray-600 text-[11px] font-semibold py-2 px-3 rounded-xl transition-all shadow-sm">
                                📄 Cek Tagihan
                            </button>
                            <button wire:click="$set('userMessage', 'Informasi Tarif'); sendMessage()" class="bg-white border border-gray-200 hover:border-[#1565C0] hover:text-[#1565C0] text-gray-600 text-[11px] font-semibold py-2 px-3 rounded-xl transition-all shadow-sm">
                                📊 Info Tarif
                            </button>
                            <button wire:click="$set('userMessage', 'Cara Pembayaran'); sendMessage()" class="bg-white border border-gray-200 hover:border-[#1565C0] hover:text-[#1565C0] text-gray-600 text-[11px] font-semibold py-2 px-3 rounded-xl transition-all shadow-sm">
                                💳 Pembayaran
                            </button>
                            <button wire:click="$set('userMessage', 'Gangguan Air'); sendMessage()" class="bg-white border border-gray-200 hover:border-[#1565C0] hover:text-[#1565C0] text-gray-600 text-[11px] font-semibold py-2 px-3 rounded-xl transition-all shadow-sm">
                                ⚙️ Gangguan
                            </button>
                            <button wire:click="$set('userMessage', 'Pengaduan'); sendMessage()" class="bg-white border border-gray-200 hover:border-[#1565C0] hover:text-[#1565C0] text-gray-600 text-[11px] font-semibold py-2 px-3 rounded-xl transition-all shadow-sm">
                                💬 Pengaduan
                            </button>
                        </div>
                    @endif

                    {{-- Messages --}}
                    @foreach($messages as $msg)
                        @if($msg['role'] === 'user')
                            <div class="flex justify-end items-end gap-2.5 group">
                                <div class="flex flex-col items-end max-w-[85%]">
                                    <div class="bg-[#1565C0] text-white px-4 py-3 rounded-2xl rounded-br-sm shadow-sm text-[13px] leading-relaxed">
                                        {{ $msg['content'] }}
                                    </div>
                                    <div class="flex items-center gap-1 mt-0.5 mr-1">
                                        <span class="text-[10px] font-medium text-gray-400">{{ \Carbon\Carbon::parse($msg['time'] ?? now())->format('H:i') }}</span>
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div class="w-7 h-7 rounded-full bg-[#E2E8F0] overflow-hidden flex-shrink-0 border border-gray-200 mb-5 shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name=User&background=E2E8F0&color=475569&size=28" alt="User" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start items-end gap-2.5 group">
                                <div class="w-8 h-8 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-0.5 shadow-sm mb-5 flex items-center justify-center">
                                    <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                                </div>
                                <div class="flex flex-col items-start max-w-[85%]">
                                    <div class="bg-[#F0F4F8] text-gray-700 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm text-[13px] leading-relaxed border border-gray-100/50">
                                        {!! nl2br(e($msg['content'])) !!}
                                        
                                        @if(!empty($msg['sources']))
                                            <div class="mt-3 pt-2.5 border-t border-gray-200/60">
                                                <p class="text-[10px] font-semibold text-gray-500 mb-1.5">Sumber:</p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($msg['sources'] as $source)
                                                        <span class="inline-flex items-center gap-1 text-[9px] bg-white text-gray-600 px-2 py-0.5 rounded-md border border-gray-200 shadow-sm" title="{{ $source['preview'] }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            {{ $source['document'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-medium text-gray-400 mt-0.5 ml-1">{{ \Carbon\Carbon::parse($msg['time'] ?? now())->format('H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    
                    {{-- Typing Indicator --}}
                    @if($isTyping)
                        <div class="flex justify-start items-end gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-0.5 shadow-sm mb-5 flex items-center justify-center">
                                <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                            </div>
                            <div class="bg-[#F0F4F8] text-gray-500 px-5 py-3.5 rounded-2xl rounded-bl-sm shadow-sm flex items-center gap-1.5 border border-gray-100/50 h-[44px]">
                                <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce"></span>
                                <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                                <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Input Area --}}
                <div class="p-3 bg-white border-t border-gray-100 shadow-[0_-4px_10px_rgb(0,0,0,0.02)] flex-shrink-0">
                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2 bg-white rounded-full px-2 py-1.5 border-2 border-gray-100 focus-within:border-blue-400 transition-all shadow-sm">
                        <input 
                            wire:model="userMessage" 
                            type="text" 
                            class="flex-1 bg-transparent border-none focus:ring-0 px-3 text-[13px] text-gray-700 placeholder-gray-400 font-medium" 
                            placeholder="Tulis pesan Anda..."
                            {{ $isTyping ? 'disabled' : '' }}
                        >
                        <button 
                            type="submit" 
                            class="bg-[#1565C0] text-white w-9 h-9 rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
                            {{ $isTyping ? 'disabled' : '' }}
                        >
                            <svg class="w-4 h-4 ml-[-1px] mt-[1px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                        </button>
                    </form>
                </div>

                {{-- Powered by footer --}}
                <div class="bg-white text-center py-1.5 flex-shrink-0">
                    <p class="text-[10px] text-gray-400 font-medium">Powered by <span class="text-[#1565C0] font-semibold">Tirta Kepri</span></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                const container = document.getElementById('chat-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
            // Initial scroll to bottom
            const container = document.getElementById('chat-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>

    <style>
        /* Responsive adjustments for widget */
        @media (max-width: 480px) {
            #chat-container {
                max-height: 50vh !important;
            }
        }
    </style>
</div>
