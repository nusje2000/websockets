<?php

declare(strict_types=1);

namespace App\Socket\Logger;

/**
 * Class NullLogger
 *
 * @package App\Socket\Logger
 */
final class NullLogger implements LoggerInterface
{
    /**
     * @param string $message
     */
    public function info(string $message): void
    {
    }

    /**
     * @param string $message
     */
    public function warning(string $message): void
    {
    }
}