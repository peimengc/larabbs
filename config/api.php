<?php

return [
    'rate_limit' => [
        'access' => env('RATE_LIMIT', '60,1'),
        'sign' => env('SIGN_RATE_LIMIT', '10,1'),
    ]
];
