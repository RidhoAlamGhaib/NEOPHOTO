<?php

use App\Models\customer;
use App\Models\User;
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

it('redirects users to the store after a successful login', function () {
    $user = User::factory()->create();

    Livewire::test('login.⚡index')
        ->set('name', $user->name)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect(route('store'));

    $this->assertAuthenticatedAs($user);
});
