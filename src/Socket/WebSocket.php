<?php

declare(strict_types=1);

namespace Nusje2000\Socket;

use Nusje2000\Socket\Connection\ConnectionStorage;
use Nusje2000\Socket\Connection\WebSocketConnection;
use Nusje2000\Socket\Connection\WebSocketConnectionInterface;
use Nusje2000\Socket\Event\ConnectionEvent;
use Nusje2000\Socket\Event\FrameEvent;
use Nusje2000\Socket\Event\SocketEventInterface;
use Nusje2000\Socket\Frame\Encoder;
use Nusje2000\Socket\Frame\FrameFactory;
use Nusje2000\Socket\Frame\FrameInterface;
use Nusje2000\Socket\Handler\DataHandler;
use Nusje2000\Socket\Handler\DataHandlerInterface;
use Nusje2000\Socket\Handler\HandshakeHandler;
use Nusje2000\Socket\Handler\HandshakeHandlerInterface;
use Nusje2000\Socket\Handshake\HandshakeFactory;
use Nusje2000\Socket\Logger\ConsoleLogger;
use InvalidArgumentException;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class WebSocket
 *
 * @package Nusje2000\Socket
 */
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
     * @var ConnectionStorage
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

    /**
     * WebSocket constructor.
     *
     * @param LoopInterface                 $loop
     * @param string                        $host
     * @param int                           $port
     *
     * @param EventDispatcherInterface|null $dispatcher
     *
     * @throws InvalidArgumentException
     */
    public function __construct(LoopInterface $loop, string $host, int $port, EventDispatcherInterface $dispatcher)
    {
        $this->host = $host;
        $this->port = $port;
        $this->loop = $loop;
        $this->dispatcher = $dispatcher;

        $this->socket = new Server(sprintf('%s:%s', $host, $port), $loop);
        $this->connections = new ConnectionStorage();
        $this->handshakeHandler = new HandshakeHandler(new HandshakeFactory(), new ConsoleLogger());
        $this->dataHandler = new DataHandler(new FrameFactory(), new Encoder());

        $this->socket->on('connection', function (ConnectionInterface $connection) {
            $this->onConnection($connection);
        });
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return ConnectionStorage
     */
    public function getConnections(): ConnectionStorage
    {
        return $this->connections;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function onConnection(ConnectionInterface $connection): void
    {
        $connection->once('data', function (string $request) use ($connection) {
            $completed = $this->handshakeHandler->handshake($this, $connection, $request);

            if ($completed) {
                $this->onHandshake($connection);
            }
        });
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @throws InvalidArgumentException
     */
    protected function onHandshake(ConnectionInterface $connection): void
    {
        $webSocketConnection = new WebSocketConnection($connection, $this->dataHandler);
        $this->connections->attach($webSocketConnection);

        $this->dispatcher->dispatch(
            SocketEventInterface::EVENT_CONNECT,
            new ConnectionEvent($this, $webSocketConnection)
        );

        $webSocketConnection->on('frame', function (FrameInterface $frame) use ($webSocketConnection) {
            $this->onFrame($webSocketConnection, $frame);
        });

        $webSocketConnection->on('end', function () use ($webSocketConnection) {
            $this->onClose($webSocketConnection);
        });
    }

    /**
     * @param WebSocketConnectionInterface $connection
     * @param FrameInterface               $frame
     */
    protected function onFrame(WebSocketConnectionInterface $connection, FrameInterface $frame): void
    {
        $this->dispatcher->dispatch(
            SocketEventInterface::EVENT_RECEIVE_FRAME,
            new FrameEvent($this, $connection, $frame)
        );
    }

    /**
     * @param WebSocketConnectionInterface $connection
     */
    protected function onClose(WebSocketConnectionInterface $connection): void
    {
        $this->connections->detach($connection);
        $this->dispatcher->dispatch(
            SocketEventInterface::EVENT_DISCONNECT,
            new ConnectionEvent($this, $connection)
        );
    }
}