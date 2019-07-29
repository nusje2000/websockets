<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\SocketConnectionInterface;
use Nusje2000\Socket\WebSocketInterface;

final class DataEvent extends AbstractSocketEvent implements ConnectionAwareEvent
{
    /**
     * @var SocketConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $data;

    public function __construct(
        WebSocketInterface $socket,
        SocketConnectionInterface $connection,
        string $data
    ) {
        parent::__construct($socket);
        $this->connection = $connection;
        $this->data = $data;
    }

    public function getConnection(): SocketConnectionInterface
    {
        return $this->connection;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
