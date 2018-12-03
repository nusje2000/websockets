<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\WebSocket;
use Nusje2000\Socket\WebSocketInterface;

/**
 * Interface SocketEventInterface
 *
 * @package Nusje2000\Socket\Event
 */
interface SocketEventInterface
{
    const EVENT_CONNECT = 'socket.connect';
    const EVENT_DISCONNECT = 'socket.disconnect';
    const EVENT_RECEIVE_FRAME = 'socket.frame.receive';
    const EVENT_RECEIVE_MESSAGE = 'socket.message.receive';

    /**
     * @return WebSocket
     */
    public function getSocket(): WebSocketInterface;
}