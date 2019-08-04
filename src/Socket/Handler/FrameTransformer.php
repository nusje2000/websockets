<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handler;

use Nusje2000\Socket\Enum\OpcodeEnum;
use Nusje2000\Socket\Frame\Frame;
use Nusje2000\Socket\Frame\FrameInterface;
use RuntimeException;

class FrameTransformer implements FrameTransformerInterface
{
    public function transformToFrame(string $raw): FrameInterface
    {
        $opcode = $this->getOpcode($raw);
        $payload = $this->getRawPayload($raw);
        $mask = $this->getMask($raw);
        $final = $this->isFinal($raw);
        $length = $this->getPayloadLenth($raw);

        if (null !== $mask) {
            $payload = $this->applyMask($payload, $mask);
        }

        if (strlen($payload) !== $length) {
            throw new RuntimeException('Payload lenth does not equal specified lenth.');
        }

        return new Frame($final, new OpcodeEnum($opcode), $payload, $mask);
    }

    public function transformToString(FrameInterface $frame): string
    {
        $final = $frame->isFinal();
        $opcode = $frame->getOpcode();
        $payload = $frame->getPayload();
        $payloadLenth = $frame->getPayloadLenth();
        $isMasked = $frame->isMasked();

        if ($isMasked) {
            throw new RuntimeException('Masking is not supported for server to client messages.');
        }

        $firstByte = ($final ? 0x80 : 0) | $opcode->getValue();
        $header = null;

        if ($payloadLenth <= 125) {
            $header = pack('CC', $firstByte, $payloadLenth);
        }

        if ($payloadLenth > 125 && $payloadLenth < 65536) {
            $header = pack('CCn', $firstByte, 126, $payloadLenth);
        }

        if ($payloadLenth >= 65536) {
            $header = pack('CCP', $firstByte, 127, $payloadLenth);
        }

        return $header . $payload;
    }

    /**
     * @param string $raw
     *
     * @return int
     */
    private function getOpcode(string $raw): int
    {
        $bin = $this->convertToBinaryString($raw[0]);
        $opcodeBin = substr($bin, 4, 4);

        return bindec($opcodeBin);
    }

    /**
     * @param string $raw
     *
     * @return string
     * @throws RuntimeException
     */
    private function getRawPayload(string $raw): string
    {
        $baseLenth = $this->getBasePayloadLenth($raw);

        if ($baseLenth < 126) {
            return substr($raw, 6);
        }

        if (126 === $baseLenth) {
            return substr($raw, 8);
        }

        if (127 === $baseLenth) {
            return substr($raw, 14);
        }

        throw new RuntimeException('This should not happen, you should investigate.');
    }

    /**
     * @param string $raw
     *
     * @return int
     */
    private function getBasePayloadLenth(string $raw): int
    {
        $binary = $this->convertToBinaryString($raw[1]);

        return bindec(substr($binary, 1, 7));
    }

    /**
     * @param string $raw
     *
     * @return int
     * @throws RuntimeException
     */
    private function getPayloadLenth(string $raw): int
    {
        $length = $this->getBasePayloadLenth($raw);

        if ($length < 126) {
            return $length;
        }

        $additionalLenth = null;

        if (126 === $length) {
            $additionalLenth = substr($raw, 2, 2);
        }

        if (127 === $length) {
            $additionalLenth = substr($raw, 2, 8);
        }

        if (null === $additionalLenth) {
            throw new RuntimeException('This should not happen, you should investigate.');
        }

        $binary = $this->convertToBinaryString($additionalLenth);

        return bindec($binary);
    }

    /**
     * Returns null when no masking is applied
     *
     * @param string $raw
     *
     * @return string|null
     * @throws RuntimeException
     */
    private function getMask(string $raw): ?string
    {
        $binary = $this->convertToBinaryString($raw[1]);
        $isMasked = bindec($binary[0]);

        if (0 === $isMasked) {
            return null;
        }

        $baseLenth = $this->getBasePayloadLenth($raw);

        if ($baseLenth < 126) {
            return substr($raw, 2, 4);
        }

        if (126 === $baseLenth) {
            return substr($raw, 4, 4);
        }

        if (127 === $baseLenth) {
            return substr($raw, 10, 4);
        }

        throw new RuntimeException('This should not happen, you should investigate.');
    }

    /**
     * @param string $payload
     * @param string $mask
     *
     * @return string
     */
    private function applyMask(string $payload, string $mask): string
    {
        $message = '';
        $length = strlen($payload);

        for ($index = 0; $index < $length; ++$index) {
            $maskIndex = $index % 4;
            $message .= $payload[$index] ^ $mask[$maskIndex];
        }

        return $message;
    }

    /**
     * @param string $raw
     *
     * @return bool
     */
    private function isFinal(string $raw): bool
    {
        $binary = $this->convertToBinaryString($raw[0]);
        $isFinal = bindec($binary[0]);

        return 1 === $isFinal;
    }

    /**
     * @param string $bytes
     *
     * @return string
     */
    private function convertToBinaryString(string $bytes): string
    {
        $unpacked = unpack('H*', $bytes);

        return base_convert(reset($unpacked), 16, 2);
    }
}
