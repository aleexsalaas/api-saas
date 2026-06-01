<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'started_at',
        'ended_at',
        'status',
        'total_price',
    ];
}
