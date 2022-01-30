<?php

namespace Olekjs\LaravelRabbitMQ\Routing;

use Closure;
use Olekjs\LaravelRabbitMQ\Contracts\EndpointCollectionInterface;

class Queue
{
    private EndpointCollectionInterface $endpoints;

    private null|string $prefix;

    public function __construct()
    {
        $this->endpoints = new EndpointCollection;
        $this->prefix    = null;
    }

    public function listen(string $endpoint, array|string|callable|object $action): self
    {
        $this->endpoints->add($this->prefix . $endpoint, $action);

        return $this;
    }

    public function name(string $prefix): self
    {
        $this->setPrefix($prefix);

        return $this;
    }

    public function group(Closure $endpoints): self
    {
        $endpoints();

        $this->clearPrefix();

        return $this;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function clearPrefix(): self
    {
        $this->prefix = null;

        return $this;
    }

    public function getEndpoints(): array
    {
        return $this->endpoints->getAll();
    }

    public function destroy(): void
    {
        $this->endpoints = new EndpointCollection;
        $this->prefix    = null;
    }
}
