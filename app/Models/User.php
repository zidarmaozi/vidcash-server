<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'user' atau 'admin'
        'validation_level',
        'balance',
        'total_withdrawn',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
        ];
    }

    /**
 * Get all of the videos for the User
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function videos()
{
    return $this->hasMany(Video::class);
}


public function withdrawals()
{
    return $this->hasMany(Withdrawal::class);
}

public function paymentAccounts()
{
    return $this->hasMany(PaymentAccount::class);
}

public function customNotifications()
{
    return $this->hasMany(Notification::class);
}
}
