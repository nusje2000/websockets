<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handler;

use Nusje2000\Socket\Frame\FrameInterface;

interface FrameTransformerInterface
{
    public function transformToFrame(string $raw): FrameInterface;

    public function transformToString(FrameInterface $frame): string;
}
