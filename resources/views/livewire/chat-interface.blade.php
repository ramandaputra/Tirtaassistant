<div>
    <div class="flex flex-col lg:flex-row gap-6 max-w-[900px] mx-auto h-[650px]">
        
        <!-- Chat Section -->
        <div class="flex-1 flex flex-col bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 overflow-hidden relative">
            
            <!-- Header -->
            <div class="bg-[#1565C0] text-white px-5 py-4 flex items-center justify-between shadow-md z-10 rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white rounded-full p-1 shadow-inner flex items-center justify-center relative">
                        <img src="{{ asset('img/icon.png') }}" class="w-8 h-8 object-contain relative top-[-1px]" alt="Bot">
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg leading-tight tracking-wide">TirtAssistant</h2>
                        <p class="text-xs text-blue-100/90 font-medium tracking-wide">Asisten Virtual Tirta Kepri</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="hover:bg-white/20 p-2 rounded-full transition-colors text-white/90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                    </button>
                    <button class="hover:bg-white/20 p-2 rounded-full transition-colors text-white/90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 p-6 overflow-y-auto bg-[#F8FAFC] flex flex-col gap-5 scroll-smooth" id="chat-container">
                <!-- Welcome Message Placeholder (if no messages) -->
                @if(count($messages) === 0)
                    <div class="flex justify-start items-end gap-3 group">
                        <div class="w-9 h-9 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-1 shadow-sm mb-5 flex items-center justify-center">
                            <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                        </div>
                        <div class="flex flex-col items-start max-w-[85%]">
                            <div class="bg-[#F0F4F8] text-gray-700 px-5 py-4 rounded-2xl rounded-bl-sm shadow-sm text-[15px] leading-relaxed border border-gray-100/50">
                                <p class="mb-2">Halo! 👋</p>
                                <p>Saya <span class="font-bold text-[#1565C0]">TirtAssistant</span>, asisten virtual dari Perumda Air Minum Tirta Kepri.</p>
                                <p class="mt-2">Ada yang bisa saya bantu?</p>
                            </div>
                            <span class="text-[11px] font-medium text-gray-400 mt-1 ml-1">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                @endif

                @foreach($messages as $msg)
                    @if($msg['role'] === 'user')
                        <div class="flex justify-end items-end gap-3 group">
                            <div class="flex flex-col items-end max-w-[85%]">
                                <div class="bg-[#1565C0] text-white px-5 py-3.5 rounded-2xl rounded-br-sm shadow-sm text-[15px] leading-relaxed">
                                    {{ $msg['content'] }}
                                </div>
                                <div class="flex items-center gap-1 mt-1 mr-1">
                                    <span class="text-[11px] font-medium text-gray-400">{{ \Carbon\Carbon::parse($msg['time'] ?? now())->format('H:i') }}</span>
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-[#E2E8F0] overflow-hidden flex-shrink-0 border border-gray-200 mb-5 shadow-sm">
                                <img src="https://ui-avatars.com/api/?name=User&background=E2E8F0&color=475569" alt="User" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start items-end gap-3 group">
                            <div class="w-9 h-9 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-1 shadow-sm mb-5 flex items-center justify-center">
                                <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                            </div>
                            <div class="flex flex-col items-start max-w-[85%]">
                                <div class="bg-[#F0F4F8] text-gray-700 px-5 py-4 rounded-2xl rounded-bl-sm shadow-sm text-[15px] leading-relaxed border border-gray-100/50">
                                    {!! nl2br(e($msg['content'])) !!}
                                    
                                    @if(!empty($msg['sources']))
                                        <div class="mt-4 pt-3 border-t border-gray-200/60">
                                            <p class="text-xs font-semibold text-gray-500 mb-2">Sumber:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($msg['sources'] as $source)
                                                    <span class="inline-flex items-center gap-1 text-[10px] bg-white text-gray-600 px-2.5 py-1 rounded-md border border-gray-200 shadow-sm" title="{{ $source['preview'] }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ $source['document'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[11px] font-medium text-gray-400 mt-1 ml-1">{{ \Carbon\Carbon::parse($msg['time'] ?? now())->format('H:i') }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                @if($isTyping)
                    <div class="flex justify-start items-end gap-3">
                        <div class="w-9 h-9 rounded-full bg-white overflow-hidden flex-shrink-0 border border-gray-100 p-1 shadow-sm mb-5 flex items-center justify-center">
                            <img src="{{ asset('img/icon.png') }}" class="w-full h-full object-contain" alt="Bot">
                        </div>
                        <div class="bg-[#F0F4F8] text-gray-500 px-5 py-4 rounded-2xl rounded-bl-sm shadow-sm flex items-center gap-1.5 border border-gray-100/50 h-[52px]">
                            <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce"></span>
                            <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                            <span class="inline-block w-2 h-2 bg-[#1565C0]/60 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t border-gray-100 shadow-[0_-4px_10px_rgb(0,0,0,0.02)]">
                <form wire:submit.prevent="sendMessage" class="flex items-center gap-3 bg-white rounded-full px-2 py-2 border-2 border-gray-100 focus-within:border-blue-400 transition-all shadow-sm">
                    <button type="button" class="text-gray-400 hover:text-gray-600 pl-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    <input 
                        wire:model="userMessage" 
                        type="text" 
                        class="flex-1 bg-transparent border-none focus:ring-0 px-2 text-[15px] text-gray-700 placeholder-gray-400 font-medium" 
                        placeholder="Tulis pesan Anda..."
                        {{ $isTyping ? 'disabled' : '' }}
                    >
                    <button 
                        type="submit" 
                        class="bg-[#1565C0] text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
                        {{ $isTyping ? 'disabled' : '' }}
                    >
                        <svg class="w-5 h-5 ml-[-2px] mt-[1px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="hidden lg:flex w-[320px] bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-7 flex-col relative overflow-hidden">
           
           <div class="relative z-10 flex flex-col items-center text-center mb-7 mt-4">
               <div class="w-28 h-28 mb-5 bg-[#E3F2FD] rounded-full flex items-center justify-center relative shadow-inner">
                   <img src="{{ asset('img/icon2.png') }}" class="w-20 h-20 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 object-contain" alt="Bot">

               </div>
               <h3 class="text-xl font-extrabold text-[#1565C0] mb-2 leading-tight">TirtaAssistant<br><span class="text-gray-800 text-lg">Siap membantu Anda!</span></h3>
               <p class="text-[13px] text-gray-500 font-medium">Tanyakan apa saja seputar layanan air Tirta Kepri.</p>
           </div>

           <div class="relative z-10 flex-1">
               <h4 class="font-bold text-gray-800 mb-4 text-sm tracking-wide">Pertanyaan Cepat</h4>
               <div class="space-y-2.5">
                   <button wire:click="$set('userMessage', 'Cek Tagihan'); sendMessage()" class="w-full bg-white border border-gray-200 hover:border-[#1565C0] hover:shadow-sm hover:text-[#1565C0] text-gray-600 text-sm font-semibold py-3 px-4 rounded-xl flex items-center gap-4 transition-all">
                       <svg class="w-5 h-5 text-blue-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                       Cek Tagihan
                   </button>
                   
                   <button wire:click="$set('userMessage', 'Informasi Tarif'); sendMessage()" class="w-full bg-white border border-gray-200 hover:border-[#1565C0] hover:shadow-sm hover:text-[#1565C0] text-gray-600 text-sm font-semibold py-3 px-4 rounded-xl flex items-center gap-4 transition-all">
                       <svg class="w-5 h-5 text-blue-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                       Informasi Tarif
                   </button>
                   
                   <button wire:click="$set('userMessage', 'Cara Pembayaran'); sendMessage()" class="w-full bg-white border border-gray-200 hover:border-[#1565C0] hover:shadow-sm hover:text-[#1565C0] text-gray-600 text-sm font-semibold py-3 px-4 rounded-xl flex items-center gap-4 transition-all">
                       <svg class="w-5 h-5 text-blue-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                       Cara Pembayaran
                   </button>
                   
                   <button wire:click="$set('userMessage', 'Gangguan Air'); sendMessage()" class="w-full bg-white border border-gray-200 hover:border-[#1565C0] hover:shadow-sm hover:text-[#1565C0] text-gray-600 text-sm font-semibold py-3 px-4 rounded-xl flex items-center gap-4 transition-all">
                       <svg class="w-5 h-5 text-blue-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                       Gangguan Air
                   </button>
                   
                   <button wire:click="$set('userMessage', 'Pengaduan'); sendMessage()" class="w-full bg-white border border-gray-200 hover:border-[#1565C0] hover:shadow-sm hover:text-[#1565C0] text-gray-600 text-sm font-semibold py-3 px-4 rounded-xl flex items-center gap-4 transition-all">
                       <svg class="w-5 h-5 text-blue-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                       Pengaduan
                   </button>
               </div>
           </div>

           <div class="mt-8 pt-6 border-t border-gray-100 text-center relative z-10 pb-4">
               <p class="text-[#1565C0] font-extrabold italic text-sm transform -rotate-3 leading-snug tracking-wide">
                   Bersama<br>
                   <span class="text-gray-800">Tirta Kepri,</span><br>
                   Air untuk Kehidupan
               </p>
           </div>
           
           <!-- Bottom decoration wave -->
           <div class="absolute bottom-0 left-0 w-full">
               <svg viewBox="0 0 1440 320" class="w-full h-auto opacity-10"><path fill="#1565C0" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,186.7C384,192,480,224,576,213.3C672,203,768,149,864,128C960,107,1056,117,1152,144C1248,171,1344,213,1392,234.7L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
           </div>
        </div>

    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                const container = document.getElementById('chat-container');
                container.scrollTop = container.scrollHeight;
            });
            // Initial scroll to bottom
            const container = document.getElementById('chat-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</div>
