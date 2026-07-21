<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\BookingResource;


class BookingController extends Controller
{

    public function index(){
        $bookings = Booking::with('user','room')->where('user_id', auth()->id())->get();
        return response()->json( BookingResource::Collection($bookings), 200);
    }
    public function store(Request $request){
        try{
            $booking_validation = $request->validate([
                'room_id'=>'required|exists:rooms,id',
                'started_at'=>'required|date_format:Y-m-d H:00:00|after_or_equal:now',
                'ended_at'=>'required|date_format:Y-m-d H:00:00|after:started_at',
            ]);

            $is_booked = Booking::where('room_id', $request->room_id)
            ->where('started_at', '<', $request->ended_at)
            ->where('ended_at','>',$request->started_at)->exists();

            if($is_booked){
                return response()->json(['status'=>'error','message'=>'The room is already booked'], 422);
            }

            $room = Room::find($request->room_id);

            $price = $room->price_per_hour;

            $start_date = Carbon::parse($request->started_at);
            $finish_date = Carbon::parse($request->ended_at);

            $hours = $start_date->diffInHours($finish_date);

            $total_price = $hours * $room->price_per_hour;

            $booking_validation['user_id'] = auth()->id();
            $booking_validation['total_price'] = $total_price;
            $booking_validation['status'] = 'confirmed';
            

            $booking = Booking::create($booking_validation);

            return response()->json(new BookingResource($booking), 201);



        } catch (\PDOException $e){
            return response()->json(['status'=>'error'], 500);
        }
    }

    public function show($id){
        try{

            $booking = Booking::with('user','room')->findOrFail($id);

            if ($booking->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized. Not your booking.'], 403);
            }

        return response()->json(new BookingResource($booking), 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'Booking not found'], 404);

    } catch(\Exception $e){
        return response()->json(['status'=>'error','message'=>'Internal Server Error'], 500);
    }
    }

    public function destroy($id){
        try{

            $booking = Booking::with('user','room')->findOrFail($id);

            if ($booking->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized. Not your booking.'], 403);
            }

            $delete = Booking::findOrFail($id);
            $delete->delete();
            return response()->json(['status'=>'ok','message'=>'Booking cancelled'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Booking not found'], 404);
    
        } catch (\Exception $e) {
            return response()->json(['status'=>'error','message'=>'Internal Server Error'], 500);
        }
    }
}
