<?php

declare(strict_types=1);

namespace Nusje2000\Socket;

use Nusje2000\Socket\Connection\SocketConnection;
use Nusje2000\Socket\Connection\SocketConnectionCollection;
use Nusje2000\Socket\Event\ConnectEvent;
use Nusje2000\Socket\Frame\Encoder;
use Nusje2000\Socket\Frame\FrameFactory;
use Nusje2000\Socket\Handler\DataHandler;
use Nusje2000\Socket\Handler\DataHandlerInterface;
use Nusje2000\Socket\Handler\HandshakeHandler;
use Nusje2000\Socket\Handler\HandshakeHandlerInterface;
use Nusje2000\Socket\Handshake\HandshakeFactory;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WebSocket implements WebSocketInterface
{
    /**
     * @var Server
     */
    protected $socket;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var SocketConnectionCollection
     */
    protected $connections;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var HandshakeHandlerInterface
     */
    protected $handshakeHandler;

    /**
     * @var DataHandlerInterface
     */
    protected $dataHandler;

    public function __construct(
        LoopInterface $loop,
        string $host,
        int $port,
        EventDispatcherInterface $dispatcher,
        ?LoggerInterface $logger = null
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->loop = $loop;
        $this->dispatcher = $dispatcher;

        $this->socket = new Server(sprintf('%s:%s', $host, $port), $loop);
        $this->connections = new SocketConnectionCollection();
        $this->handshakeHandler = new HandshakeHandler(new HandshakeFactory(), $logger);
        $this->dataHandler = new DataHandler(new FrameFactory(), new Encoder());

        $this->socket->on('connection', function (ConnectionInterface $connection) {
            $this->onConnection($connection);
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

    protected function onConnection(ConnectionInterface $connection): void
    {
        $connection->once('data', function (string $request) use ($connection) {
            $completed = $this->handshakeHandler->handshake($this, $connection, $request);

            if ($completed) {
                $this->onHandshake($connection);
            }
        });
    }

    protected function onHandshake(ConnectionInterface $connection): void
    {
        $webSocketConnection = new SocketConnection($connection);
        $this->connections->append($webSocketConnection);

        $this->dispatcher->dispatch(new ConnectEvent($this, $webSocketConnection));
    }
}
