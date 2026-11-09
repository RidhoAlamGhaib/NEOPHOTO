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

    $this->get(route('members.profile.edit'))
        ->assertRedirect(route('login'));

    $this->patch(route('members.profile.details.update'))
        ->assertRedirect(route('login'));

    $this->post(route('members.profile.update'))
        ->assertRedirect(route('login'));

    $this->get('/members/profile-picture')
        ->assertNotFound();
});

test('staff accounts cannot view or update member profile pictures', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)
        ->get(route('members.profile.edit'))
        ->assertRedirect(route('login'));

    $this->actingAs($staff)
        ->patch(route('members.profile.details.update'))
        ->assertRedirect(route('login'));

    $this->actingAs($staff)
        ->post(route('members.profile.update'))
        ->assertRedirect(route('login'));
});

test('members can upload compressed private profile pictures and replace their previous picture', function () {
    Storage::fake('local');
    Storage::disk('local')->put('members/profile-pictures/default.jpg', 'default image');

    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);
    $previousPath = "members/profile-pictures/{$membership->getKey()}/old.webp";
    $membership->update(['profile_picture_path' => $previousPath]);
    Storage::disk('local')->put($previousPath, 'previous image');

    $this->actingAs($membership, 'member')
        ->get(route('members.profile.edit'))
        ->assertSee('Edit profile')
        ->assertSee('name="profile_picture"', false)
        ->assertSee('base64,', false)
        ->assertSee('Membership details')
        ->assertSee('data-edit-field="name"', false)
        ->assertSee('data-edit-field="birthday"', false)
        ->assertSee('data-edit-field="job"', false)
        ->assertDontSee('data-edit-field="email"', false)
        ->assertDontSee('data-edit-field="phone"', false);

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
        ->post(route('members.profile.update'), [
            'profile_picture' => UploadedFile::fake()
                ->createWithContent('new-profile.png', $sourceImageContents),
        ]);

    $response->assertRedirect(route('members.profile.edit'));
    $membership->refresh();
    $newPath = $membership->profile_picture_path;

    Storage::disk('local')->assertExists($newPath);
    Storage::disk('local')->assertMissing($previousPath);
    expect($newPath)->toEndWith('.webp');
    expect(Storage::disk('local')->size($newPath))->toBeLessThanOrEqual(2 * 1024 * 1024);
    expect(Storage::disk('local')->mimeType($newPath))->toBe('image/webp');

    $this->actingAs($membership, 'member')
        ->get(route('members.profile.edit'))
        ->assertSee('data:image/webp;base64,', false)
        ->assertSee('Save new picture');

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertSee(route('members.profile.edit'))
        ->assertSee('Edit profile')
        ->assertSee('class="member-avatar"', false)
        ->assertSee('data:image/webp;base64,', false)
        ->assertDontSee('Save new picture');
});

test('members without a profile picture see the default picture embedded in their profile page', function () {
    Storage::fake('local');
    $defaultPicture = imagecreatetruecolor(1, 1);
    ob_start();
    imagejpeg($defaultPicture);
    $defaultPictureContents = ob_get_clean();
    imagedestroy($defaultPicture);
    Storage::disk('local')->put(
        'members/profile-pictures/default.jpg',
        $defaultPictureContents,
    );

    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->actingAs($membership, 'member')
        ->get(route('members.profile.edit'))
        ->assertSee('data:image/jpeg;base64,', false);

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertSee('class="member-avatar"', false)
        ->assertSee('data:image/jpeg;base64,', false);
});

test('members can update profile fields and link a changed phone to the latest customer', function () {
    $olderCustomer = customer::create([
        'name' => 'Older Customer',
        'email' => 'older@example.com',
        'telp' => '08123456789',
    ]);
    customer::create([
        'name' => 'Middle Customer',
        'email' => 'middle@example.com',
        'telp' => '08987654321',
    ]);
    $latestCustomer = customer::create([
        'name' => 'Latest Customer',
        'email' => 'latest@example.com',
        'telp' => '08987654321',
    ]);
    $membership = CustomerMembership::create([
        'name' => 'Old Name',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'customer_id' => $olderCustomer->id,
        'password' => 'password',
    ]);

    $response = $this->actingAs($membership, 'member')
        ->patch(route('members.profile.details.update'), [
            'name' => 'Updated Member',
            'email' => 'member@example.com',
            'phone' => '089-876-54321',
            'birthday' => '1990-04-15',
            'job' => 'Photographer',
        ]);

    $response->assertRedirect(route('members.profile.edit'));
    $response->assertSessionHas('profile_status', 'Profile details updated.');
    $this->assertDatabaseHas('customer_memberships', [
        'id' => $membership->id,
        'name' => 'Updated Member',
        'email' => 'member@example.com',
        'phone' => '08987654321',
        'customer_id' => $latestCustomer->id,
        'job' => 'Photographer',
    ]);
    expect($membership->fresh()->birthday->toDateString())->toBe('1990-04-15');

    $this->actingAs($membership->fresh(), 'member')
        ->get(route('members.profile.edit'))
        ->assertSee('Updated Member')
        ->assertSee('Photographer');
});

test('member profile update creates and links a customer when a changed phone has no match', function () {
    $membership = CustomerMembership::create([
        'name' => 'Existing Member',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $response = $this->actingAs($membership, 'member')
        ->patch(route('members.profile.details.update'), [
            'name' => 'Updated Member',
            'email' => 'updated@example.com',
            'phone' => '08987654321',
            'birthday' => '',
            'job' => '',
        ]);

    $response->assertRedirect(route('members.profile.edit'));
    $createdCustomer = customer::query()->where('telp', '08987654321')->firstOrFail();
    $this->assertDatabaseHas('customer_memberships', [
        'id' => $membership->id,
        'customer_id' => $createdCustomer->id,
        'birthday' => null,
        'job' => null,
    ]);
    expect($createdCustomer->name)->toBe('Updated Member');
    expect($createdCustomer->email)->toBe('updated@example.com');
});

test('member profile update rejects duplicate contact details and a future birthday', function () {
    $membership = CustomerMembership::create([
        'name' => 'Existing Member',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'birthday' => '1990-04-15',
        'job' => 'Photographer',
        'password' => 'password',
    ]);
    CustomerMembership::create([
        'name' => 'Another Member',
        'email' => 'another@example.com',
        'phone' => '08987654321',
        'password' => 'password',
    ]);

    $response = $this->actingAs($membership, 'member')
        ->patch(route('members.profile.details.update'), [
            'name' => 'Changed Name',
            'email' => 'another@example.com',
            'phone' => '08987654321',
            'birthday' => '2999-01-01',
            'job' => 'Changed Job',
        ]);

    $response->assertSessionHasErrors(['email', 'phone', 'birthday']);
    $this->assertDatabaseHas('customer_memberships', [
        'id' => $membership->id,
        'name' => 'Existing Member',
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'job' => 'Photographer',
    ]);
    expect($membership->fresh()->birthday->toDateString())->toBe('1990-04-15');
});

test('members cannot save invalid or oversized profile pictures', function () {
    Storage::fake('local');

    $membership = CustomerMembership::create([
        'email' => 'member@example.com',
        'phone' => '08123456789',
        'password' => 'password',
    ]);

    $this->actingAs($membership, 'member')
        ->post(route('members.profile.update'), [
            'profile_picture' => UploadedFile::fake()->create('profile.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('profile_picture');

    $this->actingAs($membership, 'member')
        ->post(route('members.profile.update'), [
            'profile_picture' => UploadedFile::fake()
                ->image('oversized.png', 120, 120)
                ->size(20481),
        ])
        ->assertSessionHasErrors('profile_picture');

    $this->actingAs($membership, 'member')
        ->post(route('members.profile.update'), [
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
        'customer_id' => $customer->id,
        'password' => 'password',
        'created_at' => '2026-11-06 11:00:00',
    ]);

    $this->actingAs($membership, 'member')
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('aria-label="2 successful purchases"', false)
        ->assertSee('1 purchase')
        ->assertSee('My Journey')
        ->assertSee("Purchase #{$olderPurchase->id}")
        ->assertSee("Purchase #{$cyclePurchase->id}")
        ->assertDontSee("Purchase #{$samePhonePurchase->id}")
        ->assertDontSee("Purchase #{$pendingPurchase->id}")
        ->assertDontSee("Purchase #{$otherPurchase->id}");
});

test('members without a linked customer do not inherit purchases by phone number', function () {
    $customer = customer::create([
        'name' => 'Same Phone Customer',
        'email' => 'same-phone@example.com',
        'telp' => '08123456789',
    ]);
    $purchase = Order::create([
        'customer_id' => $customer->id,
        'status' => 'paid',
        'order-UC' => 'unlinked-member-phone',
    ]);
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
        ->assertDontSee("Purchase #{$purchase->id}");
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
