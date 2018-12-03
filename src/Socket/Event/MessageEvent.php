<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Event;

use Nusje2000\Socket\Connection\WebSocketConnectionInterface;
use Nusje2000\Socket\Frame\FrameInterface;
use Nusje2000\Socket\WebSocketInterface;

/**
 * Class MessageEvent
 *
 * @package Nusje2000\Socket\Event
 */
final class MessageEvent extends AbstractSocketEvent
{
    /**
     * @var WebSocketConnectionInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $message;

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
     * @param string                       $message
     */
    public function __construct(
        WebSocketInterface $socket,
        WebSocketConnectionInterface $connection,
        FrameInterface $frame,
        string $message
    ) {
        parent::__construct($socket);
        $this->connection = $connection;
        $this->frame = $frame;
        $this->message = $message;
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

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}