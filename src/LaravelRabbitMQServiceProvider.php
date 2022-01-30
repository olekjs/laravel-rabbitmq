<?php

namespace Olekjs\LaravelRabbitMQ;

use Olekjs\LaravelRabbitMQ\Routing\Queue;
use Olekjs\LaravelRabbitMQ\Services\Connection;
use Illuminate\Support\ServiceProvider;
use Olekjs\LaravelRabbitMQ\Console\RabbitMQListenCommand;

class LaravelRabbitMQServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__ . '/config/rabbitmq.php' => config_path('rabbitmq.php')],
                'config'
            );

            $this->publishes(
                [__DIR__ . '/routes/queue.php' => 'routes/queue.php'],
                'route'
            );

            $this->commands([
                RabbitMQListenCommand::class,
            ]);
        }

        $this->app->singleton(Connection::class, function ($app) {
            return new Connection(
                $app->config->get('rabbitmq.host'),
                $app->config->get('rabbitmq.port'),
                $app->config->get('rabbitmq.user'),
                $app->config->get('rabbitmq.password'),
            );
        });

        $this->app->bind('laravel-rabbitmq-queue', function () {
            return new Queue();
        });
    }
}
