<?php

namespace App\Http\Controllers;

use App\Models\CustomerMembership;
use App\Models\Order;
use App\Services\MemberProfilePictureService;
use App\Services\MembershipJourney;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    public function index(Request $request, MembershipJourney $journeyService): View
    {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        $paidPurchases = Order::query()
            ->where('status', 'paid')
            ->whereHas('customer', fn (Builder $query) => $query->where('telp', $membership->phone));
        $membership->setAttribute('paid_purchases_count', (clone $paidPurchases)->count());

        $purchases = $paidPurchases
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);
        $journey = $journeyService->forPhone($membership->phone, $membership->created_at);

        return view('members.index', compact('journey', 'membership', 'purchases'));
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
            ->route('members.index')
            ->with('status', 'Profile picture updated.');
    }

    public function profilePicture(Request $request): StreamedResponse
    {
        $membership = $request->user('member');
        abort_unless($membership instanceof CustomerMembership, 403);

        $path = $membership->profile_picture_path;
        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
