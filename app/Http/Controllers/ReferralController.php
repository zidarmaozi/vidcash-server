<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure user has a referral code (for old users who might not have one)
        if (!$user->referral_code) {
            $user->update([
                'referral_code' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8))
            ]);
        }

        $referrals = $user->referrals()->latest()->paginate(10);

        // Get settings for display
        $referralBonus = Setting::where('key', 'referral_bonus_amount')->value('value') ?? 0;
        $referralThreshold = Setting::where('key', 'referral_threshold')->value('value') ?? 0;

        return view('referral.index', [
            'referralCode' => $user->referral_code,
            'referralLink' => route('register', ['ref' => $user->referral_code]),
            'referrals' => $referrals,
            'referralBonus' => $referralBonus,
            'referralThreshold' => $referralThreshold,
        ]);
    }
}
