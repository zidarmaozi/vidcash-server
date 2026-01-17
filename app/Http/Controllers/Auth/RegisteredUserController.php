<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        $referrer = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
        }

        // Generate unique referral code
        do {
            $newReferralCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
        } while (User::where('referral_code', $newReferralCode)->exists());

        // Initial balance: 0 + optional referee bonus
        $initialBalance = 0;
        if ($referrer) {
            $refereeBonus = \App\Models\Setting::where('key', 'referee_bonus_amount')->value('value') ?? 0;
            $initialBalance = (float) $refereeBonus;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => $newReferralCode,
            'referred_by' => $referrer ? $referrer->id : null,
            'balance' => $initialBalance,
            // Bonus limit for Referee: +1 Video per folder
            'max_videos_per_folder' => $referrer ? 21 : 20,
            'max_folders' => 10,
        ]);

        if ($referrer) {
            // Bonus for Referrer: +1 Folder Limit AND +1 Video per Folder Limit
            $referrer->increment('max_folders');
            $referrer->increment('max_videos_per_folder');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
