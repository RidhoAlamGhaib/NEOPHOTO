<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function notify(Request $request)
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $orderId = (string) $request->input('order_id');
        $expected = hash('sha512', $orderId . $request->input('status_code') . $request->input('gross_amount') . $serverKey);

        if (!$serverKey || !hash_equals($expected, (string) $request->input('signature_key'))) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('midtrans_order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $status = $request->input('transaction_status');
        if ($order->midtrans_status !== 'settlement') {
            $order->update(['midtrans_status' => $status]);
        }

        return response()->json(['message' => 'ok']);
    }
}
