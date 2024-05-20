<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function getOrder(Request $request){
        $user_id = $request->user_id;
        $orders = Order::with('booth.event')->where('id', $user_id)->get();
        if ($orders) {
            return response()->json(['code'=>'200','order_list' => $orders], 200);
        }
    }

    public function getOrderedEvent(Request $request){
        $user_id = $request->user_id;
        $orders = Order::with('booth.event')->where('id', $user_id)->get();
        if ($orders) {
            $events = [];
            foreach ($orders as $order) {
                if ($order->booth && $order->booth->event) {
                    $events[] = $order->booth->event;
                }
            }
            return response()->json(['code'=>'200','event_list' => $events], 200);
        }
    }

    public function getCountOrder(Request $request){
        $user_id = $request->user_id;
        $user = User::findOrFail($user_id);

        // Get total orders and this month's orders
        $totalOrders = $user->totalOrders();
        $thisMonthOrders = $user->thisMonthOrders();

        return response()->json([
            'code' => '200',
            'total_orders' => $totalOrders,
            'this_month_orders' => $thisMonthOrders,
        ]);
    }

}
