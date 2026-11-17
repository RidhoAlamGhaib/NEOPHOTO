@php
    // Operational hours: open 10:00 until 22:00 (last open slot is 21:00-22:00)
    $openHour = 10;
    $closeHour = 22;

    // Optional: pin outlets to a fixed hue (0-359). Others get a stable hue from their name.
    $outletHues = [
        // 'Outlet Name' => 10,
    ];

    $selectedOutlet = $insights['selectedOutlet'];

    // Display labels for the period filter (weekly/monthly are rolling windows ending today)
    $periodLabel = [
        'daily' => 'Today',
        'weekly' => 'Last 7 days',
        'monthly' => 'Last 30 days',
        '3months' => '3 months',
        '6months' => '6 months',
        'all' => 'All time',
    ][$insights['period']] ?? $insights['periodLabel'];
    $outletHue = $selectedOutlet === 'all'
        ? 249
        : ($outletHues[$selectedOutlet] ?? crc32(mb_strtolower($selectedOutlet)) % 360);

    // ── Visitors-per-day chart ────────────────────────────────────────────
    $vcToday = \Carbon\Carbon::today();
    $vcRows = array_values($insights['dailyVisitors'] ?? []);

    // Weekly / monthly are rolling windows ending today: keep only the most recent 7 / 30 days.
    $vcWindow = ['weekly' => 7, 'monthly' => 30][$insights['period']] ?? null;
    if ($vcWindow && count($vcRows) > $vcWindow) {
        $vcRows = array_slice($vcRows, -$vcWindow);
    }

    $vcN = count($vcRows);
    $vcMaxData = $vcN ? max(array_map(fn ($r) => (int) $r['visitors'], $vcRows)) : 0;

    // Which separators exist in this range (drives the legend)
    $vcHasWeekBreak = false;
    $vcHasMonthBreak = false;
    foreach ($vcRows as $vcI => $vcRow) {
        if ($vcI === 0) {
            continue;
        }
        $vcDt = \Carbon\Carbon::parse($vcRow['date']);
        if ($vcDt->day === 1) {
            $vcHasMonthBreak = true;
        } elseif ($vcDt->isMonday()) {
            $vcHasWeekBreak = true;
        }
    }

    // Standardised y-axis: round steps (1, 2, 5, 10, 20, 25, 50 ...) with at most 5 intervals.
    $vcStep = 1;
    foreach ([1, 2, 5, 10, 20, 25, 50, 100, 200, 250, 500, 1000, 2000, 2500, 5000, 10000] as $vcCandidate) {
        $vcStep = $vcCandidate;
        if (ceil($vcMaxData / $vcCandidate) <= 5) {
            break;
        }
    }
    $vcAxisMax = (int) (ceil($vcMaxData / $vcStep) * $vcStep);
    $vcAxisMax = max($vcAxisMax, $vcStep === 1 ? 4 : $vcStep);
    $vcTicks = range(0, $vcAxisMax, $vcStep);

    // Label density: weekday + date for short ranges, day number for up to a month, sparse dates beyond that.
    $vcMode = $vcN <= 14 ? 'weekday' : ($vcN <= 31 ? 'day' : 'sparse');
    $vcShowCounts = $vcN <= 20;
    $vcLabelStep = $vcMode === 'sparse' ? max(1, (int) ceil($vcN / 12)) : 1;
    $vcMinWidth = $vcMode === 'sparse' ? $vcN * 5 : $vcN * 24;
@endphp

<style>
    /* Everything is scoped to .mi-theme so the admin template cannot override it
       and this partial cannot leak styles into the rest of the dashboard. */
    .mi-theme {
        --mi-accent: hsl(var(--hue) 74% 62%);
        --mi-accent-strong: hsl(var(--hue) 70% 48%);
        --mi-soft: hsl(var(--hue) 100% 95%);
        --mi-tint: hsl(var(--hue) 100% 97.5%);
        --mi-track: hsl(var(--hue) 25% 95%);
        --mi-bar-light: hsl(var(--hue) 80% 82%);
        --mi-border: hsl(var(--hue) 25% 90%);
        --mi-week-line: hsl(var(--hue) 25% 80%);
        --mi-month-line: hsl(var(--hue) 35% 58%);
        --mi-muted: #858198;
        border-top: 5px solid var(--mi-accent);
        background: var(--mi-tint);
    }

    .mi-theme .mi-eyebrow { color: var(--mi-accent-strong); }

    /* Dropdown padding (right side leaves room for the arrow) */
    .mi-theme .form-select {
        padding-left: .9rem;
        padding-right: 2.4rem;
    }
    .mi-theme .mi-box { background: #fff; border: 1px solid var(--mi-border); border-radius: .5rem; }
    .mi-theme .mi-badge { background: var(--mi-soft); color: var(--mi-accent-strong); font-weight: 700; }

    /* Filter dropdowns: the admin template forces fixed heights/padding on selects,
       which clipped the selected text. Size them explicitly instead. */
    .mi-theme .mi-select {
        width: auto;
        height: auto;
        min-height: 34px;
        padding: .35rem 2.25rem .35rem .75rem;
        font-size: .875rem;
        line-height: 1.5;
        overflow: visible;
        text-overflow: clip;
        white-space: nowrap;
    }

    .mi-theme .mi-select-period { min-width: 150px; }
    .mi-theme .mi-select-outlet { min-width: 170px; max-width: 100%; }

    /* Filter dropdowns: a little extra padding (right side keeps room for the arrow) */
    .mi-theme .form-select {
        padding-top: .45rem;
        padding-bottom: .45rem;
        padding-left: .85rem;
        padding-right: 2.25rem;
    }

    .mi-theme form .btn {
        padding: .45rem .95rem;
    }

    /* Peak-hours chart */
    .mi-theme .mi-chart-scroll { overflow-x: auto; padding-bottom: 4px; }

    .mi-theme .mi-chart {
        display: grid;
        grid-template-columns: repeat(24, minmax(18px, 1fr));
        gap: 4px;
        min-width: 480px;
    }

    /* Visitors-per-day chart */
    .mi-theme .mi-vc { --vc-h: 140px; display: flex; align-items: flex-start; gap: 6px; }
    .mi-theme .mi-vc-axis { position: relative; flex: none; width: 34px; height: var(--vc-h); margin-top: var(--vc-top, 0px); }
    .mi-theme .mi-vc-tick {
        position: absolute;
        right: 4px;
        transform: translateY(50%);
        font-size: 10px;
        line-height: 1;
        color: var(--mi-muted);
    }
    /* padding-bottom keeps the horizontal scrollbar clear of the date labels */
    .mi-theme .mi-vc-scroll { flex: 1 1 auto; min-width: 0; overflow-x: auto; padding-bottom: 16px; }
    .mi-theme .mi-vc-plot { position: relative; padding: 0 10px; }
    .mi-theme .mi-vc-grid { position: absolute; left: 0; right: 0; top: var(--vc-top, 0px); height: var(--vc-h); z-index: 0; pointer-events: none; }
    .mi-theme .mi-vc-gridline { position: absolute; left: 0; right: 0; border-top: 1px dashed var(--mi-border); }
    .mi-theme .mi-vc-gridline.is-base { border-top-style: solid; }
    .mi-theme .mi-vc-cols {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(var(--n), minmax(0, 1fr));
        gap: var(--vc-gap, 4px);
    }
    .mi-theme .mi-vc-col { position: relative; display: flex; flex-direction: column; gap: 6px; min-width: 0; }

    /* Background separators: thin line before each Monday, bold line before the 1st of a month.
       They run from the top of the chart down to the baseline, behind the bars. */
    .mi-theme .mi-vc-col.is-week-start::before,
    .mi-theme .mi-vc-col.is-month-start::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: calc(var(--vc-label-h, 28px) + 6px);
        left: calc(var(--vc-gap, 4px) / -2);
        transform: translateX(-50%);
        z-index: -1;
        pointer-events: none;
    }
    .mi-theme .mi-vc-col.is-week-start::before { width: 1px; background: var(--mi-week-line); }
    .mi-theme .mi-vc-col.is-month-start::before { width: 2px; background: var(--mi-month-line); }
    .mi-theme .mi-vc-count {
        display: block;
        height: 14px;
        font-size: 10px;
        line-height: 14px;
        text-align: center;
        white-space: nowrap;
        color: var(--mi-muted);
    }
    .mi-theme .mi-vc-track { position: relative; display: flex; align-items: flex-end; height: var(--vc-h); }
    .mi-theme .mi-vc-bar { width: 100%; min-height: 2px; border-radius: 4px 4px 0 0; background: var(--mi-accent); }
    .mi-theme .mi-vc-bar.is-zero { background: var(--mi-bar-light); }
    .mi-theme .mi-vc-col.is-today .mi-vc-bar { background: var(--mi-accent-strong); }
    .mi-theme .mi-vc-col.is-future .mi-vc-track {
        border: 1px dashed var(--mi-bar-light);
        border-bottom: 0;
        border-radius: 4px 4px 0 0;
        background: var(--mi-soft);
        opacity: .6;
    }
    .mi-theme .mi-vc-label {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: var(--vc-label-h, 28px);
        font-size: 10px;
        line-height: 14px;
        white-space: nowrap;
        color: var(--mi-muted);
    }
    .mi-theme .mi-vc-label span { display: block; min-height: 14px; }

    /* Small stem joining each date label to its column / data point */
    .mi-theme .mi-vc-label.has-date::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 50%;
        width: 1px;
        height: 6px;
        transform: translateX(-50%);
        background: var(--mi-muted);
    }
    .mi-theme .mi-vc-col.is-future .mi-vc-label { opacity: .7; }
    .mi-theme .mi-vc-col.is-today .mi-vc-label { color: var(--mi-accent-strong); font-weight: 800; }

    .mi-theme .mi-col {
        display: grid;
        grid-template-rows: 14px 120px 14px; /* count / bar track / hour label: fixed rows keep everything aligned */
        gap: 6px;
        min-width: 0;
        text-align: center;
    }

    .mi-theme .mi-count,
    .mi-theme .mi-hour {
        display: block;
        font-size: 10px;
        line-height: 14px;
        color: var(--mi-muted);
        white-space: nowrap;
    }

    .mi-theme .mi-track {
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        border-radius: 5px;
        background: var(--mi-track);
    }

    .mi-theme .mi-bar {
        width: 100%;
        min-height: 2px;
        border-radius: 5px 5px 0 0;
        background: var(--mi-bar-light);
    }

    .mi-theme .mi-bar.is-peak { background: var(--mi-accent); }

    /* Operational hours highlight */
    .mi-theme .mi-col:not(.is-open) { opacity: .5; }

    .mi-theme .mi-col.is-open .mi-track {
        background: var(--mi-soft);
        box-shadow: inset 0 0 0 1px var(--mi-bar-light);
    }

    .mi-theme .mi-col.is-open .mi-hour {
        color: var(--mi-accent-strong);
        font-weight: 800;
    }

    .mi-theme .mi-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        margin-top: 14px;
        font-size: 12px;
        color: var(--mi-muted);
    }

    .mi-theme .mi-legend span { display: inline-flex; align-items: center; gap: 6px; }
    .mi-theme .mi-swatch { width: 14px; height: 14px; border-radius: 4px; }
    .mi-theme .mi-swatch.is-open { background: var(--mi-soft); box-shadow: inset 0 0 0 1px var(--mi-bar-light); }
    .mi-theme .mi-swatch.is-closed { background: var(--mi-track); opacity: .6; }
    .mi-theme .mi-swatch.is-peak { background: var(--mi-accent); }
    .mi-theme .mi-swatch-line { display: inline-block; width: 0; height: 14px; border-left: 1px solid var(--mi-week-line); }
    .mi-theme .mi-swatch-line.is-month { border-left: 2px solid var(--mi-month-line); }

    /* Product bars */
    .mi-theme .mi-progress { height: 8px; background: var(--mi-track); border-radius: 999px; }
    .mi-theme .mi-progress .progress-bar {
        background: linear-gradient(90deg, var(--mi-accent), var(--mi-bar-light));
        border-radius: 999px;
    }
</style>

<section class="card mb-4 mi-theme" style="--hue: {{ $outletHue }}" aria-labelledby="marketing-insights-title">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="mi-eyebrow text-uppercase fw-semibold small">Marketing insights</span>
                <h3 id="marketing-insights-title" class="mt-1 mb-1">Visitor &amp; product performance</h3>
                <p class="text-muted mb-0">
                    {{ $periodLabel }} overview of paid orders, visitors, peak hours, and best-selling products.
                </p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap gap-2" aria-label="Filter marketing insights">
                <label class="visually-hidden" for="dashboard-marketing-period">Reporting period</label>
                <select id="dashboard-marketing-period" class="form-select form-select-sm mi-select mi-select-period" name="period">
                    @foreach (['daily' => 'Today', 'weekly' => 'Last 7 days', 'monthly' => 'Last 30 days', '3months' => '3 months', '6months' => '6 months', 'all' => 'All time'] as $value => $label)
                        <option value="{{ $value }}" @selected($insights['period'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <label class="visually-hidden" for="dashboard-marketing-outlet">Outlet</label>
                <select id="dashboard-marketing-outlet" class="form-select form-select-sm mi-select mi-select-outlet" name="outlet">
                    <option value="all" @selected($selectedOutlet === 'all')>All outlets</option>
                    @foreach ($insights['outletOptions'] as $outlet)
                        <option value="{{ $outlet }}" @selected($selectedOutlet === $outlet)>{{ $outlet }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Apply</button>
            </form>
        </div>

        <div class="row">
            @foreach ([
                ['label' => 'Visitors', 'value' => $insights['visitorCount'], 'description' => 'Unique customers with a paid order'],
                ['label' => 'Paid orders', 'value' => $insights['orderCount'], 'description' => 'Paid orders in '.strtolower($periodLabel)],
                ['label' => 'Products bought', 'value' => $insights['unitsBought'], 'description' => 'Product units across paid orders'],
            ] as $stat)
                <div class="col-md-4 mb-3">
                    <div class="mi-box p-3 h-100">
                        <span class="text-muted d-block">{{ $stat['label'] }}</span>
                        <strong class="h3 d-block my-2">{{ number_format($stat['value']) }}</strong>
                        <small class="text-muted">{{ $stat['description'] }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($insights['period'] !== 'daily')
            <div class="mi-box p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h4 class="card-title mb-0">Visitors per day</h4>
                    <span class="badge mi-badge">{{ $periodLabel }}</span>
                </div>
                <p class="text-muted small mb-3">Unique customers with paid orders each day.</p>
                @if ($vcN > 0)
                    <div class="mi-vc" style="--vc-top: {{ $vcShowCounts ? '20px' : '0px' }}; --vc-gap: {{ $vcMode === 'sparse' ? '1px' : '4px' }}; --vc-label-h: {{ $vcMode === 'sparse' ? '14px' : '28px' }}">
                        <div class="mi-vc-axis" aria-hidden="true">
                            @foreach ($vcTicks as $tick)
                                <span class="mi-vc-tick" style="bottom: {{ round($tick / $vcAxisMax * 100, 2) }}%">{{ number_format($tick) }}</span>
                            @endforeach
                        </div>

                        <div class="mi-vc-scroll">
                            <div class="mi-vc-plot" style="--n: {{ $vcN }}; min-width: {{ $vcMinWidth }}px">
                                <div class="mi-vc-grid" aria-hidden="true">
                                    @foreach ($vcTicks as $tick)
                                        <div class="mi-vc-gridline {{ $tick === 0 ? 'is-base' : '' }}" style="bottom: {{ round($tick / $vcAxisMax * 100, 2) }}%"></div>
                                    @endforeach
                                </div>

                                <div class="mi-vc-cols" role="img" aria-label="Daily unique visitors">
                                    @foreach ($vcRows as $i => $row)
                                        @php
                                            $vcDate = \Carbon\Carbon::parse($row['date']);
                                            $vcFuture = $vcDate->gt($vcToday);
                                            $vcIsToday = $vcDate->isSameDay($vcToday);
                                            $vcV = (int) $row['visitors'];
                                            $vcShowLabel = $vcMode !== 'sparse' || $i % $vcLabelStep === 0;
                                            $vcMonthMark = $i === 0 || $vcDate->day === 1;
                                            $vcBreak = $i === 0 ? '' : ($vcDate->day === 1 ? 'is-month-start' : ($vcDate->isMonday() ? 'is-week-start' : ''));
                                        @endphp
                                        <div class="mi-vc-col {{ $vcFuture ? 'is-future' : '' }} {{ $vcIsToday ? 'is-today' : '' }} {{ $vcBreak }}"
                                             title="{{ $vcDate->format('D, d M Y') }} — {{ $vcFuture ? 'upcoming' : number_format($vcV).' visitors' }}">
                                            @if ($vcShowCounts)
                                                <span class="mi-vc-count">{{ ! $vcFuture && $vcV > 0 ? number_format($vcV) : '' }}</span>
                                            @endif
                                            <div class="mi-vc-track">
                                                @if (! $vcFuture)
                                                    <div class="mi-vc-bar {{ $vcV === 0 ? 'is-zero' : '' }}" style="height: {{ $vcV > 0 ? round($vcV / $vcAxisMax * 100, 2) : 0 }}%"></div>
                                                @endif
                                            </div>
                                            <div class="mi-vc-label {{ $vcShowLabel ? 'has-date' : '' }}">
                                                @if ($vcShowLabel)
                                                    @if ($vcMode === 'weekday')
                                                        <span>{{ $vcDate->format('D') }}</span>
                                                        <span>{{ $vcDate->format('j M') }}</span>
                                                    @elseif ($vcMode === 'day')
                                                        <span>{{ $vcDate->format('j') }}</span>
                                                        <span>{{ $vcMonthMark ? $vcDate->format('M') : '' }}</span>
                                                    @else
                                                        <span>{{ $vcDate->format('j M') }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mi-legend" style="margin-top: 0">
                        <span><i class="mi-swatch" style="background: var(--mi-accent)"></i> Visitors</span>
                        @if ($vcHasWeekBreak)
                            <span><i class="mi-swatch-line"></i> Sunday - Monday</span>
                        @endif
                        @if ($vcHasMonthBreak)
                            <span><i class="mi-swatch-line is-month"></i> End of month - start of next</span>
                        @endif
                    </div>
                @else
                    <p class="text-muted mb-0">No visitor data for this period.</p>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mb-3">
                <div class="mi-box p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h4 class="card-title mb-0">Peak hours</h4>
                        <span class="badge mi-badge">{{ $periodLabel }}</span>
                    </div>
                    <p class="text-muted small mb-3">Paid orders grouped by the hour they were placed.</p>
                    @if ($insights['peakHours'] !== [])
                        <p class="mb-3">
                            Busiest
                            @foreach ($insights['peakHours'] as $index => $hour)
                                @if ($index > 0), @endif
                                <strong style="color: var(--mi-accent-strong)">{{ sprintf('%02d:00–%02d:00', $hour, ($hour + 1) % 24) }}</strong>
                            @endforeach
                            <span class="text-muted"> · {{ number_format($insights['peakOrderCount']) }} orders each</span>
                        </p>
                    @else
                        <p class="text-muted mb-3">No paid orders in this period.</p>
                    @endif

                    <div class="mi-chart-scroll">
                        <div class="mi-chart" role="img" aria-label="Number of paid orders by hour">
                            @foreach ($insights['hourlyOrders'] as $hour => $orderCount)
                                <div class="mi-col {{ $hour >= $openHour && $hour < $closeHour ? 'is-open' : '' }}" title="{{ sprintf('%02d:00 — %d orders', $hour, $orderCount) }}">
                                    <span class="mi-count">{{ $orderCount > 0 ? $orderCount : '' }}</span>
                                    <div class="mi-track">
                                        <div class="mi-bar {{ in_array($hour, $insights['peakHours'], true) ? 'is-peak' : '' }}" style="height: {{ $orderCount > 0 ? max(5, round(($orderCount / $insights['maxHourlyOrders']) * 100)) : 2 }}%"></div>
                                    </div>
                                    <span class="mi-hour">{{ $hour % 4 === 0 ? sprintf('%02d', $hour) : '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mi-legend">
                        <span><i class="mi-swatch is-open"></i> Operational hours ({{ sprintf('%02d:00–%02d:00', $openHour, $closeHour) }})</span>
                        <span><i class="mi-swatch is-closed"></i> Outside hours</span>
                        <span><i class="mi-swatch is-peak"></i> Busiest hour</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-3">
                <div class="mi-box p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h4 class="card-title mb-0">Best-selling products</h4>
                        <span class="badge mi-badge">Top {{ count($insights['bestSellers']) }}</span>
                    </div>
                    <p class="text-muted small mb-3">Ranked by purchased product units.</p>
                    @forelse ($insights['bestSellers'] as $product)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge mi-badge">{{ $loop->iteration }}</span>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between gap-2">
                                    <span class="text-truncate">{{ $product['name'] }}</span>
                                    <strong>{{ number_format($product['units_sold']) }}</strong>
                                </div>
                                <div class="progress mi-progress" role="progressbar" aria-label="{{ $product['name'] }} units sold" aria-valuenow="{{ $product['units_sold'] }}" aria-valuemin="0" aria-valuemax="{{ $insights['maxProductUnits'] }}">
                                    <div class="progress-bar" style="width: {{ max(3, round(($product['units_sold'] / $insights['maxProductUnits']) * 100)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mi-box p-3 mb-0">No product purchases in this period.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <p class="text-muted small mb-0">
            Visitor counts reflect unique customers with paid orders; unpurchased walk-in visits are not currently recorded.
        </p>
    </div>
</section>
