<?php

declare(strict_types=1);

namespace App\Socket\Event;

use App\Socket\Connection\WebSocketConnectionInterface;
use App\Socket\WebSocketInterface;

/**
 * Class ConnectionEvent
 *
 * @package App\Socket\Event
 */
final class ConnectionEvent extends AbstractSocketEvent
{
    /**
     * @var WebSocketConnectionInterface
     */
    protected $connection;

    /**
     * ConnectionEvent constructor.
     *
     * @param WebSocketInterface           $socket
     * @param WebSocketConnectionInterface $connection
     */
    public function __construct(WebSocketInterface $socket, WebSocketConnectionInterface $connection)
    {
        parent::__construct($socket);
        $this->connection = $connection;
    }

    /**
     * @return WebSocketConnectionInterface
     */
    public function getConnection(): WebSocketConnectionInterface
    {
        return $this->connection;
    }
}