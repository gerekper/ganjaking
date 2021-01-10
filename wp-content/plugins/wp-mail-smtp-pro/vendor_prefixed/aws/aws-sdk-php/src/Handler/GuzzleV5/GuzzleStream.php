<?php

namespace WPMailSMTP\Vendor\Aws\Handler\GuzzleV5;

use WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamDecoratorTrait;
use WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a PSR-7 Stream to a Guzzle 5 Stream.
 *
 * @codeCoverageIgnore
 */
class GuzzleStream implements \WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var Psr7StreamInterface */
    private $stream;
    public function __construct(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
