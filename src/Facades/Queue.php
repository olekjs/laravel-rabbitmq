<?php

namespace Olekjs\LaravelRabbitMQ\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static self listen(string $endpoint, array|string|callable|object $action)
 * @method static self name(string $prefix)
 * @method static self group(Closure $endpoints)
 * @method static self setPrefix(string $prefix)
 * @method static self clearPrefix()
 * @method static array getEndpoints()
 * @method static void destroy()
 *
 * @see \Olekjs\LaravelRabbitMQ\Routing\Queue
 */
class Queue extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-rabbitmq-queue';
    }
}
