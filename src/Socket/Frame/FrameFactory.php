<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Frame;

use RuntimeException;

/**
 * Class FrameFactory
 *
 * @package Nusje2000\Socket
 */
final class FrameFactory
{
    /**
     * @param string $data
     *
     * @return Frame
     * @throws RuntimeException
     */
    public function createFrameFromData(string $data): Frame
    {
        $opcode = $this->getOpcode($data);
        $payload = $this->getRawPayload($data);
        $mask = $this->getMask($data);
        $final = $this->isFinal($data);
        $length = $this->getPayloadLenth($data);

        if (null !== $mask) {
            $payload = $this->applyMask($payload, $mask);
        }

        if (strlen($payload) !== $length) {
            throw new RuntimeException('Payload lenth does not equal specified lenth.');
        }

        return new Frame($final, new OpcodeEnum($opcode), $payload, $mask);
    }

    /**
     * @param string $data
     *
     * @return int
     */
    private function getOpcode(string $data): int
    {
        $bin = $this->convertToBinaryString($data[0]);
        $opcodeBin = substr($bin, 4, 4);

        return bindec($opcodeBin);
    }

    /**
     * @param string $data
     *
     * @return string
     * @throws RuntimeException
     */
    private function getRawPayload(string $data): string
    {
        $baseLenth = $this->getBasePayloadLenth($data);

        if ($baseLenth < 126) {
            return substr($data, 6);
        }

        if (126 === $baseLenth) {
            return substr($data, 8);
        }

        if (127 === $baseLenth) {
            return substr($data, 14);
        }

        throw new RuntimeException('This should not happen, you should investigate.');
    }

    /**
     * @param string $data
     *
     * @return int
     */
    private function getBasePayloadLenth(string $data): int
    {
        $binary = $this->convertToBinaryString($data[1]);

        return bindec(substr($binary, 1, 7));
    }

    /**
     * @param string $data
     *
     * @return int
     * @throws RuntimeException
     */
    private function getPayloadLenth(string $data): int
    {
        $length = $this->getBasePayloadLenth($data);

        if ($length < 126) {
            return $length;
        }

        $additionalLenth = null;

        if (126 === $length) {
            $additionalLenth = substr($data, 2, 2);
        }

        if (127 === $length) {
            $additionalLenth = substr($data, 2, 8);
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
     * @param string $data
     *
     * @return string|null
     * @throws RuntimeException
     */
    private function getMask(string $data): ?string
    {
        $binary = $this->convertToBinaryString($data[1]);
        $isMasked = bindec($binary[0]);

        if (0 === $isMasked) {
            return null;
        }

        $baseLenth = $this->getBasePayloadLenth($data);

        if ($baseLenth < 126) {
            return substr($data, 2, 4);
        }

        if (126 === $baseLenth) {
            return substr($data, 4, 4);
        }

        if (127 === $baseLenth) {
            return substr($data, 10, 4);
        }

        throw new RuntimeException('This should not happen, you should investigate.');
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
     * @param string $data
     *
     * @return bool
     */
    private function isFinal(string $data): bool
    {
        $binary = $this->convertToBinaryString($data[0]);
        $isFinal = bindec($binary[0]);

        return 1 === $isFinal;
    }
}
