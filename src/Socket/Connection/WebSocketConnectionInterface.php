<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Connection;

use Nusje2000\Socket\Frame\FrameInterface;
use Evenement\EventEmitterInterface;

/**
 * Interface WebSocketConnectionInterface
 *
 * @package Nusje2000\Socket\Connection
 */
interface WebSocketConnectionInterface extends EventEmitterInterface
{
    /**
     * @param FrameInterface $frame
     */
    public function write(FrameInterface $frame): void;

    /**
     * @param string $data
     */
    public function writeRaw(string $data): void;

    /**
     * Close the connection
     *
     * @return void
     */
    public function close(): void;
}