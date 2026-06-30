<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PolyEngine' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <img class="brand-logo" src="{{ asset('brand/polyengine-logo-exact.png') }}" alt="PolyEngine">
        </a>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
            <a href="{{ route('markets.index') }}" @class(['active' => request()->routeIs('markets.*')])>Markets</a>
            <a href="{{ route('opportunities.index') }}" @class(['active' => request()->routeIs('opportunities.*')])>AI Opportunities</a>
            <a href="{{ route('portfolio.index') }}" @class(['active' => request()->routeIs('portfolio.*')])>Portfolio</a>
            <a href="{{ route('history.index') }}" @class(['active' => request()->routeIs('history.*')])>Trade History</a>
            <a href="{{ route('settings.index') }}" @class(['active' => request()->routeIs('settings.*')])>Settings</a>
        </nav>
        <div class="nav-section">
            <span>Categories</span>
            <a href="{{ route('markets.index', ['sort' => 'volume']) }}">Highest Volume</a>
            <a href="{{ route('markets.index', ['category' => 'Politics']) }}">Politics</a>
            <a href="{{ route('markets.index', ['category' => 'Sports']) }}">Sports</a>
            <a href="{{ route('markets.index', ['category' => 'Crypto']) }}">Crypto</a>
            <a href="{{ route('markets.index', ['category' => 'Finance']) }}">Finance</a>
        </div>
    </aside>
    <main class="main">
        <header class="topbar">
            <div>
                <p class="eyebrow">{{ $eyebrow ?? 'Automatic Paper Trading' }}</p>
                <h1>{{ $heading ?? 'Polymarket Paper Bot' }}</h1>
            </div>
            <div class="live-pill">
                <span></span>
                Real data, fake money
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit">Logout</button>
            </form>
        </header>
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
</div>
</body>
</html>

