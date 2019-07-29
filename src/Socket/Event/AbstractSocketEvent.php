<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\WebSocketInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractSocketEvent extends Event implements SocketEventInterface
{
    /**
     * @var WebSocketInterface
     */
    protected $socket;

    public function __construct(WebSocketInterface $socket)
    {
        $this->socket = $socket;
    }

    public function getSocket(): WebSocketInterface
    {
        return $this->socket;
    }
}
