<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Withdraw') }}
        </h2>
    </x-slot>

    <main class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm"
                    role="alert">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-bold">Terjadi Kesalahan</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Kolom Kiri: Ajukan Penarikan (7/12) --}}
                <div class="lg:col-span-7 space-y-6">

                    <!-- Wallet Card -->
                    <div
                        class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl">
                        </div>
                        <div class="relative z-10">
                            <p class="text-indigo-100 text-sm font-medium mb-1">Saldo Tersedia</p>
                            <h2 class="text-4xl font-extrabold mb-4">
                                Rp{{ number_format(Auth::user()->balance, 0, ',', '.') }}</h2>
                            <div class="flex items-center gap-2 text-indigo-200 text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Minimal penarikan bervariasi tergantung metode.</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span>💸</span> Ajukan Penarikan
                        </h2>

                        @if($hasPendingWithdrawal)
                            <div
                                class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Penarikan Sedang
                                            Diproses</h3>
                                        <div class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                                            <p>Mohon tunggu hingga penarikan sebelumnya selesai diproses.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('withdrawals.store') }}" method="POST" @if($hasPendingWithdrawal)
                        onsubmit="return false;" @endif>
                            @csrf

                            <!-- Account Selection -->
                            <div class="mb-6">
                                <label for="payment_account_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ke Rekening
                                    Mana?</label>
                                <div class="relative">
                                    <select id="payment_account_id" name="payment_account_id"
                                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 @if($hasPendingWithdrawal) bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-60 @endif"
                                        required @if($hasPendingWithdrawal) disabled @endif>
                                        <option value="">-- Pilih Akun Penerima --</option>
                                        @foreach(Auth::user()->paymentAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->method_name }} -
                                                {{ $account->account_number }} ({{ $account->account_name }})</option>
                                        @endforeach
                                    </select>
                                    <!-- Custom Arrow could go here if removing default appearance -->
                                </div>
                                @if(Auth::user()->paymentAccounts->count() == 0)
                                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        Anda belum menambahkan akun pembayaran.
                                    </p>
                                @endif
                            </div>

                            <!-- Amount Selection -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Mau Tarik
                                    Berapa?</label>
                                <input type="hidden" name="amount" id="amount" required>
                                <div id="amount-buttons" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($withdrawalAmounts as $amount)
                                        <button type="button" data-amount="{{ $amount }}"
                                            class="amount-btn transition-all duration-200 w-full border-2 border-transparent bg-gray-50 dark:bg-gray-700/50 hover:border-indigo-200 dark:hover:border-indigo-800 text-gray-700 dark:text-gray-200 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md focus:outline-none ring-offset-2 focus:ring-2 focus:ring-indigo-500 @if($hasPendingWithdrawal) opacity-50 cursor-not-allowed @endif"
                                            @if($hasPendingWithdrawal) disabled @endif>
                                            Rp{{ number_format($amount, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 @if($hasPendingWithdrawal) bg-gray-400 cursor-not-allowed hover:bg-gray-400 hover:translate-y-0 shadow-none @endif"
                                @if($hasPendingWithdrawal) disabled @endif>
                                @if($hasPendingWithdrawal)
                                    Menunggu Proses Selesai...
                                @else
                                    Kirim Permintaan Penarikan
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Kolom Kanan: Akun & Riwayat (5/12) --}}
                <div class="lg:col-span-5 space-y-6">

                    <!-- Saved Accounts -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Akun Tersimpan</h2>
                            <button id="addAccountBtn"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-semibold flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="space-y-3">
                            @forelse(Auth::user()->paymentAccounts as $account)
                                <div
                                    class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600 flex justify-between items-center group hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-indigo-500 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                                                {{ $account->method_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                {{ $account->account_number }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">
                                                {{ $account->account_name }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('payment-accounts.destroy', $account) }}" method="POST"
                                        class="delete-account-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
                                            title="Hapus Akun">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div
                                    class="text-center py-8 bg-gray-50/50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500">Belum ada akun tersimpan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- History -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Riwayat Penarikan</h2>
                        <div class="space-y-0">
                            @forelse($withdrawals as $withdrawal)
                                <div
                                    class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700 last:border-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition px-2 -mx-2 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                                @if($withdrawal->status == 'pending') bg-yellow-100 text-yellow-600
                                                @elseif($withdrawal->status == 'confirmed') bg-green-100 text-green-600
                                                @else bg-red-100 text-red-600 @endif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($withdrawal->status == 'pending')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($withdrawal->status == 'confirmed') <path stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                @else <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path> @endif
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-gray-100 text-sm">
                                                Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono">{{ $withdrawal->formatted_id }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full mb-0.5
                                                @if($withdrawal->status == 'pending') bg-yellow-100 text-yellow-700
                                                @elseif($withdrawal->status == 'confirmed') bg-green-100 text-green-700
                                                @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $withdrawal->created_at->format('d M, H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-sm text-gray-500">Belum ada riwayat penarikan.</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-6">
                            {{ $withdrawals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Akun -->
        <div id="addAccountModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                    aria-hidden="true" id="addAccountModalBackdrop"></div>

                <!-- This spacer element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modal-title">Info Pembayaran
                            </h3>
                            <button id="closeModalBtn"
                                class="bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-gray-700 rounded-full p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('payment-accounts.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="method_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode</label>
                                    <select id="method_name" name="method_name"
                                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 dark:bg-gray-700 py-2.5"
                                        required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($withdrawalMethods as $method)
                                            <option value="{{ $method }}">{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="account_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor
                                        (Rek/E-Wallet)</label>
                                    <input type="text" name="account_number" id="account_number"
                                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 dark:bg-gray-700 py-2.5"
                                        placeholder="Contoh: 08123xxx atau 123456xxx" required>
                                </div>
                                <div>
                                    <label for="account_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama
                                        Pemilik</label>
                                    <input type="text" name="account_name" id="account_name"
                                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 dark:bg-gray-700 py-2.5"
                                        placeholder="Sesuai buku tabungan/akun" required>
                                </div>
                            </div>

                            <div class="mt-8">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                    Simpan Akun
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div id="delete-account-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hapus Akun?</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-6">Anda yakin ingin menghapus akun pembayaran ini? Sisa
                        saldo tidak akan hilang.</p>
                    <div class="flex gap-3 justify-center">
                        <button id="cancel-delete-account-btn" type="button"
                            class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">Batal</button>
                        <button id="confirm-delete-account-btn" type="button"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 focus:outline-none shadow-md">Ya,
                            Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Info Sukses -->
        <div id="success-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full p-6 text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4 animate-bounce">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Berhasil!</h3>
                    <p id="success-modal-text" class="text-sm text-gray-500 mt-2 mb-6"></p>
                    <button id="close-success-modal-btn" type="button"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 focus:outline-none shadow-lg">Mantap!</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // === Elemen UI ===
            const amountButtons = document.querySelectorAll('.amount-btn');
            const hiddenAmountInput = document.getElementById('amount');
            const addAccountModal = document.getElementById('addAccountModal');
            const addAccountBtn = document.getElementById('addAccountBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');

            const deleteAccountModal = document.getElementById('delete-account-modal');
            const cancelDeleteAccountBtn = document.getElementById('cancel-delete-account-btn');
            const confirmDeleteAccountBtn = document.getElementById('confirm-delete-account-btn');
            let formToDelete = null;

            const successModal = document.getElementById('success-modal');
            const successModalText = document.getElementById('success-modal-text');
            const closeSuccessModalBtn = document.getElementById('close-success-modal-btn');

            // --- Logika Modal Tambah Akun ---
            if (addAccountBtn) {
                addAccountBtn.addEventListener('click', () => addAccountModal.classList.remove('hidden'));
                closeModalBtn.addEventListener('click', () => addAccountModal.classList.add('hidden'));
                addAccountModal.addEventListener('click', (e) => {
                    if (e.target === addAccountModal) addAccountModal.classList.add('hidden');
                });
            }

            // --- Logika Pilihan Nominal ---
            amountButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Skip jika button disabled
                    if (this.disabled) return;

                    amountButtons.forEach(btn => {
                        // Reset to inactive state
                        btn.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300');
                        btn.classList.add('border-transparent', 'bg-gray-50', 'dark:bg-gray-700/50', 'text-gray-700', 'dark:text-gray-200');
                    });
                    
                    // Set active state
                    this.classList.remove('border-transparent', 'bg-gray-50', 'dark:bg-gray-700/50', 'text-gray-700', 'dark:text-gray-200');
                    this.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300');
                    
                    hiddenAmountInput.value = this.dataset.amount;
                });
            });

            // --- Logika Modal Hapus ---
            // PERBAIKAN: Gunakan class 'delete-account-form' yang lebih spesifik
            document.querySelectorAll('.delete-account-form').forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    formToDelete = this;
                    deleteAccountModal.classList.remove('hidden');
                });
            });

            if (cancelDeleteAccountBtn) {
                cancelDeleteAccountBtn.addEventListener('click', () => {
                    deleteAccountModal.classList.add('hidden');
                    formToDelete = null;
                });
            }

            if (confirmDeleteAccountBtn) {
                confirmDeleteAccountBtn.addEventListener('click', () => {
                    if (formToDelete) {
                        formToDelete.submit();
                    }
                });
            }

            // --- Logika Modal Sukses ---
            @if (session('account_added_success'))
                successModalText.textContent = "{{ session('account_added_success') }}";
                successModal.classList.remove('hidden');
            @endif

        if (closeSuccessModalBtn) {
                closeSuccessModalBtn.addEventListener('click', () => {
                    successModal.classList.add('hidden');
                });
            }
        });
    </script>
</x-app-layout>