<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\BookingResource;
use App\Http\Requests\StoreBookingRequest;


class BookingController extends Controller
{

    public function index(){
        $bookings = Booking::with('user','room')->where('user_id', auth()->id())->get();
        return response()->json( BookingResource::Collection($bookings), 200);
    }
    public function store(StoreBookingRequest $request){
        try{
            $booking_data = $request->validated();

            $is_booked = Booking::where('room_id', $booking_data['room_id'])
            ->where('started_at', '<', $booking_data['ended_at'])
            ->where('ended_at','>',$booking_data['started_at'])->exists();

            if($is_booked){
                return response()->json(['status'=>'error','message'=>'The room is already booked'], 422);
            }

            $room = Room::find($booking_data['room_id']);

            $price = $room->price_per_hour;

            $start_date = Carbon::parse($booking_data['started_at']);
            $finish_date = Carbon::parse($booking_data['ended_at']);

            $hours = $start_date->diffInHours($finish_date);

            $total_price = $hours * $room->price_per_hour;

            $booking_data['user_id'] = auth()->id();
            $booking_data['total_price'] = $total_price;
            $booking_data['status'] = 'confirmed';
            

            $booking = Booking::create($booking_data);

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
