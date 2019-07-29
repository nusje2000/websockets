<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\SocketConnectionInterface;
use Nusje2000\Socket\WebSocketInterface;

final class DisconnectEvent extends AbstractSocketEvent implements ConnectionAwareEvent
{
    /**
     * @var SocketConnectionInterface
     */
    private $connection;

    public function __construct(WebSocketInterface $socket, SocketConnectionInterface $connection)
    {
        parent::__construct($socket);
        $this->connection = $connection;
    }

    public function getConnection(): SocketConnectionInterface
    {
        return $this->connection;
    }
}
