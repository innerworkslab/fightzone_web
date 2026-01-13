<?php

return [
    // Points granted per 1.00 unit of currency (e.g. 1.00 USD -> X points)
    'points_per_unit' => env('POINTS_PER_UNIT', 1),

    // Minor unit count for the currency (e.g. 100 for cents)
    'currency_minor_unit' => env('CURRENCY_MINOR_UNIT', 100),

    // Optional min/max deposit amounts in cents
    'min_deposit_cents' => env('MIN_DEPOSIT_CENTS', 100),
    'max_deposit_cents' => env('MAX_DEPOSIT_CENTS', 10000000),

    // Upload limits for deposit screenshots (in KB)
    'screenshot_max_kb' => env('DEPOSIT_SCREENSHOT_MAX_KB', 5120),
];
