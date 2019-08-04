<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Event\MessageEvent;

use Nusje2000\Socket\Enum\OpcodeEnum;
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

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FrameEvent::class => ['receiveFrame'],
        ];
    }

    /**
     * @param FrameEvent $event
     *
     * @return void
     */
    public function receiveFrame(FrameEvent $event): void
    {
        $frame = $event->getFrame();

        if (!$frame->getOpcode()->equals(new OpcodeEnum(OpcodeEnum::TEXT))) {
            return;
        }

        $message = $frame->getPayload();

        if (null !== $message) {
            $messageEvent = new MessageEvent($event->getSocket(), $event->getConnection(), $message);
            $this->dispatcher->dispatch($messageEvent);
        }
    }
}
