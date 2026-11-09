<?php

use App\Models\customer;
use App\Models\CustomerMembership;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

it('redirects guests from the store to login', function () {
    $response = $this->get(route('store'));

    $response->assertRedirect(route('login'));
});

it('redirects the home route to the public index', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect('/index');
});

it('renders the cashier interface for authenticated users', function () {
    $customer = customer::create([
        'name' => 'Store Test Customer',
        'telp' => '08123456789',
    ]);

    $this->actingAs(User::factory()->create());

    $response = $this->get(route('store'));

    $response->assertViewIs('app');
    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'status' => 'pending',
    ]);
});

it('uses a member customer id for a sale without creating another customer', function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });
    Schema::table('orders', function (Blueprint $table): void {
        $table->unsignedInteger('total')->nullable();
        $table->unsignedBigInteger('promo_id')->nullable();
        $table->string('outlet')->nullable();
    });

    $cashier = User::factory()->create();
    $placeholderCustomer = customer::create([
        'name' => 'Placeholder',
        'telp' => '00000000000',
    ]);
    $memberCustomer = customer::create([
        'name' => 'Member Customer',
        'telp' => '08123456789',
    ]);
    $membership = CustomerMembership::create([
        'name' => 'Member Customer',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'customer_id' => $memberCustomer->id,
        'password' => 'password',
    ]);
    $product = Product::create(['name' => 'ID Photo', 'type' => '111', 'price' => 10000]);
    $order = Order::create([
        'customer_id' => $placeholderCustomer->id,
        'order-UC' => 'member-sale',
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'printType' => '111',
        'printCount' => 1,
    ]);

    $this->actingAs($cashier);
    Livewire::test('purchase.⚡create', ['id' => $order->id])
        ->set('name', 'Member Customer')
        ->set('telp', '0812-345-6789')
        ->set('email', 'member@example.com')
        ->call('addcustomer', $order->id);

    $this->assertDatabaseCount('customers', 2);
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'customer_id' => $memberCustomer->id,
        'status' => 'paid',
    ]);
    expect($membership->fresh()->customer_id)->toBe($memberCustomer->id);
});

it('creates a new customer for a sale when the phone does not match a member', function () {
    Schema::table('order_items', function (Blueprint $table): void {
        $table->string('printType')->nullable();
        $table->unsignedInteger('printCount')->nullable();
    });
    Schema::table('orders', function (Blueprint $table): void {
        $table->unsignedInteger('total')->nullable();
        $table->unsignedBigInteger('promo_id')->nullable();
        $table->string('outlet')->nullable();
    });

    $cashier = User::factory()->create();
    $placeholderCustomer = customer::create([
        'name' => 'Placeholder',
        'telp' => '00000000000',
    ]);
    $product = Product::create(['name' => 'ID Photo', 'type' => '111', 'price' => 10000]);
    $order = Order::create([
        'customer_id' => $placeholderCustomer->id,
        'order-UC' => 'guest-sale',
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'printType' => '111',
        'printCount' => 1,
    ]);

    $this->actingAs($cashier);
    Livewire::test('purchase.⚡create', ['id' => $order->id])
        ->set('name', 'Guest Customer')
        ->set('telp', '0898-765-4321')
        ->set('email', 'guest@example.com')
        ->call('addcustomer', $order->id);

    $newCustomer = customer::query()->where('telp', '08987654321')->firstOrFail();
    $this->assertDatabaseCount('customers', 2);
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'customer_id' => $newCustomer->id,
        'status' => 'paid',
    ]);
});

it('redirects users to the store after a successful login', function () {
    $user = User::factory()->create();

    Livewire::test('login.⚡index')
        ->set('name', $user->name)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect(route('store'));

    $this->assertAuthenticatedAs($user);
});
