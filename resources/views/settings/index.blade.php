<x-layouts.app heading="Settings" eyebrow="Simple setup, hard safety rules">
    <section class="two-col">
        <form class="panel form" method="post" action="{{ route('settings.update') }}">
            @csrf
            <div class="panel-head"><h2>Bot Settings</h2></div>
            <label><span>Bot enabled</span><input type="checkbox" name="enabled" value="1" @checked($portfolio->settings->enabled)></label>
            <label><span>Risk level</span>
                <select name="risk_level">
                    <option value="safe" @selected($portfolio->settings->risk_level === 'safe')>Safe</option>
                    <option value="balanced" @selected($portfolio->settings->risk_level === 'balanced')>Balanced</option>
                    <option value="aggressive" @selected($portfolio->settings->risk_level === 'aggressive')>Aggressive</option>
                </select>
            </label>
            <label><span>Max amount per trade</span><input name="max_amount_per_trade" type="number" step="1" value="{{ $portfolio->settings->max_amount_per_trade }}"></label>
            <label><span class="help-label">Minimum liquidity <b tabindex="0" data-tip="The minimum available order-book depth a market must have before the bot can enter. Higher values avoid thin markets but reduce trade count.">?</b></span><input name="minimum_liquidity" type="number" step="1" value="{{ $portfolio->settings->minimum_liquidity }}"></label>
            <label><span class="help-label">Max spread <b tabindex="0" data-tip="The largest allowed gap between best bid and best ask. Lower values mean cleaner fills; higher values allow riskier, less liquid entries.">?</b></span><input name="max_spread" type="number" step="0.001" value="{{ $portfolio->settings->max_spread }}"></label>
            <label><span class="help-label">Min edge <b tabindex="0" data-tip="The minimum estimated advantage between PolyEngine fair probability and the market price. Bigger edge means stricter entries.">?</b></span><input name="min_edge" type="number" step="0.001" value="{{ $portfolio->settings->min_edge }}"></label>
            <label><span class="help-label">Min confidence <b tabindex="0" data-tip="The minimum AI confidence score required before the bot can enter. Higher confidence filters for stronger setups.">?</b></span><input name="min_confidence" type="number" step="1" value="{{ $portfolio->settings->min_confidence }}"></label>
            <label><span class="help-label">Max open positions <b tabindex="0" data-tip="The maximum number of paper positions the bot can hold at once. This prevents the bot from overtrading.">?</b></span><input name="max_open_positions" type="number" step="1" value="{{ $portfolio->settings->max_open_positions }}"></label>
            <label><span class="help-label">Max total exposure <b tabindex="0" data-tip="The maximum fake-dollar value allowed across all open positions. This caps total paper risk at one time.">?</b></span><input name="max_total_exposure" type="number" step="1" value="{{ $portfolio->settings->max_total_exposure }}"></label>
            <button>Save Settings</button>
        </form>

        <form class="panel form" method="post" action="{{ route('settings.reset') }}">
            @csrf
            <div class="panel-head"><h2>Reset Paper Account</h2></div>
            <p class="empty">This clears fake positions and fake trade history only. No real wallet or private key exists in this MVP.</p>
            <label><span>Starting paper balance</span><input name="starting_balance" type="number" step="100" value="{{ $portfolio->starting_balance }}"></label>
            <button class="danger">Reset Portfolio</button>
        </form>
    </section>
</x-layouts.app>
