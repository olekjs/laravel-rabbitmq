<div align="center">
  <h3 align="center">Laravel RabbitMQ</h3>
  <p align="center">
    A simple package that makes it easy to work with RabbitMQ
  </p>
</div>

## Before you start using

The library was written for a private project, it's not as flexible as you might want.

For today I think that <strong>this package is WIP and I do not recommend production use</strong>.

Have fun!

## Usage
Laravel RabbitMQ has some cool features
<br>

## Producer
You can easily use the `send` method, which takes two parameters. The first is the content of the message, and the second is the endpoint that the Consumer listens to

```php
public function sendNotification(Order $order, ProducerService $service): void
{
    $service->send(
        ['order_id' => $order->id],
        'order.shipped.notification'
    );
}
```

## Consumer
### Define your own routing for queues
Simple file `route/queue.php`

```php
// Define a standard "endpoint"
Queue::listen('cart.created', CartCreated::class);

// Or group it as you prefer
Queue::name('order.')->group(function () {
    Queue::listen('shipped', OrderShipped::class);
    Queue::listen('confirmed', OrderConfirmed::class);
});

```

### Multiple call options

```php
// Pass dispatchable job
Queue::listen('order.confirmed', OrderConfirmedJob::class);

// Pass dispatchable event
Queue::listen('order.confirmed', OrderConfirmedEvent::class);

// Pass class and method as in standard Laravel routing
Queue::listen('order.confirmed', [OrderConfirmed::class, 'sendNotification']);

// You can also pass a invokable class
Queue::listen('order.confirmed', InvokableOrderConfirmed::class);

// You can also use closure for local testing
Queue::listen('order.confirmed', function (string|array $message) {
    return $message; // ['order_id' => 1]
});
```

### Fire the listener very easily
```php
php artisan rabbitmq:listen
```
### Automatic restart of the listener during changes
<img src="https://i.ibb.co/vJkYDZ7/laravel-rabbitmq.png">

## Instalation
### WIP
