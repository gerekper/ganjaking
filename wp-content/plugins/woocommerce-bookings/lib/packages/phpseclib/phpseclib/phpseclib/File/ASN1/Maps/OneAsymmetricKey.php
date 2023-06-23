<?php

/**
 * OneAsymmetricKey
 *
 * See https://tools.ietf.org/html/rfc5958
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
 * OneAsymmetricKey
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class OneAsymmetricKey
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'version' => [
                'type' => ASN1::TYPE_INTEGER,
                'mapping' => ['v1', 'v2']
            ],
            'privateKeyAlgorithm' => AlgorithmIdentifier::MAP,
            'privateKey' => PrivateKey::MAP,
            'attributes' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ] + Attributes::MAP,
            'publicKey' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ] + PublicKey::MAP
        ]
    ];
}
