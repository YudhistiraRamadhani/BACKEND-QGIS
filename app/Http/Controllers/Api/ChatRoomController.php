<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Validator;

class ChatRoomController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user(); // Mendapatkan user aktif dari Bearer Token Sanctum

    // 1. Periksa apakah user yang sedang login ini adalah seorang mekanik
    $mechanic = DB::table('mechanics')->where('user_id', $user->id)->first();

    // 2. Tarik room chat secara adaptif sesuai role yang sedang login
    $chatRooms = ChatRoom::whereHas('order', function ($query) use ($user, $mechanic) {
        if ($mechanic) {
            // ✅ JIKA MEKANIK: Ambil room chat yang orders.mechanic_id cocok dengan ID mekanik ini
            $query->where('mechanic_id', $mechanic->id);
        } else {
            // ✅ JIKA CUSTOMER: Ambil room chat yang orders.user_id cocok dengan ID customer ini
            $query->where('user_id', $user->id);
        }
    })
    ->with(['order.mechanic', 'order.workshop', 'order.user']) // Eager load relasi termasuk user customer
    ->get()
    ->map(function ($room) {
        // Ambil pesan terakhir untuk sub-title list chat Flutter
        $lastMessage = \App\Models\ChatMessage::where('chat_room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'id' => $room->id,
            'order_id' => $room->order_id,
            'order_code' => $room->order->order_code ?? '-',
            'status' => $room->order->status ?? 'pending',
            // ✅ DINAMIS: Menyediakan field 'customer_name' agar dibaca aman oleh Flutter Mekanik
            'customer_name' => $room->order->user->name ?? 'Pelanggan',
            'workshop_name' => $room->order->workshop_name ?? ($room->order->workshop->name ?? null),
            'mechanic_name' => $room->order->mechanic_name ?? ($room->order->mechanic->name ?? 'Mekanik'),
            'last_message' => $lastMessage ? $lastMessage->message : 'Belum ada pesan',
            'updated_at_formatted' => $room->updated_at->format('H:i'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Daftar chat room berhasil diambil',
        'data' => $chatRooms
    ], 200);
}
    // Membuat room chat baru berdasarkan order_id
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Gunakan firstOrCreate agar tidak terjadi duplikasi room untuk satu order yang sama
        $chatRoom = ChatRoom::firstOrCreate([
            'order_id' => $request->order_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat room berhasil dimuat/dibuat',
            'data' => $chatRoom
        ], 201);
    }

    // Mengambil detail chat room berdasarkan ID
    public function show($id)
    {
        $chatRoom = ChatRoom::with('order')->find($id);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $chatRoom
        ], 200);
    }
}
