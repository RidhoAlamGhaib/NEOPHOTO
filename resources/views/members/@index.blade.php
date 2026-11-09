<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Membership | Neophoto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fff8f8, #fff 60%, #fff0f2);
            color: #382f31;
            font-family: "Varela Round", sans-serif;
        }

        .membership-header {
            color: #c65e70;
            letter-spacing: 0.14em;
        }

        .membership-card {
            border: 1px solid #f3dfe2;
            border-radius: 1rem;
            box-shadow: 0 1rem 2.5rem rgb(97 48 57 / 8%);
        }

        .purchase-total {
            color: #c65e70;
            font-size: clamp(3.5rem, 12vw, 6rem);
            line-height: 1;
        }

        .btn-membership {
            background-color: #d97789;
            border-color: #d97789;
            color: #fff;
        }

        .btn-membership:hover,
        .btn-membership:focus {
            background-color: #bf5e70;
            border-color: #bf5e70;
            color: #fff;
        }

        .journey-step {
            display: grid;
            width: 2.5rem;
            height: 2.5rem;
            place-items: center;
            border: 2px solid #ecdcdf;
            border-radius: 50%;
            color: #78696c;
        }

        .journey-step.is-done {
            border-color: #d97789;
            background: #d97789;
            color: #fff;
        }

        .journey-step.is-next {
            border-color: #c65e70;
            color: #c65e70;
            font-weight: 700;
        }

        .journey-reward {
            border-top: 1px solid #f3dfe2;
        }
    </style>
</head>
<body>
    <main class="container py-4 py-md-5" style="max-width: 860px;">
        <header class="d-flex align-items-center justify-content-between gap-3 mb-5">
            <a class="membership-header fw-bold text-decoration-none" href="{{ route('index') }}">NEOPHOTO</a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('index') }}">Home</a>
                <form method="POST" action="{{ route('membership.logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>

        <section class="membership-card card" aria-labelledby="membership-title">
            <div class="card-body p-4 p-md-5 text-center">
                @if ($membership->profile_picture_path)
                    <img
                        class="rounded-circle mb-3"
                        src="{{ route('members.profile-picture') }}"
                        alt="Member profile picture"
                        width="112"
                        height="112"
                        style="object-fit: cover;"
                    >
                @else
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 112px; height: 112px;">
                        Member
                    </div>
                @endif
                <form
                    class="mx-auto mb-4"
                    style="max-width: 420px;"
                    method="POST"
                    action="{{ route('members.profile-picture.update') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <label class="form-label d-block fw-bold" for="profile_picture">Profile picture</label>
                    <div class="input-group">
                        <input
                            class="form-control"
                            id="profile_picture"
                            name="profile_picture"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            required
                        >
                        <button class="btn btn-membership" type="submit">Upload</button>
                    </div>
                    <small class="form-text text-secondary">JPG, PNG, or WebP; up to 20 MB. Stored images are optimized to 2 MB or less.</small>
                    @error('profile_picture')
                        <p class="text-danger small mt-2 mb-0">{{ $message }}</p>
                    @enderror
                    @if (session('status'))
                        <p class="text-success small mt-2 mb-0">{{ session('status') }}</p>
                    @endif
                </form>

                <h1 id="membership-title" class="h2 mb-2">Welcome, {{ $membership->name ?: 'Member' }}</h1>
                <p class="text-secondary mb-4">{{ $membership->email }}</p>
                <p
                    class="purchase-total fw-bold mb-2"
                    aria-label="{{ $membership->paid_purchases_count }} {{ $membership->paid_purchases_count === 1 ? 'successful purchase' : 'successful purchases' }}"
                >
                    {{ number_format($membership->paid_purchases_count) }}
                </p>
                <p class="text-secondary mb-0">
                    {{ $membership->paid_purchases_count === 1 ? 'successful purchase' : 'successful purchases' }}
                </p>
                <p class="small text-secondary mt-2 mb-0">{{ $membership->phone }}</p>
            </div>
        </section>

        <section class="membership-card card my-4" aria-labelledby="journey-title">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h2 id="journey-title" class="h4 mb-0">My Journey</h2>
                    <span class="badge rounded-pill bg-light text-dark">{{ $journey['tierLabel'] }} member</span>
                </div>
                <p class="mb-1">
                    <strong>{{ $journey['count'] }}</strong>
                    {{ $journey['count'] === 1 ? 'paid purchase' : 'paid purchases' }} this cycle
                </p>
                <p class="small text-secondary mb-3">
                    Journey resets {{ $journey['cycleEnd']->format('d M Y') }}
                    ({{ $journey['daysLeft'] }} {{ $journey['daysLeft'] === 1 ? 'day' : 'days' }} left).
                </p>

                <ol class="list-unstyled d-flex flex-wrap gap-2 mb-4" aria-label="Purchase journey progress">
                    @foreach ($journey['steps'] as $step)
                        <li>
                            <span
                                class="journey-step is-{{ $step['status'] }}"
                                aria-label="Purchase {{ $step['number'] }}: {{ $step['status'] }}"
                                title="{{ $step['reward'] ?? 'Purchase '.$step['number'] }}"
                            >{{ $step['number'] }}</span>
                        </li>
                    @endforeach
                </ol>

                @if ($journey['nextReward'])
                    <p class="alert alert-light border">
                        Next reward: <strong>{{ $journey['nextReward']['label'] }}</strong>
                        at purchase {{ $journey['nextReward']['step'] }}
                        ({{ $journey['nextReward']['remaining'] }} to go).
                    </p>
                @else
                    <p class="alert alert-light border">You have unlocked all current journey rewards.</p>
                @endif

                @if ($journey['nextTier'])
                    <p class="mb-4">
                        {{ $journey['nextTier']['remaining'] }} more
                        {{ $journey['nextTier']['remaining'] === 1 ? 'purchase' : 'purchases' }} until
                        {{ $journey['nextTier']['label'] }}.
                    </p>
                @else
                    <p class="mb-4">You have reached the top tier: {{ $journey['discount'] }}% off every transaction.</p>
                @endif

                <h3 class="h6 fw-bold">Rewards</h3>
                <ul class="list-unstyled mb-4">
                    @foreach ($journey['rewards'] as $reward)
                        <li class="journey-reward d-flex align-items-center justify-content-between gap-3 py-2">
                            <span>Purchase {{ $reward['step'] }}: {{ $reward['label'] }}</span>
                            <span class="badge rounded-pill {{ $reward['unlocked'] ? 'bg-success' : 'bg-light text-dark' }}">
                                {{ $reward['unlocked'] ? 'Unlocked' : $reward['remaining'].' to go' }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <h3 class="h6 fw-bold">Membership tiers</h3>
                <div class="row g-3">
                    @foreach ($journey['tiers'] as $tier)
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <strong>{{ $tier['label'] }}{{ $tier['current'] ? ' · Your tier' : '' }}</strong>
                                <p class="small mb-1">Purchases {{ $tier['range'] }}</p>
                                <p class="small text-secondary mb-0">{{ $tier['summary'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="membership-card card" aria-labelledby="purchase-history-title">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 id="purchase-history-title" class="h4 mb-0">Purchase history</h2>
                    <span class="badge rounded-pill bg-light text-dark">{{ $purchases->total() }} total</span>
                </div>

                @forelse ($purchases as $purchase)
                    <div class="d-flex align-items-center justify-content-between gap-3 border-top py-3">
                        <div>
                            <p class="fw-bold mb-1">Purchase #{{ $purchase->id }}</p>
                            <p class="small text-secondary mb-0">{{ $purchase->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="badge rounded-pill bg-success">Paid</span>
                    </div>
                @empty
                    <p class="border-top pt-3 text-secondary mb-0">No completed purchases yet. Your paid purchases will appear here.</p>
                @endforelse

                @if ($purchases->hasPages())
                    <div class="mt-3">{{ $purchases->links() }}</div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
