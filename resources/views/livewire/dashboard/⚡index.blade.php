<?php
use Livewire\Component;
use App\Models\product;
use App\Models\Order;
use App\Models\promo;
use App\Models\customer;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Livewire\WithPagination;
// use Livewire\WithFileDownloads;

new class extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $searchCustomer = '';
    public string $period         = 'weekly'; // daily | weekly | monthly

    public float  $sales          = 0;
    public float  $salesGrowth    = 0;
    public int    $totalOrders    = 0;
    public int    $visitors       = 0;
    public float  $visitorsGrowth = 0;
    public array  $rows           = [];

    // Summary section
    public string $summaryPeriod  = '7';
    public string $summaryStart   = '';
    public string $summaryEnd     = '';
    public array  $summaryDates   = [];
    public string $selectedOutlet = 'all';
    public array  $outletList     = [];

    // Settlement
    public string $settlementDate   = '';
    public string $settlementOutlet = 'Blok M';
    public float  $bankSettlement   = 0;
    public array  $settlementData   = [];
    public array  $availableOutlets = [];

    public array $servers = [
        ['name' => 'Server Kokas S1', 'url' => 'https://api.neophotoindonesia.my.id/status','update' => 'https://api.neophotoindonesia.my.id/update',     'status' => '','err'=>''],
        ['name' => 'Server Soho S1',  'url' => 'https://sohos1.neophotoindonesia.my.id/status','update' => 'https://sohos1.neophotoindonesia.my.id/update', 'status' => '','err'=>''],
        ['name' => 'Server Soho S2',  'url' => 'https://sohos2.neophotoindonesia.my.id/status','update' => 'https://sohos2.neophotoindonesia.my.id/update', 'status' => '','err'=>''],
        ['name' => 'Server Soho S3',  'url' => 'https://sohomain.neophotoindonesia.my.id/status','update' => 'https://sohomain.neophotoindonesia.my.id/update', 'status' => '','err'=>''],
        ['name' => 'Server Soho H1',  'url' => 'https://sohoh1.neophotoindonesia.my.id/status','update' => 'https://sohoh1.neophotoindonesia.my.id/update', 'status' => '','err'=>''],
        
    ];
    public function exportOrdersExcel(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Order::with([
            'customer:id,name,telp',
            'promo:id,nama',
            'items.product:id,name',
            'items.addons.addon:id,name,price',
        ])->where('status', 'paid');
    
        if (!empty($this->searchCustomer)) {
            $search      = $this->searchCustomer;
            $orderIds    = OrderItem::where('code', 'LIKE', "%{$search}%")->pluck('order_id');
            $customerIds = customer::where('name', 'LIKE', "%{$search}%")->pluck('id');
    
            $query->where(function ($q) use ($search, $customerIds, $orderIds) {
                $q->whereIn('customer_id', $customerIds)
                  ->orWhere('apprcode', 'LIKE', "%{$search}%")
                  ->orWhereIn('id', $orderIds)
                  ->orWhere('outlet', 'LIKE', "%{$search}%");
            });
        } elseif ($this->period !== 'all') {
            [$curStart, $curEnd] = $this->getPeriodRanges();
            $query->whereBetween('created_at', [$curStart, $curEnd]);
        }
    
        $orders = $query->latest()->get();
    
        $filename = 'orders-' . now()->format('Ymd_His') . '.csv';
    
        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
    
            // Header row
            fputcsv($handle, [
                'Customer', 'Phone', 'Outlet', 'Items', 'Addons',
                'Appr Code', 'Coupon', 'Promo', 'Total', 'Date',
            ]);
    
            foreach ($orders as $order) {
                $items = $order->items->map(fn($i) => optional($i->product)->name)->filter()->implode(', ');
                $addons = $order->items->flatMap(fn($i) => $i->addons->map(
                    fn($a) => optional($a->addon)->name . ' x' . $a->qty
                ))->filter()->implode(', ');
    
                fputcsv($handle, [
                    optional($order->customer)->name   ?? '—',
                    optional($order->customer)->telp   ?? '—',
                    $order->outlet,
                    $items,
                    $addons,
                    $order->apprcode,
                    $order->items->map(fn($i) => $i->code)->implode(', '),
                    optional($order->promo)->nama       ?? '—',
                    $order->total,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }
    
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // ── Lifecycle ──────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->computeSummary();
        $this->loadData();
        $this->settlementDate = Carbon::today()->format('Y-m-d');
        $this->loadSettlement();
    }

    public function updatedSearchCustomer(): void { $this->resetPage(); $this->loadData(); $this->computeSummary(); }
    public function updatedPeriod(): void           { $this->computeSummary(); }
    public function updatedSummaryPeriod(): void    { $this->loadData(); }
    public function updatedSelectedOutlet(): void   {}
    public function updatedSummaryStart(): void     { if ($this->summaryEnd)   $this->loadData(); }
    public function updatedSummaryEnd(): void       { if ($this->summaryStart) $this->loadData(); }
    public function updatedSettlementDate(): void   { $this->loadSettlement(); }
    public function updatedSettlementOutlet(): void { $this->loadSettlement(); }
    public function updatedBankSettlement(): void   { $this->loadSettlement(); }

    // ── Period helpers ─────────────────────────────────────────────────────────

    private function getPeriodRanges(): array
    {
        $now = Carbon::now();
        return match ($this->period) {
            'daily'   => [
                $now->copy()->startOfDay(),           $now->copy()->endOfDay(),
                $now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(),
            ],
            'monthly' => [
                $now->copy()->startOfMonth(),              $now->copy()->endOfMonth(),
                $now->copy()->subMonth()->startOfMonth(),  $now->copy()->subMonth()->endOfMonth(),
            ],
            'all' => [
                Carbon::create(2000, 1, 1)->startOfDay(), Carbon::now()->endOfDay(),
                Carbon::create(2000, 1, 1)->startOfDay(), Carbon::now()->subDay()->endOfDay(),
            ],
            default   => [
                $now->copy()->startOfWeek(),          $now->copy()->endOfWeek(),
                $now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek(),
            ],
        };
    }

    private function getSummaryRange(): array
    {
        if ($this->summaryPeriod === 'custom') {
            $start = $this->summaryStart ? Carbon::parse($this->summaryStart)->startOfDay() : Carbon::now()->subDays(6)->startOfDay();
            $end   = $this->summaryEnd   ? Carbon::parse($this->summaryEnd)->endOfDay()     : Carbon::now()->endOfDay();
        } else {
            $days  = (int) $this->summaryPeriod;
            $start = Carbon::now()->subDays($days - 1)->startOfDay();
            $end   = Carbon::now()->endOfDay();
        }
        return [$start, $end];
    }

    // ── Summary cards ──────────────────────────────────────────────────────────

    public function computeSummary(): void
    {
        if ($this->period === 'all') {
        // For "all", just compute totals without growth comparison
        $cur = Order::where('status', 'paid')
         ->selectRaw('SUM(total) as total_sales, COUNT(*) as order_count, COUNT(DISTINCT customer_id) as unique_visitors')
         ->first();

        $this->sales          = (float) ($cur->total_sales    ?? 0);
        $this->totalOrders    = (int)   ($cur->order_count     ?? 0);
        $this->visitors       = (int)   ($cur->unique_visitors ?? 0);
        $this->salesGrowth    = 0;
        $this->visitorsGrowth = 0;
        return;
    }
        [$curStart, $curEnd, $prevStart, $prevEnd] = $this->getPeriodRanges();

        $calc = fn($start, $end) => Order::where('status', 'paid')->whereBetween('created_at', [$start, $end])
        ->selectRaw('SUM(total) as total_sales, COUNT(*) as order_count, COUNT(DISTINCT customer_id) as unique_visitors')
        ->first();

        $cur  = $calc($curStart, $curEnd);
        $prev = $calc($prevStart, $prevEnd);

        $this->sales       = (float) ($cur->total_sales    ?? 0);
        $this->totalOrders = (int)   ($cur->order_count     ?? 0);
        $this->visitors    = (int)   ($cur->unique_visitors ?? 0);

        $prevSales    = (float) ($prev->total_sales    ?? 0);
        $prevVisitors = (int)   ($prev->unique_visitors ?? 0);

        $this->salesGrowth = $prevSales > 0
            ? round((($this->sales - $prevSales) / $prevSales) * 100, 1) : 0;

        $this->visitorsGrowth = $prevVisitors > 0
            ? round((($this->visitors - $prevVisitors) / $prevVisitors) * 100, 1) : 0;
    }

    // ── Orders table ───────────────────────────────────────────────────────────

    public function getOrdersTableProperty()
    {
        $query = Order::with([
            'customer:id,name,telp',
            'promo:id,nama',
            'items.product:id,name',
            'items.addons.addon:id,name,price',
        ])->where('status', 'paid');

        if (!empty($this->searchCustomer)) {
            $search      = $this->searchCustomer;
            $orderIds    = OrderItem::where('code', 'LIKE', "%{$search}%")->pluck('order_id');
            $customerIds = customer::where('name', 'LIKE', "%{$search}%")->pluck('id');

            $query->where(function ($q) use ($search, $customerIds, $orderIds) {
                $q->whereIn('customer_id', $customerIds)
                  ->orWhere('apprcode', 'LIKE', "%{$search}%")
                  ->orWhereIn('id', $orderIds)
                  ->orWhere('outlet', 'LIKE', "%{$search}%");
            });
        } else {
            [$curStart, $curEnd] = $this->getPeriodRanges();
            $query->whereBetween('created_at', [$curStart, $curEnd]);
        }

        return $query->latest()->paginate(25);
    }

    // ── Customer summary per outlet per day ────────────────────────────────────

    public function loadData(): void
    {
        $adminOutlet   = auth()->user()->outlet;
        [$start, $end] = $this->getSummaryRange();

        // Single aggregated query — DB does the heavy lifting
        $periodRows = Order::whereBetween('created_at', [$start, $end])
                 ->where('outlet', '!=', $adminOutlet)
        ->selectRaw("outlet, DATE(created_at) as date, COUNT(DISTINCT customer_id) as customers, SUM(total) as settle")
        ->groupBy('outlet', 'date')
        ->orderBy('outlet')
        ->orderBy('date')
        ->get();

        // MTD: per-day grouped counts for running accumulation in PHP
        $mtdRows = Order::where('created_at', '<=', $end)
                 ->where('outlet', '!=', $adminOutlet)
        ->selectRaw("outlet, DATE(created_at) as date, COUNT(DISTINCT customer_id) as customers")
        ->groupBy('outlet', 'date')
        ->orderBy('outlet')
        ->orderBy('date')
        ->get()
        ->groupBy('outlet');

        $outlets          = $periodRows->pluck('outlet')->unique()->sort()->values();
        $this->outletList = $outlets->toArray();

        if ($this->selectedOutlet !== 'all' && !$outlets->contains($this->selectedOutlet)) {
            $this->selectedOutlet = 'all';
        }

        $dates  = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }
        $this->summaryDates = $dates;

        // Index period data as [outlet][date] for O(1) lookups
        $periodIndex = [];
        foreach ($periodRows as $row) {
            $periodIndex[$row->outlet][$row->date] = [
                'customers' => (int)   $row->customers,
                'settle'    => (float) $row->settle,
            ];
        }

        // Build cumulative MTD per outlet
        $mtdIndex = [];
        foreach ($mtdRows as $outlet => $outletRows) {
            $seen = [];
            foreach ($outletRows->sortBy('date') as $row) {
                $seen[$row->date] = (int) $row->customers;
            }
            $running = 0;
            foreach ($dates as $date) {
                $running                    += $seen[$date] ?? 0;
                $mtdIndex[$outlet][$date]    = $running;
            }
        }

        $this->rows = $outlets->map(function ($outlet) use ($dates, $periodIndex, $mtdIndex) {
            $days           = [];
            $totalCustomers = 0;
            $totalSettle    = 0;

            foreach ($dates as $date) {
                $cell            = $periodIndex[$outlet][$date] ?? ['customers' => 0, 'settle' => 0.0];
                $mtd             = $mtdIndex[$outlet][$date]    ?? 0;
                $days[]          = [
                    'date'      => $date,
                    'customers' => $cell['customers'],
                    'settle'    => $cell['settle'],
                    'mtd'       => $mtd,
                ];
                $totalCustomers += $cell['customers'];
                $totalSettle    += $cell['settle'];
            }

            return [
                'outlet'          => $outlet,
                'days'            => $days,
                'total_customers' => $totalCustomers,
                'total_settle'    => $totalSettle,
            ];
        })->values()->toArray();
    }

    // ── Filtered rows ──────────────────────────────────────────────────────────

    public function getFilteredRowsProperty(): array
    {
        if ($this->selectedOutlet !== 'all') {
            return array_values(array_filter(
                $this->rows,
                fn($r) => $r['outlet'] === $this->selectedOutlet
            ));
        }

        if (empty($this->rows)) return [];

        $flat = [];
        foreach ($this->rows as $outletRow) {
            foreach ($outletRow['days'] as $i => $day) {
                $prevC  = $i > 0 ? $outletRow['days'][$i - 1]['customers'] : null;
                $growth = ($prevC !== null && $prevC > 0)
                    ? round((($day['customers'] - $prevC) / $prevC) * 100, 1)
                    : null;

                $flat[] = [
                    'date'      => $day['date'],
                    'outlet'    => $outletRow['outlet'],
                    'customers' => $day['customers'],
                    'settle'    => $day['settle'],
                    'growth'    => $growth,
                ];
            }
        }

        usort($flat, fn($a, $b) => [$a['date'], $a['outlet']] <=> [$b['date'], $b['outlet']]);

        return [[
            'outlet'          => 'Semua Outlet',
            'flat'            => $flat,
            'total_customers' => array_sum(array_column($flat, 'customers')),
            'total_settle'    => array_sum(array_column($flat, 'settle')),
        ]];
    }

    // ── Settlement ─────────────────────────────────────────────────────────────

    public function loadSettlement(): void
    {
        if (!$this->settlementDate) return;

        $start = Carbon::parse($this->settlementDate)->startOfDay();
        $end   = Carbon::parse($this->settlementDate)->endOfDay();

        // Available outlets — no eager load needed
        $this->availableOutlets = Order::where('status', 'paid')->whereBetween('created_at', [$start, $end])
            ->pluck('outlet')->unique()->sort()->values()->toArray();

        // Product summary — aggregate in DB
        $productRows = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.id', '<=', 6)
            ->where('orders.status', 'paid')
            ->where('orders.outlet', $this->settlementOutlet)
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('products.name, products.price, COUNT(*) as qty, SUM(products.price) as total')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->get();

        // Addon summary — addons are products with id > 6, stored in the products table
        $addonRows = OrderItemAddon::join('order_items', 'order_item_addons.order_item_id', '=', 'order_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_item_addons.addon_id', '=', 'products.id')
            ->where('products.id', '>', 6)
            ->where('orders.status', 'paid')
            ->where('orders.outlet', $this->settlementOutlet)
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('products.name, products.price, SUM(order_item_addons.qty) as qty, SUM(products.price * order_item_addons.qty) as total')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->get();

        // Promo summary — aggregate in DB
        $promoRows = Order::join('promos', 'orders.promo_id', '=', 'promos.id')
            ->where('orders.status', 'paid')
            ->where('orders.outlet', $this->settlementOutlet)
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('promos.nama as name, promos.diskon as amount, COUNT(*) as qty, SUM(promos.diskon) as total')
            ->groupBy('promos.id', 'promos.nama', 'promos.diskon')
            ->get();

        $products    = $productRows->map(fn($r) => ['name' => $r->name, 'price' => (float) $r->price, 'qty' => (int) $r->qty,  'total' => (float) $r->total])->toArray();
        $addons      = $addonRows->map( fn($r) => ['name' => $r->name, 'price' => (float) $r->price, 'qty' => (int) $r->qty,  'total' => (float) $r->total])->toArray();
        $promos      = $promoRows->map( fn($r) => ['name' => $r->name, 'amount' => (float) $r->amount, 'qty' => (int) $r->qty, 'total' => (float) $r->total])->toArray();

        $allProducts = array_merge($products, $addons);
        $transaksi   = array_sum(array_column($allProducts, 'total'));
        $potongan    = array_sum(array_column($promos, 'total'));

        $totalTrx = Order::where('status', 'paid')
                 ->where('outlet', $this->settlementOutlet)
                 ->whereBetween('created_at', [$start, $end])
                 ->count();

        $this->settlementData = [
            'transaksi' => $transaksi,
            'potongan'  => $potongan,
            'net'       => $transaksi - $potongan,
            'selisih'   => $this->bankSettlement - ($transaksi - $potongan),
            'total_trx' => $totalTrx,
            'products'  => $allProducts,
            'promos'    => $promos,
        ];
    }

    // ── Server check ───────────────────────────────────────────────────────────

    public function updateFrame(): void
{
    foreach ($this->servers as $key => $server) {
        try {
            $res = Http::timeout(10)->get($server['update']);

            if ($res->successful()) {
                $rawStatus = $res->json('status'); // Hasilnya: "started"

                // Opsi A: Jika ingin mempertahankan status asli ("started")
                $this->servers[$key]['status'] = $rawStatus;

                // Opsi B: Jika UI kamu butuh kata 'online' agar tampilannya hijau/aktif
                // $this->servers[$key]['status'] = ($rawStatus === 'started') ? 'online' : $rawStatus;
            } else {
                $this->servers[$key]['status'] = 'offline';
            }
        } catch (\Exception $e) {
            $this->servers[$key]['status'] = 'offline';
        }
    }
}
public function frame($x): void
{
     foreach (collect($this->servers)->where('update', $x) as $key => $server) {
        try {
            $res = Http::timeout(10)->get($server['update']);

            if ($res->successful()) {
                $rawStatus = $res->json('status'); // Hasilnya: "started"

                // Opsi A: Jika ingin mempertahankan status asli ("started")
                $this->servers[$key]['status'] = $rawStatus;

                // Opsi B: Jika UI kamu butuh kata 'online' agar tampilannya hijau/aktif
                // $this->servers[$key]['status'] = ($rawStatus === 'started') ? 'online' : $rawStatus;
            } else {
                $this->servers[$key]['status'] = 'offline';
            }
        } catch (\Exception $e) {
            // $this->servers[$key]['status'] = 'offline';
            dd($e);
        }
    }
}
    public function checkAll(): void
{
    foreach ($this->servers as $key => $server) {
        try {
            $res = Http::timeout(10)->get($server['url']);

            if ($res->successful()) {
                $rawStatus = $res->json('status'); // Hasilnya: "started"

                // Opsi A: Jika ingin mempertahankan status asli ("started")
                $this->servers[$key]['err'] = $res->json('error');
                $this->servers[$key]['status'] = $rawStatus;

                // Opsi B: Jika UI kamu butuh kata 'online' agar tampilannya hijau/aktif
                // $this->servers[$key]['status'] = ($rawStatus === 'started') ? 'online' : $rawStatus;
            } else {
                $this->servers[$key]['status'] = 'offline';
            }
        } catch (\Exception $e) {
            $this->servers[$key]['status'] = 'offline';
        }
    }
}

    // ── Regenerate ─────────────────────────────────────────────────────────────

    public function regenerate($id): void
    {
        $cartItems = OrderItem::where('id', $id)->get();
        $orders    = [];

        foreach ($cartItems as $item) {
            $orders[] = [
                'OrderId' => $item->id,
                'type'    => $item->printType,
                'count'   => $item->printCount,
                'kode'    => 'FREE',
            ];
        }

        $servers  = [
            'http://api.neophotoindonesia.my.id/generate',
            'http://backup1.neophotoindonesia.my.id/generate',
            'http://backup2.neophotoindonesia.my.id/generate',
        ];
        $maxRetry = 1;
        $data     = [];

        foreach ($servers as $server) {
            $attempt = 0;
            do {
                $attempt++;
                try {
                    $res = Http::timeout(90)->post($server, ['orders' => $orders]);
                } catch (\Exception $e) { sleep(2); continue; }
                if (!$res->successful()) { sleep(2); continue; }
                $data  = $res->json();
                $valid = collect($data)->filter(fn($item) => !empty($item['kupon']))->count();
                if ($valid > 0) break 2;
                sleep(2);
            } while ($attempt < $maxRetry);
        }

        foreach ($data as $result) {
            $item = $cartItems->where('id', $result['idx'])->first();
            if ($item && !empty($result['kupon'])) {
                $item->code = $result['kupon'];
                $item->save();
            }
        }

        $this->loadData();
    }
};
?>

<div class="main-panel">
    <div class="content-wrapper">

        {{-- Page Header --}}
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>
                Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>

        {{-- Period Toggle --}}
        <div class="d-flex align-items-center mb-4 gap-2">
            <span class="text-muted me-2 fw-semibold">Period:</span>
            @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'all' => 'All'] as $value => $label)
                <button wire:click="$set('period', '{{ $value }}')"
                        class="btn btn-sm {{ $period === $value ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Summary Cards --}}
        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/dashboard/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">{{ ucfirst($period) }} Sales <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">Rp {{ number_format($sales, 0, ',', '.') }}</h2>
                        <h6 class="card-text">
                            <i class="mdi {{ $salesGrowth >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i>
                            {{ $salesGrowth >= 0 ? 'Increased' : 'Decreased' }} by {{ abs($salesGrowth) }}%
                            <small class="ms-1 opacity-75">vs prev. {{ $period }}</small>
                        </h6>
                    </div>
                </div>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/dashboard/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">{{ ucfirst($period) }} Orders <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ number_format($totalOrders) }} orders</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/dashboard/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">{{ ucfirst($period) }} Visitors <i class="mdi mdi-diamond mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ number_format($visitors) }}</h2>
                        <h6 class="card-text">
                            <i class="mdi {{ $visitorsGrowth >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i>
                            {{ $visitorsGrowth >= 0 ? 'Increased' : 'Decreased' }} by {{ abs($visitorsGrowth) }}%
                            <small class="ms-1 opacity-75">vs prev. {{ $period }}</small>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ ucfirst($period) }} Orders</h4>
                        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" wire:model.live.debounce.400ms="searchCustomer"
                                   class="form-control w-auto"
                                   placeholder="Search by customer, outlet, appr code, or item code…" />
                            <a href="{{ route('orders.export', ['period' => $period, 'search' => $searchCustomer]) }}"
                               class="btn btn-sm btn-success"
                               target="_blank">
                                <i class="mdi mdi-microsoft-excel me-1"></i> Export Excel
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th class="d-none d-lg-table-cell">Phone</th>
                                        <th class="d-none d-lg-table-cell">Outlet</th>
                                        <th class="d-none d-lg-table-cell">Items</th>
                                        <th class="d-none d-lg-table-cell">Addons</th>
                                        <th>Appr Code</th>
                                        <th class="d-none d-lg-table-cell">Coupon</th>
                                        <th class="d-none d-lg-table-cell">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->ordersTable as $order)
                                        <tr>
                                            <td>{{ optional($order->customer)->name ?? '—' }}</td>
                                            <td class="d-none d-lg-table-cell">{{ optional($order->customer)->telp ?? '—' }}</td>
                                            <td class="d-none d-lg-table-cell">{{ $order->outlet }}</td>
                                            <td class="d-none d-lg-table-cell">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($order->items as $orderItem)
                                                        @if ($orderItem->product)
                                                            <li>{{ $orderItem->product->name }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($order->items as $orderItem)
                                                        @foreach ($orderItem->addons as $addonItem)
                                                            @if ($addonItem->addon)
                                                                <li>{{ $addonItem->addon->name }} &times; {{ $addonItem->qty }}</li>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info text-white"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#data{{ $order->apprcode }}">
                                                    {{ $order->apprcode }}
                                                </button>

                                                <div class="modal fade" wire:ignore.self
                                                     id="data{{ $order->apprcode }}"
                                                     data-bs-backdrop="static" data-bs-keyboard="false"
                                                     tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog" wire:ignore.self>
                                                        <div class="modal-content" wire:ignore.self>
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5">Informasi Order</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body" wire:ignore.self>
                                                                <div wire:loading.class.remove="d-none" wire:target="regenerate"
                                                                     class="d-none flex-column align-items-center justify-content-center py-5 gap-3">
                                                                    <div class="spinner-border text-primary" style="width:3.5rem;height:3.5rem;"></div>
                                                                    <p class="fw-bold text-primary mb-0 fs-5">Memproses...</p>
                                                                    <small class="text-muted">Mohon jangan tutup halaman ini</small>
                                                                </div>
                                                                <div wire:loading.class="d-none" wire:target="regenerate">
                                                                    <div class="card p-4">
                                                                        <h3 class="fw-bold mb-3">Receipt</h3>
                                                                        @foreach ($order->items as $orderItem)
                                                                            @if ($orderItem->product)
                                                                                <div class="border-bottom pb-2">
                                                                                    <div class="d-flex justify-content-between fw-bold">
                                                                                        <span>{{ $orderItem->product->name }}</span>
                                                                                        <button wire:click="regenerate({{ $orderItem->id }})"
                                                                                                class="btn btn-sm btn-primary">Regenerate</button>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center justify-content-between border-top border-bottom my-2 text-muted">
                                                                                        <span>CODE:</span>
                                                                                        <span class="fs-5 fw-bold">
                                                                                            {{ implode('-', str_split($orderItem->code, 3)) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                @if ($order->promo)
                                                    <span class="badge bg-success text-dark">{{ $order->promo->nama }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $this->ordersTable->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Per Hari per Outlet --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h4 class="card-title mb-0">Customer Per Hari per Outlet</h4>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @foreach (['7' => '7 Hari', '14' => '14 Hari', '30' => '30 Hari', 'custom' => 'Custom'] as $val => $lbl)
                                    <button wire:click="$set('summaryPeriod', '{{ $val }}')"
                                            class="btn btn-sm {{ $summaryPeriod === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        {{ $lbl }}
                                    </button>
                                @endforeach

                                @if ($summaryPeriod === 'custom')
                                    <input type="date" wire:model.live="summaryStart" class="form-control form-control-sm" style="width:150px">
                                    <span class="text-muted">s/d</span>
                                    <input type="date" wire:model.live="summaryEnd" class="form-control form-control-sm" style="width:150px">
                                @endif

                                <button onclick="exportElementToPDF()" class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-file-pdf me-1"></i> PDF
                                </button>
                                <button onclick="exportTableToExcel()" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-microsoft-excel me-1"></i> Excel
                                </button>
                                <button onclick="sendToGoogleSheets()" class="btn btn-sm btn-warning">
                                    <i class="mdi mdi-google-spreadsheet me-1"></i> G-Sheet
                                </button>
                            </div>
                        </div>

                        @php
                            $allOutlets = $outletList;
                            $tableData  = [];
                            foreach ($summaryDates as $date) {
                                $tableData[$date] = [];
                                foreach ($rows as $outletRow) {
                                    $dayData = collect($outletRow['days'])->firstWhere('date', $date);
                                    $tableData[$date][$outletRow['outlet']] = [
                                        'daily'  => $dayData ? $dayData['customers'] : 0,
                                        'settle' => $dayData ? $dayData['settle']    : 0,
                                        'mtd'    => $dayData ? $dayData['mtd']       : 0,
                                    ];
                                }
                            }
                        @endphp

                        <div class="table-responsive" id="exportForReport">
                            <style>
                                #crosstab { border-collapse: collapse; font-size: 12px; min-width: 100%; }
                                #crosstab th, #crosstab td { border: 1px solid #dee2e6; padding: 5px 10px; text-align: center; white-space: nowrap; }
                                #crosstab .th-outlet { background: #f8d7b0; font-weight: 700; font-size: 11px; letter-spacing: 0.3px; }
                                #crosstab .th-sub    { background: #fce8d5; font-size: 11px; color: #555; }
                                #crosstab .th-fixed  { background: #fce8d5; font-weight: 600; }
                                #crosstab .td-date   { text-align: left; font-weight: 600; background: #fff; }
                                #crosstab .td-day    { text-align: left; background: #fff; color: #555; }
                                #crosstab .td-daily  { background: #fff; }
                                #crosstab .td-settle { background: #f0faf4; color: #1a7a4a; font-weight: 600; font-size: 11px; }
                                #crosstab .td-mtd    { background: #fff; color: #444; }
                                #crosstab .highlight { background: #ffff00 !important; font-weight: 700; }
                                #crosstab .sep       { border-left: 2px solid #aaa; }
                                #crosstab tbody tr:hover td { filter: brightness(0.96); }
                                #crosstab .zero      { color: #ccc; }
                            </style>

                            <table id="crosstab">
                                <thead>
                                    <tr>
                                        <th class="th-fixed" rowspan="2">DATE</th>
                                        <th class="th-fixed" rowspan="2">DAY</th>
                                        @foreach ($allOutlets as $ol)
                                            <th class="th-outlet sep" colspan="3">{{ strtoupper($ol) }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($allOutlets as $ol)
                                            <th class="th-sub sep">Daily/하루</th>
                                            <th class="th-sub">Settle</th>
                                            <th class="th-sub">MTD/한달</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summaryDates as $dateIdx => $date)
                                        @php
                                            $isFirst = $dateIdx === 0;
                                            $carbon  = \Carbon\Carbon::parse($date);
                                        @endphp
                                        <tr>
                                            <td class="td-date {{ $isFirst ? 'highlight' : '' }}">
                                                {{ $carbon->format('d-M-y') }}
                                            </td>
                                            <td class="td-day {{ $isFirst ? 'highlight' : '' }}">
                                                {{ $carbon->locale('id')->isoFormat('dddd') }}
                                            </td>
                                            @foreach ($allOutlets as $ol)
                                                @php
                                                    $cell   = $tableData[$date][$ol] ?? ['daily' => 0, 'settle' => 0, 'mtd' => 0];
                                                    $daily  = $cell['daily'];
                                                    $settle = $cell['settle'];
                                                    $mtd    = $cell['mtd'];
                                                @endphp
                                                <td class="td-daily sep {{ $isFirst ? 'highlight' : ($daily === 0 ? 'zero' : '') }}">
                                                    {{ $daily > 0 ? $daily : ($isFirst ? $daily : '') }}
                                                </td>
                                                <td class="td-settle {{ $isFirst ? 'highlight' : ($settle == 0 ? 'zero' : '') }}">
                                                    {{ $settle > 0 ? number_format($settle, 0, ',', '.') : ($isFirst ? number_format($settle, 0, ',', '.') : '') }}
                                                </td>
                                                <td class="td-mtd {{ $isFirst ? 'highlight' : ($mtd === 0 ? 'zero' : '') }}">
                                                    {{ $mtd > 0 ? $mtd : ($isFirst ? $mtd : '') }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Settlement Report --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <div>
                                <h4 class="card-title mb-0">Settlement Report</h4>
                                @if ($settlementDate)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($settlementDate)->translatedFormat('l, d F Y') }}
                                        &mdash; {{ $settlementOutlet }}
                                    </small>
                                @endif
                            </div>
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <input type="text"
                                       wire:model.live.debounce.500ms="settlementOutlet"
                                       class="form-control form-control-sm"
                                       style="width:180px"
                                       placeholder="Nama Outlet"
                                       list="outlet-options" />
                                <datalist id="outlet-options">
                                    @foreach ($availableOutlets as $ol)
                                        <option value="{{ $ol }}">
                                    @endforeach
                                </datalist>
                                <input type="date"
                                       wire:model.live="settlementDate"
                                       class="form-control form-control-sm"
                                       style="width:160px" />
                            </div>
                        </div>

                        @if (!empty($settlementData))
                            @php $sd = $settlementData; @endphp
                            <div class="row">
                                <div class="col-md-5 mb-4">
                                    <table class="table table-bordered table-sm" style="font-size:13px;">
                                        <tbody>
                                            <tr class="table-light fw-bold">
                                                <td>Settlement Total</td>
                                                <td class="text-end">{{ number_format($bankSettlement, 0, ',', '.') }}</td>
                                                <td>
                                                    <input type="number"
                                                           wire:model.live.debounce.600ms="bankSettlement"
                                                           class="form-control form-control-sm"
                                                           placeholder="Input Bank"
                                                           style="width:140px;display:inline-block" />
                                                    <small class="text-muted ms-1">&lt;&lt;&lt; BANK</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaksi</td>
                                                <td class="text-end">{{ number_format($sd['transaksi'], 0, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Potongan</td>
                                                <td class="text-end">{{ number_format($sd['potongan'], 0, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Net</td>
                                                <td class="text-end">{{ number_format($sd['net'], 0, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                            <tr style="background:#ffff00;">
                                                <td class="fw-bold {{ $sd['selisih'] < 0 ? 'text-danger' : 'text-success' }}">Selisih</td>
                                                <td class="text-end fw-bold {{ $sd['selisih'] < 0 ? 'text-danger' : 'text-success' }}">
                                                    @if ($sd['selisih'] < 0)
                                                        ({{ number_format(abs($sd['selisih']), 0, ',', '.') }})
                                                    @else
                                                        {{ number_format($sd['selisih'], 0, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-7 mb-4">
                                    <p class="fw-semibold mb-1" style="font-size:13px;">Detail Transaksi</p>
                                    <table class="table table-bordered table-sm mb-3" style="font-size:13px;">
                                        <tbody>
                                            @foreach ($sd['products'] as $p)
                                                <tr>
                                                    <td>{{ $p['name'] }}</td>
                                                    <td class="text-end">{{ number_format($p['price'], 0, ',', '.') }}</td>
                                                    <td class="text-center">{{ $p['qty'] }}</td>
                                                    <td class="text-end">{{ number_format($p['total'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td colspan="3">Sub Total</td>
                                                <td class="text-end">{{ number_format($sd['transaksi'], 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    @if (!empty($sd['promos']))
                                        <p class="fw-semibold mb-1" style="font-size:13px;">Detail Potongan</p>
                                        <table class="table table-bordered table-sm mb-3" style="font-size:13px;">
                                            <tbody>
                                                @foreach ($sd['promos'] as $p)
                                                    <tr>
                                                        <td>{{ $p['name'] }}</td>
                                                        <td class="text-end">{{ number_format($p['amount'], 0, ',', '.') }}</td>
                                                        <td class="text-center">{{ $p['qty'] }}</td>
                                                        <td class="text-end">{{ number_format($p['total'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="3">Sub Total</td>
                                                    <td class="text-end">{{ number_format($sd['potongan'], 0, ',', '.') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    <table class="table table-bordered table-sm" style="font-size:13px;">
                                        <tbody>
                                            <tr class="fw-bold">
                                                <td>Total Transaksi</td>
                                                <td class="text-center">{{ $sd['total_trx'] }} trx</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Tidak ada data untuk outlet dan tanggal ini.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Server Status --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Servers
                            <button wire:click="checkAll" class="btn btn-sm btn-primary ms-2">
                                <span wire:loading wire:target="checkAll" class="spinner-border spinner-border-sm me-1"></span>
                                Check All
                            </button>
                            <button wire:click="updateFrame" class="btn btn-sm btn-info ms-2">
                                <span wire:loading wire:target="updateFrame" class="spinner-border spinner-border-sm me-1"></span>
                                Update All Frame
                            </button>
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Server Name</th>
                                        <th>URL</th>
                                        <th>Status</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($servers as $server)
                                        <tr>
                                            <td>{{ $server['name'] }}</td>
                                            <td>
                                                <a href="{{ $server['url'] }}" target="_blank" rel="noopener">
                                                    {{ $server['url'] }}
                                                </a>
                                            </td>
                                            <td>
                                                @if ($server['status'] === 'Idle')
                                                    <span class="badge bg-success">Ok</span>
                                                @elseif ($server['status'] === 'started')
                                                    <span class="badge bg-warning">Updating</span>
                                                @elseif ($server['status'] === 'Updating')
                                                    <span class="badge bg-warning">Updating</span>
                                                @elseif ($server['status'] === 'Failed')
                                                    <span class="badge bg-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">Update Failed</span>
                                                    <!-- Modal -->
                                                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                          <div class="modal-dialog">
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                              </div>
                                                              <div class="modal-body">
                                                                {{$server['err']}}
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                @else
                                                    <span class="badge bg-info">{{$server['status']}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button wire:click="frame('{{$server['update']}}')" class="btn btn-sm btn-info ms-2">
                                                    <span wire:loading wire:target="frame('{{$server['update']}}')" class="spinner-border spinner-border-sm me-1"></span>
                                                    Update Frame
                                                </button>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /.content-wrapper --}}

    <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                <a href="#" target="_blank">PT. Neophoto Indonesia</a>. All rights reserved.
            </span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i>
            </span>
        </div>
    </footer>
</div>

@once
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>

function exportElementToPDF() {
    const element = document.getElementById('exportForReport');
    const options = {
        margin:      0.5,
        filename:    'customer-summary.pdf',
        image:       { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF:       { unit: 'in', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(options).from(element).save();
}

function exportTableToExcel() {
    const table = document.getElementById('crosstab');
    if (!table) { alert('Tidak ada data.'); return; }

    const wb    = XLSX.utils.book_new();
    const ws    = XLSX.utils.table_to_sheet(table);
    const range = XLSX.utils.decode_range(ws['!ref']);
    const cols  = [];

    for (let c = range.s.c; c <= range.e.c; c++) {
        let max = 8;
        for (let r = range.s.r; r <= range.e.r; r++) {
            const cell = ws[XLSX.utils.encode_cell({r, c})];
            if (cell && cell.v) max = Math.max(max, String(cell.v).length + 2);
        }
        cols.push({ wch: Math.min(max, 24) });
    }
    ws['!cols'] = cols;

    const now      = new Date();
    const filename = `customer-summary-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}.xlsx`;

    XLSX.utils.book_append_sheet(wb, ws, 'Customer Summary');
    XLSX.writeFile(wb, filename);
}

const GSHEET_URL = 'YOUR_GOOGLE_APPS_SCRIPT_URL_HERE';

function sendToGoogleSheets() {
    const table = document.getElementById('crosstab');
    if (!table) { alert('Tidak ada data.'); return; }

    const btn  = document.querySelector('[onclick="sendToGoogleSheets()"]');
    const orig = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
    btn.disabled  = true;

    const data = [];
    table.querySelectorAll('tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('th, td').forEach(cell => row.push(cell.innerText.trim()));
        data.push(row);
    });

    fetch(GSHEET_URL, {
        method:  'POST',
        mode:    'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ data }),
    })
    .then(() => {
        btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Sent!';
        btn.classList.replace('btn-warning', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.classList.replace('btn-success', 'btn-warning');
            btn.disabled = false;
        }, 3000);
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = orig;
        btn.disabled  = false;
        alert('Gagal kirim ke Google Sheets.');
    });
}
</script>
@endpush
@endonce