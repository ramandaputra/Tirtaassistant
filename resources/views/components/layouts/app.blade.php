<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel RAG Chatbot</title>
    <!-- Tailwind CSS (CDN for demo purposes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-blue-600">🧠 RAG Chatbot</h1>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Knowledge Base Manager -->
                    <div class="lg:col-span-1 space-y-6">
                        @livewire('knowledge-manager')
                        
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2">Info Sistem</h3>
                            <ul class="text-xs text-gray-600 space-y-2">
                                <li class="flex justify-between"><span>Vector Store:</span> <span class="font-medium text-gray-900">SQLite (JSON)</span></li>
                                <li class="flex justify-between"><span>Embedding:</span> <span class="font-medium text-gray-900">Gemini (gemini-embedding-exp-03-07)</span></li>
                                <li class="flex justify-between"><span>Chat Model:</span> <span class="font-medium text-gray-900">Gemini (gemini-2.0-flash)</span></li>
                                <li class="flex justify-between"><span>Queue:</span> <span class="font-medium text-gray-900">Database</span></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Right Column: Chat Interface -->
                    <div class="lg:col-span-2">
                        @livewire('chat-interface')
                    </div>
                </div>
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
