<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('withdraw') }}
        </h2>
    </x-slot>

    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Kolom Kiri: Ajukan Penarikan & Tambah Akun --}}
                <div class="space-y-8">
                    <div class="bg-white p-6 rounded-lg">
                        <h2 class="text-xl font-bold mb-4">Ajukan Penarikan</h2>
                        <div class="mb-4 bg-indigo-50 p-4 rounded-md">
                            <p class="text-sm text-gray-600">Saldo Anda Saat Ini</p>
                            <p class="text-2xl font-bold text-indigo-800">Rp{{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
                        </div>
                        <form action="{{ route('withdrawals.store') }}" method="POST">
                            @csrf
                            <div>
                                <label for="payment_account_id" class="block text-sm font-medium text-gray-700">Pilih Akun Penarikan</label>
                                <select id="payment_account_id" name="payment_account_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach(Auth::user()->paymentAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->method_name }} - {{ $account->account_number }} ({{ $account->account_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Jumlah Penarikan (IDR)</label>
                                <input type="hidden" name="amount" id="amount" required>
                                <div id="amount-buttons" class="mt-2 grid grid-cols-3 gap-2">
                                    @foreach($withdrawalAmounts as $amount)
                                        <button type="button" data-amount="{{ $amount }}" 
                                                class="amount-btn w-full bg-white border border-gray-300 text-gray-700 py-2 rounded-md text-sm">
                                            Rp{{ number_format($amount, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <button type="submit" class="mt-6 w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Kirim Permintaan</button>
                        </form>
                    </div>
                </div>

                {{-- Kolom Kanan: Akun Tersimpan & Riwayat --}}
                <div class="space-y-8">
                    <div class="bg-white p-6 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Akun Tersimpan</h2>
                            <button id="addAccountBtn" class="text-sm text-indigo-600 hover:underline font-semibold">+ Tambah Akun</button>
                        </div>
                        <div class="space-y-3">
                            @forelse(Auth::user()->paymentAccounts as $account)
                                <div class="p-3 bg-gray-50 rounded-md flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold">{{ $account->method_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $account->account_number }}</p>
                                        <p class="text-xs text-gray-500">a/n {{ $account->account_name }}</p>
                                    </div>
                                    {{-- PERBAIKAN: Tambahkan class 'delete-account-form' --}}
                                    <form action="{{ route('payment-accounts.destroy', $account) }}" method="POST" class="delete-account-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Anda belum menyimpan akun pembayaran.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg">
                        <h2 class="text-xl font-bold mb-4">Riwayat Penarikan</h2>
                        <div class="space-y-4">
                            @forelse($withdrawals as $withdrawal)
                                <div class="flex justify-between items-center">
                                    <div>
            {{-- Tampilkan ID baru di sini --}}
            <p class="font-semibold">{{ $withdrawal->formatted_id }} - Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">{{ $withdrawal->created_at->format('d M Y, H:i') }}</p>
        </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($withdrawal->status == 'pending') bg-yellow-200 text-yellow-800
                                        @elseif($withdrawal->status == 'confirmed') bg-green-200 text-green-800
                                        @else bg-red-200 text-red-800 @endif">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada riwayat penarikan.</p>
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
        <div id="addAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Tambah Akun Pembayaran Baru</h2>
                    <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800">&times;</button>
                </div>
                <form action="{{ route('payment-accounts.store') }}" method="POST">
                    @csrf
                    <div>
                        <label for="method_name" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                        <select id="method_name" name="method_name" class="mt-1 block w-full border-gray-300 rounded-md" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach($withdrawalMethods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <label for="account_name" class="block text-sm font-medium text-gray-700">Nama Pemilik Akun</label>
                        <input type="text" name="account_name" id="account_name" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="mt-4">
                        <label for="account_number" class="block text-sm font-medium text-gray-700">Nomor Rekening / E-Wallet</label>
                        <input type="text" name="account_number" id="account_number" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-gray-700 text-white py-2 px-4 rounded-md hover:bg-gray-800">Simpan Akun</button>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div id="delete-account-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-5">Hapus Akun Pembayaran</h3>
                <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus akun ini? Aksi ini tidak dapat dibatalkan.</p>
                <div class="mt-6 flex justify-center space-x-4">
                    <button id="cancel-delete-account-btn" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">Batal</button>
                    <button id="confirm-delete-account-btn" type="button" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">Hapus</button>
                </div>
            </div>
        </div>

        <!-- Modal Info Sukses -->
        <div id="success-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-5">Berhasil</h3>
                <p id="success-modal-text" class="mt-2 text-sm text-gray-500"></p>
                <div class="mt-6">
                    <button id="close-success-modal-btn" type="button" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">OK</button>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        if(addAccountBtn) {
            addAccountBtn.addEventListener('click', () => addAccountModal.classList.remove('hidden'));
            closeModalBtn.addEventListener('click', () => addAccountModal.classList.add('hidden'));
            addAccountModal.addEventListener('click', (e) => {
                if (e.target === addAccountModal) addAccountModal.classList.add('hidden');
            });
        }
        
        // --- Logika Pilihan Nominal ---
        amountButtons.forEach(button => {
            button.addEventListener('click', function() {
                amountButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                    btn.classList.add('bg-white', 'text-gray-700');
                });
                this.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                this.classList.remove('bg-white', 'text-gray-700');
                hiddenAmountInput.value = this.dataset.amount;
            });
        });

        // --- Logika Modal Hapus ---
        // PERBAIKAN: Gunakan class 'delete-account-form' yang lebih spesifik
        document.querySelectorAll('.delete-account-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                formToDelete = this;
                deleteAccountModal.classList.remove('hidden');
            });
        });

        if(cancelDeleteAccountBtn) {
            cancelDeleteAccountBtn.addEventListener('click', () => {
                deleteAccountModal.classList.add('hidden');
                formToDelete = null;
            });
        }

        if(confirmDeleteAccountBtn) {
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
