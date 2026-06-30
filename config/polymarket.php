<?php

return [
    'gamma_url' => env('POLYMARKET_GAMMA_URL', 'https://gamma-api.polymarket.com'),
    'clob_url' => env('POLYMARKET_CLOB_URL', 'https://clob.polymarket.com'),
    'data_url' => env('POLYMARKET_DATA_URL', 'https://data-api.polymarket.com'),
    'market_sync_limit' => (int) env('POLYMARKET_MARKET_SYNC_LIMIT', 100),
];
