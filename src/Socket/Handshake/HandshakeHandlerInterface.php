<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handshake;

use Nusje2000\Socket\Connection\SocketConnectionInterface;
use Nusje2000\Socket\WebSocketInterface;

interface HandshakeHandlerInterface
{
    public function handshake(WebSocketInterface $socket, SocketConnectionInterface $connection, string $request): bool;
}
