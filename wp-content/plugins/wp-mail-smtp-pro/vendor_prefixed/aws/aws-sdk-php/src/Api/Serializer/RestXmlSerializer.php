<?php

namespace WPMailSMTP\Vendor\Aws\Api\Serializer;

use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\Api\Service;
/**
 * @internal
 */
class RestXmlSerializer extends \WPMailSMTP\Vendor\Aws\Api\Serializer\RestSerializer
{
    /** @var XmlBody */
    private $xmlBody;
    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     * @param XmlBody $xmlBody  Optional XML formatter to use
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, $endpoint, \WPMailSMTP\Vendor\Aws\Api\Serializer\XmlBody $xmlBody = null)
    {
        parent::__construct($api, $endpoint);
        $this->xmlBody = $xmlBody ?: new \WPMailSMTP\Vendor\Aws\Api\Serializer\XmlBody($api);
    }
    protected function payload(\WPMailSMTP\Vendor\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = 'application/xml';
        $opts['body'] = (string) $this->xmlBody->build($member, $value);
    }
}
