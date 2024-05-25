<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Booth;

class OrderController extends Controller
{
    public function getOrder(Request $request)
    {
        $user_id = $request->user_id;
        $orders = Order::with('booth.event')->where('id', $user_id)->get();
        if ($orders) {
            return response()->json(['code' => '200', 'order_list' => $orders], 200);
        }
    }

    public function getOrderedEvent(Request $request)
    {
        $user_id = $request->user_id;
        $orders = Order::with('booth.event')->where('id', $user_id)->get();
        if ($orders) {
            $events = [];
            foreach ($orders as $order) {
                if ($order->booth && $order->booth->event) {
                    $events[] = $order->booth->event;
                }
            }
            return response()->json(['code' => '200', 'event_list' => $events], 200);
        }
    }

    public function getCountOrder(Request $request)
    {
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

    public function makeOrder(Request $request)
    {
        $nomorBooth = $request->nomor_booth;
        $hargabayar = $request->harga_bayar;
        $tglOrder = now();
        $idUser = $request->id;
        $idBooth = $request->id_booth;

        $validOrder = Order::where('id_booth', $idBooth)->where('nomor_booth', $nomorBooth)->first();
        if (!$validOrder) {
            $booth = Booth::find($idBooth);
            if ($nomorBooth > $booth->jumlah_booth) {
                return response()->json(['message' => 'booth not available'], 406);
            }
            $order = Order::create([
                'nomor_booth' => $nomorBooth,
                'harga_bayar' => $hargabayar,
                'tgl_order' => $tglOrder,
                'id' => $idUser,
                'id_booth' => $idBooth
            ]);
            if ($order) {
                $orderDetail = Order::where('id', $idUser)->where('id_booth', $idBooth)->where('nomor_booth', $nomorBooth)->where('tgl_order', $tglOrder)->first();
                return response()->json(['message' => 'ok', 'order_detail' => $orderDetail], 200);
            } else {
                return response()->json(['message' => 'unknown eror while creating order'], 406);
            }
        } else {
            return response()->json(['message' => 'booth not available'], 406);
        }
    }
}
