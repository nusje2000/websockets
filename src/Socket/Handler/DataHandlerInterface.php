<?php

declare(strict_types=1);

namespace App\Socket\Handler;

use App\Socket\Frame\FrameInterface;

/**
 * Class DataHandlerInterface
 *
 * @package App\Socket\Handler
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