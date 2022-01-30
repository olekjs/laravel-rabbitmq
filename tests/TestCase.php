<?php

namespace Olekjs\LaravelRabbitMQ\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Olekjs\LaravelRabbitMQ\LaravelRabbitMQServiceProvider;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelRabbitMQServiceProvider::class,
        ];
    }
}
