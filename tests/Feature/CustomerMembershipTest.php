<?php

use App\Models\customer;
use App\Models\CustomerMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('homepage separates member login and registration with modal switching links', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('data-membership-mode="login"', false)
        ->assertSee('data-membership-mode="register"', false)
        ->assertSee('Belum menjadi member?')
        ->assertSee('Sudah menjadi member?')
        ->assertSee('Member login')
        ->assertSee('Become a member');
});

test('members can register from the homepage', function () {
    $response = $this->post(route('membership.register'), [
        'name' => 'Member Customer',
        'email' => 'Member@Example.com',
        'phone' => '08123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $membership = CustomerMembership::query()->firstOrFail();

    $response->assertRedirect(route('index'));
    $this->assertAuthenticatedAs($membership, 'member');
    $this->assertGuest('web');
    $customer = customer::query()->findOrFail($membership->customer_id);
    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Member Customer',
        'email' => 'member@example.com',
        'telp' => '08123456789',
    ]);
    $this->assertDatabaseHas('customer_memberships', [
        'name' => 'Member Customer',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'customer_id' => $customer->id,
    ]);
    expect(Hash::check('password', $membership->password))->toBeTrue();
});

test('member registration links the latest customer with the same phone number', function () {
    $olderCustomer = customer::query()->create([
        'name' => 'Older Customer',
        'telp' => '08123456789',
    ]);
    $latestCustomer = customer::query()->create([
        'name' => 'Latest Customer',
        'telp' => '08123456789',
    ]);

    $this->post(route('membership.register'), [
        'name' => 'Member Customer',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect(route('index'));

    $membership = CustomerMembership::query()->firstOrFail();

    $this->assertDatabaseHas('customer_memberships', [
        'id' => $membership->id,
        'customer_id' => $latestCustomer->id,
    ]);
    expect($membership->customer->is($latestCustomer))->toBeTrue();
    expect($membership->customer_id)->not->toBe($olderCustomer->id);
});

test('members can log in with their email and password', function () {
    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->post(route('membership.login'), [
        'email' => 'MEMBER@EXAMPLE.COM',
        'password' => 'password',
    ])
        ->assertRedirect(route('index'));

    $this->assertAuthenticatedAs($membership, 'member');
    $this->assertGuest('web');
});

test('members cannot log in with an incorrect password', function () {
    CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->post(route('membership.login'), [
        'email' => 'member@example.com',
        'password' => 'incorrect',
    ])
        ->assertSessionHasErrors('email', null, 'membershipLogin');

    $this->assertGuest('member');
    $this->assertGuest('web');
});

test('registration rejects an email already used by another member', function () {
    CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->post(route('membership.register'), [
        'name' => 'Another Member',
        'email' => 'MEMBER@EXAMPLE.COM',
        'phone' => '08987654321',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasErrors('email', null, 'membershipRegistration');

    $this->assertDatabaseCount('customer_memberships', 1);
    $this->assertGuest('member');
});

test('registration requires member account details', function () {
    $this->post(route('membership.register'), [])
        ->assertSessionHasErrors(['name', 'email', 'phone', 'password'], null, 'membershipRegistration');

    $this->assertDatabaseCount('customer_memberships', 0);
});

test('member accounts cannot access staff-only routes', function () {
    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    Auth::guard('member')->login($membership);

    $this->get('/store')
        ->assertRedirect(route('login'));

    $this->assertGuest('web');
});

test('member logout does not log out a staff account', function () {
    $staff = User::factory()->create();
    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->actingAs($staff, 'web');
    Auth::guard('member')->login($membership);

    $this->post(route('membership.logout'))
        ->assertRedirect(route('index'));

    $this->assertGuest('member');
    $this->assertAuthenticatedAs($staff, 'web');
});
