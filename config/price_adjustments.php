<?php

return [
    'adjustments' => [
        'trending' => env('PRICE_ADJUSTMENTS_TRENDING', 1.0),
        'under' => env('PRICE_ADJUSTMENTS_UNDER', 1.0),
        '' => env('PRICE_ADJUSTMENTS_DEFAULT', 1.0),
        null => env('PRICE_ADJUSTMENTS_DEFAULT', 1.0),
    ],
];
