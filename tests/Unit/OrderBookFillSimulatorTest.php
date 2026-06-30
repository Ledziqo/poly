<?php

namespace Tests\Unit;

use App\Models\MarketOutcome;
use App\Services\Trading\OrderBookFillSimulator;
use PHPUnit\Framework\TestCase;

class OrderBookFillSimulatorTest extends TestCase
{
    public function test_buy_walks_ask_depth(): void
    {
        $outcome = new MarketOutcome([
            'price' => 0.40,
            'order_book' => [
                'asks' => [
                    ['price' => 0.41, 'size' => 100],
                    ['price' => 0.42, 'size' => 200],
                ],
                'bids' => [],
            ],
        ]);

        $fill = (new OrderBookFillSimulator())->buy($outcome, 84, 0.05);

        $this->assertSame('filled', $fill['status']);
        $this->assertEquals(202.381, $fill['shares'], '', 0.01);
        $this->assertEquals(0.4151, $fill['avg_price'], '', 0.001);
    }

    public function test_buy_rejects_when_slippage_is_too_high(): void
    {
        $outcome = new MarketOutcome([
            'price' => 0.40,
            'order_book' => [
                'asks' => [
                    ['price' => 0.50, 'size' => 100],
                ],
                'bids' => [],
            ],
        ]);

        $fill = (new OrderBookFillSimulator())->buy($outcome, 40, 0.03);

        $this->assertSame('rejected', $fill['status']);
    }
}
