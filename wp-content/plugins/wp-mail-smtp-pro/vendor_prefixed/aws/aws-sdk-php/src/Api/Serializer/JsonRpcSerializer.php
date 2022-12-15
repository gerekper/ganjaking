<?php

namespace WPMailSMTP\Vendor\Aws\Api\Serializer;

use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\EndpointV2\EndpointProviderV2;
use WPMailSMTP\Vendor\Aws\EndpointV2\EndpointV2SerializerTrait;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * Prepares a JSON-RPC request for transfer.
 * @internal
 */
class JsonRpcSerializer
{
    use EndpointV2SerializerTrait;
    /** @var JsonBody */
    private $jsonFormatter;
    /** @var string */
    private $endpoint;
    /** @var Service */
    private $api;
    /** @var string */
    private $contentType;
    /**
     * @param Service  $api           Service description
     * @param string   $endpoint      Endpoint to connect to
     * @param JsonBody $jsonFormatter Optional JSON formatter to use
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, $endpoint, \WPMailSMTP\Vendor\Aws\Api\Serializer\JsonBody $jsonFormatter = null)
    {
        $this->endpoint = $endpoint;
        $this->api = $api;
        $this->jsonFormatter = $jsonFormatter ?: new \WPMailSMTP\Vendor\Aws\Api\Serializer\JsonBody($this->api);
        $this->contentType = \WPMailSMTP\Vendor\Aws\Api\Serializer\JsonBody::getContentType($api);
    }
    /**
     * When invoked with an AWS command, returns a serialization array
     * containing "method", "uri", "headers", and "body" key value pairs.
     *
     * @param CommandInterface $command Command to serialize into a request.
     * @param $endpointProvider Provider used for dynamic endpoint resolution.
     * @param $clientArgs Client arguments used for dynamic endpoint resolution.
     *
     * @return RequestInterface
     */
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $command, $endpointProvider = null, $clientArgs = null)
    {
        $operationName = $command->getName();
        $operation = $this->api->getOperation($operationName);
        $commandArgs = $command->toArray();
        $headers = ['X-Amz-Target' => $this->api->getMetadata('targetPrefix') . '.' . $operationName, 'Content-Type' => $this->contentType];
        if ($endpointProvider instanceof \WPMailSMTP\Vendor\Aws\EndpointV2\EndpointProviderV2) {
            $this->setRequestOptions($endpointProvider, $command, $operation, $commandArgs, $clientArgs, $headers);
        }
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request($operation['http']['method'], $this->endpoint, $headers, $this->jsonFormatter->build($operation->getInput(), $commandArgs));
    }
}
