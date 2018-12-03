<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Event\MessageEvent;
use Nusje2000\Socket\Event\SocketEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FrameEventSubscriber
 *
 * @package Nusje2000\Socket\EventSubscriber
 */
final class FrameEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * SocketEventSubscriber constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SocketEventInterface::EVENT_RECEIVE_FRAME => [
                ['receiveFrame'],
            ],
        ];
    }

    /**
     * @param FrameEvent $event
     *
     * @return void
     */
    public function receiveFrame(FrameEvent $event): void
    {
        $socket = $event->getSocket();
        $connection = $event->getConnection();
        $frame = $event->getFrame();

        if (!$frame->isFinal() || $frame->isControl()) {
            return;
        }

        $message = $frame->getPayload();

        if (null !== $message) {
            $this->dispatcher->dispatch(
                SocketEventInterface::EVENT_RECEIVE_MESSAGE,
                new MessageEvent($socket, $connection, $frame, $message)
            );
        }
    }
}