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

    $validated = $request->validate([
        'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . $user->balance,
        'payment_account_id' => 'required|exists:payment_accounts,id',
    ]);
    
    // Pastikan akun pembayaran milik user
    $paymentAccount = $user->paymentAccounts()->findOrFail($validated['payment_account_id']);

    // Langsung buat catatan penarikan baru tanpa memotong saldo
$user->withdrawals()->create([
    'amount' => $validated['amount'],
    'payment_info' => $paymentAccount->method_name . ' - ' . $paymentAccount->account_number . ' a/n ' . $paymentAccount->account_name,
    'status' => 'pending',
]);

return back()->with('success', 'Permintaan penarikan berhasil diajukan.');
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

    return view('withdrawals.index', [
        'withdrawals' => $withdrawals,
        'minWithdrawal' => $minWithdrawal,
        'withdrawalMethods' => $withdrawalMethods,
        'withdrawalAmounts' => $withdrawalAmounts
    ]);
}
}
