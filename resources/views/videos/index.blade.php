<x-app-layout>
    {{-- CSS untuk Tabel Responsif --}}
    <style>
        @media screen and (max-width: 768px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { 
                display: block; 
                margin-bottom: 1rem;
                border-bottom: 2px solid #ddd;
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
            }
            .responsive-table td:last-child { border-bottom: 0; }
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
                                <option value="views_count" @if($filters['sort_by'] == 'views_count') selected @endif>Valid Views</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Views</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($videos as $video)
                            <tr>
                                <td class="p-4" data-label="Pilih"><input type="checkbox" class="link-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded" value="{{ $video->id }}" data-link="{{ $video->generated_link }}"></td>
                                <td class="px-6 py-4" data-label="Link">
                                    <a href="{{ $video->generated_link }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:underline break-all">{{ $video->generated_link }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Views">{{ $video->views_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Dibuat">{{ $video->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-label="Tindakan">
                                    <div class="flex items-center space-x-4">
                                        <button type="button" class="copy-btn text-gray-500 hover:text-indigo-600" data-link="{{ $video->generated_link }}">Salin</button>
                                        <form method="POST" action="{{ route('videos.destroy', $video) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn text-red-600 hover:text-red-800">Hapus</button>
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
    <div id="info-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                 <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 id="info-modal-title" class="text-lg font-medium text-gray-900 mt-5">Berhasil</h3>
            <p id="info-modal-text" class="mt-2 text-sm text-gray-500"></p>
            <div class="mt-6">
                <button id="close-info-btn" type="button" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">OK</button>
            </div>
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
                    infoModal.classList.remove('hidden');
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
                    infoModal.classList.add('hidden');
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
