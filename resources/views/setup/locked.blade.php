<x-layouts.app heading="Setup Locked" eyebrow="Protected server setup">
    <section class="panel">
        <div class="panel-head"><h2>Setup access required</h2></div>
        <p class="empty">Add <code>SETUP_TOKEN</code> to your <code>.env</code>, then open <code>/setup?token=YOUR_TOKEN</code>.</p>
        <p class="empty">After setup is finished, set <code>SETUP_ENABLED=false</code>.</p>
    </section>
</x-layouts.app>
