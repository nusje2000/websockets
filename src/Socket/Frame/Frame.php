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
     * @var OpcodeEnum
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
     * @param bool       $finalFragment
     * @param OpcodeEnum $opcode
     * @param string     $payload
     * @param string     $maskingKey
     */
    public function __construct(
        bool $finalFragment,
        OpcodeEnum $opcode,
        string $payload,
        ?string $maskingKey = null
    ) {
        $this->opcode = $opcode;
        $this->payload = $payload;
        $this->maskingKey = $maskingKey;
        $this->finalFragment = $finalFragment;
    }

    public function getOpcode(): OpcodeEnum
    {
        return $this->opcode;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getPayloadLenth(): int
    {
        if (null === $this->payload) {
            return 0;
        }

        return strlen($this->payload);
    }

    public function getMaskingKey(): ?string
    {
        return $this->maskingKey;
    }

    public function isClosing(): bool
    {
        return OpcodeEnum::CLOSE === $this->opcode;
    }

    public function isFinal(): bool
    {
        return $this->finalFragment;
    }

    public function isMasked(): bool
    {
        return null !== $this->maskingKey;
    }

    public function isControl(): bool
    {
        return OpcodeEnum::isControlCode($this->opcode);
    }

    public function isNonControl(): bool
    {
        return OpcodeEnum::isNonControlCode($this->opcode);
    }
}
