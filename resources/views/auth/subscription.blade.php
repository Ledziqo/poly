<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Required - PolyEngine</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
<main class="auth-shell wide">
    <a class="auth-logo" href="{{ route('landing') }}"><img src="{{ asset('brand/polyengine-logo.png') }}" alt="PolyEngine"></a>
    <section class="auth-panel subscription-panel">
        <p class="eyebrow">Subscription Required</p>
        <h1>Please buy a subscription to access our tool.</h1>
        <p>Message <strong>{{ config('polyengine.telegram') }}</strong> on Telegram to activate your PolyEngine account.</p>
        <div class="subscription-actions">
            <a class="primary-btn" href="https://t.me/{{ ltrim(config('polyengine.telegram'), '@') }}">Message on Telegram</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="secondary-button">Logout</button></form>
        </div>
    </section>
</main>
</body>
</html>
