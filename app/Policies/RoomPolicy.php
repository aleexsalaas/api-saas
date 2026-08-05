<?php

namespace App\Policies;

use App\Models\User;

class RoomPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Room $room): bool
    {
        return $user->business_id === $room->business_id;
    }

    public function create(User $user): bool
    {
        return !is_null($user->business_id);
    }

    public function update(User $user, Room $room): bool
    {
        return $user->business_id === $room->business_id;
    }

    public function delete(User $user, Room $room){
        return $user->business_id === $room->business_id;
    }
}
