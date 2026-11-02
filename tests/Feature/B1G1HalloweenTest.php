<?php

use App\Models\customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });

    product::create(['id' => 1, 'name' => 'Standar 2 Lembar', 'type' => '101', 'price' => '50000']);
    product::create(['id' => 2, 'name' => 'Standar 4 Foto', 'type' => '103', 'price' => '45000']);
    product::create(['id' => 3, 'name' => 'Standar 6 Foto', 'type' => '105', 'price' => '45000']);
    product::create(['id' => 4, 'name' => 'High Angle', 'type' => '104', 'price' => '60000']);
    product::create(['id' => 14, 'name' => 'B1G1 HALLOWEEN', 'type' => '106', 'price' => '60000']);

    $customer = customer::create(['name' => 'B1G1 Test', 'telp' => '08123456789']);

    $this->order = Order::create([
        'customer_id' => $customer->id,
        'order-UC' => 'b1g1-test',
    ]);
});

test('b1g1 adds a paid high angle line plus the free product picked in the dropdown', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('b1g1FreeProductId', 3)
        ->call('toggleProduct', 14);

    $lines = OrderItem::where('order_id', $this->order->id)->where('product_id', 14)->get();

    expect($lines)->toHaveCount(2);
    $this->assertDatabaseHas('order_items', ['order_id' => $this->order->id, 'product_id' => 14, 'printType' => '104', 'printCount' => 1]);
    $this->assertDatabaseHas('order_items', ['order_id' => $this->order->id, 'product_id' => 14, 'printType' => '105', 'printCount' => 1]);
});

test('b1g1 free standar 2 lembar prints two sheets', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('b1g1FreeProductId', 1)
        ->call('toggleProduct', 14);

    $this->assertDatabaseHas('order_items', ['order_id' => $this->order->id, 'product_id' => 14, 'printType' => '101', 'printCount' => 2]);
});

test('b1g1 rejects a free product outside standar 1-3 and falls back to standar 2 lembar', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('b1g1FreeProductId', 4)
        ->call('toggleProduct', 14);

    $this->assertDatabaseHas('order_items', ['order_id' => $this->order->id, 'product_id' => 14, 'printType' => '101', 'printCount' => 2]);
    $this->assertDatabaseMissing('order_items', ['order_id' => $this->order->id, 'product_id' => 14, 'printType' => '104', 'printCount' => 2]);
});

test('b1g1 decrease removes the whole pair', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('b1g1FreeProductId', 2)
        ->call('toggleProduct', 14)
        ->call('increaseProduct', 14)
        ->call('decreaseProduct', 14);

    expect(OrderItem::where('order_id', $this->order->id)->where('product_id', 14)->count())->toBe(2);
});

test('checkout charges 60k once per b1g1 unit and labels the free product', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('b1g1FreeProductId', 2)
        ->call('toggleProduct', 14);

    $this->actingAs(User::factory()->create(['outlet' => 'Neo Soho']));

    $checkout = Livewire::test('purchase.⚡create', ['id' => $this->order->id]);

    expect((float) $checkout->instance()->total)->toBe(60000.0);
    $checkout->assertSee('(Gratis: Standar 4 Foto)')
        ->assertSee('GRATIS');
});
