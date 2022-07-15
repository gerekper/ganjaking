<?php

namespace WPMailSMTP\Vendor\Aws\Api\Serializer;

use WPMailSMTP\Vendor\Aws\Api\Shape;
use WPMailSMTP\Vendor\Aws\Api\ListShape;
/**
 * @internal
 */
class Ec2ParamBuilder extends \WPMailSMTP\Vendor\Aws\Api\Serializer\QueryParamBuilder
{
    protected function queryName(\WPMailSMTP\Vendor\Aws\Api\Shape $shape, $default = null)
    {
        return ($shape['queryName'] ?: \ucfirst(@$shape['locationName'] ?: "")) ?: $default;
    }
    protected function isFlat(\WPMailSMTP\Vendor\Aws\Api\Shape $shape)
    {
        return \false;
    }
    protected function format_list(\WPMailSMTP\Vendor\Aws\Api\ListShape $shape, array $value, $prefix, &$query)
    {
        // Handle empty list serialization
        if (!$value) {
            $query[$prefix] = \false;
        } else {
            $items = $shape->getMember();
            foreach ($value as $k => $v) {
                $this->format($items, $v, $prefix . '.' . ($k + 1), $query);
            }
        }
    }
}
