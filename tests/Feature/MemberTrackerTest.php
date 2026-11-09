<?php

use App\Models\customer;
use App\Models\CustomerMembership;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

test('guests are redirected from member account pages', function () {
    $this->get(route('members.index'))
        ->assertRedirect(route('login'));

    $this->get(route('members.profile-picture'))
        ->assertRedirect(route('login'));

    $this->post(route('members.profile-picture.update'))
        ->assertRedirect(route('login'));
});

test('staff accounts cannot view or update member profile pictures', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)
        ->get(route('members.profile-picture'))
        ->assertRedirect(route('login'));

    $this->actingAs($staff)
        ->post(route('members.profile-picture.update'))
        ->assertRedirect(route('login'));
});

test('members can upload compressed private profile pictures and replace their previous picture', function () {
    Storage::fake('local');

    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);
    $previousPath = "members/profile-pictures/{$membership->getKey()}/old.webp";
    $membership->update(['profile_picture_path' => $previousPath]);
    Storage::disk('local')->put($previousPath, 'previous image');

    mt_srand(1234);
    $sourceImage = imagecreatetruecolor(1800, 1800);
    for ($y = 0; $y < 1800; $y++) {
        for ($x = 0; $x < 1800; $x++) {
            imagesetpixel($sourceImage, $x, $y, mt_rand(0, 0xFFFFFF));
        }
    }
    ob_start();
    imagepng($sourceImage);
    $sourceImageContents = ob_get_clean();
    imagedestroy($sourceImage);
    mt_srand();

    expect(strlen($sourceImageContents))->toBeGreaterThan(2 * 1024 * 1024);

    $response = $this->actingAs($membership, 'member')
        ->post(route('members.profile-picture.update'), [
            'profile_picture' => UploadedFile::fake()
                ->createWithContent('new-profile.png', $sourceImageContents),
        ]);

    $response->assertRedirect(route('members.index'));
    $membership->refresh();
    $newPath = $membership->profile_picture_path;

    Storage::disk('local')->assertExists($newPath);
    Storage::disk('local')->assertMissing($previousPath);
    expect($newPath)->toEndWith('.webp');
    expect(Storage::disk('local')->size($newPath))->toBeLessThanOrEqual(2 * 1024 * 1024);
    expect(Storage::disk('local')->mimeType($newPath))->toBe('image/webp');

    $this->actingAs($membership, 'member')
        ->get(route('members.profile-picture'))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/webp')
        ->assertHeader('Cache-Control', 'max-age=3600, private')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertSee(route('members.profile-picture'), false)
        ->assertSee('Upload');
});

test('members cannot save invalid or oversized profile pictures', function () {
    Storage::fake('local');

    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->actingAs($membership, 'member')
        ->post(route('members.profile-picture.update'), [
            'profile_picture' => UploadedFile::fake()->create('profile.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('profile_picture');

    $this->actingAs($membership, 'member')
        ->post(route('members.profile-picture.update'), [
            'profile_picture' => UploadedFile::fake()
                ->image('oversized.png', 120, 120)
                ->size(20481),
        ])
        ->assertSessionHasErrors('profile_picture');

    $this->actingAs($membership, 'member')
        ->post(route('members.profile-picture.update'), [
            'profile_picture' => UploadedFile::fake()->image('too-wide.png', 4097, 100),
        ])
        ->assertSessionHasErrors('profile_picture');

    expect($membership->fresh()->profile_picture_path)->toBeNull();
    expect(Storage::disk('local')->allFiles())->toBe([]);
});

test('member registration stores a normalized phone number without a customer id', function () {
    $this->post(route('membership.register'), [
        'name' => 'Member Customer',
        'email' => 'member@example.com',
        'phone' => '0812-345-6789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('index'));

    $membership = CustomerMembership::query()->firstOrFail();

    expect($membership->phone)->toBe('08123456789');
    expect($membership->name)->toBe('Member Customer');
    expect(Schema::hasColumn('customer_memberships', 'customer_id'))->toBeFalse();

    $this->get(route('members.index'))
        ->assertOk()
        ->assertSee('Welcome, Member Customer')
        ->assertSee('member@example.com')
        ->assertSee('08123456789');
});

test('member registration rejects a duplicate normalized phone number', function () {
    CustomerMembership::create([
        'email' => 'existing@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->post(route('membership.register'), [
        'name' => 'Another Member',
        'email' => 'another@example.com',
        'phone' => '0812-345-6789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasErrorsIn('membershipRegistration', ['phone']);

    $this->assertDatabaseCount('customer_memberships', 1);
});

test('members see purchase and journey history matched by phone before registration', function () {
    $this->travelTo('2026-11-06 12:00:00');

    $customer = customer::create([
        'name' => 'Existing Customer',
        'email' => 'existing@example.com',
        'telp' => '08123456789',
        'created_at' => '2025-10-01 09:00:00',
        'updated_at' => '2025-10-01 09:00:00',
    ]);
    $samePhoneCustomer = customer::create([
        'name' => 'Same Phone Customer',
        'email' => 'same-phone@example.com',
        'telp' => '08123456789',
        'created_at' => '2025-10-02 09:00:00',
        'updated_at' => '2025-10-02 09:00:00',
    ]);
    $otherCustomer = customer::create([
        'name' => 'Other Customer',
        'email' => 'other@example.com',
        'telp' => '08987654321',
    ]);

    $olderPurchase = Order::create([
        'customer_id' => $customer->id,
        'status' => 'paid',
        'order-UC' => 'retroactive-older',
        'created_at' => '2026-09-20 10:00:00',
    ]);
    $cyclePurchase = Order::create([
        'customer_id' => $customer->id,
        'status' => 'paid',
        'order-UC' => 'retroactive-cycle-1',
        'created_at' => '2026-10-15 10:00:00',
    ]);
    $samePhonePurchase = Order::create([
        'customer_id' => $samePhoneCustomer->id,
        'status' => 'paid',
        'order-UC' => 'retroactive-cycle-2',
        'created_at' => '2026-11-05 10:00:00',
    ]);
    $pendingPurchase = Order::create([
        'customer_id' => $customer->id,
        'status' => 'pending',
        'order-UC' => 'retroactive-pending',
        'created_at' => '2026-11-04 10:00:00',
    ]);
    $otherPurchase = Order::create([
        'customer_id' => $otherCustomer->id,
        'status' => 'paid',
        'order-UC' => 'unmatched-phone',
        'created_at' => '2026-11-03 10:00:00',
    ]);
    $membership = CustomerMembership::create([
        'name' => 'New Member',
        'email' => 'new-member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
        'created_at' => '2026-11-06 11:00:00',
    ]);

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('aria-label="3 successful purchases"', false)
        ->assertSee('<strong>2</strong>', false)
        ->assertSee('paid purchases this cycle')
        ->assertSee('My Journey')
        ->assertSee("Purchase #{$olderPurchase->id}")
        ->assertSee("Purchase #{$cyclePurchase->id}")
        ->assertSee("Purchase #{$samePhonePurchase->id}")
        ->assertDontSee("Purchase #{$pendingPurchase->id}")
        ->assertDontSee("Purchase #{$otherPurchase->id}");
});

test('members without matching paid purchases see empty trackers', function () {
    $membership = CustomerMembership::create([
        'name' => 'New Member',
        'email' => 'new-member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('aria-label="0 successful purchases"', false)
        ->assertSee('My Journey')
        ->assertSee('No completed purchases yet.')
        ->assertSee('<strong>0</strong>', false)
        ->assertSee('paid purchases this cycle');
});

test('the database prevents duplicate member phone numbers', function () {
    CustomerMembership::create([
        'email' => 'existing@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    expect(fn () => CustomerMembership::create([
        'email' => 'another@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]))->toThrow(QueryException::class);

    $this->assertDatabaseCount('customer_memberships', 1);
});
