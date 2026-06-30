<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup - PolyEngine</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
<main class="auth-shell">
    <a class="auth-logo" href="{{ route('landing') }}"><img src="{{ asset('brand/polyengine-logo.png') }}" alt="PolyEngine"></a>
    <form class="auth-panel" method="post" action="{{ route('signup.store') }}">
        @csrf
        <p class="eyebrow">Create Account</p>
        <h1>Start your PolyEngine profile</h1>
        @if ($errors->any())
            <div class="auth-error">{{ $errors->first() }}</div>
        @endif
        <label><span>Name</span><input type="text" name="name" value="{{ old('name') }}" required autofocus></label>
        <label><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required></label>
        <label><span>Password</span><input type="password" name="password" required></label>
        <button>Create Account</button>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </form>
</main>
</body>
</html>
