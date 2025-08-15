<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload
        </h2>
    </x-slot>

    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="messageArea" class="mb-6"></div>

            {{-- Menampilkan pesan error jika link sudah diklaim --}}
            @if (session('claim_errors') && count(session('claim_errors')) > 0)
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <strong class="font-bold">Gagal mengklaim beberapa link:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach (session('claim_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Section -->
            <div>
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button id="tab-file" class="tab-btn tab-active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                            Upload dari komputer
                        </button>
                        <button id="tab-shortlink" class="tab-btn text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Generate dari link videy.co
                        </button>
                    </nav>
                </div>
                <div class="mt-8">
                    <!-- Panel Upload -->
                    <div id="panel-file">
                        <div class="bg-white border p-6 sm:p-8 rounded-lg">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Upload Video Baru</h2>
                            <div id="upload-flow-container">
                                <div id="upload-container">
                                    <p class="text-sm text-gray-500 mb-6">Seret dan lepas file video Anda di sini atau klik untuk memilih file. Maks 100MB.</p>
                                    <input type="file" id="selectedFile" class="hidden" accept="video/*">
                                    <div id="upload-box" class="mt-1 flex justify-center px-6 pt-10 pb-12 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:border-indigo-500 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <p class="text-xs text-gray-500 mt-2">MP4, WEBM, MOV hingga 100MB</p>
                                            <button type="button" id="upload-button" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Pilih File Video</button>
                                        </div>
                                    </div>
                                    <p id="upload-error" class="mt-2 text-sm text-red-600 hidden"></p>
                                </div>
                                <div id="progress-container" class="hidden">
                                    <div class="flex justify-between mb-1"><span id="progress-status" class="text-base font-medium text-indigo-700">Mengupload...</span><span id="progress-text" class="text-sm font-medium text-indigo-700">0%</span></div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5"><div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div></div>
                                </div>
                                <div id="result-container" class="hidden">
                                    <h3 class="text-lg font-semibold text-gray-900">Link Berhasil Dibuat!</h3>
                                    <div class="mt-2 flex items-center justify-between bg-gray-50 p-3 rounded-md border">
                                        <a href="#" id="result-link" target="_blank" class="font-semibold text-indigo-600 break-all"></a>
                                        <button type="button" id="result-copy-btn" class="copy-btn flex-shrink-0 ml-4 px-3 py-1 bg-gray-200 text-gray-700 text-xs font-bold rounded-full hover:bg-gray-300">Salin</button>
                                    </div>
                                    <button type="button" id="upload-another-btn" class="mt-6 w-full bg-gray-700 text-white py-2 px-4 rounded-md hover:bg-gray-800">Upload Video Lain</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Panel Generate Link -->
                    <div id="panel-shortlink" class="hidden">
                        <div class="bg-white  p-6 sm:p-8 rounded-lg">
                            <div id="generate-container">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Generate dari URL</h2>
                                <p class="text-sm text-gray-500 mb-6">Tempel satu atau lebih URL video dari `videy.co`. Pisahkan dengan baris baru.</p>
                                <form id="generateLinkForm">
                                    <textarea id="originalUrls" name="originalUrls" rows="8" class="w-full p-3 border border-black rounded-md" placeholder="Contoh:&#10;https://videy.co/v/?id=xxxx&#10;https://videy.co/v/?id=yyyy"></textarea>
                                    <button id="generateLinkButton" type="submit" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Generate Links</button>
                                </form>
                            </div>
                            <div id="generate-result-container" class="hidden">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Hasil Generate</h3>
                                    <button id="copy-all-generated-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Salin Semua</button>
                                </div>
                                <div id="generate-errors" class="mt-2"></div>
                                <textarea id="generate-success-textarea" readonly class="w-full p-3 border border-black rounded-md h-48 bg-gray-50"></textarea>
                                <button type="button" id="generate-another-btn" class="mt-6 w-full bg-gray-700 text-white py-2 px-4 rounded-md hover:bg-gray-800">Generate Video Lain</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Info (untuk Salin) -->
    <div id="info-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                 <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">Berhasil</h3>
            <p id="info-modal-text" class="mt-2 text-sm text-gray-500"></p>
            <div class="mt-6">
                <button id="close-info-btn" type="button" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">OK</button>
            </div>
        </div>
    </div>
    
    {{-- Memuat jQuery dari CDN --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // === Elemen UI Umum ===
            const messageArea = document.getElementById('messageArea');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanels = { file: document.getElementById('panel-file'), shortlink: document.getElementById('panel-shortlink') };

            // === Elemen UI untuk UPLOAD ===
            const uploadContainer = document.getElementById('upload-container');
            const progressContainer = document.getElementById('progress-container');
            const resultContainer = document.getElementById('result-container');
            const uploadBox = document.getElementById('upload-box');
            const fileInput = document.getElementById('selectedFile');
            const uploadButton = document.getElementById('upload-button');
            const resultLink = document.getElementById('result-link');
            const resultCopyBtn = document.getElementById('result-copy-btn');
            const uploadAnotherBtn = document.getElementById('upload-another-btn');

            // === Elemen UI untuk GENERATE LINK ===
            const generateContainer = document.getElementById('generate-container');
            const generateResultContainer = document.getElementById('generate-result-container');
            const generateLinkForm = document.getElementById('generateLinkForm');
            const generateLinkButton = document.getElementById('generateLinkButton');
            const originalUrlsTextarea = document.getElementById('originalUrls');
            const generateSuccessTextarea = document.getElementById('generate-success-textarea');
            const generateErrorsContainer = document.getElementById('generate-errors');
            const generateAnotherBtn = document.getElementById('generate-another-btn');
            const copyAllGeneratedBtn = document.getElementById('copy-all-generated-btn');
            
            // === Elemen Modal Info ===
            const infoModal = document.getElementById('info-modal');
            const infoModalText = document.getElementById('info-modal-text');
            const closeInfoBtn = document.getElementById('close-info-btn');

            // --- FUNGSI BANTUAN ---
            function displayMessage(message, type = "info") {
                const colorClass = type === 'success' ? 'bg-green-100 text-green-700' : type === 'error' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700';
                messageArea.innerHTML = `<div class="p-4 rounded-md ${colorClass}">${message}</div>`;
                setTimeout(() => { messageArea.innerHTML = ''; }, 5000);
            }

            function showInfoModal(text) {
                infoModalText.textContent = text;
                infoModal.classList.remove('hidden');
            }

            function copyToClipboard(text, successMessage) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showInfoModal(successMessage);
                } catch (err) {
                    alert('Gagal menyalin.');
                }
                document.body.removeChild(textarea);
            }
            
            // --- LOGIKA MODAL INFO ---
            if(closeInfoBtn) {
                closeInfoBtn.addEventListener('click', () => infoModal.classList.add('hidden'));
            }
            
            // --- LOGIKA TAB ---
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.id.split('-')[1];
                    tabButtons.forEach(btn => {
                        btn.classList.remove('tab-active', 'border-indigo-500', 'text-indigo-600');
                        btn.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    button.classList.add('tab-active', 'border-indigo-500', 'text-indigo-600');
                    button.classList.remove('text-gray-500');
                    Object.values(tabPanels).forEach(panel => panel.classList.add('hidden'));
                    if (tabPanels[tabId]) tabPanels[tabId].classList.remove('hidden');
                });
            });

            // --- LOGIKA UPLOAD ---
            if (uploadBox) {
                uploadBox.addEventListener('click', () => fileInput.click());
                uploadButton.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => handleFileUpload(fileInput.files));
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eName => {
                    uploadBox.addEventListener(eName, e => { e.preventDefault(); e.stopPropagation(); });
                });
                uploadBox.addEventListener('drop', (e) => handleFileUpload(e.dataTransfer.files));
            }
            if (uploadAnotherBtn) {
                uploadAnotherBtn.addEventListener('click', resetUploadUI);
            }

            function handleFileUpload(files) {
                if (files.length === 0) return;
                uploadVideo(files[0]);
            }

            function uploadVideo(file) {
                const formData = new FormData();
                formData.append('file', file);
                
                uploadContainer.classList.add('hidden');
                resultContainer.classList.add('hidden');
                progressContainer.classList.remove('hidden');

                const progressStatus = document.getElementById('progress-status');
                const progressText = document.getElementById('progress-text');
                const progressBar = document.getElementById('progress-bar');
                
                $.ajax({
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                progressBar.style.width = percentComplete + '%';
                                progressText.textContent = percentComplete + '%';
                                if (percentComplete === 100) progressStatus.textContent = "Processing...";
                            }
                        }, false);
                        return xhr;
                    },
                    url: `https://videy.co/api/upload`,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: async function (result) {
                        progressStatus.textContent = 'Menyimpan ke database...';
                        try {
                            const response = await fetch('/videos/save-link', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ original_url: result.link })
                            });
                            const saveData = await response.json();
                            if (!response.ok) throw new Error(saveData.message);

                            progressContainer.classList.add('hidden');
                            resultContainer.classList.remove('hidden');
                            
                            resultLink.href = saveData.video.generated_link;
                            resultLink.textContent = saveData.video.generated_link;
                            resultCopyBtn.dataset.link = saveData.video.generated_link;
                        } catch (error) {
                            displayMessage(error.message || 'Gagal menyimpan video.', 'error');
                            resetUploadUI();
                        }
                    },
                    error: function () {
                        displayMessage("Upload ke videy.co gagal.", 'error');
                        resetUploadUI();
                    }
                });
            }

            function resetUploadUI() {
                resultContainer.classList.add('hidden');
                progressContainer.classList.add('hidden');
                uploadContainer.classList.remove('hidden');
                fileInput.value = '';
                progressStatus.textContent = 'Mengupload...';
            }

            // --- LOGIKA GENERATE LINK ---
            if (generateLinkForm) {
                generateLinkForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const urls = originalUrlsTextarea.value.split(/\s+/).filter(url => url.trim() !== '');
                    if (urls.length === 0) return;

                    generateLinkButton.disabled = true;
                    generateLinkButton.textContent = 'Memproses...';

                    try {
                        const response = await fetch("{{ route('videos.generateFromLinks') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ urls: urls })
                        });
                        const result = await response.json();
                        
                        generateContainer.classList.add('hidden');
                        generateResultContainer.classList.remove('hidden');
                        
                        generateErrorsContainer.innerHTML = '';
                        if (result.errors && result.errors.length > 0) {
                            const errorList = result.errors.map(err => `<li>${err}</li>`).join('');
                            generateErrorsContainer.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4"><strong class="font-bold">Beberapa link gagal:</strong><ul class="list-disc list-inside mt-2">${errorList}</ul></div>`;
                        }
                        
                        generateSuccessTextarea.value = '';
                        if (result.new_videos && result.new_videos.length > 0) {
                            const successLinks = result.new_videos.map(video => video.generated_link).join('\n');
                            generateSuccessTextarea.value = successLinks;
                        }
                    } catch (error) {
                        displayMessage('Terjadi kesalahan.', 'error');
                    } finally {
                        generateLinkButton.disabled = false;
                        generateLinkButton.textContent = 'Generate Links';
                    }
                });
            }

            if (generateAnotherBtn) {
                generateAnotherBtn.addEventListener('click', () => {
                    generateResultContainer.classList.add('hidden');
                    generateContainer.classList.remove('hidden');
                    originalUrlsTextarea.value = '';
                });
            }
            
            if (copyAllGeneratedBtn) {
                copyAllGeneratedBtn.addEventListener('click', () => {
                    const textToCopy = generateSuccessTextarea.value;
                    if (textToCopy) {
                        copyToClipboard(textToCopy, 'Semua link berhasil disalin!');
                    }
                });
            }

            // --- LOGIKA TOMBOL SALIN (BERLAKU UNTUK SEMUA) ---
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
