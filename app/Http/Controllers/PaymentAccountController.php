<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentAccount;

class PaymentAccountController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'method_name' => 'required|string|max:255',
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
    ]);

    Auth::user()->paymentAccounts()->create($validated);

    // Ganti 'success' menjadi 'account_added_success'
    return redirect()->route('withdrawals.index')
        ->with('account_added_success', 'Akun pembayaran baru berhasil ditambahkan.');
}

    public function destroy(PaymentAccount $account)
{
    // Pastikan user hanya bisa menghapus akun miliknya sendiri
    if (Auth::id() !== $account->user_id) {
        abort(403);
    }

    $account->delete();

    return back()->with('success', 'Akun pembayaran berhasil dihapus.');
}

}