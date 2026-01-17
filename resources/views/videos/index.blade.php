<x-app-layout>
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Folder Navigation -->
            <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                <div class="flex items-center gap-2 overflow-visible w-full md:w-auto">
                    @if($folders->count() <= 4)
                        {{-- Horizontal List Mode (Legacy) --}}
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 hide-scrollbar">
                            <a href="{{ route('videos.index') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ !request('folder_id') ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
                                Semua Video
                            </a>
                            @foreach($folders as $folder)
                                <div class="group relative flex items-center">
                                    <a href="{{ route('videos.index', ['folder_id' => $folder->id]) }}"
                                        class="px-4 py-2 pr-9 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('folder_id') == $folder->id ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
                                        {{ $folder->name }}
                                    </a>
                                    <button type="button"
                                        class="folder-settings-btn absolute right-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 {{ request('folder_id') == $folder->id ? 'text-indigo-200 hover:text-white' : '' }}"
                                        data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->name }}"
                                        data-folder-slug="{{ $folder->slug }}" data-folder-public="{{ $folder->public_link }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Dropdown Mode (Compact) --}}
                        <div class="relative relative-dropdown-container">
                            <button type="button" id="folder-dropdown-btn"
                                class="flex items-center justify-between gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors w-full md:w-64">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                                    {{ $currentFolder ? $currentFolder->name : 'Semua Video' }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="folder-dropdown-menu"
                                class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <a href="{{ route('videos.index') }}"
                                        class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 {{ !request('folder_id') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}">
                                        Semua Video
                                    </a>
                                    @foreach($folders as $folder)
                                        <div
                                            class="group flex items-center justify-between px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 {{ request('folder_id') == $folder->id ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                            <a href="{{ route('videos.index', ['folder_id' => $folder->id]) }}"
                                                class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-200 truncate {{ request('folder_id') == $folder->id ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                                                {{ $folder->name }}
                                            </a>
                                            <button type="button"
                                                class="folder-settings-btn p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-gray-600 rounded-lg transition-colors"
                                                data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->name }}"
                                                data-folder-slug="{{ $folder->slug }}"
                                                data-folder-public="{{ $folder->public_link }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Create Folder Button --}}
                    @php
                        $folderCount = $folders->count();
                        $folderLimit = auth()->user()->max_folders;
                        $isFolderLimitReached = $folderCount >= $folderLimit;
                    @endphp

                    <div class="flex items-center gap-2">
                        <button type="button" id="create-folder-btn" @if($isFolderLimitReached) disabled
                        title="Maksimal folder tercapai" @endif
                            class="px-3 py-2 rounded-lg border-2 border-dashed {{ $isFolderLimitReached ? 'border-red-200 text-red-500 cursor-not-allowed bg-red-50 dark:bg-red-900/10 dark:border-red-900/30' : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-indigo-500 hover:text-indigo-500' }} text-sm font-medium transition-colors whitespace-nowrap flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Folder Baru
                            @if($isFolderLimitReached) <span class="text-xs ml-1 font-bold">(Penuh)</span> @endif
                        </button>
                    </div>
                </div>

                @if(isset($currentFolder))
                    <div class="flex items-center gap-2">
                        <a href="{{ $currentFolder->public_link }}" target="_blank"
                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Lihat Folder Publik
                        </a>
                    </div>
                @endif
            </div>

            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <h1
                            class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 tracking-tight">
                            Kelola Link
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur dan monitor semua link video Anda
                            di sini.</p>
                    </div>

                    <a href="{{ route('videos.create') }}"
                        class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <span
                            class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                        <svg class="w-5 h-5 mr-2 -ml-1 transition-transform group-hover:rotate-90" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Buat Link Baru
                    </a>
                </div>

                <!-- Usage Dashboard (Stats Grid) -->
                <div
                    class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Global Folder Usage --}}
                    @php
                        $folderCount = $folders->count();
                        $folderLimit = auth()->user()->max_folders;
                        $folderPercent = ($folderCount / $folderLimit) * 100;
                        $isFolderFull = $folderCount >= $folderLimit;
                    @endphp
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600 dark:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Penggunaan
                                    Folder</span>
                                <span
                                    class="text-xs font-semibold {{ $isFolderFull ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $folderCount }} / {{ $folderLimit }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="{{ $isFolderFull ? 'bg-red-500' : 'bg-indigo-600' }} h-2 rounded-full transition-all duration-500"
                                    style="width: {{ min($folderPercent, 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Current Folder Capacity (Conditional) --}}
                    @if(isset($currentFolder) && $currentFolder)
                        @php
                            $videoCount = $currentFolder->videos->count();
                            $videoLimit = auth()->user()->max_videos_per_folder;
                            $videoPercent = ($videoCount / $videoLimit) * 100;
                            $isVideoFull = $videoCount >= $videoLimit;
                        @endphp
                        <div class="flex items-center gap-4 animate-fade-in">
                            <div
                                class="p-3 bg-violet-50 dark:bg-violet-900/20 rounded-xl text-violet-600 dark:text-violet-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate pr-2">Kapasitas:
                                        {{ $currentFolder->name }}</span>
                                    <span
                                        class="text-xs font-semibold {{ $isVideoFull ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $videoCount }} / {{ $videoLimit }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="{{ $isVideoFull ? 'bg-red-500' : 'bg-violet-600' }} h-2 rounded-full transition-all duration-500"
                                        style="width: {{ min($videoPercent, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Total Links --}}
                        <div class="hidden md:flex items-center gap-4 opacity-70">
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-xl text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Semua
                                        Link</span>
                                    <span
                                        class="text-xs font-semibold text-gray-500">{{ auth()->user()->videos()->count() }}
                                        Link</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gray-300 dark:bg-gray-600 h-2 rounded-full w-full"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Merged Panel: Control & Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-200">

                <!-- Header / Controls -->
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                        <!-- Left Side: Thumb Toggle & Filters -->
                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                            <!-- Select All -->
                            <div
                                class="flex items-center bg-white dark:bg-gray-700 rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-600 shadow-sm">
                                <input type="checkbox" id="select-all-checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 cursor-pointer">
                                <label for="select-all-checkbox"
                                    class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">Semua</label>
                            </div>

                            <!-- Thumbnail Toggle Switch -->
                            <label
                                class="inline-flex items-center cursor-pointer bg-white dark:bg-gray-700 rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-600 shadow-sm select-none">
                                <input type="checkbox" id="toggle-thumbnails" class="sr-only peer">
                                <div
                                    class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600">
                                </div>
                                <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">Thumbnail</span>
                            </label>

                            <!-- Filters -->
                            <form action="{{ route('videos.index') }}" method="GET" class="flex items-center gap-2">
                                <!-- Per Page Selector -->
                                <select name="per_page"
                                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="10" @if(($filters['per_page'] ?? 10) == 10) selected @endif>10 per hal
                                    </option>
                                    <option value="20" @if(($filters['per_page'] ?? '') == 20) selected @endif>20 per hal
                                    </option>
                                    <option value="50" @if(($filters['per_page'] ?? '') == 50) selected @endif>50 per hal
                                    </option>
                                    <option value="100" @if(($filters['per_page'] ?? '') == 100) selected @endif>100 per
                                        hal</option>
                                    <option value="500" @if(($filters['per_page'] ?? '') == 500) selected @endif>500 per
                                        hal</option>
                                </select>

                                <select name="sort_by"
                                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="created_at" @if(($filters['sort_by'] ?? '') == 'created_at') selected
                                    @endif>Tanggal</option>
                                    <option value="views_count" @if(($filters['sort_by'] ?? '') == 'views_count') selected
                                    @endif>Views</option>
                                </select>
                                <select name="sort_dir"
                                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="desc" @if(($filters['sort_dir'] ?? '') == 'desc') selected @endif>
                                        Terbaru</option>
                                    <option value="asc" @if(($filters['sort_dir'] ?? '') == 'asc') selected @endif>Terlama
                                    </option>
                                </select>

                                <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                            </form>
                        </div>

                        <!-- Right Side: Search & Bulk Actions -->
                        <div class="flex items-center w-full md:w-auto md:justify-end gap-2">
                            <!-- Search -->
                            <form id="search-container" action="{{ route('videos.index') }}" method="GET"
                                class="relative flex-1 md:flex-none md:w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm"
                                    placeholder="Cari video..." value="{{ $filters['search'] ?? '' }}">
                            </form>

                            <!-- Bulk Actions (Hidden by default) -->
                            <div id="bulk-action-bar"
                                class="hidden flex items-center gap-2 animate-fade-in pl-2 md:pl-4 md:border-l border-gray-200 dark:border-gray-700">
                                <span id="selected-count"
                                    class="hidden md:inline-block text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">0</span>
                                <button id="bulk-copy-btn"
                                    class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                    title="Salin Link">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                        </path>
                                    </svg>
                                </button>
                                <button id="bulk-move-btn"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-blue-200 dark:border-blue-900/50 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                    title="Pindahkan ke Folder">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </button>
                                <button id="bulk-delete-btn"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-900/30"
                                    title="Hapus">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] md:min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="p-4 w-10"><input type="checkbox" id="select-all-checkbox-table"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                                </th>
                                <!-- Thumbnail Column Header (Toggleable) -->
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider thumbnail-col hidden">
                                    Thumbnail</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Judul Video</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    ID Video</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Views</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Dibuat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($videos as $video)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                    <td class="p-4"><input type="checkbox"
                                            class="link-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                                            value="{{ $video->id }}" data-link="{{ $video->generated_link }}"></td>

                                    <!-- Thumbnail Column (Toggleable) -->
                                    <td class="px-6 py-4 whitespace-nowrap thumbnail-col hidden">
                                        @if($video->thumbnail_url)
                                            <div class="relative w-24 h-14 bg-gray-200 dark:bg-gray-700 rounded overflow-hidden cursor-pointer view-thumbnail-btn group/thumb"
                                                data-image-url="{{ $video->thumbnail_url }}"
                                                data-video-title="{{ $video->title }}">
                                                <img src="{{ $video->thumbnail_url }}" alt="Thumb" loading="lazy"
                                                    class="w-full h-full object-cover">
                                                <div
                                                    class="absolute inset-0 bg-black/0 group-hover/thumb:bg-black/20 transition-colors flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white opacity-0 group-hover/thumb:opacity-100 transition-opacity drop-shadow-md"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 border border-transparent">
                                                No Image
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Judul (2 Lines) -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 max-w-[250px]"
                                                title="{{ $video->title }}">{{ $video->title }}</div>
                                        </div>
                                    </td>

                                    <!-- ID / Link -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button type="button"
                                            class="copy-btn group flex items-center space-x-2 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                                            data-link="{{ $video->generated_link }}" title="Klik untuk menyalin link">
                                            <span
                                                class="font-mono bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded text-xs group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors border border-indigo-100 dark:border-indigo-800">
                                                {{ $video->video_code }}
                                            </span>
                                            <svg class="h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity text-indigo-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>

                                    <!-- Info Lain -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($video->views_count) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $video->created_at->format('d M Y') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button type="button"
                                                class="edit-btn p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors dark:text-blue-400 dark:hover:bg-blue-900/30"
                                                data-video-id="{{ $video->id }}" data-video-title="{{ $video->title }}"
                                                title="Edit">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('videos.destroy', $video) }}"
                                                class="delete-form inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="delete-btn p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors dark:text-red-400 dark:hover:bg-red-900/30"
                                                    title="Hapus">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Belum ada video
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai dengan membuat
                                                link baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $videos->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    <!-- Hidden Forms & Modals -->
    <!-- Form tersembunyi untuk Bulk Action -->
    <form id="bulk-action-form" action="{{ route('videos.bulkAction') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input">
        <input type="hidden" name="folder_id" id="bulk-folder-id-input">
    </form>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-confirm-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all scale-100">
            <div class="p-6 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" stroke="currentColor" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hapus Link</h3>
                <p id="delete-modal-text" class="mt-2 text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin? Aksi
                    ini tidak dapat dibatalkan.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-center gap-3">
                <button id="cancel-delete-btn" type="button"
                    class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Batal</button>
                <button id="confirm-delete-btn" type="button"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-colors">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Thumbnail -->
    <div id="thumbnail-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-300">
        <div
            class="relative bg-black rounded-xl overflow-hidden shadow-2xl max-w-3xl w-full max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center p-4 bg-black/50 absolute top-0 left-0 right-0 z-10">
                <h3 id="thumbnail-modal-title" class="text-white font-medium text-sm truncate pr-4">Video Thumbnail</h3>
                <button id="close-thumbnail-btn" type="button"
                    class="text-white/70 hover:text-white bg-black/20 hover:bg-white/20 rounded-full p-1 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 flex items-center justify-center bg-black p-0 overflow-hidden">
                <img id="thumbnail-modal-image" src="" alt="Thumbnail" class="max-w-full max-h-[80vh] object-contain"
                    loading="lazy">
            </div>
        </div>
    </div>

    <!-- Modal Edit Title (Reused & Styled) -->
    <div id="edit-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md transform transition-all scale-100"
            id="edit-modal-content">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Judul Video</h3>
                <button id="close-edit-modal" type="button"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="edit-form" class="p-6">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label for="edit-title-input"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Video</label>
                        <input type="text" id="edit-title-input" name="title"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors"
                            required>
                        <div class="flex justify-end mt-1">
                            <span id="char-count" class="text-xs text-gray-400">0/255</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="cancel-edit-btn" type="button"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none transition-colors">Batal</button>
                    <button id="save-edit-btn" type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none shadow-sm transition-colors flex items-center">
                        <span id="save-text">Simpan</span>
                        <svg id="save-icon" class="ml-2 h-4 w-4 hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Create Folder -->
    <div id="create-folder-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm transform transition-all scale-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Buat Folder Baru</h3>
                <form action="{{ route('folders.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="folder-name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Folder</label>
                        <input type="text" id="folder-name" name="name"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors"
                            required>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="cancel-create-folder"
                            class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Buat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Manage Folder (Rename/Delete) -->
    <div id="manage-folder-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm transform transition-all scale-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pengaturan Folder</h3>

                <!-- Rename Form -->
                <form id="rename-folder-form" method="POST" class="mb-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="edit-folder-name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Folder</label>
                        <input type="text" id="edit-folder-name" name="name"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors"
                            required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Simpan
                            Nama</button>
                    </div>
                </form>

                <hr class="border-gray-200 dark:border-gray-700 my-4">

                <!-- Delete Form -->
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400">Hapus Folder</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Link di dalam folder tidak akan terhapus.
                        </p>
                    </div>
                    <form id="delete-folder-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors dark:bg-red-900/30 dark:text-red-400 dark:border-red-900/50 dark:hover:bg-red-900/50">Hapus</button>
                    </form>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" id="close-manage-folder"
                        class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Move to Folder -->
    <div id="move-folder-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm transform transition-all scale-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pindahkan ke Folder</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pilih folder tujuan untuk link yang dipilih.
                </p>

                <div class="flex flex-col gap-2 max-h-60 overflow-y-auto">
                    @foreach($folders as $folder)
                        @php
                            $isFull = $folder->videos()->count() >= auth()->user()->max_videos_per_folder;
                        @endphp
                        <button type="button"
                            class="select-folder-btn flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left {{ $isFull ? 'opacity-50 cursor-not-allowed' : '' }}"
                            data-folder-id="{{ $folder->id }}" @if($isFull) disabled title="Folder Penuh" @endif>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $folder->name }}
                                @if($isFull) <span class="text-xs text-red-500 ml-2">(Penuh)</span> @endif
                            </span>
                            <span class="text-xs text-gray-400">{{ $folder->videos()->count() }} /
                                {{ auth()->user()->max_videos_per_folder }}</span>
                        </button>
                    @endforeach
                    <button type="button"
                        class="select-folder-btn flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left"
                        data-folder-id="">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 italic">Lepaskan dari Folder
                            (Umum)</span>
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" id="cancel-move-folder"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // === Utilities ===
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            const showToast = (message, type = 'success') => {
                Toast.fire({
                    icon: type,
                    title: message
                });
            };

            const copyToClipboard = (text, message = 'Link disalin ke clipboard') => {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showToast(message);
                    }).catch(() => {
                        fallbackCopyTextToClipboard(text, message);
                    });
                } else {
                    fallbackCopyTextToClipboard(text, message);
                }
            };

            const fallbackCopyTextToClipboard = (text, message) => {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                textarea.style.top = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showToast(message);
                    } else {
                        showToast('Gagal menyalin link', 'error');
                    }
                } catch (err) {
                    showToast('Gagal menyalin link', 'error');
                }
                document.body.removeChild(textarea);
            };

            // === Bulk Actions UI ===
            const allCheckboxes = document.querySelectorAll('.link-checkbox');
            const selectAllCheckboxes = document.querySelectorAll('#select-all-checkbox, #select-all-checkbox-table');
            const bulkActionBar = document.getElementById('bulk-action-bar');
            const searchContainer = document.getElementById('search-container');
            const selectedCountSpan = document.getElementById('selected-count');
            const selectedVideos = new Set();

            const updateBulkUI = () => {
                const count = selectedVideos.size;
                selectedCountSpan.textContent = count;

                if (count > 0) {
                    bulkActionBar.classList.remove('hidden');
                    searchContainer.classList.add('hidden', 'md:hidden'); // Hide on mobile too if active
                } else {
                    bulkActionBar.classList.add('hidden');
                    searchContainer.classList.remove('hidden', 'md:hidden');
                }

                const allChecked = count === allCheckboxes.length && count > 0;
                selectAllCheckboxes.forEach(cb => cb.checked = allChecked);
            };

            allCheckboxes.forEach(cb => {
                cb.addEventListener('change', (e) => {
                    e.target.checked ? selectedVideos.add(e.target.value) : selectedVideos.delete(e.target.value);
                    updateBulkUI();
                });
            });

            selectAllCheckboxes.forEach(master => {
                master.addEventListener('change', (e) => {
                    const checked = e.target.checked;
                    allCheckboxes.forEach(cb => {
                        cb.checked = checked;
                        checked ? selectedVideos.add(cb.value) : selectedVideos.delete(cb.value);
                    });
                    updateBulkUI();
                });
            });

            // === Thumbnail Toggle Logic ===
            const thumbToggle = document.getElementById('toggle-thumbnails');
            const thumbCols = document.querySelectorAll('.thumbnail-col');

            // Load saved state
            const savedThumbState = localStorage.getItem('showThumbnails');
            if (savedThumbState === 'true') {
                thumbToggle.checked = true;
                thumbCols.forEach(el => el.classList.remove('hidden'));
            }

            thumbToggle.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                localStorage.setItem('showThumbnails', isChecked);
                thumbCols.forEach(el => {
                    if (isChecked) el.classList.remove('hidden');
                    else el.classList.add('hidden');
                });
            });

            // === Actions (Copy, Delete) ===
            document.body.addEventListener('click', (e) => {
                const copyBtn = e.target.closest('.copy-btn');
                if (copyBtn) {
                    copyToClipboard(copyBtn.dataset.link);
                }
            });

            // Bulk Copy
            document.getElementById('bulk-copy-btn')?.addEventListener('click', () => {
                const links = Array.from(allCheckboxes)
                    .filter(cb => selectedVideos.has(cb.value))
                    .map(cb => cb.dataset.link);

                if (links.length) {
                    copyToClipboard(links.join('\n'), `${links.length} link disalin`);
                }
            });

            // === Modals Management ===
            const toggleModal = (modalId, show = true) => {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                if (show) {
                    modal.classList.remove('hidden');
                } else {
                    modal.classList.add('hidden');
                }
            };

            // Thumbnail Modal
            const thumbModal = document.getElementById('thumbnail-modal');
            const thumbImg = document.getElementById('thumbnail-modal-image');
            const thumbTitle = document.getElementById('thumbnail-modal-title');

            document.body.addEventListener('click', (e) => {
                const btn = e.target.closest('.view-thumbnail-btn');
                if (btn) {
                    thumbImg.src = btn.dataset.imageUrl;
                    thumbTitle.textContent = btn.dataset.videoTitle || 'Thumbnail';
                    toggleModal('thumbnail-modal', true);
                }
            });

            document.getElementById('close-thumbnail-btn')?.addEventListener('click', () => toggleModal('thumbnail-modal', false));
            thumbModal?.addEventListener('click', (e) => {
                if (e.target === thumbModal) toggleModal('thumbnail-modal', false);
            });

            // Delete Modal & Logic
            let formToSubmit = null;
            const deleteModal = document.getElementById('delete-confirm-modal');

            const showDeleteConfirm = (form, text) => {
                formToSubmit = form;
                document.getElementById('delete-modal-text').textContent = text;
                toggleModal('delete-confirm-modal', true);
            };

            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    showDeleteConfirm(form, 'Yakin ingin menghapus link ini?');
                });
            });

            document.getElementById('bulk-delete-btn')?.addEventListener('click', () => {
                if (!selectedVideos.size) return;
                const form = document.getElementById('bulk-action-form');
                form.querySelector('#bulk-action-input').value = 'delete';

                // Clear old inputs
                form.querySelectorAll('input[name="video_ids[]"]').forEach(el => el.remove());

                // Add new inputs
                selectedVideos.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'video_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                showDeleteConfirm(form, `Yakin menghapus ${selectedVideos.size} link terpilih?`);
            });

            document.getElementById('confirm-delete-btn')?.addEventListener('click', () => formToSubmit?.submit());
            document.getElementById('cancel-delete-btn')?.addEventListener('click', () => toggleModal('delete-confirm-modal', false));

            // Edit Modal Logic
            const editModal = document.getElementById('edit-modal');
            const editTitleInput = document.getElementById('edit-title-input');
            let currentEditId = null;

            document.body.addEventListener('click', (e) => {
                const btn = e.target.closest('.edit-btn');
                if (btn) {
                    currentEditId = btn.dataset.videoId;
                    editTitleInput.value = btn.dataset.videoTitle;
                    toggleModal('edit-modal', true);
                }
            });

            document.getElementById('close-edit-modal')?.addEventListener('click', () => toggleModal('edit-modal', false));
            document.getElementById('cancel-edit-btn')?.addEventListener('click', () => toggleModal('edit-modal', false));

            document.getElementById('edit-form')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!currentEditId) return;

                const btn = document.getElementById('save-edit-btn');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Saving...';

                try {
                    const res = await fetch(`/videos/${currentEditId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: editTitleInput.value })
                    });

                    if (res.ok) {
                        showToast('Judul berhasil diupdate');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        throw new Error('Gagal update');
                    }
                } catch (err) {
                    showToast(err.message || 'Error', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });

            // Char counter logic
            editTitleInput?.addEventListener('input', function () {
                document.getElementById('char-count').textContent = `${this.value.length}/255`;
            });

            // === FOLDER LOGIC (Event Delegation) ===

            document.body.addEventListener('click', (e) => {
                // Create Folder Button
                const createBtn = e.target.closest('#create-folder-btn');
                if (createBtn) {
                    toggleModal('create-folder-modal', true);
                    return;
                }

                // Close Buttons (Delegated for consistency, though ID listeners work too)
                if (e.target.closest('#cancel-create-folder')) {
                    toggleModal('create-folder-modal', false);
                    return;
                }

                // Manage Folder Button (Settings)
                const settingsBtn = e.target.closest('.folder-settings-btn');
                if (settingsBtn) {
                    e.preventDefault();
                    e.stopPropagation();

                    const id = settingsBtn.dataset.folderId;
                    const name = settingsBtn.dataset.folderName;
                    const renameForm = document.getElementById('rename-folder-form');
                    const deleteForm = document.getElementById('delete-folder-form');
                    const nameInput = document.getElementById('edit-folder-name');

                    if (renameForm) renameForm.action = `/folders/${id}`;
                    if (deleteForm) deleteForm.action = `/folders/${id}`;
                    if (nameInput) nameInput.value = name;

                    toggleModal('manage-folder-modal', true);
                    return;
                }

                // Bulk Move Button
                const bulkMoveBtn = e.target.closest('#bulk-move-btn');
                if (bulkMoveBtn) {
                    if (selectedVideos.size === 0) return;
                    toggleModal('move-folder-modal', true);
                    return;
                }

                // Select Folder in Move Modal
                const selectFolderBtn = e.target.closest('.select-folder-btn');
                if (selectFolderBtn) {
                    const folderId = selectFolderBtn.dataset.folderId;
                    const form = document.getElementById('bulk-action-form');

                    form.querySelector('#bulk-action-input').value = 'move';
                    form.querySelector('#bulk-folder-id-input').value = folderId;

                    // Clear old inputs
                    form.querySelectorAll('input[name="video_ids[]"]').forEach(el => el.remove());

                    // Add new inputs
                    selectedVideos.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'video_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    form.submit();
                    return;
                }

                // Close Modals on Backdrop Click
                if (e.target.id === 'create-folder-modal') toggleModal('create-folder-modal', false);
                if (e.target.id === 'manage-folder-modal') toggleModal('manage-folder-modal', false);
                if (e.target.id === 'move-folder-modal') toggleModal('move-folder-modal', false);
            });

            // Close buttons specific IDs (Keep existing or rely on delegation? Delegation covers logic but explicit IDs are fine)
            // Close buttons specific IDs (Keep existing or rely on delegation? Delegation covers logic but explicit IDs are fine)
            document.getElementById('close-manage-folder')?.addEventListener('click', () => toggleModal('manage-folder-modal', false));
            document.getElementById('cancel-move-folder')?.addEventListener('click', () => toggleModal('move-folder-modal', false));

            // === Session Flash Messages ===
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif

            // === Folder Dropdown Logic ===
            const dropdownBtn = document.getElementById('folder-dropdown-btn');
            const dropdownMenu = document.getElementById('folder-dropdown-menu');

            if (dropdownBtn && dropdownMenu) {
                dropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!dropdownMenu.contains(e.target) && !dropdownBtn.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>