<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Access - PolyEngine</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
<main class="auth-shell">
    <a class="auth-logo" href="{{ route('landing') }}"><img src="{{ asset('brand/polyengine-logo-exact.png') }}" alt="PolyEngine"></a>
    <form class="auth-panel" method="post" action="{{ route('signup.store') }}">
        @csrf
        <p class="eyebrow">Private Access</p>
        <h1>Request PolyEngine access</h1>
        @if ($errors->any())
            <div class="auth-error">{{ $errors->first() }}</div>
        @endif
        <label><span>Name</span><input type="text" name="name" value="{{ old('name') }}" required autofocus></label>
        <label><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required></label>
        <label><span>Telegram username</span><input type="text" name="telegram" value="{{ old('telegram') }}" placeholder="@yourname"></label>
        <label><span>WhatsApp</span><input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Number or @username"></label>
        <label><span>Trading experience</span>
            <select name="trading_experience">
                <option value="">Select one</option>
                <option value="Beginner" @selected(old('trading_experience') === 'Beginner')>Beginner</option>
                <option value="Intermediate" @selected(old('trading_experience') === 'Intermediate')>Intermediate</option>
                <option value="Advanced" @selected(old('trading_experience') === 'Advanced')>Advanced</option>
                <option value="Professional" @selected(old('trading_experience') === 'Professional')>Professional</option>
            </select>
        </label>
        <label><span>What do you want to use PolyEngine for?</span><textarea name="message" rows="4" placeholder="Paper trading, signal research, copy-wallet tracking...">{{ old('message') }}</textarea></label>
        <button>Submit Request</button>
        <p>Already approved? <a href="{{ route('login') }}">Login</a></p>
    </form>
</main>
</body>
</html>

