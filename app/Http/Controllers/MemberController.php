<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\CustomerMembership;
use App\Models\Order;
use App\Services\MemberProfilePictureService;
use App\Services\MembershipJourney;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function editProfile(Request $request): View
    {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        $profilePicture = $this->profilePictureDataUri($membership);

        return view('members.edit-profile', compact('membership', 'profilePicture'));
    }

    private function profilePictureDataUri(CustomerMembership $membership): string
    {
        $path = $membership->profile_picture_path ?? 'members/profile-pictures/default.jpg';
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);
        $mimeType = $disk->mimeType($path);
        abort_unless(is_string($mimeType), 404);

        $profilePicture = sprintf(
            'data:%s;base64,%s',
            $mimeType,
            base64_encode($disk->get($path)),
        );

        return $profilePicture;
    }

    public function index(Request $request, MembershipJourney $journeyService): View
    {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        $paidPurchases = Order::query()
            ->where('status', 'paid')
            ->where('customer_id', $membership->customer_id ?? 0);
        $membership->setAttribute('paid_purchases_count', (clone $paidPurchases)->count());

        $purchases = $paidPurchases
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);
        $journey = $journeyService->forCustomerId($membership->customer_id, $membership->created_at);
        $profilePicture = $this->profilePictureDataUri($membership);

        return view('members.index', compact('journey', 'membership', 'profilePicture', 'purchases'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        if (is_string($request->input('email'))) {
            $request->merge([
                'email' => Str::lower(trim($request->input('email'))),
            ]);
        }

        if (is_string($request->input('phone'))) {
            $request->merge([
                'phone' => preg_replace('/[^0-9+]/', '', $request->input('phone')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customer_memberships', 'email')->ignore($membership),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^\\+?[0-9]{7,20}$/',
                Rule::unique('customer_memberships', 'phone')->ignore($membership),
            ],
            'birthday' => ['nullable', 'date', 'before_or_equal:today'],
            'job' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($membership, $validated): void {
            if ($membership->phone !== $validated['phone']) {
                $customerId = customer::query()
                    ->where('telp', $validated['phone'])
                    ->orderByDesc('id')
                    ->value('id');

                if ($customerId === null) {
                    $customerId = customer::query()->create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'telp' => $validated['phone'],
                    ])->getKey();
                }

                $membership->customer_id = $customerId;
            }

            $membership->fill($validated);
            $membership->saveOrFail();
        });

        return redirect()
            ->route('members.profile.edit')
            ->with('profile_status', 'Profile details updated.');
    }

    public function updateProfilePicture(
        Request $request,
        MemberProfilePictureService $profilePictureService
    ): RedirectResponse {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        $validated = $request->validate([
            'profile_picture' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'dimensions:max_width=4096,max_height=4096',
                'max:20480',
            ],
        ]);

        $profilePictureService->replace($membership, $validated['profile_picture']);

        return redirect()
            ->route('members.profile.edit')
            ->with('status', 'Profile picture updated.');
    }
}
