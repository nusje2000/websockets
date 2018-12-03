<?php

declare(strict_types=1);

namespace App\Socket\Connection;

use App\Socket\Frame\FrameInterface;
use App\Socket\Handler\DataHandler;
use App\Socket\Handler\DataHandlerInterface;
use Evenement\EventEmitter;
use InvalidArgumentException;
use React\Socket\ConnectionInterface;
use React\Stream\Util;
use RuntimeException;

/**
 * Class WebSocketConnection
 *
 * @package App\Socket\Connection
 */
final class WebSocketConnection extends EventEmitter implements WebSocketConnectionInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * WebSocketConnection constructor.
     *
     * @param ConnectionInterface  $connection
     * @param DataHandlerInterface $dataHandler
     */
    public function __construct(ConnectionInterface $connection, DataHandlerInterface $dataHandler)
    {
        $this->connection = $connection;
        $this->dataHandler = $dataHandler;
        Util::forwardEvents($this->connection, $this, ['data', 'end', 'error', 'close', 'pipe', 'drain']);

        $this->connection->on('data', function (string $data) {
            $this->onData($data);
        });
    }

    /**
     * @param FrameInterface $frame
     *
     * @throws RuntimeException
     */
    public function write(FrameInterface $frame): void
    {
        $this->writeRaw($this->dataHandler->convertToString($frame));
    }

    /**
     * @param string $data
     */
    public function writeRaw(string $data): void
    {
        $this->connection->write($data);
    }

    /**
     * Close the connection
     *
     * @return void
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * @param string $data
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    private function onData(string $data)
    {
        $frame = $this->dataHandler->convertToFrame($data);

        $this->emit('frame', [$frame]);
    }
}