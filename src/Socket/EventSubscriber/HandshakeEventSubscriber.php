<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\ConnectEvent;
use Nusje2000\Socket\Event\HandshakeEvent;
use Nusje2000\Socket\Handshake\HandshakeHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HandshakeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var HandshakeHandlerInterface
     */
    private $handshakeHandler;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, HandshakeHandlerInterface $handshakeHandler)
    {
        $this->dispatcher = $dispatcher;
        $this->handshakeHandler = $handshakeHandler;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            HandshakeEvent::class => ['handleHandshake'],
        ];
    }

    public function handleHandshake(HandshakeEvent $event): void
    {
        $completed = $this->handshakeHandler->handshake(
            $event->getSocket(),
            $event->getConnection(),
            $event->getRequest()
        );

        if ($completed) {
            $this->dispatcher->dispatch(
                new ConnectEvent(
                    $event->getSocket(),
                    $event->getConnection()
                )
            );

            return;
        }

        $event->getConnection()->close();
    }
}
