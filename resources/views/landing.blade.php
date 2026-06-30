<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PolyEngine - AI Paper Trading for Polymarket</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/polyengine-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-body">
<header class="landing-nav">
    <div class="landing-nav-inner">
        <a class="landing-brand" href="{{ route('landing') }}">
            <img src="{{ asset('brand/polyengine-logo-exact.png') }}" alt="PolyEngine">
        </a>
        <nav>
            <a href="#signals">Signals</a>
            <a href="#paper">Automation</a>
            <a href="#pricing">Access</a>
        </nav>
        <div class="nav-actions">
            <a href="{{ route('login') }}">Login</a>
            <a class="nav-cta" href="{{ route('signup') }}">Request Access</a>
        </div>
    </div>
</header>

<main>
    <section class="hero">
        <div class="hero-content">
            <p class="eyebrow">Private AI paper trading lab</p>
            <h1>See the trades before you risk the money.</h1>
            <p class="hero-copy">PolyEngine turns live Polymarket data into ranked signals, simulated entries, risk controls, and paper PnL so you can test the bot before copying anything with real capital.</p>
            <div class="hero-actions">
                <a class="primary-btn" href="{{ route('signup') }}">Request Access</a>
                <a class="secondary-btn" href="{{ route('login') }}">Member Login</a>
            </div>
            <div class="hero-proof">
                <span>Live Polymarket data</span>
                <span>Automatic paper execution</span>
                <span>Decision logs</span>
            </div>
        </div>
    </section>

    <section class="landing-band" id="signals">
        <div class="section-head">
            <p class="eyebrow">Signal Engine</p>
            <h2>Rank real markets by edge, confidence, depth, and timing.</h2>
            <p>Built for watching the full trading loop instead of guessing from a single displayed price.</p>
        </div>
        <div class="feature-grid">
            <article><span>01</span><h3>Market Scoring</h3><p>Prices, liquidity, volume, expiry, movement, and spread feed a ranked opportunity board.</p></article>
            <article><span>02</span><h3>Realistic Paper Fills</h3><p>Entries use order book depth, slippage, partial fills, and liquidity checks for a closer simulation.</p></article>
            <article><span>03</span><h3>Decision Memory</h3><p>Every skip, entry, exit, resize, and pause gets a plain-English explanation trail.</p></article>
        </div>
    </section>

    <section class="split-band" id="paper">
        <div>
            <p class="eyebrow">Paper Execution</p>
            <h2>Automatic bot behavior with fake money only.</h2>
            <p>The MVP is designed to learn before going live: paper balance, simulated entries, exits, PnL, win rate, exposure, and a full trade history that you can review before copying anything manually.</p>
        </div>
        <div class="terminal-mock">
            <div><span>Strong Entry</span><b>YES @ 38.4%</b><em>Edge +7.2%</em></div>
            <div><span>Skipped</span><b>Spread too wide</b><em>Max 8.0%</em></div>
            <div><span>Exited</span><b>Take profit hit</b><em>+18.6%</em></div>
        </div>
    </section>

    <section class="landing-band">
        <div class="section-head">
            <p class="eyebrow">Workflow</p>
            <h2>One clean loop from live data to paper PnL.</h2>
        </div>
        <div class="steps">
            <div><strong>Sync</strong><p>Pull live Polymarket markets, outcomes, liquidity, volume, and order books.</p></div>
            <div><strong>Score</strong><p>Estimate edge and confidence using market quality, depth, movement, and expiry context.</p></div>
            <div><strong>Simulate</strong><p>Run automatic paper entries/exits with realistic safety checks.</p></div>
            <div><strong>Review</strong><p>Track PnL, win rate, open positions, trade history, and decision explanations.</p></div>
        </div>
    </section>

    <section class="pricing-band" id="pricing">
        <div>
            <p class="eyebrow">Private Access</p>
            <h2>Subscription-gated while the engine is being refined.</h2>
            <p>Create an account, then message @Aesliex on Telegram to activate your PolyEngine subscription.</p>
        </div>
        <a class="primary-btn" href="{{ route('signup') }}">Request Access</a>
    </section>
</main>
</body>
</html>

