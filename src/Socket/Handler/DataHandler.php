<?php

declare(strict_types=1);

namespace App\Socket\Handler;

use App\Socket\Frame\Encoder;
use App\Socket\Frame\FrameFactory;
use App\Socket\Frame\FrameInterface;
use RuntimeException;

/**
 * Class DataHandler
 *
 * @package App\Socket\Handler
 */
final class DataHandler implements DataHandlerInterface
{
    /**
     * @var FrameFactory
     */
    private $factory;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * DataHandler constructor.
     *
     * @param FrameFactory $factory
     * @param Encoder      $encoder
     */
    public function __construct(FrameFactory $factory, Encoder $encoder)
    {
        $this->factory = $factory;
        $this->encoder = $encoder;
    }

    /**
     * @param string $data
     *
     * @return FrameInterface
     * @throws RuntimeException
     */
    public function convertToFrame(string $data): FrameInterface
    {
        return $this->factory->createFrameFromData($data);
    }

    /**
     * @param FrameInterface $frame
     *
     * @return string
     * @throws RuntimeException
     */
    public function convertToString(FrameInterface $frame): string
    {
        return $this->encoder->encode($frame);
    }
}