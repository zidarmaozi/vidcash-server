<x-app-layout>
    {{-- CSS untuk Tabel Responsif dan Animasi --}}
    <style>
        @media screen and (max-width: 768px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { 
                display: block; 
                margin-bottom: 1rem;
                border-bottom: 2px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                text-align: right;
                border-bottom: 1px solid #eee;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: bold;
                text-align: left;
                margin-right: 1rem;
                color: #374151;
            }
            .responsive-table td:last-child { border-bottom: 0; }
            
            /* Mobile button improvements */
            .responsive-table .flex.items-center.space-x-3 {
                flex-direction: column;
                gap: 0.5rem;
                align-items: stretch;
            }
            
            .responsive-table .inline-flex.items-center {
                justify-content: center;
                width: 100%;
                font-size: 0.75rem;
                padding: 0.5rem 0.75rem;
            }
        }
        
        /* Smooth transitions for all interactive elements */
        .copy-btn, .edit-btn, .delete-btn {
            transition: all 0.2s ease-in-out;
        }
        
        .copy-btn:hover, .edit-btn:hover, .delete-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Loading animation for buttons */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .loading {
            animation: pulse 2s infinite;
        }
        
        /* Modal backdrop blur effect */
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
        
        /* Custom scrollbar for modal */
        .modal-content::-webkit-scrollbar {
            width: 4px;
        }
        
        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .modal-content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Kelola Link</h1>
                <a href="{{ route('videos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm">
                    Buat Link Baru
                </a>
            </div>

            <!-- Panel Kontrol & Filter -->
            <div class="bg-white p-4 rounded-lg border mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <!-- Kolom Kiri: Pilih Semua & Filter -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="select-all-checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="select-all-checkbox" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">Pilih Semua</label>
                        </div>
                        <form action="{{ route('videos.index') }}" method="GET" class="flex items-center space-x-2">
                            <select name="per_page" class="text-sm border-gray-300 rounded-md" onchange="this.form.submit()">
                                <option value="10" @if($filters['per_page'] == 10) selected @endif>10</option>
                                <option value="25" @if($filters['per_page'] == 25) selected @endif>25</option>
                                <option value="50" @if($filters['per_page'] == 50) selected @endif>50</option>
                            </select>
                            <select name="sort_by" class="text-sm border-gray-300 rounded-md" onchange="this.form.submit()">
                                <option value="created_at" @if($filters['sort_by'] == 'created_at') selected @endif>Tanggal</option>
                                <option value="views_count" @if($filters['sort_by'] == 'views_count') selected @endif>Views</option>
                            </select>
                            <select name="sort_dir" class="text-sm border-gray-300 rounded-md" onchange="this.form.submit()">
                                <option value="desc" @if($filters['sort_dir'] == 'desc') selected @endif>Terbaru</option>
                                <option value="asc" @if($filters['sort_dir'] == 'asc') selected @endif>Terlama</option>
                            </select>
                        </form>
                    </div>

                    <!-- Kolom Kanan: Search / Bulk Actions -->
                    <div class="flex items-center md:justify-end">
                        <!-- Wadah Pencarian (Awalnya terlihat) -->
                        <form id="search-container" action="{{ route('videos.index') }}" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
                            <input type="text" name="search" class="w-full text-sm border-gray-300 rounded-md" placeholder="Pencarian..." value="{{ $filters['search'] ?? '' }}">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md">Cari</button>
                        </form>

                        <!-- Wadah Bulk Action (Awalnya tersembunyi) -->
                        <div id="bulk-action-bar" class="hidden flex items-center space-x-2">
                            <span id="selected-count" class="text-sm font-semibold"></span>
                            <button id="bulk-copy-btn" class="px-4 py-2 text-sm font-medium rounded-md hover:bg-gray-200">Salin</button>
                            <button id="bulk-delete-btn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Responsif -->
            <div class="bg-white rounded-lg border overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 responsive-table">
                     <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4"><input type="checkbox" id="select-all-checkbox-table" class="h-4 w-4 text-indigo-600 border-gray-300 rounded"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Video</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Views</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($videos as $video)
                            <tr>
                                <td class="p-4" data-label="Pilih"><input type="checkbox" class="link-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" value="{{ $video->id }}" data-link="{{ $video->generated_link }}"></td>
                                <td class="px-6 py-4" data-label="Video">
                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-gray-900">{{ $video->title }}</div>
                                        <a href="{{ $video->generated_link }}" target="_blank" class="text-xs text-indigo-600 hover:underline break-all">{{ $video->generated_link }}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Views">{{ $video->views_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Dibuat">{{ $video->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Tindakan">
                                    <div class="flex items-center space-x-3">
                                        <button type="button" class="copy-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1" data-link="{{ $video->generated_link }}">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Salin
                                        </button>
                                        <button type="button" class="edit-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1" data-video-id="{{ $video->id }}" data-video-title="{{ $video->title }}">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('videos.destroy', $video) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 hover:text-red-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $videos->appends(request()->query())->links() }}
            </div>
        </div>
    </main>
    
    <!-- Form tersembunyi untuk Bulk Action -->
    <form id="bulk-action-form" action="{{ route('videos.bulkAction') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input">
        <input type="hidden" name="folder_id" id="bulk-folder-id-input">
        {{-- Input untuk video_ids[] akan ditambahkan oleh JavaScript --}}
    </form>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-confirm-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">Hapus Link</h3>
            <p id="delete-modal-text" class="mt-2 text-sm text-gray-500">Apakah Anda yakin? Aksi ini tidak dapat dibatalkan.</p>
            <div class="mt-6 flex justify-center space-x-4">
                <button id="cancel-delete-btn" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">Batal</button>
                <button id="confirm-delete-btn" type="button" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Info (untuk Salin) -->
    <div id="info-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 transition-opacity duration-300 modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm text-center transform transition-all duration-300 scale-95 opacity-0" id="info-modal-content">
            <div class="p-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-green-400 to-green-500 shadow-lg animate-pulse">
                     <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 id="info-modal-title" class="text-xl font-semibold text-gray-900 mt-6">Berhasil!</h3>
                <p id="info-modal-text" class="mt-2 text-sm text-gray-600"></p>
                <div class="mt-8">
                    <button id="close-info-btn" type="button" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Title -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 transition-opacity duration-300 modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="edit-modal-content">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg">
                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Edit Judul Video</h3>
                        <p class="text-sm text-gray-500">Ubah judul video Anda</p>
                    </div>
                </div>
                <button id="close-edit-modal" type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="edit-form" class="p-6">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label for="edit-title-input" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Video
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="edit-title-input" name="title" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400" 
                                   maxlength="255" required placeholder="Masukkan judul video...">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span id="char-count" class="text-xs text-gray-400">0/255</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Judul akan membantu Anda mengidentifikasi video dengan mudah</p>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button id="cancel-edit-btn" type="button" 
                            class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Batal
                    </button>
                    <button id="save-edit-btn" type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-lg">
                        <span class="flex items-center space-x-2">
                            <svg id="save-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span id="save-text">Simpan</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // === Elemen UI ===
                const allCheckboxes = document.querySelectorAll('.link-checkbox');
                const selectAllCheckboxHeader = document.getElementById('select-all-checkbox');
                const selectAllCheckboxTable = document.getElementById('select-all-checkbox-table');
                const bulkActionBar = document.getElementById('bulk-action-bar');
                const searchContainer = document.getElementById('search-container');
                const selectedCountSpan = document.getElementById('selected-count');
                const bulkActionForm = document.getElementById('bulk-action-form');
                const bulkCopyBtn = document.getElementById('bulk-copy-btn');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
                
                // === Elemen Modal Hapus ===
                const deleteModal = document.getElementById('delete-confirm-modal');
                const deleteModalText = document.getElementById('delete-modal-text');
                const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
                const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
                let formToSubmit = null;

                // === Elemen Modal Info ===
                const infoModal = document.getElementById('info-modal');
                const infoModalText = document.getElementById('info-modal-text');
                const closeInfoBtn = document.getElementById('close-info-btn');
                
                // === Elemen Modal Edit ===
                const editModal = document.getElementById('edit-modal');
                const editForm = document.getElementById('edit-form');
                const editTitleInput = document.getElementById('edit-title-input');
                const cancelEditBtn = document.getElementById('cancel-edit-btn');
                const saveEditBtn = document.getElementById('save-edit-btn');
                
                let selectedVideos = new Set();

                // === Fungsi untuk menyalin teks (kompatibel dengan HTTP) ===
                function copyToClipboard(text, successMessage) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        showInfoModal(successMessage);
                    } catch (err) {
                        alert('Gagal menyalin. Silakan coba secara manual.');
                    }
                    document.body.removeChild(textarea);
                }

                // === Fungsi untuk update UI ===
                function updateBulkUI() {
                    const hasSelection = selectedVideos.size > 0;
                    if (bulkActionBar) bulkActionBar.classList.toggle('hidden', !hasSelection);
                    if (searchContainer) searchContainer.classList.toggle('hidden', hasSelection);
                    if (hasSelection) {
                        selectedCountSpan.textContent = `${selectedVideos.size} terpilih`;
                    }
                    const allChecked = selectedVideos.size === allCheckboxes.length && allCheckboxes.length > 0;
                    if (selectAllCheckboxHeader) selectAllCheckboxHeader.checked = allChecked;
                    if (selectAllCheckboxTable) selectAllCheckboxTable.checked = allChecked;
                }

                allCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        checkbox.checked ? selectedVideos.add(checkbox.value) : selectedVideos.delete(checkbox.value);
                        updateBulkUI();
                    });
                });

                // === FUNGSI DIPERBAIKI UNTUK "PILIH SEMUA" ===
                function handleSelectAll(masterCheckbox) {
                    const isChecked = masterCheckbox.checked;
                    allCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                        // Perbarui Set secara manual di dalam loop
                        if (isChecked) {
                            selectedVideos.add(checkbox.value);
                        } else {
                            selectedVideos.delete(checkbox.value);
                        }
                    });
                    // Panggil update UI sekali saja setelah loop selesai
                    updateBulkUI();
                }
                
                if(selectAllCheckboxHeader) selectAllCheckboxHeader.addEventListener('change', () => handleSelectAll(selectAllCheckboxHeader));
                if(selectAllCheckboxTable) selectAllCheckboxTable.addEventListener('change', () => handleSelectAll(selectAllCheckboxTable));
                
                // === Logika Modal ===
                function showDeleteModal(form, text) {
                    formToSubmit = form;
                    deleteModalText.textContent = text;
                    deleteModal.classList.remove('hidden');
                }
                
                function showInfoModal(text) {
                    infoModalText.textContent = text;
                    
                    // Show modal with animation
                    infoModal.classList.remove('hidden');
                    
                    // Trigger animation after a small delay
                    setTimeout(() => {
                        const modalContent = document.getElementById('info-modal-content');
                        modalContent.style.transform = 'scale(1)';
                        modalContent.style.opacity = '1';
                    }, 10);
                }

                cancelDeleteBtn.addEventListener('click', () => {
                    deleteModal.classList.add('hidden');
                    formToSubmit = null;
                });

                confirmDeleteBtn.addEventListener('click', () => {
                    if (formToSubmit) {
                        formToSubmit.submit();
                    }
                });

                closeInfoBtn.addEventListener('click', () => {
                    const modalContent = document.getElementById('info-modal-content');
                    modalContent.style.transform = 'scale(0.95)';
                    modalContent.style.opacity = '0';
                    
                    setTimeout(() => {
                        infoModal.classList.add('hidden');
                    }, 300);
                });

                // === Fungsi Modal Edit ===
                let currentVideoId = null;

                function showEditModal(videoId, currentTitle) {
                    currentVideoId = videoId;
                    editTitleInput.value = currentTitle;
                    updateCharCount();
                    
                    // Show modal with animation
                    editModal.classList.remove('hidden');
                    
                    // Trigger animation after a small delay
                    setTimeout(() => {
                        const modalContent = document.getElementById('edit-modal-content');
                        modalContent.style.transform = 'scale(1)';
                        modalContent.style.opacity = '1';
                    }, 10);
                    
                    // Focus input after animation
                    setTimeout(() => {
                        editTitleInput.focus();
                        editTitleInput.select();
                    }, 300);
                }

                function hideEditModal() {
                    const modalContent = document.getElementById('edit-modal-content');
                    modalContent.style.transform = 'scale(0.95)';
                    modalContent.style.opacity = '0';
                    
                    setTimeout(() => {
                        editModal.classList.add('hidden');
                        currentVideoId = null;
                        editTitleInput.value = '';
                        updateCharCount();
                    }, 300);
                }

                function updateCharCount() {
                    const charCount = document.getElementById('char-count');
                    const currentLength = editTitleInput.value.length;
                    const maxLength = 255;
                    
                    charCount.textContent = `${currentLength}/${maxLength}`;
                    
                    // Change color based on character count
                    if (currentLength > maxLength * 0.9) {
                        charCount.classList.remove('text-gray-400');
                        charCount.classList.add('text-red-500');
                    } else if (currentLength > maxLength * 0.7) {
                        charCount.classList.remove('text-gray-400', 'text-red-500');
                        charCount.classList.add('text-yellow-500');
                    } else {
                        charCount.classList.remove('text-yellow-500', 'text-red-500');
                        charCount.classList.add('text-gray-400');
                    }
                }

                // Event listeners untuk modal edit
                cancelEditBtn.addEventListener('click', hideEditModal);
                
                // Close modal when clicking close button
                document.getElementById('close-edit-modal').addEventListener('click', hideEditModal);
                
                // Close modal when clicking outside
                editModal.addEventListener('click', function(e) {
                    if (e.target === editModal) {
                        hideEditModal();
                    }
                });
                
                // Character counter
                editTitleInput.addEventListener('input', updateCharCount);
                
                // Escape key to close modal
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
                        hideEditModal();
                    }
                });

                editForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    if (!currentVideoId) return;

                    const newTitle = editTitleInput.value.trim();
                    if (!newTitle) {
                        // Add error styling
                        editTitleInput.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                        editTitleInput.focus();
                        
                        // Remove error styling after 3 seconds
                        setTimeout(() => {
                            editTitleInput.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                        }, 3000);
                        
                        return;
                    }

                    // Set loading state
                    setLoadingState(true);

                    try {
                        const response = await fetch(`/videos/${currentVideoId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title: newTitle
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Success animation
                            setSuccessState();
                            
                            setTimeout(() => {
                                hideEditModal();
                                showInfoModal('Judul video berhasil diperbarui!');
                                // Reload page to show updated data
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat memperbarui judul.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        setErrorState(error.message);
                    } finally {
                        // Reset loading state after delay
                        setTimeout(() => {
                            setLoadingState(false);
                        }, 1000);
                    }
                });

                function setLoadingState(isLoading) {
                    const saveIcon = document.getElementById('save-icon');
                    const saveText = document.getElementById('save-text');
                    
                    if (isLoading) {
                        saveEditBtn.disabled = true;
                        saveText.textContent = 'Menyimpan...';
                        
                        // Add spinning animation to icon
                        saveIcon.innerHTML = `
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        `;
                    } else {
                        saveEditBtn.disabled = false;
                        saveText.textContent = 'Simpan';
                        
                        // Reset icon
                        saveIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        `;
                    }
                }

                function setSuccessState() {
                    const saveIcon = document.getElementById('save-icon');
                    const saveText = document.getElementById('save-text');
                    
                    saveIcon.innerHTML = `
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                    saveText.textContent = 'Berhasil!';
                    saveEditBtn.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                    saveEditBtn.classList.add('from-green-500', 'to-green-600', 'bg-green-500');
                }

                function setErrorState(message) {
                    const saveIcon = document.getElementById('save-icon');
                    const saveText = document.getElementById('save-text');
                    
                    saveIcon.innerHTML = `
                        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `;
                    saveText.textContent = 'Gagal';
                    saveEditBtn.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                    saveEditBtn.classList.add('from-red-500', 'to-red-600', 'bg-red-500');
                    
                    // Show error message
                    setTimeout(() => {
                        alert(message);
                        // Reset button after showing error
                        setTimeout(() => {
                            setLoadingState(false);
                        }, 1000);
                    }, 500);
                }

                // Event listener untuk tombol edit
                document.body.addEventListener('click', function(event) {
                    if (event.target.closest('.edit-btn')) {
                        const button = event.target.closest('.edit-btn');
                        const videoId = button.dataset.videoId;
                        const currentTitle = button.dataset.videoTitle;
                        showEditModal(videoId, currentTitle);
                    }
                });


                // === Event Listener untuk Tombol Aksi ===
                if (bulkDeleteBtn) {
                    bulkDeleteBtn.addEventListener('click', () => {
                        if (selectedVideos.size > 0) {
                            // PERBAIKAN: Siapkan form SEBELUM menampilkan modal
                            document.getElementById('bulk-action-input').value = 'delete';
                            bulkActionForm.querySelectorAll('input[name="video_ids[]"]').forEach(input => input.remove());
                            selectedVideos.forEach(videoId => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'video_ids[]';
                                input.value = videoId;
                                bulkActionForm.appendChild(input);
                            });
                            showDeleteModal(bulkActionForm, `Apakah Anda yakin ingin menghapus ${selectedVideos.size} link yang dipilih?`);
                        } else {
                            alert('Pilih setidaknya satu link.');
                        }
                    });
                }

                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', e => {
                        e.preventDefault(); // Mencegah submit langsung
                        showDeleteModal(form, 'Apakah Anda yakin ingin menghapus link ini?');
                    });
                });

                if (bulkCopyBtn) {
                    bulkCopyBtn.addEventListener('click', () => {
                        let linksToCopy = [];
                        allCheckboxes.forEach(checkbox => {
                            if (checkbox.checked) {
                                linksToCopy.push(checkbox.dataset.link);
                            }
                        });
                        if (linksToCopy.length > 0) {
                            copyToClipboard(linksToCopy.join('\n'), `${linksToCopy.length} link berhasil disalin!`);
                        }
                    });
                }

                // Event listener untuk tombol salin individual
                document.body.addEventListener('click', function(event) {
                    if (event.target.closest('.copy-btn')) {
                        const button = event.target.closest('.copy-btn');
                        const linkToCopy = button.dataset.link;
                        copyToClipboard(linkToCopy, 'Link berhasil disalin!');
                    }
                });
            });
        </script>
</x-app-layout>
