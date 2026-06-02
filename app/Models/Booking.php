<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'room_id',
        'started_at',
        'ended_at',
        'status',
        'total_price',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function room(){
        return $this->belongsTo(Room::class);
    }
}
