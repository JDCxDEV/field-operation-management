<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginSession extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'location',
        'os',
        'ip',
        'device',
        'status',
        'created_at'
    ];
}
