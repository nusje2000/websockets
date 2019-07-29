<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Connection;

use Evenement\EventEmitterInterface;

interface SocketConnectionInterface extends EventEmitterInterface
{
    public function write(string $data): void;

    public function close(): void;

    public function getRemoteAddress(): string;

    public function getLocalAddress(): string;
}
