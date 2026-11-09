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

        /* ── My Journey ─────────────────────────────────────────── */
        .tier-silver  { --tier-color: #b3b3b3; --tier-ink: #333333; }
        .tier-gold    { --tier-color: #f5c800; --tier-ink: #4a3a00; }
        .tier-diamond { --tier-color: #2cb1f0; --tier-ink: #06335a; }

        .journey-tier-badge { background: var(--tier-color); color: var(--tier-ink); font-weight: 700; padding: .5em .9em; }

        .journey-stat {
            height: 100%;
            padding: 1rem 1.1rem;
            border: 1px solid #f3dfe2;
            border-radius: .75rem;
            background: #fffafb;
        }
        .journey-stat-label { font-size: .72rem; letter-spacing: .1em; text-transform: uppercase; color: #a08a8e; }

        .journey-path { --node: clamp(34px, 9.5vw, 54px); padding: 10px 6px 4px; }
        .journey-row { display: flex; align-items: center; list-style: none; margin: 0; padding: 0; }
        .journey-row.is-reverse { flex-direction: row-reverse; }
        .journey-step { display: flex; align-items: center; flex: 1 1 0; min-width: 0; }
        .journey-step:last-child { flex: 0 0 auto; }
        .journey-row.is-reverse .journey-step { flex-direction: row-reverse; }

        .journey-link { position: relative; flex: 1 1 auto; height: 3px; margin: 0 .45rem; color: #ecdcdf; background: currentColor; border-radius: 2px; }
        .journey-link.is-done, .journey-drop.is-done { color: #d97789; }
        .journey-link::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 7px solid currentColor;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }
        .journey-row.is-reverse .journey-link::after {
            right: auto;
            left: -3px;
            border-left: 0;
            border-right: 7px solid currentColor;
        }

        .journey-drop {
            position: relative;
            width: 3px;
            height: 30px;
            margin: 2px calc(var(--node) / 2 - 1.5px) 2px auto;
            color: #ecdcdf;
            background: currentColor;
        }
        .journey-drop::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -3px;
            transform: translateX(-50%);
            border-top: 7px solid currentColor;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }

        .journey-node { position: relative; flex: none; width: var(--node); height: var(--node); }
        .journey-face {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--tier-color);
            color: var(--tier-ink);
            font-size: calc(var(--node) * .38);
            font-weight: 700;
        }
        .journey-node.tier-diamond .journey-face {
            border-radius: 0;
            padding-bottom: 12%;
            background: linear-gradient(160deg, #8fe3ff 0%, #2cb1f0 55%, #0c7fd6 100%);
            clip-path: polygon(20% 0, 80% 0, 100% 38%, 50% 100%, 0 38%);
        }
        .journey-node.is-upcoming .journey-face { opacity: .4; }
        .journey-node.is-next .journey-face { opacity: .8; }
        .journey-node.is-next::before,
        .journey-node.is-current::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px dashed #d97789;
        }
        .journey-node.is-current::before { border-style: solid; box-shadow: 0 0 0 4px rgb(217 119 137 / 18%); }

        .journey-gift {
            position: absolute;
            top: -7px;
            right: -7px;
            z-index: 1;
            display: grid;
            place-items: center;
            width: 20px;
            height: 20px;
            border: 1.5px solid #d97789;
            border-radius: 50%;
            background: #fff;
            font-size: 11px;
            line-height: 1;
        }
        .journey-node.is-done .journey-gift { background: #d97789; }

        .journey-chip {
            display: grid;
            place-items: center;
            flex: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--tier-color);
            color: var(--tier-ink);
            font-size: .8rem;
            font-weight: 700;
        }

        .journey-tier { height: 100%; padding: 1rem; border: 1px solid #f3dfe2; border-radius: .75rem; }
        .journey-tier.is-current { border-color: var(--tier-color); box-shadow: 0 0 0 2px var(--tier-color); }

        /* ── Member summary ─────────────────────────────────────── */
        .member-summary { display: flex; align-items: center; flex-wrap: wrap; gap: 1.25rem; }
        .member-identity { flex: 1 1 11rem; min-width: 0; }
        .member-identity h1 { overflow-wrap: anywhere; }

        .avatar-upload { position: relative; display: block; flex: none; width: 104px; height: 104px; margin: 0; border-radius: 50%; cursor: pointer; }
        .avatar-upload img,
        .avatar-placeholder { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .avatar-upload [hidden] { display: none !important; }
        .avatar-placeholder { display: grid; place-items: center; background: #fbe9ec; color: #c65e70; font-size: 2.25rem; font-weight: 700; }
        .avatar-badge {
            position: absolute;
            right: 0;
            bottom: 0;
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border: 3px solid #fff;
            border-radius: 50%;
            background: #d97789;
            color: #fff;
            transition: background-color .15s;
        }
        .avatar-upload:hover .avatar-badge { background: #bf5e70; }
        .avatar-upload:focus-within { outline: 3px solid rgb(217 119 137 / 40%); outline-offset: 3px; }

        .member-count { margin-left: auto; text-align: right; }
        .member-count-number { display: block; color: #c65e70; font-size: clamp(2.5rem, 8vw, 3.75rem); font-weight: 700; line-height: 1; }
        .member-count-label { display: block; margin-top: .35rem; font-size: .85rem; color: #6c757d; }

        .upload-actions { display: none; align-items: center; gap: .75rem; margin-top: 1.25rem; }
        .upload-actions.is-visible { display: flex; }

        @media (max-width: 575.98px) {
            .member-count { display: flex; align-items: baseline; gap: .75rem; flex-basis: 100%; margin-left: 0; padding-top: 1rem; border-top: 1px solid #f3dfe2; text-align: left; }
            .member-count-label { margin-top: 0; }
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

        <section class="membership-card card mb-4" aria-labelledby="membership-title">
            <div class="card-body p-4 p-md-5">
                <form
                    method="POST"
                    action="{{ route('members.profile-picture.update') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="member-summary">
                        <label class="avatar-upload" for="profile_picture" title="Change profile picture">
                            <img
                                data-avatar-img
                                src="{{ $membership->profile_picture_path ? route('members.profile-picture') : '' }}"
                                alt="{{ $membership->name ?: 'Member' }} profile picture"
                                width="104"
                                height="104"
                                @unless ($membership->profile_picture_path) hidden @endunless
                            >
                            <span class="avatar-placeholder" data-avatar-placeholder @if ($membership->profile_picture_path) hidden @endif>
                                {{ mb_strtoupper(mb_substr($membership->name ?: 'Member', 0, 1)) }}
                            </span>
                            <span class="avatar-badge" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </span>
                            <span class="visually-hidden">Change profile picture</span>
                        </label>
                        <input
                            class="visually-hidden"
                            id="profile_picture"
                            name="profile_picture"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            required
                        >

                        <div class="member-identity">
                            <h1 id="membership-title" class="h3 mb-1">{{ $membership->name ?: 'Member' }}</h1>
                            @if ($membership->phone)
                                <p class="text-secondary mb-0">{{ $membership->phone }}</p>
                            @endif
                            @if ($membership->email)
                                <p class="small text-secondary mb-0 text-break">{{ $membership->email }}</p>
                            @endif
                        </div>

                        <div
                            class="member-count"
                            aria-label="{{ $membership->paid_purchases_count }} {{ $membership->paid_purchases_count === 1 ? 'successful purchase' : 'successful purchases' }}"
                        >
                            <span class="member-count-number">{{ number_format($membership->paid_purchases_count) }}</span>
                            <span class="member-count-label">
                                {{ $membership->paid_purchases_count === 1 ? 'successful purchase' : 'successful purchases' }}
                            </span>
                        </div>
                    </div>

                    <div class="upload-actions" data-upload-actions>
                        <button class="btn btn-membership btn-sm" type="submit">Save new picture</button>
                        <span class="small text-secondary">JPG, PNG, or WebP; up to 20 MB.</span>
                    </div>
                    @error('profile_picture')
                        <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                    @enderror
                    @if (session('status'))
                        <p class="text-success small mt-3 mb-0">{{ session('status') }}</p>
                    @endif
                </form>
            </div>
        </section>

        @php
            $journeyRows = [array_slice($journey['steps'], 0, 5), array_slice($journey['steps'], 5)];
            $plural = fn (int $n): string => $n === 1 ? '1 purchase' : $n.' purchases';
        @endphp

        <section class="membership-card card mb-4 journey-card" aria-labelledby="journey-title">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                    <h2 id="journey-title" class="h4 mb-0">My Journey</h2>
                    <span class="badge rounded-pill journey-tier-badge tier-{{ $journey['tier'] }}">{{ $journey['tierLabel'] }} member</span>
                </div>
                <p class="text-secondary mb-4">Every paid purchase moves you one step further and unlocks rewards and member discounts.</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="journey-stat">
                            <span class="journey-stat-label">This cycle</span>
                            <p class="h4 fw-bold mb-1">{{ $plural($journey['count']) }}</p>
                            <p class="small text-secondary mb-0">{{ $journey['tierLabel'] }}: {{ $journey['perk'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="journey-stat">
                            <span class="journey-stat-label">Next reward</span>
                            @if ($journey['nextReward'])
                                <p class="h6 fw-bold mb-1">{{ $journey['nextReward']['label'] }}</p>
                                <p class="small text-secondary mb-0">
                                    At purchase #{{ $journey['nextReward']['step'] }} · {{ $plural($journey['nextReward']['remaining']) }} to go
                                </p>
                            @else
                                <p class="h6 fw-bold mb-1">All rewards unlocked</p>
                                <p class="small text-secondary mb-0">More rewards are coming. Keep going!</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="journey-stat">
                            <span class="journey-stat-label">Journey resets</span>
                            <p class="h6 fw-bold mb-1">{{ $journey['cycleEnd']->format('d M Y') }}</p>
                            <p class="small text-secondary mb-0">
                                {{ $journey['daysLeft'] === 1 ? '1 day' : $journey['daysLeft'].' days' }} left · every 12 months from registration
                            </p>
                        </div>
                    </div>
                </div>

                @if ($journey['nextTier'])
                    <p class="mb-3">
                        <strong>{{ $plural($journey['nextTier']['remaining']) }}</strong> to go until
                        <strong>{{ $journey['nextTier']['label'] }}</strong> (purchase #{{ $journey['nextTier']['step'] }}).
                    </p>
                @else
                    <p class="mb-3">You have reached the top tier. Enjoy <strong>{{ $journey['discount'] }}% off</strong> every transaction.</p>
                @endif

                <div class="journey-path">
                    @foreach ($journeyRows as $rowIndex => $rowSteps)
                        <ol class="journey-row {{ $rowIndex === 1 ? 'is-reverse' : '' }}" @if ($rowIndex === 1) start="6" @endif>
                            @foreach ($rowSteps as $step)
                                <li class="journey-step">
                                    <span class="journey-node tier-{{ $step['tier'] }} is-{{ $step['status'] === 'done' ? 'done' : ($step['status'] === 'next' ? 'next' : 'upcoming') }} {{ $step['current'] ? 'is-current' : '' }}">
                                        <span class="journey-face">{{ $step['number'] }}</span>
                                        @if ($step['reward'])
                                            <span class="journey-gift" aria-hidden="true">🎁</span>
                                        @endif
                                        <span class="visually-hidden">
                                            Purchase {{ $step['number'] }}{{ $step['number'] === 11 ? ' and beyond' : '' }}{{ $step['reward'] ? ': '.$step['reward'] : '' }},
                                            {{ $step['status'] === 'done' ? 'completed' : ($step['status'] === 'next' ? 'next step' : 'upcoming') }}.
                                        </span>
                                    </span>
                                    @if (! $loop->last)
                                        <span class="journey-link {{ $step['number'] < $journey['count'] ? 'is-done' : '' }}" aria-hidden="true"></span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>

                        @if ($rowIndex === 0)
                            <div class="journey-drop {{ $journey['count'] > 5 ? 'is-done' : '' }}" aria-hidden="true"></div>
                        @endif
                    @endforeach
                </div>
                <p class="small text-secondary mt-3 mb-4">
                    🎁 marks a reward step · the dashed ring is your next step · step 11 and beyond keeps you at Diamond.
                </p>

                <h3 class="h6 fw-bold mb-2">Rewards</h3>
                <ul class="list-unstyled mb-4">
                    @foreach ($journey['rewards'] as $reward)
                        <li class="d-flex align-items-center justify-content-between gap-3 border-top py-2">
                            <span class="d-flex align-items-center gap-3">
                                <span class="journey-chip tier-{{ $reward['tier'] }}">{{ $reward['step'] }}</span>
                                <span>{{ $reward['label'] }}</span>
                            </span>
                            @if ($reward['unlocked'])
                                <span class="badge rounded-pill bg-success">Unlocked</span>
                            @else
                                <span class="badge rounded-pill bg-light text-dark">{{ $plural($reward['remaining']) }} to go</span>
                            @endif
                        </li>
                    @endforeach
                    <li class="border-top pt-2 small text-secondary">More rewards after purchase #11.</li>
                </ul>

                <h3 class="h6 fw-bold mb-2">Membership tiers</h3>
                <div class="row g-3">
                    @foreach ($journey['tiers'] as $tier)
                        <div class="col-md-4">
                            <div class="journey-tier tier-{{ $tier['key'] }} {{ $tier['current'] ? 'is-current' : '' }}">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="journey-chip tier-{{ $tier['key'] }}" aria-hidden="true"></span>
                                        <strong>{{ $tier['label'] }}</strong>
                                    </span>
                                    @if ($tier['current'])
                                        <span class="badge rounded-pill bg-dark">Your tier</span>
                                    @endif
                                </div>
                                <p class="small text-secondary mb-1">Purchases {{ $tier['range'] }}</p>
                                <p class="small mb-0">{{ $tier['summary'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="small text-secondary mt-4 mb-0">
                    Your journey restarts every 12 months from the date you registered
                    ({{ $journey['cycleStart']->format('d M Y') }} – {{ $journey['cycleEnd']->format('d M Y') }} is the current cycle).
                </p>
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
    <script>
        (function () {
            const input = document.getElementById('profile_picture');
            const img = document.querySelector('[data-avatar-img]');
            const placeholder = document.querySelector('[data-avatar-placeholder]');
            const actions = document.querySelector('[data-upload-actions]');
            if (!input) return;

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) { actions.classList.remove('is-visible'); return; }

                img.src = URL.createObjectURL(file);
                img.hidden = false;
                placeholder.hidden = true;
                actions.classList.add('is-visible');
            });
        })();
    </script>
</body>
</html>
