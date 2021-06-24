<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\Exception\AwsException;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7;
/**
 * @internal Decorates a parser and validates the x-amz-crc32 header.
 */
class Crc32ValidatingParser extends \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractParser
{
    /**
     * @param callable $parser Parser to wrap.
     */
    public function __construct(callable $parser)
    {
        $this->parser = $parser;
    }
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $command, \WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        if ($expected = $response->getHeaderLine('x-amz-crc32')) {
            $hash = \hexdec(\WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::hash($response->getBody(), 'crc32b'));
            if ($expected != $hash) {
                throw new \WPMailSMTP\Vendor\Aws\Exception\AwsException("crc32 mismatch. Expected {$expected}, found {$hash}.", $command, ['code' => 'ClientChecksumMismatch', 'connection_error' => \true, 'response' => $response]);
            }
        }
        $fn = $this->parser;
        return $fn($command, $response);
    }
    public function parseMemberFromStream(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parseMemberFromStream($stream, $member, $response);
    }
}
