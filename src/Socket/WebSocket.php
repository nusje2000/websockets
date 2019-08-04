<?php

declare(strict_types=1);

namespace Nusje2000\Socket;

use Nusje2000\Socket\Connection\SocketConnection;
use Nusje2000\Socket\Connection\SocketConnectionCollection;
use Nusje2000\Socket\Event\HandshakeEvent;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WebSocket implements WebSocketInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var SocketConnectionCollection
     */
    private $connections;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        LoopInterface $loop,
        string $host,
        int $port,
        EventDispatcherInterface $dispatcher
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->loop = $loop;
        $this->dispatcher = $dispatcher;

        $this->connections = new SocketConnectionCollection();
        $server = new Server(sprintf('%s:%s', $host, $port), $loop);

        $server->on('connection', function (ConnectionInterface $connection) {
            $this->handleConnection($connection);
        });
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getConnections(): SocketConnectionCollection
    {
        return $this->connections;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    private function handleConnection(ConnectionInterface $baseConnection): void
    {
        $connection = new SocketConnection($baseConnection);

        $connection->once('data', function (string $request) use ($connection) {
            $this->dispatcher->dispatch(new HandshakeEvent($this, $connection, $request));
        });
    }
}
