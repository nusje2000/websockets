<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Connection;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\Util;

final class SocketConnection extends EventEmitter implements SocketConnectionInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        Util::forwardEvents($this->connection, $this, ['data', 'end', 'error', 'close', 'pipe', 'drain']);
    }

    public function getRemoteAddress(): string
    {
        return $this->connection->getRemoteAddress();
    }

    public function getLocalAddress(): string
    {
        return $this->connection->getLocalAddress();
    }

    public function write(string $data): void
    {
        $this->connection->write($data);
    }

    public function close(): void
    {
        $this->connection->close();
    }
}
