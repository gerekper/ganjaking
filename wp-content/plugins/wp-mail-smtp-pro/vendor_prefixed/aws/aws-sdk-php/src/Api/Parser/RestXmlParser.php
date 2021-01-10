<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements REST-XML parsing (e.g., S3, CloudFront, etc...)
 */
class RestXmlParser extends \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractRestParser
{
    use PayloadParserTrait;
    /**
     * @param Service   $api    Service description
     * @param XmlParser $parser XML body parser
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, \WPMailSMTP\Vendor\Aws\Api\Parser\XmlParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \WPMailSMTP\Vendor\Aws\Api\Parser\XmlParser();
    }
    protected function payload(\WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, array &$result)
    {
        $result += $this->parseMemberFromStream($response->getBody(), $member, $response);
    }
    public function parseMemberFromStream(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, $response)
    {
        $xml = $this->parseXml($stream, $response);
        return $this->parser->parse($member, $xml);
    }
}
