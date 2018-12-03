<?php

declare(strict_types=1);

namespace App\Socket;

use App\Socket\Connection\ConnectionStorage;
use React\EventLoop\LoopInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface WebSocketInterface
 *
 * @package App\Socket
 */
interface WebSocketInterface
{
    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @return ConnectionStorage
     */
    public function getConnections(): ConnectionStorage;

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface;
}