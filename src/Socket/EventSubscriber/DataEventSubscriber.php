<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\DataEvent;
use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Handler\DataHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DataEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataHandler $dataHandler)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->dataHandler = $dataHandler;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DataEvent::class => ['handleData'],
        ];
    }

    public function handleData(DataEvent $event): void
    {
        $frame = $this->dataHandler->convertToFrame($event->getData());

        $this->eventDispatcher->dispatch(new FrameEvent($event->getSocket(), $event->getConnection(), $frame));
    }
}
