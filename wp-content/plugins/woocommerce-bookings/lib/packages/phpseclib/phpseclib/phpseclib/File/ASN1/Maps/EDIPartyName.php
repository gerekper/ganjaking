<?php

/**
 * EDIPartyName
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
 * EDIPartyName
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class EDIPartyName
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'nameAssigner' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ] + DirectoryString::MAP,
            // partyName is technically required but \phpseclib3\File\ASN1 doesn't currently support non-optional constants and
            // setting it to optional gets the job done in any event.
            'partyName' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ] + DirectoryString::MAP
        ]
    ];
}
