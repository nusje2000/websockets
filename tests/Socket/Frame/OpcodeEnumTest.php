<?php

declare(strict_types=1);

namespace Tests\Nusje2000\Socket\Frame;

use Nusje2000\Socket\Enum\OpcodeEnum;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class OpcodeEnumTest
 *
 * @package Tests\Socket\Frame
 */
class OpcodeEnumTest extends TestCase
{
    /**
     * @dataProvider controlCodeProvider
     *
     * @param int  $code
     * @param bool $expect
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testIsControlCode(int $code, bool $expect): void
    {
        $opcode = new OpcodeEnum($code);
        self::assertEquals($expect, $opcode->isControlCode());
    }

    /**
     * @dataProvider nonControlCodeProvider
     *
     * @param int  $code
     * @param bool $expect
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testIsNonControlCode(int $code, bool $expect): void
    {
        $opcode = new OpcodeEnum($code);
        self::assertEquals($expect, $opcode->isNonControlCode());
    }

    /**
     * @dataProvider validProvider
     *
     * @param int  $code
     * @param bool $expect
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testIsValid(int $code, bool $expect): void
    {
        self::assertEquals($expect, OpcodeEnum::isValid($code));
    }

    /**
     * @return array
     */
    public function controlCodeProvider(): array
    {
        return [
            [0, true],
            [1, false],
            [2, false],
            [3, false],
            [4, false],
            [5, false],
            [6, false],
            [7, false],
            [8, true],
            [9, true],
            [10, true],
            [11, true],
            [12, true],
            [13, true],
            [14, true],
            [15, true],
        ];
    }

    /**
     * @return array
     */
    public function nonControlCodeProvider(): array
    {
        return [
            [0, false],
            [1, true],
            [2, true],
            [3, true],
            [4, true],
            [5, true],
            [6, true],
            [7, true],
            [8, false],
            [9, false],
            [10, false],
            [11, false],
            [12, false],
            [13, false],
            [14, false],
            [15, false],
        ];
    }

    /**
     * @return array
     */
    public function validProvider(): array
    {
        return [
            [-1, false],
            [0, true],
            [1, true],
            [2, true],
            [3, true],
            [4, true],
            [5, true],
            [6, true],
            [7, true],
            [8, true],
            [9, true],
            [10, true],
            [11, true],
            [12, true],
            [13, true],
            [14, true],
            [15, true],
            [20, false],
            [100, false],
        ];
    }
}
