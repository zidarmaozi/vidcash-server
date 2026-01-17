<x-app-layout>
    <main class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Hero Section with Premium Gradient -->
            <div class="relative rounded-3xl overflow-hidden mb-10 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-900"></div>
                <div
                    class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150">
                </div>

                <div
                    class="relative z-10 px-8 py-12 md:py-20 md:px-16 text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-12">
                    <div class="md:w-3/5 space-y-6">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-indigo-200 text-xs font-semibold backdrop-blur-md">
                            <span>✨ Program Referral Spesial</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight">
                            Ajak Teman, <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500">Lipat
                                Gandakan</span> Keuntungan!
                        </h1>
                        <p class="text-lg text-indigo-100 max-w-xl">
                            Dapatkan bonus uang tunai <b>Rp {{ number_format($referralBonus, 0, ',', '.') }}</b> DAN
                            upgrade akun permanen untuk setiap teman yang aktif.
                        </p>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            <a href="#share-section"
                                class="px-8 py-3.5 rounded-full bg-white text-indigo-900 font-bold hover:bg-indigo-50 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                Mulai Undang
                            </a>
                        </div>
                    </div>

                    <!-- Decorative 3D-ish Elements -->
                    <div class="md:w-2/5 flex justify-center relative">
                        <div class="absolute inset-0 bg-indigo-500 rounded-full blur-3xl opacity-30 animate-pulse">
                        </div>
                        <div
                            class="relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/20 p-8 rounded-3xl transform rotate-3 hover:rotate-0 transition duration-500">
                            <div class="text-center space-y-4">
                                <div class="text-5xl">🚀</div>
                                <div class="text-white font-bold text-xl">Upgrade Akun</div>
                                <div class="text-indigo-200 text-sm opacity-80">+1 Folder Slot<br>+1 Video/Folder</div>
                            </div>
                        </div>
                        <div
                            class="absolute -bottom-6 -right-6 bg-gradient-to-br from-amber-400 to-orange-500 p-6 rounded-2xl shadow-xl transform -rotate-12 animate-bounce-slow">
                            <span class="text-3xl">💰</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How It Works (Steps) -->
            <div class="mb-16">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cara Kerja Sederhana</h2>
                    <p class="text-gray-500 dark:text-gray-400">Tiga langkah mudah untuk memaksimalkan akun Anda.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    <!-- Connecting Line (Desktop) -->
                    <div
                        class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 dark:bg-gray-700 -z-10 transform -translate-y-1/2">
                    </div>

                    <!-- Step 1 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-lg text-center relative z-10 group hover:-translate-y-2 transition duration-300">
                        <div
                            class="w-16 h-16 mx-auto bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                            🔗
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">1. Bagikan Link</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Salin kode unik atau link referral Anda di
                            bawah ini dan bagikan ke teman.</p>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-lg text-center relative z-10 group hover:-translate-y-2 transition duration-300">
                        <div
                            class="w-16 h-16 mx-auto bg-violet-100 dark:bg-violet-900/50 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                            👤
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">2. Teman Bergabung</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Teman mendaftar menggunakan kode Anda.
                            Mereka langsung dapat bonus limit!</p>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-lg text-center relative z-10 group hover:-translate-y-2 transition duration-300">
                        <div
                            class="w-16 h-16 mx-auto bg-amber-100 dark:bg-amber-900/50 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                            🎉
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">3. Terima Hadiah</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Anda dapat upgrade akun OTOMATIS dan saldo
                            tunai saat mereka aktif.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="share-section">
                <!-- Share Card (Golden Ticket Style) -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-8">
                        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white text-center">
                            <h3 class="font-bold text-xl uppercase tracking-wider">Tiket Emas Anda</h3>
                            <p class="text-indigo-100 text-sm opacity-80 mt-1">Gunakan untuk mengundang</p>
                        </div>

                        <div class="p-8 space-y-8">
                            <!-- Code Section -->
                            <div class="text-center group cursor-pointer"
                                onclick="copyToClipboard('{{ $referralCode }}', 'Kode referral disalin!')">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Kode
                                    Unik</label>
                                <div
                                    class="relative bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-indigo-200 dark:border-indigo-800 rounded-xl p-4 transition group-hover:border-indigo-500 group-hover:bg-indigo-50 dark:group-hover:bg-gray-800">
                                    <span
                                        class="font-mono text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-widest">{{ $referralCode }}</span>
                                    <div
                                        class="absolute -top-3 -right-3 bg-indigo-600 text-white p-1.5 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition transform scale-75 group-hover:scale-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <p
                                    class="text-xs text-center text-gray-400 mt-2 group-hover:text-indigo-500 transition">
                                    Klik untuk menyalin kode</p>
                            </div>

                            <hr class="border-gray-100 dark:border-gray-700">

                            <!-- Link Section -->
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Atau
                                    Bagikan Link</label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ $referralLink }}"
                                        class="flex-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-600 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                    <button onclick="copyToClipboard('{{ $referralLink }}', 'Link referral disalin!')"
                                        class="p-3 bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 text-center border-t border-gray-100 dark:border-gray-700">
                            <a href="https://wa.me/?text=Gabung%20VidCash%20sekarang!%20Daftar%20pakai%20kode%20{{ $referralCode }}%20untuk%20dapat%20bonus%20spesial:%20{{ $referralLink }}"
                                target="_blank"
                                class="text-sm font-semibold text-green-600 hover:text-green-700 inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                                Bagikan ke WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Referral List (Refined) -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Teman</h3>
                                <p class="text-sm text-gray-500">Pantau performa referral Anda</p>
                            </div>
                            <span
                                class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-300 shadow-sm border border-indigo-200 dark:border-indigo-800">
                                Total: {{ $referrals->total() }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-500 uppercase bg-gray-50/50 dark:bg-gray-700/50 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 rounded-tl-lg">Nama</th>
                                        <th scope="col" class="px-6 py-4">Bergabung</th>
                                        <th scope="col" class="px-6 py-4">Kode</th>
                                        <th scope="col" class="px-6 py-4 text-right rounded-tr-lg">Status Hadiah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($referrals as $referral)
                                        <tr
                                            class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-sm">
                                                        {{ substr($referral->name, 0, 2) }}
                                                    </div>
                                                    <span
                                                        class="font-semibold text-gray-900 dark:text-white">{{ $referral->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                                {{ $referral->created_at->format('d M Y') }}
                                            </td>
                                            <td
                                                class="px-6 py-4 font-mono text-xs text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded w-fit">
                                                {{ $referral->referral_code }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($referral->referral_reward_claimed)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        Diklaim
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800"
                                                        title="Menunggu teman mencapai threshold">
                                                        ⏳ Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-20 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                        <svg class="w-10 h-10 text-gray-300" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-gray-900 font-semibold text-lg">Belum ada teman</p>
                                                    <p class="text-gray-500 text-sm mt-1">Mulai bagikan kodemu dan dapatkan
                                                        hadiahnya!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
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