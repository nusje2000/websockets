<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handler;

use Nusje2000\Socket\Frame\FrameInterface;

/**
 * Class DataHandlerInterface
 *
 * @package Nusje2000\Socket\Handler
 */
interface DataHandlerInterface
{
    /**
     * Converts raw message into Frame object
     */
    public function convertToFrame(string $data): FrameInterface;

    /**
     * Converts Frame object into raw message
     */
    public function convertToString(FrameInterface $frame): string;
}
