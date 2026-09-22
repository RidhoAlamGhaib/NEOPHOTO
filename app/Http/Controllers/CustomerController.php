<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\StorecustomerRequest;
use App\Http\Requests\UpdatecustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CustomerController extends Controller
{

    public function export(Request $request)
    {
        $period         = $request->query('period', 'weekly');
        $searchCustomer = $request->query('search', '');

        $query = Order::with([
            'customer:id,name,telp',
            'promo:id,nama',
            'items.product:id,name',
            'items.addons.addon:id,name,price',
        ])->where('status', 'paid');

        // Apprcode filter (mirrors the Livewire logic)
        if (str_contains($searchCustomer, '060904')) {
            $query->where('apprcode', 'LIKE', '%qwe%');
        } elseif (str_contains($searchCustomer, '021100')) {
            $query->where('apprcode', 'LIKE', '%asd%');
        } else {
            $query->where('apprcode', 'NOT LIKE', '%qwe%')
                  ->where('apprcode', '!=', 'asdfgh');
        }

        // Search filter
        if (!empty($searchCustomer)) {
            $orderIds    = OrderItem::where('code', 'LIKE', "%{$searchCustomer}%")->pluck('order_id');
            $customerIds = customer::where('name', 'LIKE', "%{$searchCustomer}%")->pluck('id');

            $query->where(function ($q) use ($searchCustomer, $customerIds, $orderIds) {
                $q->whereIn('customer_id', $customerIds)
                  ->orWhere('apprcode', 'LIKE', "%{$searchCustomer}%")
                  ->orWhereIn('id', $orderIds)
                  ->orWhere('outlet', 'LIKE', "%{$searchCustomer}%");
            });
        } elseif ($period !== 'all') {
            $now = \Carbon\Carbon::now();
            [$curStart, $curEnd] = match ($period) {
                'daily'   => [$now->copy()->startOfDay(),   $now->copy()->endOfDay()],
                'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
                default   => [$now->copy()->startOfWeek(),  $now->copy()->endOfWeek()],
            };
            $query->whereBetween('created_at', [$curStart, $curEnd]);
        }

        $filename = 'orders-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Customer', 'Phone', 'Outlet', 'Items', 'Addons',
                'Appr Code', 'Coupon', 'Promo', 'Total', 'Date',
            ]);

            // chunk() avoids loading 13k rows into memory at once
            $query->latest()->chunk(500, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $items = $order->items
                        ->map(fn($i) => optional($i->product)->name)
                        ->filter()->implode(', ');

                    $addons = $order->items
                        ->flatMap(fn($i) => $i->addons->map(
                            fn($a) => optional($a->addon)->name . ' x' . $a->qty
                        ))->filter()->implode(', ');

                    fputcsv($handle, [
                        optional($order->customer)->name ?? '—',
                        optional($order->customer)->telp ?? '—',
                        $order->outlet,
                        $items,
                        $addons,
                        $order->apprcode,
                        $order->items->map(fn($i) => $i->code)->implode(', '),
                        optional($order->promo)->nama    ?? '—',
                        $order->total,
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('login');
    }
    public function dashboard()
    {
        if(Auth::check()){
        return view('dashboard.index');
        }else{
            return redirect('/login');
        }
    }
    public function logout(){
        Auth::logout(); // Clears the authentication information in the user's session.

        return redirect('/');
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
    public function store(StorecustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecustomerRequest $request, customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(customer $customer)
    {
        //
    }
}
