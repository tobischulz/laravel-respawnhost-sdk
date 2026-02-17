<?php

return [

    'base_url' => env('RESPAWNHOST_BASE_URL', 'https://respawnhost.com/api/v1'),

    'api_key' => env('RESPAWNHOST_API_KEY'),

    'timeout' => (int) env('RESPAWNHOST_TIMEOUT', 30),

    'connect_timeout' => (int) env('RESPAWNHOST_CONNECT_TIMEOUT', 10),

    'retry' => [

        'times' => (int) env('RESPAWNHOST_RETRY_TIMES', 1),

        'sleep' => (int) env('RESPAWNHOST_RETRY_SLEEP', 200),

    ],

    'user_agent' => env('RESPAWNHOST_USER_AGENT', 'laravel-respawnhost-sdk'),

    'catalog_base_url' => env('RESPAWNHOST_CATALOG_BASE_URL', 'https://respawnhost.com'),

];
