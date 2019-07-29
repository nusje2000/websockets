<?php

declare(strict_types=1);

namespace Nusje2000\Socket;

use Nusje2000\Socket\Connection\SocketConnectionCollection;
use React\EventLoop\LoopInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface WebSocketInterface
{
    public function getHost(): string;

    public function getPort(): int;

    public function getConnections(): SocketConnectionCollection;

    public function getEventDispatcher(): EventDispatcherInterface;

    public function getLoop(): LoopInterface;
}
