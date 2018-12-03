<?php

declare(strict_types=1);

namespace App\Socket\Logger;

/**
 * Interface LoggerInterface
 *
 * @package App\Socket\Logger
 */
interface LoggerInterface
{
    /**
     * @param string $message
     */
    public function info(string $message): void;
    /**
     * @param string $message
     */
    public function warning(string $message): void;
}