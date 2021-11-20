<?php

namespace WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint;

use WPMailSMTP\Vendor\Aws;
use WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException;
class Configuration implements \WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint\ConfigurationInterface
{
    private $useDualstackEndpoint;
    public function __construct($useDualstackEndpoint, $region)
    {
        $this->useDualstackEndpoint = \WPMailSMTP\Vendor\Aws\boolean_value($useDualstackEndpoint);
        if (\is_null($this->useDualstackEndpoint)) {
            throw new \WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException("'use_dual_stack_endpoint' config option" . " must be a boolean value.");
        }
        if ($this->useDualstackEndpoint == \true && (\strpos($region, "iso-") !== \false || \strpos($region, "-iso") !== \false)) {
            throw new \WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException("Dual-stack is not supported in ISO regions");
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isUseDualstackEndpoint()
    {
        return $this->useDualstackEndpoint;
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['use_dual_stack_endpoint' => $this->isUseDualstackEndpoint()];
    }
}
