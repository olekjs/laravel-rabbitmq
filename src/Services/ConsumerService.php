<?php

namespace Olekjs\LaravelRabbitMQ\Services;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use Olekjs\LaravelRabbitMQ\Facades\Queue;
use Illuminate\Foundation\Bus\Dispatchable as DispatchableBus;
use Illuminate\Foundation\Events\Dispatchable as DispatchableEvent;

class ConsumerService
{
    public function __construct(private Connection $connection) { }

    public function consume(): AMQPChannel
    {
        $channel = $this->getChannel();

        $this->declareExchange();

        $queueName = $this->declareQueue();

        include $this->getRoutingPath();
        $queueRouting = Queue::getEndpoints();

        $this->bindQueueRouting($queueName, $queueRouting);

        $callback = $this->getCallback($queueRouting);

        $channel->basic_consume(
            $queueName,
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        return $channel;
    }

    public function getChannel(): AMQPChannel
    {
        return $this->connection->channel();
    }

    public function getExchangeName(): string
    {
        return config('rabbitmq.exchange', 'default');
    }

    public function getRoutingName(): string
    {
        return config('rabbitmq.routing_file', 'queue');
    }

    public function getRoutingPath(): string
    {
        return base_path('routes/' . $this->getRoutingName() . '.php');
    }

    private function declareExchange(): void
    {
        $this->getChannel()->exchange_declare(
            $this->getExchangeName(),
            'direct',
            false,
            false,
            false
        );
    }

    private function declareQueue(): string
    {
        list($queueName) = $this->getChannel()->queue_declare(
            '',
            false,
            false,
            true,
            false
        );

        return $queueName;
    }

    private function bindQueueRouting(string $queueName, array $queueRouting): void
    {
        foreach ($queueRouting as $endpoint => $action) {
            $this->getChannel()->queue_bind($queueName, $this->getExchangeName(), $endpoint);
        }
    }

    private function getCallback(array $queueRouting): Closure
    {
        return function ($message) use ($queueRouting) {
            $action      = $queueRouting[$message->getRoutingKey()];
            $messageBody = $message->getBody();

            if (is_array($action)) {
                list($class, $method) = $action;

                call_user_func([new $class, $method], $messageBody);
            }

            if (is_string($action)) {
                call_user_func(new $action);
            }

            if ($action instanceof Closure) {
                $action();
            }

            if ($this->actionIsDispachable($action)) {
                dispatch($action);
            }
        };
    }

    private function actionIsDispachable(array|string|callable|object $action): bool
    {
        if (!is_object($action)) {
            return false;
        }

        $target   = [DispatchableBus::class, DispatchableEvent::class];
        $haystack = class_uses($action::class);

        if (count(array_intersect($haystack, $target)) > 0) {
            return true;
        }

        return false;
    }
}
