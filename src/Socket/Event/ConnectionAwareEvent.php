<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\SocketConnectionInterface;

interface ConnectionAwareEvent
{
    public function getConnection(): SocketConnectionInterface;
}
