<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\ConnectEvent;
use Nusje2000\Socket\Event\DataEvent;
use Nusje2000\Socket\Event\DisconnectEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConnectionEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConnectEvent::class => ['handleConnection'],
            DisconnectEvent::class => ['handleDisconnect'],
        ];
    }

    public function handleConnection(ConnectEvent $event): void
    {
        $socket = $event->getSocket();
        $connection = $event->getConnection();

        $socket->getConnections()->append($connection);

        $connection->on('data', function (string $data) use ($socket, $connection) {
            $this->dispatcher->dispatch(new DataEvent($socket, $connection, $data));
        });

        $connection->on('close', function () use ($socket, $connection) {
            $this->dispatcher->dispatch(new DisconnectEvent($socket, $connection));
        });

        $connection->on('end', function () use ($socket, $connection) {
            $this->dispatcher->dispatch(new DisconnectEvent($socket, $connection));
        });
    }

    public function handleDisconnect(DisconnectEvent $event): void
    {
        $connections = $event->getSocket()->getConnections();
        $connection = $event->getConnection();

        if ($connections->contains($connection)) {
            $connections->remove($connection);
        }
    }
}
