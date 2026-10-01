<div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Knowledge Base
            </h2>
        </div>

        <div class="p-6">
            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Upload Area -->
            <form wire:submit.prevent="uploadDocument" class="mb-8">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unggah Dokumen Baru (PDF, TXT, CSV)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-gray-50 hover:bg-gray-100 transition cursor-pointer relative"
                             onclick="document.getElementById('file-upload').click()">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="relative cursor-pointer bg-transparent rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        Pilih File
                                    </span>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, TXT up to 10MB
                                </p>
                            </div>
                            <input id="file-upload" wire:model="document" type="file" class="sr-only" accept=".pdf,.txt,.csv">
                        </div>
                    </div>
                </div>
                
                @error('document') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                
                <div wire:loading wire:target="document" class="text-sm text-blue-600 mt-2">
                    Mengunggah...
                </div>

                @if($document)
                    <div class="mt-4 flex items-center justify-between bg-blue-50 p-3 rounded border border-blue-100">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-blue-800">{{ $document->getClientOriginalName() }}</span>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm font-medium hover:bg-blue-700 transition" wire:loading.attr="disabled">
                            Mulai Proses
                        </button>
                    </div>
                @endif
            </form>

            <!-- Document List -->
            <div>
                <h3 class="text-md font-medium text-gray-800 mb-3">Dokumen Tersimpan</h3>
                
                @if($documents->isEmpty())
                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-500 text-sm">Belum ada dokumen di knowledge base.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chunks</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" wire:poll.5s>
                                @foreach($documents as $doc)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $doc->original_name }}</div>
                                            </div>
                                            @if($doc->error_message)
                                                <div class="text-xs text-red-500 mt-1 max-w-xs truncate" title="{{ $doc->error_message }}">
                                                    Error: {{ $doc->error_message }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $doc->file_size_formatted }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $doc->status === 'ready' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $doc->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $doc->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $doc->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ $doc->status_badge }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $doc->chunk_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Yakin ingin menghapus dokumen ini beserta semua datanya?" class="text-red-600 hover:text-red-900 ml-3">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
