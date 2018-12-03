<?php

declare(strict_types=1);

use Nusje2000\Socket\Event\MessageEvent;
use Nusje2000\Socket\Event\SocketEventInterface;
use Nusje2000\Socket\EventSubscriber\FrameEventSubscriber;
use Nusje2000\Socket\Frame\FrameFactory;
use Nusje2000\Socket\Frame\OpcodeEnum;
use Nusje2000\Socket\Logger\ConsoleLogger;
use Nusje2000\Socket\WebSocket;
use React\EventLoop\Factory as LoopFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

include_once __DIR__.'/../vendor/autoload.php';

$loop = LoopFactory::create();

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function () use ($loop) {
        $loop->stop();
    });

    pcntl_signal(SIGINT, function () use ($loop) {
        $loop->stop();
    });
}

$logger = new ConsoleLogger();
$dispatcher = new EventDispatcher();
$frameFactory = new FrameFactory();

$dispatcher->addSubscriber(new FrameEventSubscriber($dispatcher));

$dispatcher->addListener(
    SocketEventInterface::EVENT_RECEIVE_MESSAGE,
    function (MessageEvent $event) use ($frameFactory) {
        $socket = $event->getSocket();
        $connection = $event->getConnection();
        $message = $event->getMessage();

        foreach ($socket->getConnections()->getStorage() as $target) {
            $patern = $target === $connection ? 'you: %s' : 'stranger: %s';
            $frame = $frameFactory->createFrame(true, OpcodeEnum::TEXT, sprintf($patern, $message));
            $target->write($frame);
        }
    }
);

try {
    new WebSocket($loop, '127.0.0.1', 8001, $dispatcher);
    $loop->run();
} catch (Throwable $exception) {
    echo 'Shutdown due to: '.$exception->getMessage();
    $loop->stop();
}
