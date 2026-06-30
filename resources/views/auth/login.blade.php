<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PolyEngine</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
<main class="auth-shell">
    <a class="auth-logo" href="{{ route('landing') }}"><img src="{{ asset('brand/polyengine-logo-exact.png') }}" alt="PolyEngine"></a>
    <form class="auth-panel" method="post" action="{{ route('login.store') }}">
        @csrf
        <p class="eyebrow">Member Login</p>
        <h1>Access PolyEngine</h1>
        @if ($errors->any())
            <div class="auth-error">{{ $errors->first() }}</div>
        @endif
        <label><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label><span>Password</span><input type="password" name="password" required></label>
        <label class="checkline"><input type="checkbox" name="remember" value="1"><span>Remember me</span></label>
        <button>Login</button>
        <p>Need access? <a href="{{ route('signup') }}">Create an account</a></p>
    </form>
</main>
</body>
</html>

