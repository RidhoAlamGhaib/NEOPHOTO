<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Neophoto</title>
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

        .profile-picture-preview {
            position: relative;
            display: grid;
            place-items: center;
            width: 144px;
            height: 144px;
            overflow: hidden;
            border-radius: 50%;
            background: #fbe9ec;
            color: #c65e70;
            font-size: 3rem;
            font-weight: 700;
        }

        .profile-picture-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-picture-preview [hidden] {
            display: none !important;
        }
    </style>
</head>
<body>
    <main class="container py-4 py-md-5" style="max-width: 760px;">
        <header class="d-flex align-items-center justify-content-between gap-3 mb-5">
            <a class="membership-header fw-bold text-decoration-none" href="{{ route('index') }}">NEOPHOTO</a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('members.index') }}">Membership</a>
                <form method="POST" action="{{ route('membership.logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>

        <section class="membership-card card" aria-labelledby="edit-profile-title">
            <div class="card-body p-4 p-md-5">
                <h1 id="edit-profile-title" class="h3 mb-2">Edit profile</h1>
                <p class="text-secondary mb-4">Update your profile picture and review your membership details.</p>

                <form
                    method="POST"
                    action="{{ route('members.profile.update') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4 mb-4">
                        <div class="profile-picture-preview flex-shrink-0">
                            <img
                                data-avatar-img
                                src="{{ $profilePicture }}"
                                alt="{{ $membership->name ?: 'Member' }} profile picture"
                            >
                            <span data-avatar-placeholder hidden>
                                {{ mb_strtoupper(mb_substr($membership->name ?: 'Member', 0, 1)) }}
                            </span>
                        </div>

                        <div class="flex-grow-1 w-100">
                            <label class="form-label fw-bold" for="profile_picture">Profile picture</label>
                            <input
                                class="form-control"
                                id="profile_picture"
                                name="profile_picture"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                required
                            >
                            <p class="form-text text-secondary mb-0">JPG, PNG, or WebP; up to 20 MB.</p>
                            @error('profile_picture')
                                <p class="text-danger small mt-2 mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if (session('status'))
                        <p class="text-success mb-3">{{ session('status') }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-membership" type="submit">Save new picture</button>
                        <a class="btn btn-outline-secondary" href="{{ route('members.index') }}">Cancel</a>
                    </div>
                </form>

                <hr class="my-4">

                <h2 class="h5 mb-3">Membership details</h2>
                @if (session('profile_status'))
                    <p class="text-success mb-3">{{ session('profile_status') }}</p>
                @endif
                <form method="POST" action="{{ route('members.profile.details.update') }}">
                    @csrf
                    @method('PATCH')

                    @foreach ([
                        'name' => ['label' => 'Name', 'type' => 'text', 'value' => $membership->name],
                        'email' => ['label' => 'Email', 'type' => 'email', 'value' => $membership->email],
                        'phone' => ['label' => 'Phone', 'type' => 'tel', 'value' => $membership->phone],
                        'birthday' => ['label' => 'Birthday', 'type' => 'date', 'value' => $membership->birthday?->format('Y-m-d')],
                        'job' => ['label' => 'Job', 'type' => 'text', 'value' => $membership->job],
                    ] as $field => $details)
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="{{ $field }}">{{ $details['label'] }}</label>
                            <div class="input-group">
                                <input
                                    class="form-control @error($field) is-invalid @enderror"
                                    id="{{ $field }}"
                                    name="{{ $field }}"
                                    type="{{ $details['type'] }}"
                                    value="{{ old($field, $details['value']) }}"
                                    @if ($field === 'birthday') max="{{ now()->toDateString() }}" @endif
                                    @if ($field === 'name' || $field === 'email' || $field === 'phone') required @endif
                                    readonly
                                >
                                @unless (in_array($field, ['email', 'phone'], true))
                                    <button
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        data-edit-field="{{ $field }}"
                                        aria-controls="{{ $field }}"
                                        aria-label="Edit {{ strtolower($details['label']) }}"
                                    >
                                        Edit
                                    </button>
                                @endunless
                            </div>
                            @error($field)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-membership" type="submit">Save changes</button>
                        <a class="btn btn-outline-secondary" href="{{ route('members.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <script>
        (function () {
            const input = document.getElementById('profile_picture');
            const img = document.querySelector('[data-avatar-img]');
            const placeholder = document.querySelector('[data-avatar-placeholder]');

            document.querySelectorAll('[data-edit-field]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.editField);
                    input.readOnly = !input.readOnly;
                    button.textContent = input.readOnly ? 'Edit' : 'Done';
                    button.setAttribute('aria-label', `${input.readOnly ? 'Edit' : 'Finish editing'} ${input.labels[0].textContent}`);
                    if (!input.readOnly) {
                        input.focus();
                    }
                });
            });

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) {
                    return;
                }

                img.src = URL.createObjectURL(file);
                img.hidden = false;
                placeholder.hidden = true;
            });
        })();
    </script>
</body>
</html>
