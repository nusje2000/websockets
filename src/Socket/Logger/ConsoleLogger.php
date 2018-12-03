<?php

declare(strict_types=1);

namespace Nusje2000\Socket\Logger;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class ConsoleLogger
 *
 * @package Nusje2000\Socket\Logger
 */
class ConsoleLogger implements LoggerInterface
{
    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * ConsoleLogger constructor.
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $outputStyle = new OutputFormatterStyle('red');
        $this->output->getFormatter()->setStyle('warning', $outputStyle);
        $outputStyle = new OutputFormatterStyle('cyan');
        $this->output->getFormatter()->setStyle('info', $outputStyle);
        $outputStyle = new OutputFormatterStyle('green');
        $this->output->getFormatter()->setStyle('notice', $outputStyle);
        $outputStyle = new OutputFormatterStyle(null, null, ['bold']);
        $this->output->getFormatter()->setStyle('timestamp', $outputStyle);
    }

    /**
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->output->writeln(
            sprintf('<timestamp>[%s]</timestamp><info> INFO: %s</info>', date('Y-m-d H:i:s'), $message)
        );
    }

    /**
     * @param string $message
     */
    public function notice(string $message): void
    {
        $this->output->writeln(
            sprintf('<timestamp>[%s]</timestamp><notice> NOTICE: %s</notice>', date('Y-m-d H:i:s'), $message)
        );
    }

    /**
     * @param string $message
     */
    public function warning(string $message): void
    {
        $this->output->writeln(
            sprintf('<timestamp>[%s]</timestamp><warning> WARNING: %s</warning>', date('Y-m-d H:i:s'), $message)
        );
    }
}