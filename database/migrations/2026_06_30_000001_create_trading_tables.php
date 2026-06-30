<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('polymarket_id')->unique();
            $table->string('slug')->nullable()->index();
            $table->string('question', 1000);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category_name')->nullable()->index();
            $table->boolean('active')->default(true)->index();
            $table->boolean('closed')->default(false)->index();
            $table->boolean('archived')->default(false);
            $table->decimal('volume', 18, 2)->default(0);
            $table->decimal('liquidity', 18, 2)->default(0);
            $table->decimal('last_price', 8, 4)->nullable();
            $table->decimal('price_change_24h', 8, 4)->nullable();
            $table->timestamp('end_at')->nullable()->index();
            $table->timestamp('synced_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('market_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('token_id')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('price', 8, 4)->nullable();
            $table->decimal('best_bid', 8, 4)->nullable();
            $table->decimal('best_ask', 8, 4)->nullable();
            $table->decimal('spread', 8, 4)->nullable();
            $table->decimal('liquidity', 18, 2)->default(0);
            $table->json('order_book')->nullable();
            $table->timestamp('price_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['market_id', 'name']);
        });

        Schema::create('price_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_outcome_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 8, 4)->nullable();
            $table->decimal('best_bid', 8, 4)->nullable();
            $table->decimal('best_ask', 8, 4)->nullable();
            $table->decimal('spread', 8, 4)->nullable();
            $table->decimal('liquidity', 18, 2)->default(0);
            $table->json('order_book')->nullable();
            $table->timestamp('captured_at')->index();
            $table->timestamps();
        });

        Schema::create('ai_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_outcome_id')->constrained()->cascadeOnDelete();
            $table->decimal('market_probability', 8, 4)->nullable();
            $table->decimal('fair_probability', 8, 4)->nullable();
            $table->decimal('edge', 8, 4)->nullable();
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->string('grade')->default('Skip')->index();
            $table->json('features')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamp('scored_at')->index();
            $table->timestamps();
        });

        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Paper Portfolio');
            $table->decimal('starting_balance', 18, 2)->default(10000);
            $table->decimal('cash_balance', 18, 2)->default(10000);
            $table->decimal('realized_pnl', 18, 2)->default(0);
            $table->decimal('unrealized_pnl', 18, 2)->default(0);
            $table->decimal('total_exposure', 18, 2)->default(0);
            $table->string('mode')->default('paper');
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('market_outcome_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('open')->index();
            $table->decimal('shares', 18, 4);
            $table->decimal('avg_entry_price', 8, 4);
            $table->decimal('current_price', 8, 4)->nullable();
            $table->decimal('cost_basis', 18, 2);
            $table->decimal('market_value', 18, 2)->default(0);
            $table->decimal('realized_pnl', 18, 2)->default(0);
            $table->decimal('unrealized_pnl', 18, 2)->default(0);
            $table->decimal('take_profit_price', 8, 4)->nullable();
            $table->decimal('stop_loss_price', 8, 4)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('market_outcome_id')->constrained()->cascadeOnDelete();
            $table->string('side');
            $table->string('source')->default('bot');
            $table->decimal('shares', 18, 4);
            $table->decimal('avg_price', 8, 4);
            $table->decimal('notional', 18, 2);
            $table->decimal('slippage', 8, 4)->default(0);
            $table->string('fill_status')->default('filled');
            $table->json('fill_details')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamp('executed_at')->index();
            $table->timestamps();
        });

        Schema::create('bot_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->decimal('max_amount_per_trade', 18, 2)->default(100);
            $table->string('risk_level')->default('balanced');
            $table->decimal('max_daily_loss', 18, 2)->default(250);
            $table->unsignedInteger('max_open_positions')->default(20);
            $table->decimal('max_total_exposure', 18, 2)->default(3000);
            $table->decimal('minimum_liquidity', 18, 2)->default(500);
            $table->decimal('max_spread', 8, 4)->default(0.1000);
            $table->decimal('max_slippage', 8, 4)->default(0.0300);
            $table->decimal('min_edge', 8, 4)->default(0.0300);
            $table->unsignedTinyInteger('min_confidence')->default(60);
            $table->timestamps();
        });

        Schema::create('bot_decision_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('market_outcome_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('status')->index();
            $table->text('reason');
            $table->json('context')->nullable();
            $table->timestamp('decided_at')->index();
            $table->timestamps();
        });

        Schema::create('wallet_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_outcome_id')->nullable()->constrained()->nullOnDelete();
            $table->string('wallet')->index();
            $table->string('trader_name')->nullable();
            $table->string('activity_type')->default('trade');
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('price', 8, 4)->nullable();
            $table->string('data_quality')->default('api-derived');
            $table->json('raw_payload')->nullable();
            $table->timestamp('observed_at')->index();
            $table->timestamps();
        });

        Schema::create('top_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_outcome_id')->constrained()->cascadeOnDelete();
            $table->string('wallet')->index();
            $table->string('trader_name')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('shares', 18, 4)->default(0);
            $table->unsignedInteger('rank')->default(0);
            $table->string('data_quality')->default('estimated');
            $table->timestamp('observed_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_positions');
        Schema::dropIfExists('wallet_activities');
        Schema::dropIfExists('bot_decision_logs');
        Schema::dropIfExists('bot_settings');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('ai_signals');
        Schema::dropIfExists('price_snapshots');
        Schema::dropIfExists('market_outcomes');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('categories');
    }
};
