<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $stats = $this->getStats();
            @endphp
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Total Videos</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Active Videos</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                    </div>
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-lg p-3">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Inactive Videos</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                        <svg class="h-8 w-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        {{-- Loading Indicator --}}
        <div wire:loading class="flex justify-center py-8">
            <div class="flex items-center space-x-3 text-gray-500 dark:text-gray-400">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary-600 border-t-transparent"></div>
                <span class="text-sm font-medium">Loading videos...</span>
            </div>
        </div>

        {{-- Video Gallery --}}
        <div wire:loading.remove>
            @php
                $videos = $this->getVideos();
            @endphp

            @if($videos->total() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($videos as $video)
                        <a href="{{ route('filament.admin.resources.videos.show', $video) }}" 
                           class="group flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300 transform hover:-translate-y-1">
                            
                            {{-- Thumbnail Image - Fixed aspect ratio --}}
                            <div class="relative w-full aspect-video bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 overflow-hidden">
                                <img src="{{ $video->thumbnail_url }}" 
                                     alt="{{ $video->title }}" 
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                
                                {{-- Status Badge --}}
                                <div class="absolute top-3 right-3 z-10">
                                    @if($video->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-green-500 text-white shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-600 text-white shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </div>

                                {{-- Play Overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-40">
                                    <div class="bg-white dark:bg-gray-900 rounded-full p-4 shadow-xl transform group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Video Info - Fixed height for consistent card sizes --}}
                            <div class="flex-1 flex flex-col p-4 min-h-[140px]">
                                <h3 class="font-semibold text-gray-900 dark:text-white text-base mb-3 line-clamp-2 min-h-[3rem] group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors leading-snug">
                                    {{ $video->title }}
                                </h3>
                                
                                <div class="mt-auto space-y-2">
                                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="truncate font-medium">{{ $video->user->name }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="font-mono text-xs">{{ $video->video_code }}</span>
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-medium">{{ number_format($video->views()->count()) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $video->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination Controls --}}
                @if($videos->hasPages())
                    <div class="mt-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                {{-- Results Info --}}
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing 
                                    <span class="font-semibold">{{ $videos->firstItem() }}</span>
                                    to
                                    <span class="font-semibold">{{ $videos->lastItem() }}</span>
                                    of
                                    <span class="font-semibold">{{ $videos->total() }}</span>
                                    videos
                                </div>

                                {{-- Pagination Links --}}
                                <div class="flex items-center gap-2">
                                    {{-- Previous Button --}}
                                    @if ($videos->onFirstPage())
                                        <span class="px-4 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">
                                            Previous
                                        </span>
                                    @else
                                        <button wire:click="previousPage" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            Previous
                                        </button>
                                    @endif

                                    {{-- Page Numbers --}}
                                    <div class="flex items-center gap-1">
                                        @foreach ($videos->getUrlRange(1, $videos->lastPage()) as $page => $url)
                                            @if ($page == $videos->currentPage())
                                                <span class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 rounded-lg">
                                                    {{ $page }}
                                                </span>
                                            @elseif ($page == 1 || $page == $videos->lastPage() || abs($page - $videos->currentPage()) <= 2)
                                                <button wire:click="gotoPage({{ $page }})" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    {{ $page }}
                                                </button>
                                            @elseif (abs($page - $videos->currentPage()) == 3)
                                                <span class="px-2 py-2 text-gray-500 dark:text-gray-400">...</span>
                                            @endif
                                        @endforeach
                                    </div>

                                    {{-- Next Button --}}
                                    @if ($videos->hasMorePages())
                                        <button wire:click="nextPage" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            Next
                                        </button>
                                    @else
                                        <span class="px-4 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">
                                            Next
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No videos found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        @if(!empty($this->data['search']))
                            No videos match your search criteria. Try adjusting your filters or search terms.
                        @else
                            There are no videos with thumbnails yet. Upload videos with thumbnails to see them here.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
