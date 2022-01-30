<?php

namespace Olekjs\LaravelRabbitMQ\Services;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class ProducerService
{
    public function __construct(private Connection $connection) { }

    public function send(string $message, string $endpoint): void
    {
        $this->getChannel()->exchange_declare(
            $this->getExchangeName(),
            'direct',
            false,
            false,
            false
        );

        $queueMessage = new AMQPMessage($message);

        $this->getChannel()->basic_publish($queueMessage, $this->getExchangeName(), $endpoint);

        $this->closeConnection();
    }

    private function closeConnection(): void
    {
        $this->getChannel()->close();
        $this->connection->close();
    }


    private function getChannel(): AMQPChannel
    {
        return $this->connection->channel();
    }

    private function getExchangeName(): string
    {
        return config('rabbitmq.exchange', 'default');
    }
}
