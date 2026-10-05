<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">

    <title>Knowledge Base Management — TirtAssistant</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN for guaranteed modern styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            500: '#1565C0',
                            600: '#0f52a1',
                            700: '#0d47a1',
                            800: '#0a387d',
                            900: '#072b61',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gradient-to-br from-slate-50 via-blue-50/20 to-slate-100 text-slate-800 antialiased flex flex-col min-h-screen">
    {{-- ===== Top Navbar ===== --}}
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            {{-- Brand Logo & Title --}}
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#1565C0] to-cyan-500 p-0.5 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center p-1.5">
                            <img src="{{ asset('img/icon.png') }}" alt="TirtAssistant" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-base font-bold text-slate-900 group-hover:text-[#1565C0] transition-colors leading-tight">
                                TirtAssistant
                            </h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-[#1565C0]">
                                Knowledge Hub
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">PDAM Tirta Kepri — RAG Knowledge Base</p>
                    </div>
                </a>
            </div>

            {{-- Quick Nav Actions --}}
            <nav class="flex items-center gap-2 sm:gap-3">
                <a 
                    href="{{ url('/chatbot') }}" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                >
                    <svg class="w-4 h-4 text-[#1565C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Uji Chatbot</span>
                </a>

                @if(file_exists(public_path('widget-demo.html')))
                    <a 
                        href="{{ asset('widget-demo.html') }}" 
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Demo Widget</span>
                    </a>
                @endif

                <div class="h-4 w-px bg-slate-200 mx-1"></div>

                <div class="flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Gemini RAG Aktif
                </div>
            </nav>
        </div>
    </header>

    {{-- ===== Main Content ===== --}}
    <main class="flex-1 mx-auto max-w-6xl w-full px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">
        {{-- Hero Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#1565C0] border border-blue-200/50 mb-3">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                    Knowledge Base Management
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Basis Data Pengetahuan AI
                </h2>
                <p class="text-sm text-slate-500 mt-1.5 max-w-2xl leading-relaxed">
                    Unggah dokumen acuan (SOP layanan, tarif air, FAQ, dan panduan) dengan mudah via drag & drop. AI akan secara otomatis memecah berkas menjadi potongan teks serta membentuk indeks vektor semantik untuk respon chatbot yang akurat.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a 
                    href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-xs transition"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        {{-- Livewire Knowledge Manager Component --}}
        @livewire('knowledge-manager')
    </main>

    {{-- ===== Footer ===== --}}
    <footer class="mt-auto border-t border-slate-200/80 bg-white/60 py-6 text-center text-xs text-slate-500">
        <div class="mx-auto max-w-6xl px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} Perumda Air Minum Tirta Kepri — TirtAssistant RAG Engine.</p>
            <p class="text-slate-400">Didukung oleh Google Gemini Vector Embeddings & Laravel Livewire.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
