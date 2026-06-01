<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class BookingController extends Controller
{

    public function index(){
        $bookings = Booking::all();
        return $bookings;
    }
    public function store(Request $request){
        try{
            $booking_validation = $request->validate([
                'user_id'=>'required|exists:users,id',
                'room_id'=>'required|exists:rooms,id',
                'started_at'=>'required|date_format:Y-m-d H:00:00|after_or_equal:now',
                'ended_at'=>'required|date_format:Y-m-d H:00:00|after:started_at',
            ]);

            $room = Room::find($request->room_id);

            $price = $room->price_per_hour;

            $start_date = Carbon::parse($request->started_at);
            $finish_date = Carbon::parse($request->ended_at);

            $hours = $start_date->diffInHours($finish_date);

            $total_price = $hours * $room->price_per_hour;

            $booking_validation['total_price'] = $total_price;
            $booking_validation['status'] = 'confirmed';

            $booking = Booking::create($booking_validation);

            return response()->json($booking, 201);



        } catch (\PDOException $e){
            return response()->json(['status'=>'error'], 500);
        }
    }
}
