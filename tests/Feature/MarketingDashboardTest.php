<?php

use App\Models\customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (! Schema::hasColumn('order_items', 'printCount')) {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->unsignedInteger('printCount')->nullable();
        });
    }

    if (! Schema::hasColumn('order_items', 'printType')) {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->unsignedInteger('printType')->nullable();
        });
    }

    if (! Schema::hasColumn('orders', 'outlet')) {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('outlet')->nullable();
        });
    }

    if (! Schema::hasColumn('orders', 'total')) {
        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('total', 12, 2)->nullable();
        });
    }
});

test('the standalone marketing route is no longer available', function () {
    $response = $this->get('/marketing');

    $response->assertNotFound();
});

test('users with the marketing outlet see non-monetary purchase insights', function () {
    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(16, 0));

    $user = User::factory()->create(['outlet' => 'marketing']);
    $firstVisitor = customer::create(['name' => 'First visitor', 'telp' => '0811111111']);
    $secondVisitor = customer::create(['name' => 'Second visitor', 'telp' => '0822222222']);
    $portrait = product::create(['name' => 'Portrait Print', 'type' => 'print', 'price' => '100']);
    $classic = product::create(['name' => 'Classic Strip', 'type' => 'print', 'price' => '100']);
    $b1g1 = product::create(['id' => 14, 'name' => 'B1G1 Halloween', 'type' => '106', 'price' => '100']);

    $peakOrderOne = Order::create([
        'customer_id' => $firstVisitor->id,
        'order-UC' => 'marketing-peak-one',
        'status' => 'paid',
        'created_at' => '2026-11-02 14:10:00',
        'updated_at' => '2026-11-02 14:10:00',
    ]);
    OrderItem::create([
        'order_id' => $peakOrderOne->id,
        'product_id' => $portrait->id,
        'printCount' => 2,
    ]);

    $peakOrderTwo = Order::create([
        'customer_id' => $secondVisitor->id,
        'order-UC' => 'marketing-peak-two',
        'status' => 'paid',
        'created_at' => '2026-11-02 14:45:00',
        'updated_at' => '2026-11-02 14:45:00',
    ]);
    OrderItem::create([
        'order_id' => $peakOrderTwo->id,
        'product_id' => $classic->id,
        'printCount' => 2,
    ]);

    $repeatOrder = Order::create([
        'customer_id' => $firstVisitor->id,
        'order-UC' => 'marketing-repeat',
        'status' => 'paid',
        'created_at' => '2026-11-03 10:15:00',
        'updated_at' => '2026-11-03 10:15:00',
    ]);
    OrderItem::create([
        'order_id' => $repeatOrder->id,
        'product_id' => $portrait->id,
        'printCount' => 1,
    ]);

    $b1g1Order = Order::create([
        'customer_id' => $firstVisitor->id,
        'order-UC' => 'marketing-b1g1',
        'status' => 'paid',
        'created_at' => '2026-11-03 12:15:00',
        'updated_at' => '2026-11-03 12:15:00',
    ]);
    OrderItem::create([
        'order_id' => $b1g1Order->id,
        'product_id' => $b1g1->id,
        'printType' => 104,
        'printCount' => 1,
    ]);
    OrderItem::create([
        'order_id' => $b1g1Order->id,
        'product_id' => $b1g1->id,
        'printType' => 101,
        'printCount' => 2,
    ]);

    $oldOrder = Order::create([
        'customer_id' => $firstVisitor->id,
        'order-UC' => 'marketing-older',
        'status' => 'paid',
        'created_at' => '2026-11-01 14:00:00',
        'updated_at' => '2026-11-01 14:00:00',
    ]);
    OrderItem::create([
        'order_id' => $oldOrder->id,
        'product_id' => $portrait->id,
        'printCount' => 9,
    ]);

    $unpaidOrder = Order::create([
        'customer_id' => $secondVisitor->id,
        'order-UC' => 'marketing-unpaid',
        'status' => 'pending',
        'created_at' => '2026-11-05 15:00:00',
        'updated_at' => '2026-11-05 15:00:00',
    ]);
    OrderItem::create([
        'order_id' => $unpaidOrder->id,
        'product_id' => $portrait->id,
        'printCount' => 100,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewHas('insights', fn ($insights) => $insights['visitorCount'] === 2
            && $insights['orderCount'] === 5
            && $insights['unitsBought'] === 5
            && $insights['peakHours'] === [14]
            && collect($insights['bestSellers'])->mapWithKeys(
                fn ($product) => [$product['name'] => $product['units_sold']]
            )->all() === [
                'Portrait Print' => 3,
                'B1G1 Halloween' => 1,
                'Classic Strip' => 1,
            ]
            && count($insights['dailyVisitors']) === 7
            && $insights['dailyVisitors'][2]['date'] === '2026-11-01'
            && $insights['dailyVisitors'][2]['visitors'] === 1
            && $insights['dailyVisitors'][3]['date'] === '2026-11-02'
            && $insights['dailyVisitors'][3]['visitors'] === 2
            && $insights['dailyVisitors'][4]['date'] === '2026-11-03'
            && $insights['dailyVisitors'][4]['visitors'] === 1
            && $insights['dailyVisitors'][6]['date'] === '2026-11-05'
            && $insights['dailyVisitors'][6]['visitors'] === 0)
        ->assertSee('14:00–15:00')
        ->assertSee('Portrait Print')
        ->assertSee('Classic Strip')
        ->assertDontSee('Revenue')
        ->assertDontSee('Rp');
});

test('marketing period selection limits the displayed statistics', function () {
    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(16, 0));

    $user = User::factory()->create(['outlet' => 'marketing']);
    $visitor = customer::create(['name' => 'Daily visitor', 'telp' => '0833333333']);
    $product = product::create(['name' => 'Daily Print', 'type' => 'print', 'price' => '100']);

    $todayOrder = Order::create([
        'customer_id' => $visitor->id,
        'order-UC' => 'marketing-today',
        'status' => 'paid',
        'created_at' => '2026-11-05 10:00:00',
        'updated_at' => '2026-11-05 10:00:00',
    ]);
    OrderItem::create([
        'order_id' => $todayOrder->id,
        'product_id' => $product->id,
        'printCount' => 2,
    ]);

    $olderOrder = Order::create([
        'customer_id' => $visitor->id,
        'order-UC' => 'marketing-yesterday',
        'status' => 'paid',
        'created_at' => '2026-11-04 10:00:00',
        'updated_at' => '2026-11-04 10:00:00',
    ]);
    OrderItem::create([
        'order_id' => $olderOrder->id,
        'product_id' => $product->id,
        'printCount' => 7,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard', ['period' => 'daily']));

    $response->assertOk()
        ->assertViewHas('insights', fn ($insights) => $insights['visitorCount'] === 1
            && $insights['orderCount'] === 1
            && $insights['unitsBought'] === 1
            && $insights['periodLabel'] === 'Today'
            && $insights['dailyVisitors'] === [])
        ->assertDontSee('Visitors per day');
});

test('three and six month periods show daily visitor counts with zero-activity days', function () {
    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(16, 0));

    $user = User::factory()->create(['outlet' => 'marketing']);
    User::factory()->create(['outlet' => 'Blok M']);
    $firstVisitor = customer::create(['name' => 'First visitor', 'telp' => '0866666666']);
    $secondVisitor = customer::create(['name' => 'Second visitor', 'telp' => '0877777777']);
    $otherOutletVisitor = customer::create(['name' => 'Other outlet visitor', 'telp' => '0888888888']);
    $oldVisitor = customer::create(['name' => 'Old visitor', 'telp' => '0899999999']);

    foreach ([
        [$firstVisitor, 'Blok M', '2026-08-05 10:00:00', 'visitor-august-one'],
        [$firstVisitor, 'Blok M', '2026-08-05 11:00:00', 'visitor-august-two'],
        [$secondVisitor, 'Blok M', '2026-08-05 12:00:00', 'visitor-august-three'],
        [$firstVisitor, 'Blok M', '2026-08-06 10:00:00', 'visitor-august-next-day'],
        [$otherOutletVisitor, 'Kokas', '2026-08-05 10:00:00', 'visitor-other-outlet'],
        [$oldVisitor, 'Blok M', '2026-05-05 10:00:00', 'visitor-may'],
    ] as [$visitor, $outlet, $createdAt, $orderCode]) {
        Order::create([
            'customer_id' => $visitor->id,
            'order-UC' => $orderCode,
            'status' => 'paid',
            'outlet' => $outlet,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    $threeMonthResponse = $this->actingAs($user)->get(route('dashboard', [
        'period' => '3months',
        'outlet' => 'Blok M',
    ]));

    $threeMonthResponse->assertOk()
        ->assertViewHas('insights', fn ($insights) => $insights['periodLabel'] === 'Past 3 months'
            && $insights['visitorCount'] === 2
            && $insights['orderCount'] === 4
            && count($insights['dailyVisitors']) === 93
            && collect($insights['dailyVisitors'])->whereIn('date', ['2026-08-05', '2026-08-06'])->mapWithKeys(
                fn ($day) => [$day['date'] => $day['visitors']]
            )->all() === [
                '2026-08-05' => 2,
                '2026-08-06' => 1,
            ])
        ->assertSee('Visitors per day')
        ->assertSee('Daily unique visitors', false);

    $sixMonthResponse = $this->get(route('dashboard', [
        'period' => '6months',
        'outlet' => 'Blok M',
    ]));

    $sixMonthResponse->assertOk()
        ->assertViewHas('insights', fn ($insights) => $insights['periodLabel'] === 'Past 6 months'
            && $insights['visitorCount'] === 3
            && $insights['orderCount'] === 5
            && count($insights['dailyVisitors']) === 185
            && $insights['dailyVisitors'][0]['date'] === '2026-05-05'
            && $insights['dailyVisitors'][0]['visitors'] === 1
            && $insights['dailyVisitors'][92]['date'] === '2026-08-05'
            && $insights['dailyVisitors'][92]['visitors'] === 2);
});

test('outlet selection filters statistics and only offers eligible user outlets', function () {
    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(16, 0));

    $marketingUser = User::factory()->create(['outlet' => 'marketing']);
    User::factory()->create(['outlet' => 'Blok M']);
    User::factory()->create(['outlet' => 'Kokas']);
    User::factory()->create(['outlet' => 'admin']);
    User::factory()->create(['outlet' => 'superadmin']);
    User::factory()->create(['outlet' => 'Marketing']);
    User::factory()->create(['outlet' => 'All Outlet']);
    User::factory()->create(['outlet' => 'all']);

    $blokMCustomer = customer::create(['name' => 'Blok M visitor', 'telp' => '0844444444']);
    $kokasCustomer = customer::create(['name' => 'Kokas visitor', 'telp' => '0855555555']);
    $product = product::create(['name' => 'Outlet Print', 'type' => 'print', 'price' => '100']);

    $blokMOrder = Order::create([
        'customer_id' => $blokMCustomer->id,
        'order-UC' => 'marketing-outlet-blok-m',
        'status' => 'paid',
        'outlet' => 'Blok M',
        'created_at' => '2026-11-05 10:00:00',
        'updated_at' => '2026-11-05 10:00:00',
    ]);
    OrderItem::create([
        'order_id' => $blokMOrder->id,
        'product_id' => $product->id,
        'printCount' => 3,
    ]);

    $kokasOrder = Order::create([
        'customer_id' => $kokasCustomer->id,
        'order-UC' => 'marketing-outlet-kokas',
        'status' => 'paid',
        'outlet' => 'Kokas',
        'created_at' => '2026-11-05 14:00:00',
        'updated_at' => '2026-11-05 14:00:00',
    ]);
    OrderItem::create([
        'order_id' => $kokasOrder->id,
        'product_id' => $product->id,
        'printCount' => 5,
    ]);

    $response = $this->actingAs($marketingUser)->get(route('dashboard', [
        'period' => 'daily',
        'outlet' => 'Blok M',
    ]));

    $response->assertOk()
        ->assertViewHas('insights', fn ($insights) => $insights['selectedOutlet'] === 'Blok M'
            && $insights['visitorCount'] === 1
            && $insights['orderCount'] === 1
            && $insights['unitsBought'] === 1
            && in_array('Blok M', $insights['outletOptions'], true)
            && in_array('Kokas', $insights['outletOptions'], true)
            && ! in_array('admin', $insights['outletOptions'], true)
            && ! in_array('superadmin', $insights['outletOptions'], true)
            && ! in_array('Marketing', $insights['outletOptions'], true)
            && ! in_array('All Outlet', $insights['outletOptions'], true)
            && ! in_array('all', $insights['outletOptions'], true))
        ->assertSee('value="Blok M" selected', false)
        ->assertDontSee('value="admin"', false)
        ->assertDontSee('value="superadmin"', false)
        ->assertDontSee('value="Marketing"', false)
        ->assertDontSee('value="All Outlet"', false);
});

test('outlet selector rejects outlets outside the user outlet list', function () {
    $marketingUser = User::factory()->create(['outlet' => 'marketing']);
    User::factory()->create(['outlet' => 'Blok M']);
    User::factory()->create(['outlet' => 'admin']);

    $response = $this->actingAs($marketingUser)->get(route('dashboard', ['outlet' => 'admin']));

    $response->assertInvalid(['outlet']);
});

test('marketing users see only marketing insights on the dashboard', function () {
    User::factory()->create(['outlet' => 'Blok M']);
    $user = User::factory()->create(['outlet' => 'marketing']);

    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(16, 0));
    $response = $this->actingAs($user)->get(route('dashboard', ['period' => '3months']));

    $response->assertSee('Visitor & product performance')
        ->assertSee('3 months')
        ->assertSee('Visitors per day')
        ->assertSee('Peak hours')
        ->assertSee('Best-selling products')
        ->assertSee('All outlets')
        ->assertSee('Blok M')
        ->assertDontSee('Periodic sales, orders & visitors')
        ->assertDontSee('Generate code')
        ->assertDontSee('Orders Table')
        ->assertDontSee('Project Manager')
        ->assertDontSee('Notifications')
        ->assertDontSee('Search projects');
});

test('admin and superadmin users can access the dashboard', function (string $outlet) {
    $user = User::factory()->create(['outlet' => $outlet]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertSee('Visitor & product performance');
})->with([
    'admin outlet' => 'admin',
    'superadmin outlet' => 'superadmin',
]);
