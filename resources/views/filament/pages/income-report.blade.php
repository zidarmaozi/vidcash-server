<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Section - Top of Page -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    📊 Report Filters
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Last updated: {{ now()->format('M d, Y H:i') }}
                    </span>
                    <button wire:click="loadData" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Time Period
                    </label>
                    <select wire:model.live="timeFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                @if($timeFilter === 'custom')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date
                        </label>
                        <input 
                            type="date" 
                            wire:model.live="customStartDate"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date
                        </label>
                        <input 
                            type="date" 
                            wire:model.live="customEndDate"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                @endif
                
                <div class="flex items-end">
                    <button wire:click="loadData" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Refresh Data
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Income</p>
                        <p class="text-3xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                        <p class="text-green-200 text-xs mt-1">{{ $validatedViews }} validated views</p>
                    </div>
                    <div class="text-green-200">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Views</p>
                        <p class="text-3xl font-bold">{{ number_format($totalViews) }}</p>
                        <p class="text-blue-200 text-xs mt-1">{{ $validatedViews }} valid | {{ $failedViews }} failed</p>
                    </div>
                    <div class="text-blue-200">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Validation Rate</p>
                        <p class="text-3xl font-bold">{{ $totalViews > 0 ? round(($validatedViews / $totalViews) * 100, 1) : 0 }}%</p>
                        <p class="text-purple-200 text-xs mt-1">Success rate</p>
                    </div>
                    <div class="text-purple-200">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Avg Income/View</p>
                        <p class="text-3xl font-bold">Rp {{ number_format($avgIncomePerView, 2, ',', '.') }}</p>
                        <p class="text-orange-200 text-xs mt-1">Per view</p>
                    </div>
                    <div class="text-orange-200">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Withdrawals</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}</p>
                        <p class="text-gray-500 dark:text-gray-500 text-xs mt-1">Confirmed</p>
                    </div>
                    <div class="text-yellow-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($pendingWithdrawals, 0, ',', '.') }}</p>
                        <p class="text-gray-500 dark:text-gray-500 text-xs mt-1">Awaiting approval</p>
                    </div>
                    <div class="text-red-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Net Income</p>
                        <p class="text-2xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($netIncome, 0, ',', '.') }}</p>
                        <p class="text-gray-500 dark:text-gray-500 text-xs mt-1">Income - Payouts</p>
                    </div>
                    <div class="{{ $netIncome >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Income Trend Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    📈 Income Trend
                </h3>
                <div class="h-80">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
            
            <!-- Top Earners Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    👥 Top Earners
                </h3>
                <div class="h-80">
                    <canvas id="earnersChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Videos Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                🎬 Top Performing Videos
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Video Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Income</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Views</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($topVideos as $index => $video)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $video['title'] }}</td>
                            <td class="px-6 py-4 text-sm text-green-600 font-medium">Rp {{ number_format($video['income'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ number_format($video['views']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Income Trend Chart
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            const incomeData = @json($incomeData);
            
            new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: incomeData.map(item => item.date),
                    datasets: [{
                        label: 'Daily Income (IDR)',
                        data: incomeData.map(item => item.income),
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                    }, {
                        label: 'Views Count',
                        data: incomeData.map(item => item.views),
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Income (IDR)',
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Views Count',
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return 'Income: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    } else {
                                        return 'Views: ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            // Top Earners Chart
            const earnersCtx = document.getElementById('earnersChart').getContext('2d');
            const earnersData = @json($topEarners);
            
            new Chart(earnersCtx, {
                type: 'bar',
                data: {
                    labels: earnersData.map(user => user.name),
                    datasets: [{
                        label: 'Income (IDR)',
                        data: earnersData.map(user => user.income),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(220, 38, 127, 0.8)',
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(236, 72, 153)',
                            'rgb(251, 146, 60)',
                            'rgb(239, 68, 68)',
                            'rgb(16, 185, 129)',
                            'rgb(99, 102, 241)',
                            'rgb(245, 158, 11)',
                            'rgb(220, 38, 127)',
                        ],
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Income (IDR)',
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Income: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                },
                                afterLabel: function(context) {
                                    const userIndex = context.dataIndex;
                                    const userData = earnersData[userIndex];
                                    if (userData) {
                                        return 'Videos: ' + userData.videos;
                                    }
                                    return '';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
