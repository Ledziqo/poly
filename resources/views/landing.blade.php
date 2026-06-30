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
    <a class="landing-brand" href="{{ route('landing') }}">
        <img src="{{ asset('brand/polyengine-logo.png') }}" alt="PolyEngine">
    </a>
    <nav>
        <a href="#signals">Signals</a>
        <a href="#paper">Paper Trading</a>
        <a href="#pricing">Access</a>
        <a href="{{ route('login') }}">Login</a>
        <a class="nav-cta" href="{{ route('signup') }}">Get Access</a>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-media" aria-hidden="true">
            <img src="{{ asset('brand/polyengine-main.png') }}" alt="">
        </div>
        <div class="hero-content">
            <p class="eyebrow">AI powered. Markets driven.</p>
            <h1>Automatic paper trading for Polymarket signals.</h1>
            <p class="hero-copy">PolyEngine watches real Polymarket markets, evaluates order-book depth, scores opportunities, and runs fake-money trades so you can test signal quality before risking real capital.</p>
            <div class="hero-actions">
                <a class="primary-btn" href="{{ route('signup') }}">Start with Paper Mode</a>
                <a class="secondary-btn" href="{{ route('login') }}">Member Login</a>
            </div>
            <div class="hero-stats">
                <span><strong>Real</strong> market data</span>
                <span><strong>Fake</strong> money execution</span>
                <span><strong>Full</strong> decision logs</span>
            </div>
        </div>
    </section>

    <section class="landing-band" id="signals">
        <div class="section-head">
            <p class="eyebrow">Signal Engine</p>
            <h2>Find markets worth watching before they become obvious.</h2>
        </div>
        <div class="feature-grid">
            <article><span>01</span><h3>AI Opportunity Ranking</h3><p>Markets are ranked by price, estimated fair probability, edge, confidence, liquidity, spread, expiry timing, and movement risk.</p></article>
            <article><span>02</span><h3>Order Book Awareness</h3><p>Paper entries are simulated against bid/ask depth instead of pretending the displayed price is always executable.</p></article>
            <article><span>03</span><h3>Explainable Decisions</h3><p>Every entry, skip, exit, and risk block is logged so you can understand what the bot saw and why it acted.</p></article>
        </div>
    </section>

    <section class="split-band" id="paper">
        <div>
            <p class="eyebrow">Paper Execution</p>
            <h2>Real Polymarket feel. No real-money execution.</h2>
            <p>PolyEngine is built for disciplined testing: fake balance, simulated fills, partial fill handling, slippage checks, take profit, stop loss, exposure limits, and portfolio tracking.</p>
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
            <h2>From market scan to paper PnL in one loop.</h2>
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
            <h2>PolyEngine is subscription-gated while the paper bot is being refined.</h2>
            <p>Create an account, then message {{ config('polyengine.telegram') }} on Telegram to activate your subscription.</p>
        </div>
        <a class="primary-btn" href="{{ route('signup') }}">Create Account</a>
    </section>
</main>
</body>
</html>
