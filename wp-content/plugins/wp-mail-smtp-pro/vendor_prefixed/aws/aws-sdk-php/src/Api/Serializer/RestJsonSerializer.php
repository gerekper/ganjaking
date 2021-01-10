<?php

namespace WPMailSMTP\Vendor\Aws\Api\Serializer;

use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
/**
 * Serializes requests for the REST-JSON protocol.
 * @internal
 */
class RestJsonSerializer extends \WPMailSMTP\Vendor\Aws\Api\Serializer\RestSerializer
{
    /** @var JsonBody */
    private $jsonFormatter;
    /** @var string */
    private $contentType;
    /**
     * @param Service  $api           Service API description
     * @param string   $endpoint      Endpoint to connect to
     * @param JsonBody $jsonFormatter Optional JSON formatter to use
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, $endpoint, \WPMailSMTP\Vendor\Aws\Api\Serializer\JsonBody $jsonFormatter = null)
    {
        parent::__construct($api, $endpoint);
        $this->contentType = 'application/json';
        $this->jsonFormatter = $jsonFormatter ?: new \WPMailSMTP\Vendor\Aws\Api\Serializer\JsonBody($api);
    }
    protected function payload(\WPMailSMTP\Vendor\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = $this->contentType;
        $opts['body'] = (string) $this->jsonFormatter->build($member, $value);
    }
}
