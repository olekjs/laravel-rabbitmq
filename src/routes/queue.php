<?php

use Olekjs\LaravelRabbitMQ\Facades\Queue;

Queue::listen('order.shipped');
