<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Frame;

/**
 * Class OpcodeEnum
 *
 * @package Nusje2000\Socket\Frame
 */
final class OpcodeEnum
{
    public CONST CONTINUE = 0x0;
    public CONST TEXT = 0x1;
    public CONST BIN = 0x2;
    public CONST OPCODE_3 = 0x3;
    public CONST OPCODE_4 = 0x4;
    public CONST OPCODE_5 = 0x5;
    public CONST OPCODE_6 = 0x6;
    public CONST OPCODE_7 = 0x7;
    public CONST CLOSE = 0x8;
    public CONST PING = 0x9;
    public CONST PONG = 0xA;
    public CONST OPCODE_11 = 0xB;
    public CONST OPCODE_12 = 0xC;
    public CONST OPCODE_13 = 0xD;
    public CONST OPCODE_14 = 0xE;
    public CONST OPCODE_15 = 0xF;

    /**
     * @return array
     */
    public static function getControlCodes(): array
    {
        return [
            self::CONTINUE,
            self::CLOSE,
            self::PING,
            self::PONG,
            self::OPCODE_11,
            self::OPCODE_12,
            self::OPCODE_13,
            self::OPCODE_14,
            self::OPCODE_15,
        ];
    }

    /**
     * @param int $opcode
     *
     * @return bool
     */
    public static function isControlCode(int $opcode): bool
    {
        return in_array($opcode, self::getControlCodes(), true);
    }

    /**
     * @return array
     */
    public static function getNonControlCodes(): array
    {
        return [
            self::TEXT,
            self::BIN,
            self::OPCODE_3,
            self::OPCODE_4,
            self::OPCODE_5,
            self::OPCODE_6,
            self::OPCODE_7,
        ];
    }

    /**
     * @param int $opcode
     *
     * @return bool
     */
    public static function isNonControlCode(int $opcode): bool
    {
        return in_array($opcode, self::getNonControlCodes(), true);
    }

    /**
     * @param int $opcode
     *
     * @return bool
     */
    public static function isValid(int $opcode): bool
    {
        return $opcode >= self::CONTINUE && $opcode <= self::OPCODE_15;
    }
}
