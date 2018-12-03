<?php

declare(strict_types=1);

namespace App\Socket\Connection;

use SplObjectStorage;

/**
 * Class ConnectionStorage
 *
 * @package App\Socket\Connection
 */
final class ConnectionStorage
{
    /**
     * @var SplObjectStorage|WebSocketConnectionInterface[]
     */
    private $connections;

    /**
     * ConnectionStorage constructor.
     */
    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    /**
     * @param WebSocketConnectionInterface $connection
     */
    public function attach(WebSocketConnectionInterface $connection): void
    {
        $this->connections->attach($connection);
    }

    /**
     * @param WebSocketConnectionInterface $connection
     */
    public function detach(WebSocketConnectionInterface $connection): void
    {
        $connection->close();
        $this->connections->detach($connection);
    }

    /**
     * @param WebSocketConnectionInterface $connection
     *
     * @return bool
     */
    public function contains(WebSocketConnectionInterface $connection): bool
    {
        return $this->connections->contains($connection);
    }

    /**
     * @return SplObjectStorage|WebSocketConnectionInterface[]
     */
    public function getStorage(): SplObjectStorage
    {
        return $this->connections;
    }
}