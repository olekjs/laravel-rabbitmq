<?php

namespace Olekjs\LaravelRabbitMQ\Routing;

use Countable;
use IteratorAggregate;
use Olekjs\LaravelRabbitMQ\Contracts\EndpointCollectionInterface;
use Traversable;
use ArrayIterator;

class EndpointCollection implements Countable, IteratorAggregate, EndpointCollectionInterface
{
    private array $endpoints;

    public function add(string $endpoint, array|string|callable|object $action): void
    {
        $this->endpoints[$endpoint] = $action;
    }

    public function getAll(): array
    {
        return $this->endpoints;
    }

    public function count(): int
    {
        return count($this->endpoints);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->endpoints);
    }
}
