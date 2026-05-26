<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Validator;

class ChatRoomController extends Controller
{
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
