<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'HealthCare Plus' }}</title>
  @php
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
@endphp

@if (!empty($manifest))

    {{-- Load compiled SCSS (converted to CSS) --}}
    @if (isset($manifest['resources/scss/app.scss']['file']))
        <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/scss/app.scss']['file']) }}">
    @endif

    {{-- Load compiled JS --}}
    @if (isset($manifest['resources/js/app.js']['file']))
        <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
    @endif

@endif

    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 0;
        }
        .auth-card {
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <div class="card shadow-lg auth-card">
        <div class="card-body p-5">
            {{ $slot }}
        </div>
    </div>
</body>
</html>