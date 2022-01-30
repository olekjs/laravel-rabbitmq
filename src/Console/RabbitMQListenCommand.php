<?php

namespace Olekjs\LaravelRabbitMQ\Console;

use Illuminate\Console\Command;
use PhpAmqpLib\Channel\AMQPChannel;
use Olekjs\LaravelRabbitMQ\Facades\Queue;
use Olekjs\LaravelRabbitMQ\Services\ConsumerService;

class RabbitMQListenCommand extends Command
{
    protected $signature = 'rabbitmq:listen';

    protected $description = 'Run RabbitMQ worker';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(ConsumerService $consumer): int
    {
        $channel = $consumer->consume();

        if ($this->appIsUnderDevelopment()) {
            $this->listenAndCheckingForChanges($channel, $consumer);
        }

        $this->listen($channel);

        return 0;
    }

    protected function listen(AMQPChannel $channel): void
    {
        while ($channel->is_open()) {
            $channel->wait();
        }
    }

    protected function listenAndCheckingForChanges(AMQPChannel $channel, ConsumerService $consumer): int
    {
        while ($channel->is_open()) {
            if ($this->routingHasBeenChanged($consumer)) {
                Queue::destroy();

                return $this->handle($consumer);
            }

            $channel->wait(null, true);

            usleep(1000 * 1000);
        }
    }

    protected function routingHasBeenChanged(ConsumerService $consumer): bool
    {
        $routingFile = $consumer->getRoutingPath();
        $hasRouting  = file_exists($routingFile);

        $routingLastModified = $hasRouting
            ? filemtime($routingFile)
            : now()->addDays(30)->getTimestamp();

        if ($hasRouting) {
            clearstatcache(false, $routingFile);
        }

        if ($hasRouting && filemtime($routingFile) > $routingLastModified) {
            $this->comment('Routing modified. Restarting listener...');

            return true;
        }

        return false;
    }

    private function appIsUnderDevelopment(): bool
    {
        return $this->laravel->environment('local');
    }
}
