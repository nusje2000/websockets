<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Frame;

use RuntimeException;

/**
 * Class Encoder
 *
 * @package Nusje2000\Socket
 */
final class Encoder
{
    /**
     * @param FrameInterface $frame
     *
     * @return string
     * @throws RuntimeException
     */
    public function encode(FrameInterface $frame): string
    {
        $final = $frame->isFinal();
        $opcode = $frame->getOpcode();
        $payload = $frame->getPayload();
        $payloadLenth = $frame->getPayloadLenth();
        $isMasked = $frame->isMasked();

        if ($isMasked) {
            throw new RuntimeException('Masking is not supported for server to client messages.');
        }

        $firstByte = ($final ? 0x80 : 0) | $opcode;
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

        return $header.$payload;
    }
}