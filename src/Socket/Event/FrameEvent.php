<?php

declare(strict_types=1);

namespace App\Socket\Event;

use App\Socket\Connection\WebSocketConnectionInterface;
use App\Socket\Frame\FrameInterface;
use App\Socket\WebSocketInterface;

/**
 * Class FrameEvent
 *
 * @package App\Socket\Event
 */
final class FrameEvent extends AbstractSocketEvent
{
    /**
     * @var WebSocketConnectionInterface
     */
    protected $connection;

    /**
     * @var FrameInterface
     */
    protected $frame;

    /**
     * FrameEvent constructor.
     *
     * @param WebSocketInterface           $socket
     * @param WebSocketConnectionInterface $connection
     * @param FrameInterface               $frame
     */
    public function __construct(
        WebSocketInterface $socket,
        WebSocketConnectionInterface $connection,
        FrameInterface $frame
    ) {
        parent::__construct($socket);
        $this->frame = $frame;
        $this->connection = $connection;
    }

    /**
     * @return WebSocketConnectionInterface
     */
    public function getConnection(): WebSocketConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @return FrameInterface
     */
    public function getFrame(): FrameInterface
    {
        return $this->frame;
    }
}