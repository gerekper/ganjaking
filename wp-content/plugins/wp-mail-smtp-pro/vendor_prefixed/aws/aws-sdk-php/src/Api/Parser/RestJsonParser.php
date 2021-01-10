<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements REST-JSON parsing (e.g., Glacier, Elastic Transcoder)
 */
class RestJsonParser extends \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractRestParser
{
    use PayloadParserTrait;
    /**
     * @param Service    $api    Service description
     * @param JsonParser $parser JSON body builder
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, \WPMailSMTP\Vendor\Aws\Api\Parser\JsonParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \WPMailSMTP\Vendor\Aws\Api\Parser\JsonParser();
    }
    protected function payload(\WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, array &$result)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            $result += $this->parser->parse($member, $jsonBody);
        }
    }
    public function parseMemberFromStream(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, $response)
    {
        $jsonBody = $this->parseJson($stream, $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
        return [];
    }
}
