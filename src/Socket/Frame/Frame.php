<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Frame;

/**
 * Class Frame
 *
 * @package Nusje2000\Socket
 */
final class Frame implements FrameInterface
{
    /**
     * @var int
     */
    private $opcode;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var string|null
     */
    private $maskingKey;

    /**
     * @var bool
     */
    private $finalFragment;

    /**
     * Frame constructor.
     *
     * @param bool   $finalFragment
     * @param int    $opcode
     * @param string $payload
     * @param string $maskingKey
     */
    public function __construct(
        bool $finalFragment,
        int $opcode,
        string $payload,
        ?string $maskingKey
    ) {
        $this->opcode = $opcode;
        $this->payload = $payload;
        $this->maskingKey = $maskingKey;
        $this->finalFragment = $finalFragment;
    }

    /**
     * @return int
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function getPayloadLenth(): int
    {
        if (null === $this->payload) {
            return 0;
        }

        return strlen($this->payload);
    }

    /**
     * @return string
     */
    public function getMaskingKey(): ?string
    {
        return $this->maskingKey;
    }

    /**
     * @return bool
     */
    public function isClosing(): bool
    {
        return OpcodeEnum::CLOSE === $this->opcode;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->finalFragment;
    }

    /**
     * @return bool
     */
    public function isMasked(): bool
    {
        return null !== $this->maskingKey;
    }

    /**
     * @return bool
     */
    public function isControl(): bool
    {
        return OpcodeEnum::isControlCode($this->opcode);
    }

    /**
     * @return bool
     */
    public function isNonControl(): bool
    {
        return OpcodeEnum::isNonControlCode($this->opcode);
    }
}