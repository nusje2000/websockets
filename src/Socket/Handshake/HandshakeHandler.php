<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handshake;

use Nusje2000\Socket\Connection\SocketConnectionInterface;
use Nusje2000\Socket\Exception\HandshakeException;
use Nusje2000\Socket\WebSocketInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class HandshakeHandler
 *
 * @package Nusje2000\Socket\Handler
 */
final class HandshakeHandler implements HandshakeHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HandshakeFactory
     */
    private $handshakeFactory;

    /**
     * HandshakeHandler constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = new NullLogger();
        }

        $this->logger = $logger;
        $this->handshakeFactory = new HandshakeFactory();
    }

    /**
     * @inheritdoc
     */
    public function handshake(WebSocketInterface $socket, SocketConnectionInterface $connection, string $request): bool
    {
        $host = $socket->getHost();
        $port = $socket->getPort();

        try {
            $response = $this->handshakeFactory->createOpeningHandshake($host, $port, $request);
        } catch (HandshakeException $exception) {
            $this->logger->warning(sprintf(
                'Handshake with %s failed (%s).',
                $connection->getRemoteAddress(),
                $exception->getMessage()
            ));

            $connection->close();

            return false;
        }

        $this->logger->info(sprintf(
            'Handshake with %s completed.',
            $connection->getRemoteAddress()
        ));

        $connection->write($response);

        return true;
    }
}
