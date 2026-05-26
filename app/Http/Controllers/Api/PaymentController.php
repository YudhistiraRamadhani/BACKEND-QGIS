<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Menangani inisiasi transaksi/pembayaran pertama kali
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'method'   => 'required|string', // Isi sesuai enum payment_method (Contoh: CASH, E-WALLET)
            'amount'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Simulasi integrasi kode payment gateway (misal Xendit)
        $mockXenditId = 'xnd_' . Str::random(12);
        $mockCheckoutUrl = 'https://checkout.xendit.co/v2/invoice/' . Str::random(8);

        $payment = Payment::create([
            'order_id'     => $request->order_id,
            'xendit_id'    => $mockXenditId,
            'method'       => $request->method,
            'status'       => 'PENDING', // Status awal invoice
            'amount'       => $request->amount,
            'checkout_url' => $mockCheckoutUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice pembayaran berhasil dibuat',
            'data' => $payment
        ], 201);
    }

    // Menerima kiriman data dari Webhook Payment Gateway (Xendit) secara real-time
    public function webhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'xendit_id' => 'required|string',
            'status'    => 'required|string', // Contoh: SUCCESS, FAILED, EXPIRED
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Payload tidak valid'], 400);
        }

        $payment = Payment::where('xendit_id', $request->xendit_id)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Data transaksi tidak ditemukan'
            ], 404);
        }

        // Petakan status dari payment gateway ke enum internal status Anda
        $finalStatus = 'PENDING';
        if ($request->status === 'SUCCESS') {
            $finalStatus = 'PAID';
        } elseif (in_array($request->status, ['FAILED', 'EXPIRED'])) {
            $finalStatus = 'FAILED';
        }

        $payment->update([
            'status' => $finalStatus,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui melalui webhook'
        ], 200);
    }
}
