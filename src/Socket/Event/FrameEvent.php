<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\WebSocketConnectionInterface;
use Nusje2000\Socket\Frame\FrameInterface;
use Nusje2000\Socket\WebSocketInterface;

/**
 * Class FrameEvent
 *
 * @package Nusje2000\Socket\Event
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