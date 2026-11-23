<?php

use App\Models\customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    Schema::table('products', function (Blueprint $table): void {
        $table->string('status')->default('active');
    });

    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });

    Product::create(['id' => 1, 'name' => 'Standar 2 Lembar', 'type' => '101', 'price' => '50000']);
    Product::create(['id' => 2, 'name' => 'Standar 4 Foto', 'type' => '103', 'price' => '45000']);
    Product::create(['id' => 3, 'name' => 'Standar 6 Foto', 'type' => '105', 'price' => '45000']);
    Product::create(['id' => 4, 'name' => 'High Angle', 'type' => '104', 'price' => '60000']);

    $customer = customer::create(['name' => 'BTS Test', 'telp' => '08123456789']);
    $this->order = Order::create([
        'customer_id' => $customer->id,
        'order-UC' => 'bts-test',
    ]);

    $this->btsProduct = Product::where('type', '107')->firstOrFail();
});

test('BTS package generates one code for the selected product', function () {
    $cart = Livewire::test('cart.⚡cart-create')
        ->assertSee('Paket Khusus BTS')
        ->assertSee('Pilih produk untuk dibuatkan kode')
        ->assertSee('High Angle')
        ->assertSee('Rp 65.000')
        ->set('btsProductId', 3)
        ->call('toggleProduct', $this->btsProduct->id);

    $cart->assertHasNoErrors()
        ->assertDontSee('wire:model.live="btsProductId"', false)
        ->assertSee('Standar 6 Foto ×1');

    $item = OrderItem::where('order_id', $this->order->id)->firstOrFail();

    expect(OrderItem::where('order_id', $this->order->id)->count())->toBe(1);
    expect((int) $item->product_id)->toBe((int) $this->btsProduct->id);
    expect((string) $item->printType)->toBe('105');
    expect((int) $item->printCount)->toBe(1);

    Http::preventStrayRequests();
    Http::fake(fn () => Http::response([['idx' => 999, 'kupon' => 'BTS123']]));

    expect(Livewire::new('purchase.⚡create')->puppet($this->order->id))->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->data()['orders'][0]['type'] === '105');
    $this->assertDatabaseHas('order_items', ['id' => $item->id, 'code' => 'BTS123']);

    $this->actingAs(User::factory()->create(['outlet' => 'Neo Soho']));

    Livewire::test('purchase.⚡create', ['id' => $this->order->id])
        ->assertSee('Paket Khusus BTS (Kode untuk: Standar 6 Foto)')
        ->assertSee('Rp 65.000');
});

test('BTS package preserves the two-print count for Standar 2 Lembar', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('btsProductId', 1)
        ->call('toggleProduct', $this->btsProduct->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $this->order->id,
        'product_id' => $this->btsProduct->id,
        'printType' => '101',
        'printCount' => 2,
    ]);

    expect(OrderItem::where('order_id', $this->order->id)->count())->toBe(1);
});

test('BTS package keeps its selected product when adding another unit', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('btsProductId', 3)
        ->call('toggleProduct', $this->btsProduct->id);

    Livewire::test('cart.⚡cart-create')
        ->set('btsProductId', 2)
        ->call('increaseProduct', $this->btsProduct->id);

    expect(OrderItem::where('order_id', $this->order->id)->where('printType', '105')->count())->toBe(2);
    expect(OrderItem::where('order_id', $this->order->id)->where('printType', '103')->count())->toBe(0);
});

test('BTS package rejects a product outside its selector choices', function () {
    Livewire::test('cart.⚡cart-create')
        ->set('btsProductId', 14)
        ->call('toggleProduct', $this->btsProduct->id)
        ->assertHasErrors('btsProductId');

    expect(OrderItem::where('order_id', $this->order->id)->count())->toBe(0);
});
