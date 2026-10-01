<div>
    <div class="flex flex-col h-[600px] border border-gray-200 rounded-lg shadow-sm bg-white overflow-hidden">
        
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4 font-bold flex justify-between items-center shadow-md z-10">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span>AI Assistant RAG</span>
            </div>
            <button wire:click="createNewSession" class="text-xs bg-white text-blue-600 px-3 py-1 rounded-full font-semibold hover:bg-blue-50 transition">
                Sesi Baru
            </button>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-4" id="chat-container">
            @foreach($messages as $msg)
                @if($msg['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="bg-blue-500 text-white p-3 rounded-l-xl rounded-br-xl max-w-[80%] shadow-sm">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @else
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-200 text-gray-800 p-4 rounded-r-xl rounded-bl-xl max-w-[90%] shadow-sm leading-relaxed">
                            {!! nl2br(e($msg['content'])) !!}
                            
                            @if(!empty($msg['sources']))
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 mb-2">Sumber Dokumen:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($msg['sources'] as $source)
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200" title="{{ $source['preview'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                {{ $source['document'] }} ({{ $source['score'] }}%)
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
            
            @if($isTyping)
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-200 text-gray-500 p-3 rounded-r-xl rounded-bl-xl shadow-sm flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="inline-block w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="inline-block w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input Area -->
        <div class="p-3 border-t border-gray-200 bg-white">
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input 
                    wire:model="userMessage" 
                    type="text" 
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    placeholder="Tanya sesuatu tentang dokumen..."
                    {{ $isTyping ? 'disabled' : '' }}
                >
                <button 
                    type="submit" 
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    {{ $isTyping ? 'disabled' : '' }}
                >
                    <span>Kirim</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                const container = document.getElementById('chat-container');
                container.scrollTop = container.scrollHeight;
            });
        });
    </script>
</div>
