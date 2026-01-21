<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('videos.index')" :active="request()->routeIs('videos.index')">
                        {{ __('Kelola Video') }}
                    </x-nav-link>
                    <x-nav-link :href="route('videos.create')" :active="request()->routeIs('videos.create')">
                        {{ __('Upload Video') }}
                    </x-nav-link>
                    <x-nav-link :href="route('withdrawals.index')" :active="request()->routeIs('withdrawals.index')">
                        {{ __('Withdraw') }}
                    </x-nav-link>
                    <x-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.index')">
                        {{ __('Event') }}
                    </x-nav-link>
                    <x-nav-link :href="route('referral.index')" :active="request()->routeIs('referral.index')">
                        {{ __('Undang Teman') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Chat Icon (Desktop) -->
                <a href="{{ route('chat.index') }}"
                    class="p-2 rounded-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </a>

                <!-- Tombol Notifikasi (Desktop) -->
                <div class="relative me-4" x-data="{ open: false }">
                    <button id="notification-bell" @click="open = ! open; markNotificationsAsRead()"
                        class="p-2 rounded-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span
                                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </button>
                    <!-- Dropdown Notifikasi -->
                    <div x-show="open" @click.away="open = false" style="display: none;"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50">
                        <div
                            class="p-3 font-bold border-b border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                            Notifikasi</div>
                        @if(isset($userNotifications))
                            @forelse($userNotifications as $notification)
                                <div
                                    class="p-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition @if(!$notification->read_at) bg-indigo-50 dark:bg-indigo-900/20 @endif">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{!! $notification->message !!}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="p-3 text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru.</p>
                            @endforelse
                        @endif
                    </div>
                </div>

                <!-- Dropdown Profil (Desktop) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Right Side (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <!-- Chat Icon (Mobile) -->
                <a href="{{ route('chat.index') }}"
                    class="p-2 mr-2 rounded-full text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </a>

                <!-- Tombol Notifikasi (Mobile) -->
                <div class="relative" x-data="{ open: false }">
                    <button id="notification-bell" @click="open = ! open; markNotificationsAsRead()"
                        class="inline-flex items-center p-2 rounded-full text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center h-5 w-5 text-xs font-bold text-white bg-red-600 rounded-full">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </button>
                    <!-- Dropdown Notifikasi Mobile -->
                    <div x-show="open" @click.away="open = false" style="display: none;"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50">
                        <div
                            class="p-3 font-bold border-b border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                            Notifikasi</div>
                        @if(isset($userNotifications))
                            @forelse($userNotifications as $notification)
                                <div
                                    class="p-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition @if(!$notification->read_at) bg-indigo-50 dark:bg-indigo-900/20 @endif">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{!! $notification->message !!}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="p-3 text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru.</p>
                            @endforelse
                        @endif
                    </div>
                </div>

                <!-- Hamburger Button -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Menu Hamburger) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('videos.index')" :active="request()->routeIs('videos.index')">
                {{ __('Kelola Video') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('videos.create')" :active="request()->routeIs('videos.create')">
                {{ __('Upload Video') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('withdrawals.index')" :active="request()->routeIs('withdrawals.index')">
                {{ __('Withdraw') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.index')">
                {{ __('Event') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('referral.index')" :active="request()->routeIs('referral.index')">
                {{ __('Undang Teman') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    <script>
        function markNotificationsAsRead() {
            // Kirim request ke server untuk menandai notifikasi sebagai sudah dibaca
            fetch('{{ route('notifications.read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Hapus badge angka merah setelah diklik
                    const unreadBadge = document.querySelector('#notification-bell span');
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                }
            });
        }
    </script>
</nav>