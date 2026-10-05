<div class="space-y-8">
    {{-- ===== Flash Alerts ===== --}}
    @if (session()->has('message'))
        <div x-data x-init="setTimeout(() => $el.remove(), 6000)"
             class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('message') }}</div>
            <button type="button" @click="$el.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('warning') }}</div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ===== Overview Statistics Cards ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        {{-- Card 1: Total Docs --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_12px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:border-blue-300 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1565C0] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Dokumen</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>

        {{-- Card 2: Chunks Vector --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_12px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:border-indigo-300 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Vektor Chunks</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_chunks']) }}</h3>
            </div>
        </div>

        {{-- Card 3: Ready Status --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_12px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:border-emerald-300 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siap Digunakan</p>
                <h3 class="text-2xl font-bold text-emerald-600">{{ number_format($stats['ready']) }}</h3>
            </div>
        </div>

        {{-- Card 4: Processing / Failed --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_12px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:border-amber-300 transition-colors">
            <div class="w-12 h-12 rounded-xl {{ $stats['failed'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-600' }} flex items-center justify-center shrink-0">
                @if ($stats['failed'] > 0)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Proses / Kendala</p>
                <h3 class="text-2xl font-bold {{ $stats['failed'] > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                    {{ $stats['processing'] }} <span class="text-xs font-normal text-slate-400">/ {{ $stats['failed'] }} gagal</span>
                </h3>
            </div>
        </div>
    </div>

    {{-- ===== DRAG & DROP UPLOAD ZONE ===== --}}
    <div
        x-data="{
            isDragging: false,
            isUploading: false,
            uploadProgress: 0,
            handleDrop(e) {
                this.isDragging = false;
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    const input = $refs.fileInput;
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false; uploadProgress = 0"
        x-on:livewire-upload-error="isUploading = false; uploadProgress = 0"
        x-on:livewire-upload-progress="uploadProgress = $event.detail.progress"
        class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden transition-all duration-300"
    >
        {{-- Header section --}}
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50/70 via-white to-blue-50/30">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-[#1565C0] text-white flex items-center justify-center text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </span>
                    Unggah Dokumen Knowledge Base
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tarik dan letakkan dokumen untuk diekstrak menjadi embedding teks RAG AI.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#1565C0] border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Multi-file Drag & Drop
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Interactive Drop Zone Box --}}
            <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.fileInput.click()"
                :class="{
                    'border-[#1565C0] bg-blue-50/60 ring-4 ring-blue-500/20 scale-[1.01] shadow-lg': isDragging,
                    'border-slate-300 hover:border-[#1565C0]/60 bg-gradient-to-b from-slate-50/60 to-white hover:bg-blue-50/20': !isDragging
                }"
                class="relative border-2 border-dashed rounded-2xl p-8 sm:p-12 text-center cursor-pointer transition-all duration-300 group select-none"
            >
                {{-- Hidden file input — wire:model binds to `stagedFiles` --}}
                <input
                    x-ref="fileInput"
                    wire:model="stagedFiles"
                    type="file"
                    multiple
                    class="hidden"
                    accept=".pdf,.txt,.csv,.md"
                >

                {{-- Center Icon & Text --}}
                <div class="max-w-md mx-auto space-y-4">
                    {{-- Animated Upload Icon --}}
                    <div
                        :class="isDragging ? 'scale-110 -translate-y-1 bg-[#1565C0] text-white shadow-blue-300' : 'bg-blue-50 text-[#1565C0] group-hover:scale-105 group-hover:bg-[#1565C0] group-hover:text-white'"
                        class="w-20 h-20 mx-auto rounded-3xl flex items-center justify-center shadow-md transition-all duration-300"
                    >
                        <svg class="w-10 h-10 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>

                    {{-- Titles --}}
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-slate-800 group-hover:text-[#1565C0] transition-colors">
                            <span x-show="!isDragging">Tarik & Lepaskan berkas di sini</span>
                            <span x-show="isDragging" class="text-[#1565C0] font-bold">Lepaskan berkas untuk menambahkan!</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            atau <span class="text-[#1565C0] font-semibold underline underline-offset-2">pilih dari komputer Anda</span>
                        </p>
                    </div>

                    {{-- Format pills --}}
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">PDF</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">TXT</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">CSV</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">MARKDOWN</span>
                        <span class="text-xs text-slate-400 font-medium ml-1">Maks. 10MB/berkas</span>
                    </div>
                </div>

                {{-- Livewire Uploading Overlay --}}
                <div
                    x-show="isUploading"
                    x-cloak
                    class="absolute inset-0 bg-white/95 rounded-2xl flex flex-col items-center justify-center p-6 z-20 backdrop-blur-sm"
                >
                    <div class="w-full max-w-xs space-y-3">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-[#1565C0]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengunggah ke server...
                            </span>
                            <span x-text="uploadProgress + '%'" class="text-[#1565C0] font-bold"></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                            <div
                                class="h-full bg-gradient-to-r from-[#1565C0] to-cyan-500 rounded-full transition-all duration-200"
                                :style="`width: ${uploadProgress}%`"
                            ></div>
                        </div>
                        <p class="text-[11px] text-slate-400 text-center">Mohon tunggu, berkas sedang dipersiapkan...</p>
                    </div>
                </div>
            </div>

            {{-- Validation errors --}}
            @error('stagedFiles.*')
                <div class="mt-3 flex items-center gap-2 text-xs font-medium text-rose-600 bg-rose-50 p-3 rounded-xl border border-rose-100">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            {{-- Staged Files Preview List --}}
            @if (!empty($stagedFiles))
                <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-800">Berkas Siap Diproses</h4>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#1565C0]">
                                {{ count($stagedFiles) }} Berkas
                            </span>
                        </div>
                        <button
                            type="button"
                            wire:click="clearStagedFiles"
                            class="text-xs font-medium text-slate-500 hover:text-rose-600 transition-colors flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Kosongkan Pilihan
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1">
                        @foreach ($stagedFiles as $index => $file)
                            @php
                                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                                $sizeKb = round($file->getSize() / 1024, 1);
                                $sizeFormatted = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';
                                $extColors = [
                                    'pdf' => 'bg-rose-100 text-rose-700',
                                    'csv' => 'bg-emerald-100 text-emerald-700',
                                    'txt' => 'bg-slate-200 text-slate-700',
                                    'md'  => 'bg-sky-100 text-sky-700',
                                ];
                                $colorClass = $extColors[$ext] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/60 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 font-bold text-xs {{ $colorClass }}">
                                        {{ strtoupper($ext) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $file->getClientOriginalName() }}">
                                            {{ $file->getClientOriginalName() }}
                                        </p>
                                        <p class="text-[11px] text-slate-400">{{ $sizeFormatted }}</p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeStagedFile({{ $index }})"
                                    class="text-slate-400 hover:text-rose-500 p-1 rounded-md transition-colors"
                                    title="Hapus dari antrean"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Upload action button --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-3">
                        <p class="text-xs text-slate-500">
                            Dokumen akan otomatis dipotong (<span class="italic font-medium">chunked</span>) dan digenerate vektor embedding Gemini AI.
                        </p>
                        <button
                            type="button"
                            wire:click="uploadDocuments"
                            wire:loading.attr="disabled"
                            id="btn-upload-process"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="uploadDocuments" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Unggah & Proses AI ({{ count($stagedFiles) }} Berkas)
                            </span>
                            <span wire:loading wire:target="uploadDocuments" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses Embedding Dokumen...
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== SAVED DOCUMENTS REPOSITORY & MANAGEMENT ===== --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden">
        {{-- Table Toolbar Header --}}
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1565C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Daftar Dokumen Knowledge Base
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola berkas acuan yang aktif digunakan untuk menjawab pertanyaan pengguna.</p>
            </div>

            {{-- Search & Filter Controls --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative min-w-[220px]">
                    <input
                        id="search-documents"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama dokumen..."
                        class="w-full pl-9 pr-8 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1565C0]/20 focus:border-[#1565C0] text-slate-800 placeholder-slate-400 transition"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if (!empty($search))
                        <button type="button" wire:click="$set('search', '')" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                {{-- Status Filter Pills --}}
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs">
                    @foreach (['all' => 'Semua', 'ready' => 'Siap', 'processing' => 'Proses', 'failed' => 'Gagal'] as $filterValue => $filterLabel)
                        <button
                            type="button"
                            wire:click="$set('statusFilter', '{{ $filterValue }}')"
                            class="px-2.5 py-1 rounded-lg font-medium transition-all
                                {{ $statusFilter === $filterValue
                                    ? 'bg-white shadow-sm font-semibold ' . match($filterValue) { 'ready' => 'text-emerald-700', 'failed' => 'text-rose-700', 'processing' => 'text-amber-700', default => 'text-slate-800' }
                                    : 'text-slate-600 hover:text-slate-900'
                                }}"
                        >
                            {{ $filterLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Table with auto-poll if jobs are running --}}
        <div class="overflow-x-auto" @if($hasProcessing) wire:poll.3s @endif>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">Dokumen</th>
                        <th class="px-4 py-3.5">Ukuran</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Chunks</th>
                        <th class="px-4 py-3.5">Waktu Unggah</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($knowledgeDocs as $doc)
                        <tr wire:key="doc-{{ $doc->id }}" class="hover:bg-slate-50/70 transition-colors group">
                            {{-- Document Name & Type --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $docExt = $doc->extension ?: 'file';
                                        $docExtColors = [
                                            'pdf' => 'bg-rose-100 text-rose-700',
                                            'csv' => 'bg-emerald-100 text-emerald-700',
                                            'txt' => 'bg-slate-100 text-slate-700',
                                            'md'  => 'bg-sky-100 text-sky-700',
                                        ];
                                        $docExtClass = $docExtColors[$docExt] ?? 'bg-slate-100 text-slate-600';
                                    @endphp
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 font-bold text-xs {{ $docExtClass }}">
                                        {{ strtoupper($docExt) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 group-hover:text-[#1565C0] transition-colors truncate max-w-xs sm:max-w-sm" title="{{ $doc->original_name }}">
                                            {{ $doc->original_name }}
                                        </div>
                                        @if ($doc->error_message)
                                            <div class="text-[11px] text-rose-500 mt-0.5 truncate max-w-xs" title="{{ $doc->error_message }}">
                                                Error: {{ $doc->error_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Size --}}
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                {{ $doc->file_size_formatted }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($doc->status === 'ready')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Siap
                                    </span>
                                @elseif ($doc->status === 'processing')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memproses...
                                    </span>
                                @elseif ($doc->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Antrean
                                    </span>
                                @elseif ($doc->status === 'failed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60" title="{{ $doc->error_message }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Gagal
                                    </span>
                                @endif
                            </td>

                            {{-- Chunks --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $doc->chunk_count ?? 0 }} chunks
                                </span>
                            </td>

                            {{-- Uploaded At --}}
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $doc->created_at ? $doc->created_at->diffForHumans() : '-' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                <button type="button" wire:click="inspectDocument({{ $doc->id }})"
                                    class="inline-flex items-center gap-1 text-[#1565C0] hover:text-[#0D47A1] hover:underline transition-colors"
                                    title="Lihat potongan teks dan embedding">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat
                                </button>

                                <button type="button" wire:click="reprocessDocument({{ $doc->id }})"
                                    wire:confirm="Proses ulang ekstraksi teks dan vector embedding untuk dokumen ini?"
                                    class="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900 transition-colors"
                                    title="Proses ulang dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Ulang
                                </button>

                                <button type="button" wire:click="deleteDocument({{ $doc->id }})"
                                    wire:confirm="Yakin ingin menghapus dokumen '{{ $doc->original_name }}'? Seluruh potongan teks dan vektor akan dihapus secara permanen."
                                    class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 transition-colors"
                                    title="Hapus dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-700">Tidak ada dokumen ditemukan</h4>
                                    <p class="text-xs text-slate-400">
                                        @if (!empty($search) || $statusFilter !== 'all')
                                            Tidak ada dokumen yang cocok dengan filter pencarian saat ini.
                                        @else
                                            Belum ada dokumen yang diunggah. Gunakan area drag and drop di atas.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($knowledgeDocs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $knowledgeDocs->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: Chunk Inspector ===== --}}
    @if ($showChunkModal && $selectedDocument)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1565C0] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 truncate" title="{{ $selectedDocument->original_name }}">
                                Inspeksi Chunks: {{ $selectedDocument->original_name }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ $selectedDocument->chunk_count }} potongan teks · {{ $selectedDocument->file_size_formatted }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeInspectModal"
                        class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Chunks List --}}
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    @forelse ($selectedDocument->chunks as $chunk)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 hover:border-blue-200 transition-colors">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-[#1565C0] flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#1565C0]"></span>
                                    Chunk #{{ $chunk->chunk_index + 1 }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 font-mono text-[11px]">{{ $chunk->token_count }} tokens</span>
                                    @if ($chunk->hasEmbedding())
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-100 text-emerald-700">Vektor Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-700">Tanpa Vektor</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-slate-700 font-mono leading-relaxed bg-white p-3 rounded-xl border border-slate-200/60 whitespace-pre-wrap max-h-48 overflow-y-auto">
                                {{ $chunk->content }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Belum ada chunks tersimpan untuk dokumen ini.
                        </div>
                    @endforelse
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                    <button type="button" wire:click="closeInspectModal"
                        class="px-5 py-2 rounded-xl text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
