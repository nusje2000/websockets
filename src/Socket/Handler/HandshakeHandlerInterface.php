<?php

declare(strict_types=1);

namespace App\Socket\Handler;

use App\Socket\WebSocketInterface;
use React\Socket\ConnectionInterface;

/**
 * Interface HandshakeHandlerInterface
 *
 * @package App\Socket\Handler
 */
interface HandshakeHandlerInterface
{
    /**
     * @param WebSocketInterface  $socket
     * @param ConnectionInterface $connection
     * @param string              $request
     *
     * @return bool
     */
    public function handshake(WebSocketInterface $socket, ConnectionInterface $connection, string $request): bool;
}