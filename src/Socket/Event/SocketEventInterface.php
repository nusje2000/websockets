<?php

declare(strict_types=1);

namespace App\Socket\Event;

use App\Socket\WebSocket;
use App\Socket\WebSocketInterface;

/**
 * Interface SocketEventInterface
 *
 * @package App\Socket\Event
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