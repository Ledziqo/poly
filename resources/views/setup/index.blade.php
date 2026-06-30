<x-layouts.app heading="Hostinger Setup" eyebrow="Run safe setup tasks from the browser">
    @if ($lastStatus)
        <section class="notice">{{ $lastStatus }}</section>
    @endif

    @if ($lastOutput)
        <section class="panel">
            <div class="panel-head"><h2>Last Output</h2></div>
            <pre class="setup-output">{{ $lastOutput }}</pre>
        </section>
    @endif

    <section class="panel">
        <div class="panel-head">
            <h2>Setup Actions</h2>
            <span>Run these in order on first deploy</span>
        </div>
        <div class="setup-actions">
            @foreach ($commands as $key => $definition)
                <form method="post" action="{{ route('setup.run', ['token' => $token]) }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="action" value="{{ $key }}">
                    <button type="submit">{{ $definition['label'] }}</button>
                </form>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Recommended First Run</h2></div>
        <p class="empty">Run: migrations, sync markets, sync order books, score AI opportunities, run paper bot once, refresh portfolio.</p>
        <p class="empty">Then turn on Hostinger cron for <code>php artisan schedule:run</code>.</p>
    </section>
</x-layouts.app>
