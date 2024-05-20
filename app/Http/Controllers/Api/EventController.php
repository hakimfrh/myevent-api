<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;

class EventController extends Controller
{
    public function getEvent(Request $request){
        $id_event = $request->id_event;
        $event = Event::where('id_event',$id_event)->first();

        if($event){
            return response()->json(['code'=>'200','event' => $event], 200);
        }else{
            return response()->json(['code'=>'404','message' => 'event not found'], 200);
        }
    }

    public function getAllEvent(){
        $event = Event::get();
        return response()->json(['code'=>'200','event_list' => $event], 200);
    }


}
