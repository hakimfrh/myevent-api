<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Booth;

class EventController extends Controller
{
    public function getEvent(Request $request)
    {
        $id_event = $request->id_event;
        $event = Event::where('id_event', $id_event)->first();

        if ($event) {
            return response()->json(['code' => '200', 'event' => $event], 200);
        } else {
            return response()->json(['code' => '404', 'message' => 'event not found'], 200);
        }
    }

    public function getAllEvent(Request $request)
    {
        // $event = Event::where('status', 'verified')->get();
        date_default_timezone_set('Asia/Jakarta');
        $time = now();

        $query = Event::query();

        if ($request->has('harga_min') || $request->has('harga_max')) {
            if ($request->has('harga_min')) {
                $minPrice = $request->query('harga_min');
            } else {
                $minPrice = 0;
            }
            if ($request->has('harga_max')) {
                $maxPrice = $request->query('harga_max');
            } else {
                $maxPrice = 9999999999;
            }

            $query->whereHas('booths', function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('harga_booth', [$minPrice, $maxPrice]);
            });
        }

        if ($request->has('kategori')) {
            $kategori = $request->kategori;
            $query->where('kategori_event', $kategori);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $query->whereBetween('tanggal_event', [$startDate, $endDate]);
        }

        if ($request->has('latitude') && $request->has('longitude') && $request->has('distance')) {
            $latitude = $request->query('latitude');
            $longitude = $request->query('longitude');
            $distance = $request->query('distance'); // Distance in kilometers

            $haversine = "(6371 * acos(cos(radians($latitude)) 
                          * cos(radians(latitude)) 
                          * cos(radians(longitude) - radians($longitude)) 
                          + sin(radians($latitude)) 
                          * sin(radians(latitude))))";

            $query->selectRaw("*, {$haversine} AS distance")
                  ->having('distance', '<', $distance)
                  ->orderBy('distance');
        }

        // Apply search filters dynamically
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('nama_event', 'like', '%' . $search . '%')
                ->orWhere('penyelenggara_event', 'like', '%' . $search . '%')
                ->orWhere('kategori_event', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%')
                ->orWhere('alamat', 'like', '%' . $search . '%');
        }

        // Fetch the filtered events
        $events = $query->where('status', 'verified')->where('tanggal_pendaftaran', '<', $time)->where('tanggal_penutupan', '>', $time)->get();

        return response()->json(['code' => '200', 'event_list' => $events], 200);
    }

    public function isEnrolled(Request $request)
    {
        $userId = $request->id_user;
        $eventId = $request->id_event;
        $user = User::findOrFail($userId);

        $orders = $user->orders()->whereHas('booth', function ($query) use ($eventId) {
            $query->where('id_event', $eventId);
        })->whereNot('status_order', 'ditolak')->first();
        if ($orders) {
            return response()->json([
                'enrolled' => true,
                'id_order' => $orders->id_order,
                'tgl_order' => $orders->tgl_order,
                'status_order' => $orders->status_order,
                'user_id' => $userId,
                'event_id' => $eventId
            ]);
        } else {
            return response()->json([
                'enrolled' => false,
                'user_id' => $userId,
                'event_id' => $eventId
            ]);
        }
    }

    public function getBooth(Request $request)
    {
        $eventId = $request->id_event;
        $booths = Booth::where('id_event', $eventId)->get();
        return response()->json(['code' => '200', 'booth_list' => $booths], 200);
    }

    public function getBoothRange(Request $request)
    {
        $eventId = $request->id_event;
        $maxHargaBooth = Booth::where('id_event', $eventId)->max('harga_booth');
        $minHargaBooth = Booth::where('id_event', $eventId)->min('harga_booth');
        if ($maxHargaBooth == null) {
            $maxHargaBooth = 0;
        }
        if ($minHargaBooth == null) {
            $minHargaBooth = 0;
        }

        return response()->json([
            'max_harga_booth' => $maxHargaBooth,
            'min_harga_booth' => $minHargaBooth,
        ]);
    }

    public function getBoothTotal(Request $request)
    {
        $eventId = $request->id_event;
        $totalBooths = Booth::where('id_event', $eventId)->sum('jumlah_booth');
        return response()->json(['code' => '200', 'booth_total' => (string)$totalBooths], 200);
    }

    public function getBoothAvailable(Request $request)
    {
        $eventId = $request->id_event;
        // Get all booths for the given event
        $booths = Booth::where('id_event', $eventId)->get();

        $boothData = [];

        foreach ($booths as $booth) {
            $jumlahBooth = $booth->jumlah_booth;

            // Get all selected booth numbers for this booth and cast to integer
            $selectedBooths = $booth->orders()->pluck('nomor_booth')->map(function ($value) {
                return (int) $value;
            })->toArray();

            // Generate all booth numbers from 1 to jumlahBooth
            $allBooths = range(1, $jumlahBooth);

            // Determine the unselected booth numbers
            $unselectedBooths = array_diff($allBooths, $selectedBooths);
            $unselectedBoothsAsString = array_map(function ($value) {
                return (string) $value;
            }, array_values($unselectedBooths));

            $boothData[] = [
                'id_booth' => $booth->id_booth,
                'tipe_booth' => $booth->tipe_booth,
                'harga_booth' => $booth->harga_booth,
                'booth_remaining' => count($unselectedBooths),
                'booth_available' => array_values($unselectedBoothsAsString)
            ];
        }

        return response()->json(['booth_available' => $boothData]);
    }
}
