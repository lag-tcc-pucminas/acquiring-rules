<?php

declare(strict_types=1);

return [
    'acquirer-gateway' => [
        'base_uri' => env('ACQUIRER_GATEWAY_URL'),
        'timeout' => env('ACQUIRER_GATEWAY_REQUEST_TIMEOUT', 5),
        'connect_timeout' => 5,
        'headers' => [
            'User-Agent' => env('APP_NAME', 'acquiring-rules')
        ]
    ]
];
