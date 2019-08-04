<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Exception;

use LogicException;

class HandshakeException extends LogicException
{
    public static function missingWebSocketKey(): self
    {
        return new static('Cannot create handshake without key in initial request.');
    }
}
