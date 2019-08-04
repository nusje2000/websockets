<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\DataEvent;
use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Handler\FrameTransformerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DataEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var FrameTransformerInterface
     */
    private $frameTransformer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, FrameTransformerInterface $frameTransformer)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->frameTransformer = $frameTransformer;
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
        $this->eventDispatcher->dispatch(
            new FrameEvent(
                $event->getSocket(),
                $event->getConnection(),
                $this->frameTransformer->transformToFrame($event->getData())
            )
        );
    }
}
