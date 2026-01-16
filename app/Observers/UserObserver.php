<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Notification;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if balance changed and if user has a referrer and reward not claimed yet
        if ($user->wasChanged('balance') && $user->referred_by && !$user->referral_reward_claimed) {

            $threshold = Setting::where('key', 'referral_threshold')->value('value');

            // If setup is valid and balance meets threshold
            if ($threshold !== null && $user->balance >= (float) $threshold) {

                $bonusAmount = Setting::where('key', 'referral_bonus_amount')->value('value');

                if ($bonusAmount > 0) {
                    $referrer = $user->referrer;

                    if ($referrer) {
                        // Add bonus to referrer
                        $referrer->increment('balance', (float) $bonusAmount);

                        // Mark reward as claimed
                        $user->referral_reward_claimed = true;
                        $user->saveQuietly(); // Prevent infinite loop if we were observing this same model/field

                        // Create Notification
                        Notification::create([
                            'user_id' => $referrer->id,
                            'type' => 'referral',
                            'message' => "Selamat! Anda mendapatkan bonus referral Rp " . number_format($bonusAmount, 0, ',', '.') . " karena teman Anda ({$user->name}) telah mencapai target saldo."
                        ]);
                    }
                }
            }
        }
    }
}
