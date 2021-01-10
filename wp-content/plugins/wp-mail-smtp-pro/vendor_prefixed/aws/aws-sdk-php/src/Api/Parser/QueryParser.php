<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\Result;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Parses query (XML) responses (e.g., EC2, SQS, and many others)
 */
class QueryParser extends \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractParser
{
    use PayloadParserTrait;
    /** @var bool */
    private $honorResultWrapper;
    /**
     * @param Service   $api                Service description
     * @param XmlParser $xmlParser          Optional XML parser
     * @param bool      $honorResultWrapper Set to false to disable the peeling
     *                                      back of result wrappers from the
     *                                      output structure.
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, \WPMailSMTP\Vendor\Aws\Api\Parser\XmlParser $xmlParser = null, $honorResultWrapper = \true)
    {
        parent::__construct($api);
        $this->parser = $xmlParser ?: new \WPMailSMTP\Vendor\Aws\Api\Parser\XmlParser();
        $this->honorResultWrapper = $honorResultWrapper;
    }
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $command, \WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $output = $this->api->getOperation($command->getName())->getOutput();
        $xml = $this->parseXml($response->getBody(), $response);
        if ($this->honorResultWrapper && $output['resultWrapper']) {
            $xml = $xml->{$output['resultWrapper']};
        }
        return new \WPMailSMTP\Vendor\Aws\Result($this->parser->parse($output, $xml));
    }
    public function parseMemberFromStream(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, $response)
    {
        $xml = $this->parseXml($stream, $response);
        return $this->parser->parse($member, $xml);
    }
}
