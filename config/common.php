<?php

return [
    'list_count' => env('COMMON_LIST_COUNT', 20),

    /*
    |--------------------------------------------------------------------------
    | Purchasable Types
    |--------------------------------------------------------------------------
    |
    | Define all purchasable entity types that users can buy.
    | This is used for validation in purchase controllers and services.
    |
    */
    'purchasable_types' => [
        'App\\Models\\Package',
        'package',
        // Add more purchasable types here as needed
        // 'App\\Models\\Course',
        // 'App\\Models\\Equipment',
    ],
];
