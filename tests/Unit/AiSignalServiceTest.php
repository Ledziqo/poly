<?php

namespace Tests\Unit;

use App\Models\Market;
use App\Models\MarketOutcome;
use App\Services\Signals\AiSignalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiSignalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_liquidity_does_not_create_large_edge_for_long_shots(): void
    {
        $market = Market::create([
            'polymarket_id' => 'world-cup-long-shot',
            'question' => 'Will Egypt win the 2026 FIFA World Cup?',
            'active' => true,
            'closed' => false,
            'volume' => 1_000_000,
        ]);

        $outcome = MarketOutcome::create([
            'market_id' => $market->id,
            'name' => 'Yes',
            'price' => 0.002,
            'spread' => 0.001,
            'liquidity' => 35_575,
            'order_book' => [
                'bids' => [['price' => 0.002, 'size' => 10_000]],
                'asks' => [['price' => 0.002, 'size' => 10_000]],
            ],
        ]);

        $signal = (new AiSignalService())->score($outcome->load('market'));

        $this->assertSame('Skip', $signal->grade);
        $this->assertLessThan(0.003, (float) $signal->fair_probability);
        $this->assertLessThan(0.001, (float) $signal->edge);
    }

    public function test_order_book_imbalance_can_move_normal_priced_markets_with_a_cap(): void
    {
        $market = Market::create([
            'polymarket_id' => 'normal-priced-market',
            'question' => 'Will a normal priced outcome resolve yes?',
            'active' => true,
            'closed' => false,
            'volume' => 1_000_000,
        ]);

        $outcome = MarketOutcome::create([
            'market_id' => $market->id,
            'name' => 'Yes',
            'price' => 0.40,
            'spread' => 0.001,
            'liquidity' => 50_000,
            'order_book' => [
                'bids' => [['price' => 0.40, 'size' => 10_000]],
                'asks' => [],
            ],
        ]);

        $signal = (new AiSignalService())->score($outcome->load('market'));

        $this->assertSame('Strong Entry', $signal->grade);
        $this->assertEquals(0.48, (float) $signal->fair_probability);
        $this->assertEquals(0.08, (float) $signal->edge);
    }
}
