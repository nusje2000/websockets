<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Logger;

/**
 * Interface LoggerInterface
 *
 * @package Nusje2000\Socket\Logger
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