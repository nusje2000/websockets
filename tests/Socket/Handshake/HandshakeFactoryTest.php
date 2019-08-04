<?php

declare(strict_types=1);

namespace Tests\Nusje2000\Socket\Handshake;

use Nusje2000\Socket\Exception\HandshakeException;
use Nusje2000\Socket\Handshake\HandshakeFactory;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class HandshakeFactoryTest
 *
 * @package Tests\Nusje2000\Socket\Handshake
 */
class HandshakeFactoryTest extends TestCase
{
    /**
     * @var HandshakeFactory
     */
    protected $factory;

    /**
     * @dataProvider handshakeProvider
     *
     * @param string      $host
     * @param int         $port
     * @param string      $request
     * @param string      $response
     * @param string|null $exception
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testCreateOpeningHandshake(
        string $host,
        int $port,
        string $request,
        string $response = null,
        string $exception = null
    ): void {
        $responseHttp = null;
        $requestHttp = file_get_contents(__DIR__ . '/../../Resources/handshake/' . $request);

        if (null !== $response) {
            $responseHttp = file_get_contents(__DIR__ . '/../../Resources/handshake/' . $response);
        }

        if (null !== $exception) {
            $this->expectException($exception);
        }

        $actual = $this->factory->createOpeningHandshake($host, $port, $requestHttp);
        self::assertEquals($responseHttp, $actual);
    }

    /**
     * @return array
     */
    public function handshakeProvider(): array
    {
        return [
            ['127.0.0.1', 1337, 'request_1.http', 'response_1.http'],
            ['some-host', 8000, 'request_2.http', 'response_2.http'],
            ['127.0.0.1', 1337, 'request_3.http', null, HandshakeException::class],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->factory = new HandshakeFactory();
    }
}
