<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingsController extends Controller
{
    //a function that return a list of all avilable rooms

    public function availableRooms(){
        $rooms = Room::where('available_rooms', '>', 0)->get();

        return response()->json($rooms, 200);
    }

    // a function that books a room

    public function bookRoom(Request $request){
            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'room_id'=>'required|exists:rooms,id',
                'date_in'=>'required|date',
                'date_out'=>'required|after_or_equal:date_in'
            ]);

            #get the specific room
            $room = Room::findOrFail($request->room_id);

            #reduce the available rooms if booked
            if($room->availableRooms > 0){
                $room->decrement('available_rooms');

                //create the booking
                $booking = Booking::create([
                    'first_name'=> $request->first_name,
                    'last_name'=> $request->last_name,
                    'room_id'=> $request->room_id,
                    'date_in'=> $request->date_in,
                    'date_out'=> $request->date_out
                ]);

                return response()->json([
                    'message'=> 'Room has been successfully booked',
                    'booking' => $booking,
                ], 201);}

            else{
                    return response()->json([
                        'error'=> 'No avilable rooms'
                    ],400);
                }
            }
    public function checkout($bookingId){
        $booking = Booking::findOrFail($bookingId);
        $room = $booking->room;

        $room->increment('availbale_rooms');
        $booking->delete();

        return response()->json([
            'message'=>'Room is niw avilable'
        ], 201);
    }
    
    }

