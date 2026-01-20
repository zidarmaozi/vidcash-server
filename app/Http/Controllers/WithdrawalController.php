<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class WithdrawalController extends Controller
{
    // app/Http/Controllers/WithdrawalController.php
    public function store(Request $request)
    {
        $user = Auth::user();
        $minWithdrawal = Setting::where('key', 'min_withdrawal')->first()->value ?? 10000;

        // Cek apakah user sudah memiliki withdrawal yang masih pending
        $hasPendingWithdrawal = $user->withdrawals()->where('status', 'pending')->exists();

        if ($hasPendingWithdrawal) {
            return back()->withErrors(['withdrawal' => 'Anda masih memiliki permintaan penarikan yang sedang diproses. Silakan tunggu hingga selesai sebelum mengajukan penarikan baru.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal,
            'payment_account_id' => 'required|exists:payment_accounts,id',
        ]);

        // Pastikan akun pembayaran milik user
        $paymentAccount = $user->paymentAccounts()->findOrFail($validated['payment_account_id']);

        // Hitung Admin Fee
        $feeConfig = Setting::where('key', 'withdrawal_fee_config')->first()->value ?? '[]';
        $feeConfig = json_decode($feeConfig, true);
        $adminFee = 0;

        foreach ($feeConfig as $config) {
            if ($config['method'] == $paymentAccount->method_name && $config['amount'] == $validated['amount']) {
                $adminFee = $config['fee'];
                break;
            }
        }

        $totalDeduction = $validated['amount'] + $adminFee;

        // Validasi saldo
        if ($user->balance < $totalDeduction) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi (Saldo: Rp' . number_format($user->balance, 0, ',', '.') . ', Total penarikan + admin: Rp' . number_format($totalDeduction, 0, ',', '.') . ')']);
        }

        // Langsung buat catatan penarikan baru tanpa memotong saldo (saldo dipotong saat confirmed by admin, atau bisa di sini jika flow manual)
        // Revisi: Sebaiknya memotong saldo saat request dibuat utk mencegah double spend? 
        // Flow current implementation sepertinya manual approval, jadi saldo masih utuh? 
        // TAPI wait, validated rule max:balance sebelumnya ada. Skrg saya hapus dan ganti logic manual.

        // CREATE withdrawal with fee
        $user->withdrawals()->create([
            'amount' => $validated['amount'],
            'admin_fee' => $adminFee,
            'payment_info' => $paymentAccount->method_name . ' - ' . $paymentAccount->account_number . ' a/n ' . $paymentAccount->account_name,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan penarikan berhasil diajukan. Biaya admin: Rp' . number_format($adminFee, 0, ',', '.'));
    }

    public function index()
    {
        $user = Auth::user();

        // Ambil data dari tabel settings, berikan nilai default jika tidak ada
        $minWithdrawal = Setting::where('key', 'min_withdrawal')->first()->value ?? 10000;
        $amountsString = Setting::where('key', 'withdrawal_amounts')->first()->value ?? '10000,25000,50000';
        $withdrawalAmounts = explode(',', $amountsString);

        // Cek dulu apakah setting ada sebelum di-explode
        $methodsSetting = Setting::where('key', 'withdrawal_methods')->first();
        $withdrawalMethods = $methodsSetting ? explode(',', $methodsSetting->value) : []; // Beri array kosong jika null

        $withdrawals = $user->withdrawals()->latest()->paginate(10);

        // Cek apakah ada withdrawal pending
        $hasPendingWithdrawal = $user->withdrawals()->where('status', 'pending')->exists();

        // Admin Fee Config
        $feeConfigRaw = Setting::where('key', 'withdrawal_fee_config')->first()->value ?? '[]';
        // Kirim raw JSON string biar bisa diparse JS

        return view('withdrawals.index', [
            'withdrawals' => $withdrawals,
            'minWithdrawal' => $minWithdrawal,
            'withdrawalMethods' => $withdrawalMethods,
            'withdrawalAmounts' => $withdrawalAmounts,
            'hasPendingWithdrawal' => $hasPendingWithdrawal,
            'feeConfig' => $feeConfigRaw
        ]);
    }
}
