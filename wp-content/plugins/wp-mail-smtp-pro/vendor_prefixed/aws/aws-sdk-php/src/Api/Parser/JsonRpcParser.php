<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\Result;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements JSON-RPC parsing (e.g., DynamoDB)
 */
class JsonRpcParser extends \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractParser
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
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $command, \WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $operation = $this->api->getOperation($command->getName());
        $result = null === $operation['output'] ? null : $this->parseMemberFromStream($response->getBody(), $operation->getOutput(), $response);
        return new \WPMailSMTP\Vendor\Aws\Result($result ?: []);
    }
    public function parseMemberFromStream(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parse($member, $this->parseJson($stream, $response));
    }
}
