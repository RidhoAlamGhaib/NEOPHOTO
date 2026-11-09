<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketing insights</title>
    <link rel="stylesheet" href="{{ asset('assets/dashboard/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/assets/css/style.css') }}">
</head>
<body class="bg-light">
    <main class="container py-4">
        @include('dashboard.partials.marketing-insights', ['insights' => $insights])
    </main>
</body>
</html>
