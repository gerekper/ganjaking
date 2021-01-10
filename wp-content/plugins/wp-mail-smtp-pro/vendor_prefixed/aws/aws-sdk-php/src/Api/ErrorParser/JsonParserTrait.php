<?php

namespace WPMailSMTP\Vendor\Aws\Api\ErrorParser;

use WPMailSMTP\Vendor\Aws\Api\Parser\PayloadParserTrait;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
/**
 * Provides basic JSON error parsing functionality.
 */
trait JsonParserTrait
{
    use PayloadParserTrait;
    private function genericHandler(\WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $code = (string) $response->getStatusCode();
        return ['request_id' => (string) $response->getHeaderLine('x-amzn-requestid'), 'code' => null, 'message' => null, 'type' => $code[0] == '4' ? 'client' : 'server', 'parsed' => $this->parseJson($response->getBody(), $response)];
    }
    protected function payload(\WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface $response, \WPMailSMTP\Vendor\Aws\Api\StructureShape $member)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
    }
}
