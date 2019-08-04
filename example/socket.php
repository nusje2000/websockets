<?php

declare(strict_types=1);

use Nusje2000\Socket\Enum\OpcodeEnum;
use Nusje2000\Socket\Event\MessageEvent;
use Nusje2000\Socket\EventSubscriber\ConnectionEventSubscriber;
use Nusje2000\Socket\EventSubscriber\DataEventSubscriber;
use Nusje2000\Socket\EventSubscriber\DebugEventSubscriber;
use Nusje2000\Socket\EventSubscriber\FrameEventSubscriber;
use Nusje2000\Socket\EventSubscriber\HandshakeEventSubscriber;
use Nusje2000\Socket\Frame\Frame;
use Nusje2000\Socket\Handler\FrameTransformer;
use Nusje2000\Socket\Handshake\HandshakeHandler;
use Nusje2000\Socket\Logger\DebugLogger;
use Nusje2000\Socket\WebSocket;
use React\EventLoop\Factory as LoopFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

include_once __DIR__ . '/../vendor/autoload.php';

$loop = LoopFactory::create();

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function () use ($loop) {
        $loop->stop();
    });

    pcntl_signal(SIGINT, function () use ($loop) {
        $loop->stop();
    });
}

$dispatcher = new EventDispatcher();
$frameTransformer = new FrameTransformer();
$logger = new DebugLogger();

$dispatcher->addSubscriber(new HandshakeEventSubscriber($dispatcher, new HandshakeHandler()));
$dispatcher->addSubscriber(new ConnectionEventSubscriber($dispatcher));
$dispatcher->addSubscriber(new DataEventSubscriber($dispatcher, $frameTransformer));
$dispatcher->addSubscriber(new FrameEventSubscriber($dispatcher));
$dispatcher->addSubscriber(new DebugEventSubscriber($logger));

$dispatcher->addListener(
    MessageEvent::class,
    function (MessageEvent $event) use ($frameTransformer) {
        $socket = $event->getSocket();
        $connection = $event->getConnection();
        $message = $event->getMessage();

        foreach ($socket->getConnections() as $target) {
            $raw = $frameTransformer->transformToString(new Frame(
                true,
                new OpcodeEnum(OpcodeEnum::TEXT),
                sprintf($target === $connection ? 'you: %s' : 'stranger: %s', $message)
            ));

            $target->write($raw);
        }
    }
);

try {
    new WebSocket($loop, '127.0.0.1', 8001, $dispatcher);

    $loop->futureTick(function () use ($logger) {
        $logger->info('Socket is running.');
    });

    $loop->run();
} catch (Throwable $exception) {
    echo 'Shutdown due to: ' . $exception->getMessage();
    $loop->stop();
}
