<?php

declare(strict_types=1);

namespace App\Socket\Event;

use App\Socket\Connection\WebSocketConnectionInterface;
use App\Socket\Frame\FrameInterface;
use App\Socket\WebSocketInterface;

/**
 * Class MessageEvent
 *
 * @package App\Socket\Event
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