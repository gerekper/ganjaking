<?php

/**
 * PersonalName
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 *
 * Modified by woocommerce on 14-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1\Maps;

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1;

/**
 * PersonalName
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class PersonalName
{
    const MAP = [
        'type' => ASN1::TYPE_SET,
        'children' => [
            'surname' => [
                'type' => ASN1::TYPE_PRINTABLE_STRING,
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ],
            'given-name' => [
                'type' => ASN1::TYPE_PRINTABLE_STRING,
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ],
            'initials' => [
                'type' => ASN1::TYPE_PRINTABLE_STRING,
                'constant' => 2,
                'optional' => true,
                'implicit' => true
            ],
            'generation-qualifier' => [
                'type' => ASN1::TYPE_PRINTABLE_STRING,
                'constant' => 3,
                'optional' => true,
                'implicit' => true
            ]
        ]
    ];
}
