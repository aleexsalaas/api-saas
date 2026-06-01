<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index(){
        $rooms = Room::all();
        return $rooms;
    }

    public function store(Request $request){
        try{

        $room_validation = $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string|max:255',
            'capacity'=>'required|integer|min:1',
            'price_per_hour'=>'required|numeric|min:0'
        ]);

        $room = Room::create($room_validation);

        return response()->json($room, 201);



        } catch(\PDOException $e){
            return response()->json(['status'=>'error'], 500);
        }
    }
}
