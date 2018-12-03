<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\WebSocketInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractSocketEvent
 *
 * @package Nusje2000\Socket\Event
 */
abstract class AbstractSocketEvent extends Event implements SocketEventInterface
{
    /**
     * @var WebSocketInterface
     */
    protected $socket;

    /**
     * ConnectionEvent constructor.
     *
     * @param WebSocketInterface $socket
     */
    public function __construct(WebSocketInterface $socket)
    {
        $this->socket = $socket;
    }

    /**
     * @return WebSocketInterface
     */
    public function getSocket(): WebSocketInterface
    {
        return $this->socket;
    }
}