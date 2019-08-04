<?php

declare(strict_types=1);

namespace Nusje2000\Socket\EventSubscriber;

use Nusje2000\Socket\Event\ConnectEvent;
use Nusje2000\Socket\Event\ConnectionAwareEvent;
use Nusje2000\Socket\Event\DataEvent;
use Nusje2000\Socket\Event\DisconnectEvent;
use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Event\HandshakeEvent;
use Nusje2000\Socket\Event\MessageEvent;
use Nusje2000\Socket\Event\SocketEventInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DebugEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->warning(
            sprintf('%s is for debugging only, make sure to not use this in a production environment.', __CLASS__)
        );
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            HandshakeEvent::class => ['logEvent', 255],
            ConnectEvent::class => ['logEvent', 255],
            DisconnectEvent::class => ['logEvent', 255],
            MessageEvent::class => ['logEvent', 255],
            DataEvent::class => ['logEvent', 255],
            FrameEvent::class => ['logEvent', 255],
        ];
    }

    public function logEvent(SocketEventInterface $event): void
    {
        $message = sprintf('received event "%s"', $this->getEventName($event));
        $context = [
        ];

        if ($event instanceof ConnectionAwareEvent) {
            $context['local_address'] = $event->getConnection()->getLocalAddress();
            $context['remote_address'] = $event->getConnection()->getRemoteAddress();
        }

        if ($event instanceof DataEvent) {
            $context['size'] = sprintf('%d bytes', strlen($event->getData()));
        }

        if ($event instanceof FrameEvent) {
            $frame = $event->getFrame();
            $context['final'] = $frame->isFinal() ? 'yes' : 'no';
            $context['opcode'] = $frame->getOpcode()->getValue();
            $context['payload_length'] = $frame->getPayloadLenth();
        }

        if ($event instanceof MessageEvent) {
            $context['message'] = $event->getMessage();
        }

        $this->logger->debug($message, $context);
    }

    private function getEventName(SocketEventInterface $event): string
    {
        $class = get_class($event);
        $parts = explode('\\', $class);

        return end($parts);
    }
}
