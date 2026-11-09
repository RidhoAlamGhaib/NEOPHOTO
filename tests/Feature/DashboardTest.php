<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('dashboard access redirects users without an allowed outlet to login', function (?string $outlet) {
    $user = User::factory()->create(['outlet' => $outlet]);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect('/login');
    $this->get(route('orders.export'))->assertRedirect('/login');
})->with([
    'outlet staff' => 'Blok M',
    'user without outlet' => null,
]);
