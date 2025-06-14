<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_start_date' => 'datetime',
        'trial_end_date' => 'datetime'
    ];
    //
}
