<?php

return [
    'host'         => env('RABBITMQ_HOST', 'localhost'),
    'port'         => env('RABBITMQ_PORT', 5672),
    'user'         => env('RABBITMQ_USER', 'admin'),
    'password'     => env('RABBITMQ_PASSWORD', 'password'),
    'exchange'     => env('RABBITMQ_EXCHANGE', 'default'),
    'routing_file' => 'queue',
];
