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
    /**
     * @return int
     */
    public function getOpcode(): int;

    /**
     * @return string|null
     */
    public function getMaskingKey(): ?string;

    /**
     * @return string|null
     */
    public function getPayload(): ?string;

    /**
     * @return int
     */
    public function getPayloadLenth(): int;

    /**
     * @return bool
     */
    public function isFinal(): bool;

    /**
     * @return bool
     */
    public function isMasked(): bool;

    /**
     * @return bool
     */
    public function isClosing(): bool;

    /**
     * @return bool
     */
    public function isControl(): bool;
}
