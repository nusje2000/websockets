<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Connection;

use Aeviiq\Collection\AbstractObjectCollection;
use ArrayIterator;

/**
 * @method ArrayIterator|SocketConnectionInterface[] getIterator
 * @method SocketConnectionInterface|null first
 * @method SocketConnectionInterface|null last
 */
final class SocketConnectionCollection extends AbstractObjectCollection
{
    /**
     * @inheritDoc
     */
    protected function allowedInstance(): string
    {
        return SocketConnectionInterface::class;
    }
}
