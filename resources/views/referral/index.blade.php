<x-app-layout>
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-500 rounded-full opacity-10 blur-2xl">
                </div>
                <div class="relative z-10">
                    <h1
                        class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400">
                        Undang Teman
                    </h1>
                    <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-2xl">
                        Ajak teman Anda bergabung dan dapatkan hadiah saldo <b>Rp
                            {{ number_format($referralBonus, 0, ',', '.') }}</b> ketika mereka mencapai saldo <b>Rp
                            {{ number_format($referralThreshold, 0, ',', '.') }}</b>.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Share Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Kode Referral Anda</h3>

                        <div class="mb-6">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Kode Unik</label>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 text-center font-mono text-xl font-bold tracking-wider text-indigo-600 dark:text-indigo-400 select-all">
                                    {{ $referralCode }}
                                </div>
                                <button onclick="copyToClipboard('{{ $referralCode }}', 'Kode referral disalin!')"
                                    class="p-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-md hover:shadow-lg focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Link
                                Pendaftaran</label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="{{ $referralLink }}"
                                    class="flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <button onclick="copyToClipboard('{{ $referralLink }}', 'Link referral disalin!')"
                                    class="p-2 text-gray-500 hover:text-indigo-600 bg-gray-100 hover:bg-indigo-50 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition border border-gray-200 dark:border-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral List -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Teman</h3>
                            <span
                                class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                Total: {{ $referrals->total() }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nama</th>
                                        <th scope="col" class="px-6 py-3">Tanggal Gabung</th>
                                        <th scope="col" class="px-6 py-3">Kode</th>
                                        <th scope="col" class="px-6 py-3 text-right">Status Hadiah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($referrals as $referral)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ $referral->name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $referral->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs">
                                                {{ $referral->referral_code }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($referral->referral_reward_claimed)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        Diklaim
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                        Belum Tercapai
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-gray-500">Belum ada teman yang diundang.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $referrals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        const copyToClipboard = (text, message) => {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    Toast.fire({ icon: 'success', title: message });
                }).catch(() => fallbackCopy(text, message));
            } else {
                fallbackCopy(text, message);
            }
        };

        const fallbackCopy = (text, message) => {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                Toast.fire({ icon: 'success', title: message });
            } catch (err) {
                Toast.fire({ icon: 'error', title: 'Gagal menyalin' });
            }
            document.body.removeChild(textarea);
        };
    </script>
</x-app-layout>