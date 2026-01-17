<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Batasan Akun') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Informasi mengenai batasan penggunaan pada akun Anda.') }}
                            </p>
                        </header>

                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label :value="__('Maksimal Jumlah Folder')" />
                                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ Auth::user()->max_folders }} Folder
                                </div>
                            </div>

                            <div>
                                <x-input-label :value="__('Maksimal Video per Folder')" />
                                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ Auth::user()->max_videos_per_folder }} Video
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>