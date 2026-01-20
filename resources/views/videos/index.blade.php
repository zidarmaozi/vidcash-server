<x-app-layout>
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        $folderCount = $totalFolderCount;
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

            <!-- Breadcrumb & Navigation -->
            <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('videos.index') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                </svg>
                                Home
                            </a>
                        </li>
                        @if($currentFolder)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                    <span
                                        class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $currentFolder->name }}</span>
                                </div>
                            </li>
                        @endif
                    </ol>
                </nav>

                <div class="flex gap-2">
                    @if(isset($currentFolder))
                        <button type="button"
                            class="copy-btn px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-2"
                            data-link="{{ $currentFolder->public_link }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            Salin Link Folder
                        </button>
                    @endif

                    @php
                        $folderCount = $folders->count(); // In root view only, usually
                        $folderLimit = auth()->user()->max_folders;
                        $isFolderLimitReached = auth()->user()->folders()->count() >= $folderLimit;
                    @endphp

                    @if(!$currentFolder && !$filters['search'])
                        <button type="button" id="create-folder-btn" @if($isFolderLimitReached) disabled
                        title="Maksimal folder tercapai" @endif
                            class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors flex items-center gap-2 {{ $isFolderLimitReached ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Folder Baru
                        </button>
                    @endif
                </div>
            </div>


            <!-- Toolbar: Search, Filter, Sort, View Toggle -->
            <div
                class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <!-- Left: Search & Select All -->
                <div class="flex items-center gap-3 w-full md:w-auto flex-1">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <form action="{{ route('videos.index') }}" method="GET" class="w-full">
                            @if(request('folder_id')) <input type="hidden" name="folder_id"
                            value="{{ request('folder_id') }}"> @endif
                            @if(request('per_page')) <input type="hidden" name="per_page"
                            value="{{ request('per_page') }}"> @endif
                            <input type="text" name="search" value="{{ $filters['search'] }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Cari video...">
                        </form>
                    </div>
                </div>

                <!-- Right: Filters, Sort, View Toggle -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-between md:justify-end">
                    <!-- Sort Dropdown -->
                    <form id="sort-form" action="{{ route('videos.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-2 w-full md:w-auto flex-1 md:flex-none">
                        @if(request('folder_id')) <input type="hidden" name="folder_id"
                        value="{{ request('folder_id') }}"> @endif
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <select name="sort_by" onchange="document.getElementById('sort-form').submit()"
                            class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 flex-1 w-full sm:w-auto">
                            <option value="created_at" {{ $filters['sort_by'] == 'created_at' ? 'selected' : '' }}>Tanggal
                            </option>
                            <option value="views_count" {{ $filters['sort_by'] == 'views_count' ? 'selected' : '' }}>Views
                            </option>
                            <option value="title" {{ $filters['sort_by'] == 'title' ? 'selected' : '' }}>Nama</option>
                        </select>
                        <select name="sort_dir" onchange="document.getElementById('sort-form').submit()"
                            class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 flex-1 w-full sm:w-auto">
                            <option value="desc" {{ $filters['sort_dir'] == 'desc' ? 'selected' : '' }}>Baru</option>
                            <option value="asc" {{ $filters['sort_dir'] == 'asc' ? 'selected' : '' }}>Lama</option>
                        </select>
                        <select name="per_page" onchange="document.getElementById('sort-form').submit()"
                            class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 flex-1 w-full sm:w-auto">
                            @foreach([10, 20, 30, 40, 50] as $size)
                                <option value="{{ $size }}" {{ $filters['per_page'] == $size ? 'selected' : '' }}>{{ $size }}
                                    / Halaman</option>
                            @endforeach
                        </select>
                    </form>

                    <!-- View Toggle -->
                    <div
                        class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1 border border-gray-200 dark:border-gray-600 ml-auto md:ml-0">
                        <button type="button" id="view-grid-btn"
                            class="p-2 rounded-md transition-colors text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white active-view">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                        </button>
                        <button type="button" id="view-list-btn"
                            class="p-2 rounded-md transition-colors text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- UNIFIED CONTENT CONTAINER -->
            <div id="unified-content-wrapper">

                <!-- GRID VIEW -->
                <div id="grid-view-container"
                    class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 mb-8">
                    <!-- Folders (Grid) -->
                    @if(!$currentFolder && !$filters['search'])
                        @foreach($folders as $folder)
                            <a href="{{ route('videos.index', ['folder_id' => $folder->id]) }}"
                                class="group relative flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-md transition-all cursor-pointer h-full">
                                <div class="mb-3 text-yellow-400 group-hover:scale-110 transition-transform duration-200">
                                    <svg class="w-16 h-16" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                                    </svg>
                                </div>
                                <h3
                                    class="text-sm font-medium text-gray-700 dark:text-gray-200 text-center truncate w-full mb-1 group-hover:text-indigo-600">
                                    {{ $folder->name }}
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $folder->videos_count }} items</span>

                                <button type="button"
                                    class="folder-settings-btn absolute top-2 right-2 p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-gray-200 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity"
                                    data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->name }}"
                                    data-folder-slug="{{ $folder->slug }}" data-folder-public="{{ $folder->public_link }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                        </path>
                                    </svg>
                                </button>
                            </a>
                        @endforeach
                    @endif

                    <!-- Videos (Grid) -->
                    @foreach($videos as $video)
                        <div
                            class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all flex flex-col h-full z-0 hover:z-30">
                            <!-- Thumbnail -->
                            <div class="aspect-video relative bg-gray-100 dark:bg-gray-900 rounded-t-xl overflow-hidden cursor-pointer view-thumbnail-btn"
                                data-image-url="{{ $video->thumbnail_path ? Storage::url($video->thumbnail_path) : asset('images/default-thumbnail.jpg') }}"
                                data-video-title="{{ $video->title }}">
                                <img src="{{ $video->thumbnail_path ? Storage::url($video->thumbnail_path) : asset('images/default-thumbnail.jpg') }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span
                                    class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-1.5 py-0.5 rounded">
                                    {{ $video->duration ?? '00:00' }}
                                </span>
                                <!-- Selection Checkbox Overlay -->
                                <div class="absolute top-2 left-2 z-10">
                                    <input type="checkbox" name="video_ids[]" value="{{ $video->id }}"
                                        data-link="{{ $video->generated_link }}"
                                        class="video-checkbox w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm opacity-100 md:opacity-0 md:group-hover:opacity-100 checked:opacity-100 transition-opacity">
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-100 line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors"
                                    title="{{ $video->title }}">
                                    {{ $video->title }}
                                </h3>
                                <div
                                    class="mt-auto flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $video->views_count }} views</span>
                                    <span>{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <!-- Actions Dropdown (Alpine.js) -->
                            <div class="absolute top-2 right-2 z-20" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false" type="button"
                                    class="p-1.5 rounded-full bg-black/50 hover:bg-black/70 text-white backdrop-blur-sm transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 focus:outline-none overflow-hidden"
                                    style="display: none;">

                                    <!-- Copy -->
                                    <button type="button"
                                        class="copy-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                        data-link="{{ $video->generated_link }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Salin Link
                                    </button>

                                    <!-- Move -->
                                    <button type="button"
                                        class="move-video-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                        data-video-id="{{ $video->id }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Pindahkan
                                    </button>

                                    <!-- Edit -->
                                    <button type="button"
                                        class="edit-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                        data-video-id="{{ $video->id }}" data-video-title="{{ $video->title }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Edit Judul
                                    </button>

                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                    <!-- Delete -->
                                    <form action="{{ route('videos.destroy', $video->id) }}" method="POST"
                                        class="delete-form w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- LIST VIEW -->
                <div id="list-view-container"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden overflow-x-auto md:overflow-visible hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="pl-6 pr-2 py-3 text-left w-10">
                                    <input type="checkbox" id="select-all-main"
                                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th scope="col"
                                    class="pl-2 pr-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Views</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Folders (List) -->
                            @if(!$currentFolder && !$filters['search'])
                                @foreach($folders as $folder)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group cursor-pointer"
                                        onclick="if(!event.target.closest('.no-propagate')) window.location='{{ route('videos.index', ['folder_id' => $folder->id]) }}'">
                                        <td class="pl-6 pr-2 py-4 whitespace-nowrap text-center">
                                            <!-- Disabled Checkbox for Folder -->
                                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 mx-auto" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4"></path>
                                            </svg>
                                        </td>
                                        <td class="pl-2 pr-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/20 rounded text-yellow-500">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $folder->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $folder->videos_count }} item
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Folder
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                            -
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium no-propagate">
                                            <button type="button"
                                                class="folder-settings-btn text-gray-400 hover:text-indigo-600 transition-colors p-2"
                                                data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->name }}"
                                                data-folder-slug="{{ $folder->slug }}"
                                                data-folder-public="{{ $folder->public_link }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            <!-- Videos (List) -->
                            @foreach($videos as $video)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="pl-6 pr-2 py-4 whitespace-nowrap w-10">
                                        <input type="checkbox" name="video_ids[]" value="{{ $video->id }}"
                                            data-link="{{ $video->generated_link }}"
                                            class="video-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="pl-2 pr-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-16 bg-gray-100 dark:bg-gray-900 rounded overflow-hidden relative cursor-pointer view-thumbnail-btn"
                                                data-image-url="{{ $video->thumbnail_path ? Storage::url($video->thumbnail_path) : asset('images/default-thumbnail.jpg') }}"
                                                data-video-title="{{ $video->title }}">
                                                <img class="h-10 w-16 object-cover"
                                                    src="{{ $video->thumbnail_path ? Storage::url($video->thumbnail_path) : asset('images/default-thumbnail.jpg') }}">
                                            </div>
                                            <div class="ml-4 max-w-xs">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                    title="{{ $video->title }}">
                                                    {{ $video->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $video->created_at->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($video->is_active)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                        @endif
                                        @if(!$video->is_safe_content)
                                            <span class="ml-1 text-xs text-red-500" title="18+">🔞</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($video->views_count ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium relative">
                                        <div class="inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open" @click.away="open = false" type="button"
                                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 focus:outline-none z-50 text-left"
                                                style="display: none;">

                                                <!-- Copy -->
                                                <button type="button"
                                                    class="copy-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                                    data-link="{{ $video->generated_link }}">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    Salin Link
                                                </button>

                                                <!-- Move -->
                                                <button type="button"
                                                    class="move-video-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                                    data-video-id="{{ $video->id }}">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    Pindahkan
                                                </button>

                                                <!-- Edit -->
                                                <button type="button"
                                                    class="edit-btn w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"
                                                    data-video-id="{{ $video->id }}" data-video-title="{{ $video->title }}">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                        </path>
                                                    </svg>
                                                    Edit Judul
                                                </button>

                                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                                <!-- Delete -->
                                                <form action="{{ route('videos.destroy', $video->id) }}" method="POST"
                                                    class="delete-form w-full">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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

    <!-- Floating Bulk Action Bar -->
    <div id="bulk-action-bar"
        class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 w-11/12 max-w-3xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between transition-all duration-300 ease-in-out">

        <div class="flex items-center gap-4">
            <button type="button" id="cancel-selection-btn"
                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-900 dark:text-white"><span id="selected-count">0</span> Item
                    Dipilih</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Pilih aksi untuk item terpilih</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" id="bulk-copy-btn"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                    </path>
                </svg>
                <span class="hidden sm:inline">Copy Link</span>
            </button>

            <button type="button" id="bulk-move-btn"
                class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl text-sm font-semibold hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="hidden sm:inline">Pindah</span>
            </button>

            <button type="button" id="bulk-delete-btn"
                class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                <span class="hidden sm:inline">Hapus</span>
            </button>
        </div>
    </div>

    <!-- Hidden Forms & Modals -->
    <!-- Form tersembunyi untuk Bulk Action -->
    <form id="bulk-action-form" action="{{ route('videos.bulkAction') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input">
        <input type="hidden" name="folder_id" id="bulk-folder-id-input">
    </form>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-confirm-modal"
        class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300">
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
        class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-300">
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
    <div id="edit-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
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
        class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
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
        class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
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
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus folder ini?')"
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
        class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm transform transition-all scale-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pindahkan ke Folder</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pilih folder tujuan untuk link yang dipilih.
                </p>

                <div class="flex flex-col gap-2 max-h-60 overflow-y-auto">
                    @foreach($allFolders as $folder)
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

            // === VIEW TOGGLE LOGIC ===
            const viewGridBtn = document.getElementById('view-grid-btn');
            const viewListBtn = document.getElementById('view-list-btn');
            const gridContainer = document.getElementById('grid-view-container');
            const listContainer = document.getElementById('list-view-container');

            const setView = (view) => {
                if (!gridContainer || !listContainer) return;

                const activeClasses = ['text-indigo-600', 'bg-white', 'shadow-sm', 'dark:bg-gray-600', 'dark:text-white', 'active-view'];
                const inactiveClasses = ['text-gray-500', 'hover:text-indigo-600', 'dark:text-gray-400', 'dark:hover:text-white'];

                if (view === 'list') {
                    gridContainer.classList.add('hidden');
                    listContainer.classList.remove('hidden');

                    if (viewListBtn) {
                        viewListBtn.classList.remove(...inactiveClasses);
                        viewListBtn.classList.add(...activeClasses);
                    }
                    if (viewGridBtn) {
                        viewGridBtn.classList.remove(...activeClasses);
                        viewGridBtn.classList.add(...inactiveClasses);
                    }

                    localStorage.setItem('videoViewMode', 'list');
                } else {
                    listContainer.classList.add('hidden');
                    gridContainer.classList.remove('hidden');

                    if (viewGridBtn) {
                        viewGridBtn.classList.remove(...inactiveClasses);
                        viewGridBtn.classList.add(...activeClasses);
                    }
                    if (viewListBtn) {
                        viewListBtn.classList.remove(...activeClasses);
                        viewListBtn.classList.add(...inactiveClasses);
                    }

                    localStorage.setItem('videoViewMode', 'grid');
                }
            };

            // Init View
            const savedView = localStorage.getItem('videoViewMode') || 'grid';
            setView(savedView);

            viewGridBtn?.addEventListener('click', () => setView('grid'));
            viewListBtn?.addEventListener('click', () => setView('list'));


            // === BULK ACTIONS & SELECTION ===
            // Note: We have checkboxes in BOTH Grid and List.
            // When checking one, we should ideally check the other if they represent the same ID, 
            // BUT simpler is just treating them as independent inputs that feed the same 'Set'.
            // Because they are in different containers (one hidden), user only interacts with visible ones.

            const bulkActionBar = document.getElementById('bulk-action-bar');
            const searchContainer = document.getElementById('search-container');
            const selectedCountSpan = document.getElementById('selected-count');
            const selectedVideos = new Set();

            // Re-query checkboxes every time (incase of DOM updates, though here it's static)
            // We use event delegation or just dynamic query.
            const getVideoCheckboxes = () => document.querySelectorAll('.video-checkbox');

            const updateBulkUI = () => {
                const count = selectedVideos.size;
                selectedCountSpan.textContent = count;

                if (count > 0) {
                    bulkActionBar.classList.remove('hidden');
                } else {
                    bulkActionBar.classList.add('hidden');
                }

                // Sync UI state of checkboxes (if switching views)
                document.querySelectorAll('.video-checkbox').forEach(cb => {
                    cb.checked = selectedVideos.has(cb.value);
                });
            };

            // Event Listener for Checkboxes (Delegation)
            document.body.addEventListener('change', (e) => {
                if (e.target.classList.contains('video-checkbox')) {
                    const id = e.target.value;
                    if (e.target.checked) {
                        selectedVideos.add(id);
                    } else {
                        selectedVideos.delete(id);
                    }
                    updateBulkUI();
                }

                // Select All Logic
                if (e.target.id === 'select-all-checkbox' || e.target.id === 'select-all-main') {
                    const isChecked = e.target.checked;
                    // Sync select all boxes
                    const selectAllTop = document.getElementById('select-all-checkbox');
                    const selectAllMain = document.getElementById('select-all-main');

                    if (selectAllTop) selectAllTop.checked = isChecked;
                    if (selectAllMain) selectAllMain.checked = isChecked;

                    // Update all visible checkboxes
                    const visibleContainer = listContainer.classList.contains('hidden') ? gridContainer : listContainer;
                    const checkboxes = visibleContainer.querySelectorAll('.video-checkbox');

                    checkboxes.forEach(cb => {
                        cb.checked = isChecked;
                        if (isChecked) selectedVideos.add(cb.value);
                        else selectedVideos.delete(cb.value);
                    });
                    updateBulkUI();
                }
            });


            // === Thumbnail Toggle Logic ===
            const thumbToggle = document.getElementById('toggle-thumbnails');

            if (thumbToggle) {
                // Load saved state
                const savedThumbState = localStorage.getItem('showThumbnails');
                if (savedThumbState === 'true') {
                    thumbToggle.checked = true;
                    document.querySelectorAll('.thumbnail-col').forEach(el => el.classList.remove('hidden'));
                }

                thumbToggle.addEventListener('change', (e) => {
                    const isChecked = e.target.checked;
                    localStorage.setItem('showThumbnails', isChecked);

                    // Toggle list view thumbnails
                    const thumbCols = document.querySelectorAll('.thumbnail-col');
                    thumbCols.forEach(el => {
                        if (isChecked) el.classList.remove('hidden');
                        else el.classList.add('hidden');
                    });
                });
            }

            // === Actions (Copy, Delete) ===
            document.body.addEventListener('click', (e) => {
                const copyBtn = e.target.closest('.copy-btn');
                if (copyBtn) {
                    copyToClipboard(copyBtn.dataset.link);
                }
            });

            // Bulk Copy
            document.getElementById('bulk-copy-btn')?.addEventListener('click', () => {
                const links = [];
                selectedVideos.forEach(id => {
                    // Get link directly from the checkbox data attribute
                    const checkbox = document.querySelector(`.video-checkbox[value="${id}"]`);
                    if (checkbox && checkbox.dataset.link) {
                        links.push(checkbox.dataset.link);
                    }
                });

                if (links.length > 0) {
                    copyToClipboard(links.join('\n'), `${links.length} link disalin ke clipboard`);
                    // Optional: Clear selection after copy?
                    // clearSelection(); 
                } else {
                    showToast('Gagal mendapatkan link', 'error');
                }
            });

            // Cancel Selection
            const clearSelection = () => {
                selectedVideos.clear();
                document.querySelectorAll('.video-checkbox').forEach(cb => cb.checked = false);
                const selectAllTop = document.getElementById('select-all-checkbox');
                const selectAllMain = document.getElementById('select-all-main');
                if (selectAllTop) selectAllTop.checked = false;
                if (selectAllMain) selectAllMain.checked = false;
                updateBulkUI();
            };

            document.getElementById('cancel-selection-btn')?.addEventListener('click', clearSelection);



            // === Modals Management ===
            const toggleModal = (modalId, show = true) => {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                if (show) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
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

            document.body.addEventListener('submit', (e) => {
                if (e.target.classList.contains('delete-form')) {
                    e.preventDefault();
                    showDeleteConfirm(e.target, 'Yakin ingin menghapus link ini?');
                }


            });
            document.getElementById('bulk-delete-btn')?.addEventListener('click', () => {
                if (!selectedVideos.size) return;
                const form = document.querySelector('form#bulk-action-form'); // Use specific ID
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

                    // Update char count
                    document.getElementById('char-count').textContent = `${editTitleInput.value.length}/255`;

                    toggleModal('edit-modal', true);
                }
            });

            document.getElementById('close-edit-modal')?.addEventListener('click', () => toggleModal('edit-modal', false));
            document.getElementById('cancel-edit-btn')?.addEventListener('click', () => toggleModal('edit-modal', false));

            document.getElementById('edit-form')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!currentEditId) return;

                const btn = document.getElementById('save-edit-btn');
                const btnText = document.getElementById('save-text');
                const btnIcon = document.getElementById('save-icon');

                btn.disabled = true;
                btnText.textContent = 'Menyimpan...';
                btnIcon.classList.remove('hidden');
                btnIcon.classList.add('animate-spin');

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
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        throw new Error('Gagal update');
                    }
                } catch (err) {
                    showToast(err.message || 'Error', 'error');
                    btn.disabled = false;
                    btnText.textContent = 'Simpan';
                    btnIcon.classList.add('hidden');
                    btnIcon.classList.remove('animate-spin');
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

                // Close Buttons
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
                    document.querySelector('form#bulk-action-form').dataset.mode = 'bulk';
                    toggleModal('move-folder-modal', true);
                    return;
                }

                // Select Folder in Move Modal
                const selectFolderBtn = e.target.closest('.select-folder-btn');
                if (selectFolderBtn) {
                    const folderId = selectFolderBtn.dataset.folderId;

                    // We need to submit the BULK ACTION form, but aimed at 'move' action
                    const form = document.querySelector('form#bulk-action-form');
                    form.querySelector('#bulk-action-input').value = 'move';
                    form.querySelector('#bulk-folder-id-input').value = folderId;

                    // Check mode to decide whether to rebuild inputs
                    // If mode is 'single', we assume form is already populated correctly
                    if (form.dataset.mode !== 'single') {
                        // Clear old inputs
                        form.querySelectorAll('input[name="video_ids[]"]').forEach(el => el.remove());

                        // Add new inputs from checkboxes
                        selectedVideos.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'video_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                    }

                    form.submit();
                    return;
                }

                // Single Move Video Button
                const moveVideoBtn = e.target.closest('.move-video-btn');
                if (moveVideoBtn) {
                    const videoId = moveVideoBtn.dataset.videoId;

                    // Reuse the bulk action form to move a single item
                    const form = document.querySelector('form#bulk-action-form');
                    form.dataset.mode = 'single';
                    form.querySelector('#bulk-action-input').value = 'move';
                    form.querySelector('#bulk-folder-id-input').value = ''; // Will be set by modal selection

                    // Clear old inputs
                    form.querySelectorAll('input[name="video_ids[]"]').forEach(el => el.remove());

                    // Add new input for single video
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'video_ids[]';
                    input.value = videoId;
                    form.appendChild(input);

                    toggleModal('move-folder-modal', true);
                    return;
                }

                // Close Modals on Backdrop Click
                if (e.target.id === 'create-folder-modal') toggleModal('create-folder-modal', false);
                if (e.target.id === 'manage-folder-modal') toggleModal('manage-folder-modal', false);
                if (e.target.id === 'move-folder-modal') toggleModal('move-folder-modal', false);
                if (e.target.id === 'edit-modal') toggleModal('edit-modal', false);
            });

            document.getElementById('close-manage-folder')?.addEventListener('click', () => toggleModal('manage-folder-modal', false));
            document.getElementById('cancel-move-folder')?.addEventListener('click', () => toggleModal('move-folder-modal', false));

            // === Session Flash Messages ===
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif


        });
    </script>
</x-app-layout>