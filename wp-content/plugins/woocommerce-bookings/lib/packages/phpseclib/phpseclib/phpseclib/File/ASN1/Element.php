<?php

/**
 * ASN.1 Raw Element
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2012 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 *
 * Modified by woocommerce on 14-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1;

/**
 * ASN.1 Raw Element
 *
 * An ASN.1 ANY mapping will return an ASN1\Element object. Use of this object
 * will also bypass the normal encoding rules in ASN1::encodeDER()
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
class Element
{
    /**
     * Raw element value
     *
     * @var string
     */
    public $element;

    /**
     * Constructor
     *
     * @param string $encoded
     * @return \Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1\Element
     */
    public function __construct($encoded)
    {
        $this->element = $encoded;
    }
}
