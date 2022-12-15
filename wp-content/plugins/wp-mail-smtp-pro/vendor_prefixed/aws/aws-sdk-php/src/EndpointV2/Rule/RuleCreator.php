<?php

namespace WPMailSMTP\Vendor\Aws\EndpointV2\Rule;

use WPMailSMTP\Vendor\Aws\Exception\UnresolvedEndpointException;
class RuleCreator
{
    public static function create($type, $definition)
    {
        switch ($type) {
            case 'endpoint':
                return new \WPMailSMTP\Vendor\Aws\EndpointV2\Rule\EndpointRule($definition);
            case 'error':
                return new \WPMailSMTP\Vendor\Aws\EndpointV2\Rule\ErrorRule($definition);
            case 'tree':
                return new \WPMailSMTP\Vendor\Aws\EndpointV2\Rule\TreeRule($definition);
            default:
                throw new \WPMailSMTP\Vendor\Aws\Exception\UnresolvedEndpointException('Unknown rule type ' . $type . ' must be of type `endpoint`, `tree` or `error`');
        }
    }
}
