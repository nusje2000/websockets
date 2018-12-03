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
     * @param string $data
     *
     * @return FrameInterface
     */
    public function convertToFrame(string $data): FrameInterface;

    /**
     * @param FrameInterface $frame
     *
     * @return string
     */
    public function convertToString(FrameInterface $frame): string;
}