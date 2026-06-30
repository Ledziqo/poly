<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Requested - PolyEngine</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
<main class="auth-shell wide">
    <a class="auth-logo" href="{{ route('landing') }}"><img src="{{ asset('brand/polyengine-logo-silver.png') }}" alt="PolyEngine"></a>
    <section class="auth-panel subscription-panel">
        <p class="eyebrow">Request Sent</p>
        <h1>Your PolyEngine request is ready.</h1>
        <p>Send the details to <strong>{{ $telegram }}</strong> on Telegram or WhatsApp <strong>{{ $whatsapp }}</strong> so we can approve your access.</p>
        <pre class="request-summary">{{ $message }}</pre>
        <div class="subscription-actions">
            <a class="primary-btn" href="https://t.me/{{ ltrim($telegram, '@') }}?text={{ urlencode($message) }}">Send on Telegram</a>
            <a class="secondary-btn" href="https://wa.me/?text={{ urlencode($message) }}">Open WhatsApp</a>
            <a class="secondary-btn" href="{{ route('login') }}">Login</a>
        </div>
    </section>
</main>
</body>
</html>
