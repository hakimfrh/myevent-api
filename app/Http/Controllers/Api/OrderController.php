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
        $query = Order::query();
        if ($request->has('status_order')) {
            $status = $request->query('status_order');
            $validStatuses = ['validasi', 'diterima', 'ditolak', 'menunggu pembayaran', 'validasi pembayaran', 'terverifikasi'];
            if (in_array($status, $validStatuses)) {
                $query->where('status_order', $status);
            } else {
                return response()->json(['error' => 'Invalid status'], 400);
            }
        }
        $user_id = $request->user_id;
        $orders = $query->with('booth.event')->where('id', $user_id)->get();
        // $orders = Order::with('booth.event')->where('id', $user_id)->get();
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
                $orderDetail = Order::with('booth.event')->where('id', $idUser)->where('id_booth', $idBooth)->where('nomor_booth', $nomorBooth)->where('tgl_order', $tglOrder)->first();
                return response()->json(['message' => 'ok', 'order_detail' => $orderDetail], 200);
            } else {
                return response()->json(['message' => 'unknown eror while creating order'], 406);
            }
        } else {
            return response()->json(['message' => 'booth not available'], 406);
        }
    }

    public function uploadBayar(Request $request)
    {

        $idOrder = $request->id_order;
        $imageData = $request->image_data;
        // $imageName = $request->image_name;
        $imageType = $request->image_type;
        date_default_timezone_set('Asia/Jakarta');
        $time = now();
        $order = Order::with('booth.event')->find($idOrder);

        if ($order) {
            $idEvent = $order->booth->event->id_event;
            $imageName = 'img_b' . $idEvent . '_' . $idOrder . '_' . $time->format('Y-m-d_H-i-s') . '.' . $imageType;
            // Decode the base64 image data
            $decoded_image = base64_decode($imageData);
            // Save the received image data to a file
            $save_path = 'uploads/' . $idEvent . '/' . 'bayar/' . $imageName;
            // $save_path = public_path('img\\' . $imageName);
            // $save_path = '../../../../public/img/'.$imageName;
            $directory = dirname($save_path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $file_saved = file_put_contents($save_path, $decoded_image);

            // return response()->json(['message' => $imageName], 200);
            if ($file_saved) {
                $order->img_bukti_transfer = $save_path;
                $order->status_order = 'menunggu pembayaran';
                $order->tgl_bayar = $time;
                $order->save();
                return response()->json(['message' => 'ok'], 200);
            } else {
                return response()->json(['message' => 'failed saving file'], 500);
            }
        } else {
            return response()->json(['message' => 'order not found'], 404);
        }
    }
}
