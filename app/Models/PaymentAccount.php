<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    //
    protected $fillable = [
    'user_id',
    'method_name',
    'account_name',
    'account_number',
];
}
