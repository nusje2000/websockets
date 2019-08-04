<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\SocketConnectionInterface;
use Nusje2000\Socket\WebSocketInterface;

final class HandshakeEvent extends AbstractSocketEvent implements ConnectionAwareEvent
{
    /**
     * @var SocketConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $request;

    public function __construct(
        WebSocketInterface $socket,
        SocketConnectionInterface $connection,
        string $request
    ) {
        parent::__construct($socket);
        $this->connection = $connection;
        $this->request = $request;
    }

    public function getConnection(): SocketConnectionInterface
    {
        return $this->connection;
    }

    public function getRequest(): string
    {
        return $this->request;
    }
}
