<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                📊 Report Filters
            </h3>
            
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model="data.timeFilter">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                    
                    @if($data['timeFilter'] === 'custom')
                        <div>
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="date"
                                    wire:model="data.customStartDate"
                                    placeholder="Start Date"
                                />
                            </x-filament::input.wrapper>
                        </div>
                        
                        <div>
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="date"
                                    wire:model="data.customEndDate"
                                    placeholder="End Date"
                                />
                            </x-filament::input.wrapper>
                        </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        Apply Filters
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-green-100 text-sm font-medium">Total Income</p>
                        <p class="text-2xl font-bold" id="total-income">Loading...</p>
                    </div>
                    <div class="text-green-200">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-blue-100 text-sm font-medium">Total Views</p>
                        <p class="text-2xl font-bold" id="total-views">Loading...</p>
                    </div>
                    <div class="text-blue-200">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-purple-100 text-sm font-medium">Validation Rate</p>
                        <p class="text-2xl font-bold" id="validation-rate">Loading...</p>
                    </div>
                    <div class="text-purple-200">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-orange-100 text-sm font-medium">Avg Income/View</p>
                        <p class="text-2xl font-bold" id="avg-income">Loading...</p>
                    </div>
                    <div class="text-orange-200">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    📈 Income Trend
                </h3>
                <div class="h-80">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    👥 Top Earners
                </h3>
                <div class="h-80">
                    <canvas id="earnersChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Additional Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    🎬 Top Videos
                </h3>
                <div class="h-80">
                    <canvas id="videosChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    📊 Income Distribution
                </h3>
                <div class="h-80">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // This will be populated by the Filament widgets
            console.log('Income Report Page Loaded');
        });
    </script>
    @endpush
</x-filament-panels::page>
