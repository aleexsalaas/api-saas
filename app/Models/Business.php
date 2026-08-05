<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


#[Fillable(['name', 'slug'])]
class Business extends Model
{
    public function users(){
        return $this->HasMany(User::class);
    }

    public function rooms(){
        return $this->hasMany(Room::class);
    }
}
