<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Frame;

/**
 * Interface FrameInterface
 *
 * @package Nusje2000\Socket\Frame
 */
interface FrameInterface
{
    public function getOpcode(): OpcodeEnum;

    public function getMaskingKey(): ?string;

    public function getPayload(): ?string;

    public function getPayloadLenth(): int;

    public function isFinal(): bool;

    public function isMasked(): bool;

    public function isClosing(): bool;

    public function isControl(): bool;
}
