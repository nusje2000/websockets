<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Logger;

use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class DebugLogger implements LoggerInterface
{
    public function emergency($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('EMERGENCY: %s', $message), $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('ALERT: %s', $message), $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('CRITICAL: %s', $message), $context);
    }

    public function error($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('ERROR: %s', $message), $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('WARNING: %s', $message), $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('NOTICE: %s', $message), $context);
    }

    public function info($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('INFO: %s', $message), $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->writeToConsole(sprintf('DEBUG: %s', $message), $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->writeToConsole(sprintf('LOG: %s', $message), $context);
    }

    private function writeToConsole(string $message, array $context): void
    {
        echo sprintf('%s %s', $message, $this->createContextString($context)), PHP_EOL;
    }

    private function createContextString(array $context): string
    {
        $parts = [];

        foreach ($context as $key => $value) {
            $parts[] = sprintf('%s: %s', $key, print_r($value, true));
        }

        if (empty($parts)) {
            return '';
        }

        return sprintf('[%s]', implode($parts, ', '));
    }
}
