<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handshake;

use RuntimeException;

/**
 * Class HandshakeFactory
 *
 * @package Nusje2000\Socket\Handshake
 */
final class HandshakeFactory
{
    private const PREDEFINED_NONSENSE = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
    private const WEBSOCKET_KEY_REGEX = '/Sec-WebSocket-Key: (?<key>[a-z0-9=\/+]+)/i';

    /**
     * @param string $host
     * @param int    $port
     *
     * @param string $request
     *
     * @return string
     * @throws RuntimeException
     */
    public function createOpeningHandshake(string $host, int $port, string $request): string
    {
        $acceptanceKey = $this->getAcceptanceKeyFromRequest($request);
        $location = sprintf('ws://%s:%s', $host, $port);

        $response[] = 'Upgrade: websocket';
        $response[] = 'Connection: Upgrade';
        $response[] = 'WebSocket-Origin: '.$host;
        $response[] = 'WebSocket-Location: '.$location;
        $response[] = 'Sec-WebSocket-Accept: '.$acceptanceKey;

        return $this->createHttpResponse($response);
    }

    /**
     * @param string $request
     *
     * @return string
     * @throws RuntimeException
     */
    private function getAcceptanceKeyFromRequest(string $request): string
    {
        preg_match(self::WEBSOCKET_KEY_REGEX, $request, $match);

        if (isset($match['key'])) {
            return base64_encode(sha1($match['key'].self::PREDEFINED_NONSENSE, true));
        }

        throw new RuntimeException('Cannot create handshake without key in initial request.');
    }

    /**
     * @param array $lines
     *
     * @return string
     */
    private function createHttpResponse(array $lines): string
    {
        array_unshift($lines, 'HTTP/1.1 101 Web Socket Protocol Handshake');

        return implode("\n", $lines)."\n\n";
    }
}