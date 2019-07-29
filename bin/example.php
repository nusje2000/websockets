<?php

declare(strict_types=1);

use Nusje2000\Socket\Event\MessageEvent;
use Nusje2000\Socket\EventSubscriber\ConnectionEventSubscriber;
use Nusje2000\Socket\EventSubscriber\DataEventSubscriber;
use Nusje2000\Socket\EventSubscriber\DebugEventSubscriber;
use Nusje2000\Socket\EventSubscriber\FrameEventSubscriber;
use Nusje2000\Socket\Frame\Encoder;
use Nusje2000\Socket\Frame\Frame;
use Nusje2000\Socket\Frame\FrameFactory;
use Nusje2000\Socket\Frame\OpcodeEnum;
use Nusje2000\Socket\Handler\DataHandler;
use Nusje2000\Socket\Logger\ConsoleLogger;
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
$dataHandler = new DataHandler(new FrameFactory(), new Encoder());

$dispatcher->addSubscriber(new ConnectionEventSubscriber($dispatcher));
$dispatcher->addSubscriber(new DataEventSubscriber($dispatcher, $dataHandler));
$dispatcher->addSubscriber(new FrameEventSubscriber($dispatcher));

$dispatcher->addListener(
    MessageEvent::class,
    function (MessageEvent $event) use ($dataHandler) {
        $socket = $event->getSocket();
        $connection = $event->getConnection();
        $message = $event->getMessage();

        foreach ($socket->getConnections() as $target) {
            $patern = $target === $connection ? 'you: %s' : 'stranger: %s';
            $raw = $dataHandler->convertToString(new Frame(
                true,
                new OpcodeEnum(OpcodeEnum::TEXT),
                sprintf($patern, $message)
            ));
            $target->write($raw);
        }
    }
);

$logger = new ConsoleLogger();
$dispatcher->addSubscriber(new DebugEventSubscriber($logger));

try {
    new WebSocket($loop, '127.0.0.1', 8001, $dispatcher, $logger);

    $loop->futureTick(function () use ($logger) {
        $logger->info('Socket is running.');
    });

    $loop->run();
} catch (Throwable $exception) {
    echo 'Shutdown due to: ' . $exception->getMessage();
    $loop->stop();
}
