<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters;

use Automattic\WooCommerce\Utilities\NumberUtil;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Product_Helper;
use WC_Cart;
use WC_Product;

defined( 'ABSPATH' ) or exit;

/**
 * The Cart Event Data Adapter class.
 *
 * @since 2.0.0
 */
class Cart_Event_Data_Adapter extends Event_Data_Adapter {


	/** @var WC_Cart the source cart */
	protected WC_Cart $cart;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Cart $cart the cart instance
	 */
	public function __construct( WC_Cart $cart ) {

		$this->cart = $cart;
	}


	/**
	 * Converts the source into an array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function convert_from_source() : array {

		return [
			'currency' => get_woocommerce_currency(),
			'value'    => NumberUtil::round( $this->cart->get_cart_contents_total(), wc_get_price_decimals() ),
			'coupon'   => implode( ',', $this->cart->get_applied_coupons() ),
			'shipping' => NumberUtil::round( $this->cart->get_shipping_total(), wc_get_price_decimals() ),
			'tax'      => NumberUtil::round( $this->cart->get_total_tax(), wc_get_price_decimals() ),
			'items'    => array_values( array_map( static function( $item ) {

				return ( new Cart_Item_Event_Data_Adapter( $item ) )->convert_from_source();

			}, $this->cart->get_cart() ) ),
		];
	}


}
