<?php

namespace WPMailSMTP\Vendor\Aws\Handler\GuzzleV5;

use WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamDecoratorTrait;
use WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a Guzzle 5 Stream to a PSR-7 Stream.
 *
 * @codeCoverageIgnore
 */
class PsrStream implements \WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var GuzzleStreamInterface */
    private $stream;
    public function __construct(\WPMailSMTP\Vendor\GuzzleHttp\Stream\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
    public function rewind()
    {
        $this->stream->seek(0);
    }
    public function getContents()
    {
        return $this->stream->getContents();
    }
}
