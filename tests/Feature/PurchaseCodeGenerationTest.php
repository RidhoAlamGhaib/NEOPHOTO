<?php

use App\Models\customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

test('purchase generates codes for every selected product', function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });

    $customer = customer::create(['name' => 'Purchase Test', 'telp' => '08123456789']);
    $order = Order::create([
        'customer_id' => $customer->id,
        'order-UC' => 'purchase-test',
    ]);
    $firstProduct = Product::create(['name' => 'First print', 'type' => '101', 'price' => '0']);
    $secondProduct = Product::create(['name' => 'Second print', 'type' => '103', 'price' => '0']);
    $firstItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $firstProduct->id,
        'printType' => '101',
        'printCount' => 1,
    ]);
    $secondItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $secondProduct->id,
        'printType' => '103',
        'printCount' => 1,
    ]);

    Http::preventStrayRequests();
    Http::fake(function (Request $request) use ($firstItem) {
        $itemId = $request->data()['orders'][0]['OrderId'];
        $code = $itemId === $firstItem->id ? 'ABC123' : 'DEF456';

        return Http::response([['idx' => 999, 'kupon' => $code]]);
    });

    $generated = Livewire::new('purchase.⚡create')->puppet($order->id);

    expect($generated)->toBeTrue();
    $this->assertDatabaseHas('order_items', ['id' => $firstItem->id, 'code' => 'ABC123']);
    $this->assertDatabaseHas('order_items', ['id' => $secondItem->id, 'code' => 'DEF456']);
    expect(Http::recorded())->toHaveCount(2);
    Http::assertSent(fn (Request $request): bool => count($request->data()['orders']) === 1);
});

test('purchase assigns ID and Visa codes per item in a mixed order', function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });

    $customer = customer::create(['name' => 'Mixed Purchase Test', 'telp' => '08123456789']);
    $order = Order::create([
        'customer_id' => $customer->id,
        'order-UC' => 'mixed-purchase-test',
    ]);
    $idVisaProduct = Product::create(['name' => 'ID Visa', 'type' => '111', 'price' => '0']);
    $printProduct = Product::create(['name' => 'Standard print', 'type' => '101', 'price' => '0']);
    $idVisaItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $idVisaProduct->id,
        'printType' => '111',
        'printCount' => 1,
    ]);
    $printItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $printProduct->id,
        'printType' => '101',
        'printCount' => 1,
    ]);

    Http::preventStrayRequests();
    Http::fake(fn () => Http::response([['idx' => 999, 'kupon' => 'ABC123']]));

    $generated = Livewire::new('purchase.⚡create')->puppet($order->id);

    expect($generated)->toBeTrue();
    $this->assertDatabaseHas('order_items', ['id' => $idVisaItem->id, 'code' => '000000000']);
    $this->assertDatabaseHas('order_items', ['id' => $printItem->id, 'code' => 'ABC123']);
    Http::assertSent(fn (Request $request): bool => count($request->data()['orders']) === 1
        && $request->data()['orders'][0]['OrderId'] === $printItem->id);
});

test('purchase does not succeed when a selected product has no generated code', function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });

    $customer = customer::create(['name' => 'Incomplete Purchase Test', 'telp' => '08123456789']);
    $order = Order::create([
        'customer_id' => $customer->id,
        'order-UC' => 'incomplete-purchase-test',
    ]);
    $firstProduct = Product::create(['name' => 'First print', 'type' => '101', 'price' => '0']);
    $secondProduct = Product::create(['name' => 'Second print', 'type' => '103', 'price' => '0']);
    $firstItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $firstProduct->id,
        'printType' => '101',
        'printCount' => 1,
    ]);
    $secondItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $secondProduct->id,
        'printType' => '103',
        'printCount' => 1,
    ]);

    Http::preventStrayRequests();
    Http::fake(function (Request $request) use ($firstItem) {
        $itemId = $request->data()['orders'][0]['OrderId'];
        $results = $itemId === $firstItem->id
            ? [['idx' => 999, 'kupon' => 'ABC123']]
            : [];

        return Http::response($results);
    });

    $generated = Livewire::new('purchase.⚡create')->puppet($order->id);

    expect($generated)->toBeFalse();
    $this->assertDatabaseHas('order_items', ['id' => $firstItem->id, 'code' => null]);
    $this->assertDatabaseHas('order_items', ['id' => $secondItem->id, 'code' => null]);
});
