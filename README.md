# Poly Paper Terminal

Hostinger-friendly Laravel MVP for automatic Polymarket paper trading.

The app mirrors real Polymarket markets, outcomes, prices, order books, liquidity, and volume where public APIs allow it. The bot trades with fake money only, simulating fills against order book depth with spread, slippage, partial fills, exposure checks, and explanation logs.

## What Is Included

- Laravel + MySQL app for Hostinger Business hosting.
- Dark premium trading-terminal UI.
- Real Polymarket market sync from Gamma API.
- CLOB order book sync for executable bid/ask data.
- Heuristic AI opportunity scoring with fair probability, edge, confidence, and grade.
- Automatic paper bot with enter/exit/skip decisions.
- Portfolio, open positions, PnL, trade history, and bot logs.
- Settings for risk level, max trade size, liquidity/spread/edge/confidence thresholds.
- No live trading, no private keys, no real-money execution.

## Local Setup

This workspace uses a local Composer PHAR because Composer was not installed globally.

```bash
php composer.phar install
npm install
npm run build
cp .env.example .env
php artisan key:generate
```

Create a MySQL database and update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poly_paper
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate
php artisan poly:sync-markets --limit=100
php artisan poly:sync-orderbooks --limit=150
php artisan poly:score-signals --limit=300
php artisan poly:run-bot
php artisan serve
```

Open the app at `http://127.0.0.1:8000`.

## Hostinger Cron

On Hostinger, configure one cron job to run every minute from the project root:

```bash
/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

The Laravel scheduler runs:

- `poly:sync-markets --limit=100` every 15 minutes
- `poly:sync-orderbooks --limit=150` every 2 minutes
- `poly:score-signals --limit=300` every 5 minutes
- `poly:run-bot` every 5 minutes
- `poly:refresh-portfolio` every 2 minutes

If Hostinger limits one-minute cron frequency, run the same command at the fastest interval available. The app will still work, just with slower paper bot updates.

## Browser Setup Page

If you cannot run SSH commands on Hostinger, use the protected setup page.

1. Set these in `.env`:

```env
SETUP_ENABLED=true
SETUP_TOKEN=change-this-to-a-long-random-secret
```

2. Open:

```text
https://your-domain.com/setup?token=change-this-to-a-long-random-secret
```

3. Run the setup buttons in this order:

- Run database migrations
- Sync Polymarket markets
- Sync order books
- Score AI opportunities
- Run paper bot once
- Refresh portfolio PnL

4. After setup works, set:

```env
SETUP_ENABLED=false
```

Keep `/setup` disabled after launch. It intentionally exposes powerful maintenance actions.

## Important Notes

- This is paper trading only. Results are not profit guarantees.
- “1:1” means close simulation using public Polymarket APIs and realistic order-book fills, not identical real-money execution.
- Wallet/top-position tables are present for the copy-signal layer, but exact wallet data depends on available public Polymarket endpoints.
- The bot services are isolated under `app/Services` so the worker can later move to a VPS.

## Verification

```bash
php artisan test --testsuite=Unit
npm run build
php artisan list poly
```
