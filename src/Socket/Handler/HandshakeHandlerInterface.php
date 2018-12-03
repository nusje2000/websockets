<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handler;

use Nusje2000\Socket\WebSocketInterface;
use React\Socket\ConnectionInterface;

/**
 * Interface HandshakeHandlerInterface
 *
 * @package Nusje2000\Socket\Handler
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