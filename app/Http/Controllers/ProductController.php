<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function runcommand()
    {
        // Execute a simple command
        Artisan::call('config:clear');

        return 'Command executed!';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        if (Auth::check()) {
            return view('checkout', [
                'id' => $id,
                'addon' => product::whereNot('type', 'addon')->get(),
            ]);
        } else {
            return redirect('/login');
        }

    }

    public function test(string $id)
    {
        if (Auth::check()) {
            return view('test', [
                'id' => $id,
                'addon' => product::whereNot('type', 'addon')->get(),
            ]);
        } else {
            return redirect('/login');
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function view(): View
    {
        $order = Order::create([
            'customer_id' => 1,
            'order-UC' => 'ORD-'.strtoupper(uniqid()),
        ]);

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        return view('app', [
            'order' => $order,
            'orderItems' => $orderItems,
            'products' => product::whereNot('type', 'addon')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateappr()
    {
        Order::where('status', 'paid')
            ->whereNull('outlet')
            ->update([
                'outlet' => 'Kota Kasablanka',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
