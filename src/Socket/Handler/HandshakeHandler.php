<?php

declare(strict_types=1);

namespace App\Socket\Handler;

use App\Socket\Handshake\HandshakeFactory;
use App\Socket\Logger\LoggerInterface;
use App\Socket\Logger\NullLogger;
use App\Socket\WebSocketInterface;
use React\Socket\ConnectionInterface;
use RuntimeException;

/**
 * Class HandshakeHandler
 *
 * @package App\Socket\Handler
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