<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketingDashboardAnalytics
{
    private const B1G1_PRODUCT_ID = 14;

    private const B1G1_PAID_PRINT_TYPE = 104;

    /**
     * @return array<int, string>
     */
    public function outletOptions(): array
    {
        return User::query()
            ->whereNotNull('outlet')
            ->distinct()
            ->orderBy('outlet')
            ->pluck('outlet')
            ->reject(fn (string $outlet): bool => in_array(
                strtolower(trim($outlet)),
                ['admin', 'superadmin', 'marketing', 'all', 'all outlet'],
                true,
            ))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getInsights(string $period, string $selectedOutlet): array
    {
        $now = Carbon::now();

        $range = match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'monthly' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            '3months' => [$now->copy()->subMonthsNoOverflow(3)->startOfDay(), $now->copy()->endOfDay()],
            '6months' => [$now->copy()->subMonthsNoOverflow(6)->startOfDay(), $now->copy()->endOfDay()],
            default => null,
        };
        $periodLabel = match ($period) {
            'daily' => 'Today',
            'weekly' => 'Last 7 days',
            'monthly' => 'Last 30 days',
            '3months' => 'Past 3 months',
            '6months' => 'Past 6 months',
            default => 'All time',
        };

        $paidOrders = DB::table('orders')->where('status', 'paid');

        if ($selectedOutlet !== 'all') {
            $paidOrders->where('outlet', $selectedOutlet);
        }

        if ($range !== null) {
            $paidOrders->whereBetween('created_at', $range);
        }

        $summary = (clone $paidOrders)
            ->selectRaw('COUNT(DISTINCT customer_id) as visitors, COUNT(*) as orders')
            ->first();

        $dailyVisitors = [];

        if ($period !== 'daily') {
            $visitorRows = (clone $paidOrders)
                ->selectRaw('DATE(created_at) as visit_date, COUNT(DISTINCT customer_id) as visitors')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('visit_date')
                ->get();

            $chartStart = $range[0] ?? (
                $visitorRows->isNotEmpty()
                    ? Carbon::parse($visitorRows->first()->visit_date)
                    : null
            );
            $chartEnd = $range[1] ?? $now->copy()->endOfDay();

            if ($chartEnd->greaterThan($now)) {
                $chartEnd = $now->copy()->endOfDay();
            }

            if ($chartStart !== null) {
                $visitorCountsByDate = $visitorRows->mapWithKeys(fn ($row): array => [
                    $row->visit_date => (int) $row->visitors,
                ])->all();
                $chartStart = $chartStart->copy()->startOfDay();
                $chartEnd = $chartEnd->copy()->startOfDay();
                $dayCount = (int) $chartStart->diffInDays($chartEnd) + 1;
                $labelInterval = match (true) {
                    $dayCount <= 14 => 1,
                    $dayCount <= 45 => 7,
                    $dayCount <= 200 => 14,
                    default => 30,
                };

                for ($index = 0; $chartStart->lte($chartEnd); $chartStart->addDay(), $index++) {
                    $date = $chartStart->toDateString();
                    $dailyVisitors[] = [
                        'date' => $date,
                        'label' => $index % $labelInterval === 0 || $index === $dayCount - 1
                            ? $chartStart->format('M j')
                            : '',
                        'visitors' => $visitorCountsByDate[$date] ?? 0,
                    ];
                }
            }
        }

        $hourExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)",
            default => 'HOUR(created_at)',
        };
        $hourlyOrderRows = (clone $paidOrders)
            ->selectRaw("$hourExpression as hour, COUNT(*) as order_count")
            ->groupByRaw($hourExpression)
            ->orderBy('hour')
            ->get();

        $hourlyOrders = array_fill(0, 24, 0);

        foreach ($hourlyOrderRows as $row) {
            $hourlyOrders[(int) $row->hour] = (int) $row->order_count;
        }

        $peakOrderCount = max($hourlyOrders);
        $peakHours = $peakOrderCount > 0
            ? array_keys(array_filter(
                $hourlyOrders,
                fn (int $orderCount): bool => $orderCount === $peakOrderCount,
            ))
            : [];

        $productSales = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'paid')
            ->where(function ($query): void {
                $query->where('order_items.product_id', '!=', self::B1G1_PRODUCT_ID)
                    ->orWhere(function ($query): void {
                        $query->where('order_items.product_id', self::B1G1_PRODUCT_ID)
                            ->where('order_items.printType', self::B1G1_PAID_PRINT_TYPE);
                    });
            })
            ->when(
                $selectedOutlet !== 'all',
                fn ($query) => $query->where('orders.outlet', $selectedOutlet),
            )
            ->when($range !== null, fn ($query) => $query->whereBetween('orders.created_at', $range))
            ->select('products.id', 'products.name')
            ->selectRaw('COUNT(*) as units_sold')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('units_sold')
            ->orderBy('products.name')
            ->get()
            ->map(fn ($product): array => [
                'name' => $product->name,
                'units_sold' => (int) $product->units_sold,
            ])
            ->all();
        $bestSellers = array_slice($productSales, 0, 5);

        return [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'selectedOutlet' => $selectedOutlet,
            'outletOptions' => $this->outletOptions(),
            'visitorCount' => (int) ($summary->visitors ?? 0),
            'orderCount' => (int) ($summary->orders ?? 0),
            'dailyVisitors' => $dailyVisitors,
            'maxDailyVisitors' => max(1, max(array_column($dailyVisitors, 'visitors') ?: [0])),
            'unitsBought' => array_sum(array_column($productSales, 'units_sold')),
            'hourlyOrders' => $hourlyOrders,
            'peakHours' => $peakHours,
            'peakOrderCount' => $peakOrderCount,
            'maxHourlyOrders' => max(1, $peakOrderCount),
            'bestSellers' => $bestSellers,
            'maxProductUnits' => max(1, $bestSellers[0]['units_sold'] ?? 0),
        ];
    }
}
