<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Handler;

use Nusje2000\Socket\Handshake\HandshakeFactory;
use Nusje2000\Socket\Logger\LoggerInterface;
use Nusje2000\Socket\Logger\NullLogger;
use Nusje2000\Socket\WebSocketInterface;
use React\Socket\ConnectionInterface;
use RuntimeException;

/**
 * Class HandshakeHandler
 *
 * @package Nusje2000\Socket\Handler
 */
final class HandshakeHandler implements HandshakeHandlerInterface
{
    /**
     * @var HandshakeFactory
     */
    private $factory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * HandshakeHandler constructor.
     *
     * @param HandshakeFactory     $factory
     * @param LoggerInterface|null $logger
     */
    public function __construct(HandshakeFactory $factory, ?LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = new NullLogger();
        }

        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function handshake(WebSocketInterface $socket, ConnectionInterface $connection, string $request): bool
    {
        $host = $socket->getHost();
        $port = $socket->getPort();

        try {
            $response = $this->factory->createOpeningHandshake($host, $port, $request);
        } catch (RuntimeException $exception) {
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