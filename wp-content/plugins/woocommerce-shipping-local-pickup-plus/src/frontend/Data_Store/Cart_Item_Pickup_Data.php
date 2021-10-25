<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * Pickup data storage component for cart items.
 *
 * @since 2.7.0
 */
class Cart_Item_Pickup_Data extends Pickup_Data {


	/**
	 * Data storage constructor.
	 *
	 * @since 2.7.0
	 *
	 * @param string $cart_item_key the ID of the cart item
	 */
	public function __construct( $cart_item_key ) {

		$this->object_id = $cart_item_key;
	}


	/**
	 * Get the cart item pickup data, if set.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $piece optionally get a specific pickup data key instead of the whole array (default)
	 * @return string|int|\WC_Local_Pickup_Plus_Pickup_Location|array
	 */
	public function get_pickup_data( $piece = '' ) {
		return wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $this->object_id, $piece );
	}


	/**
	 * Save pickup data to session.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $pickup_data
	 */
	public function set_pickup_data( array $pickup_data ) {
		wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $this->object_id, $pickup_data );
	}


	/**
	 * Reset pickup data for the cart item (defaults to shipping).
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function delete_pickup_data() {
		wc_local_pickup_plus()->get_session_instance()->delete_cart_item_pickup_data();
	}


	/**
	 * Get the cart item.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_cart_item() {

		$cart_item    = [];
		$cart_item_id = $this->object_id;

		if ( ! empty( $cart_item_id ) && ! WC()->cart->is_empty() ) {

			$cart_contents = WC()->cart->cart_contents;

			if ( isset( $cart_contents[ $cart_item_id ] ) ) {
				$cart_item = $cart_contents[ $cart_item_id ];
			}
		}

		return $cart_item;
	}


	/**
	 * Get the ID of the product for the cart.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	private function get_product_id() {

		$cart_item  = $this->get_cart_item();
		$product_id = isset( $cart_item['product_id'] ) ? abs( $cart_item['product_id'] ) : 0;

		if ( ! empty( $cart_item['variation_id'] ) ) {
			$product_id = abs( $cart_item['variation_id'] );
		}

		return $product_id;
	}


	/**
	 * Get the product object for the cart item.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return null|\WC_Product
	 */
	public function get_product() {

		$product_id = $this->get_product_id();
		$product    = $product_id > 0 ? wc_get_product( $product_id ) : null;

		return $product instanceof \WC_Product ? $product : null;
	}


	/**
	 * Determines if the current product can be shipped, depending on the available shipping methods.
	 *
	 * If there are no shipping methods/rates available for the item's package, the item should be picked up instead.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.3.1
	 *
	 * @return bool
	 */
	public function can_be_shipped() {

		return ! wc_local_pickup_plus()->get_products_instance()->product_must_be_picked_up( $this->get_product() );
	}


	/**
	 * Checks whether a cart item may have shipping available that hasn't been calculated yet.
	 *
	 * Note: this method shouldn't be open to public unless refactored because its intent and name are ambiguous.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @internal
	 *
	 * @since 2.3.17
	 *
	 * @param string $cart_item_id
	 * @return bool
	 */
	public function cart_item_may_have_shipping( $cart_item_id ) {

		$may_be_shipped = false;

		// shipping has not yet been calculated
		if ( ! WC()->customer->has_calculated_shipping() ) {

			$package = wc_local_pickup_plus()->get_packages_instance()->get_cart_item_package( $cart_item_id );

			// package is currently set to ship via LPP
			if ( isset( $package['ship_via'] ) && in_array( 'local_pickup_plus', $package['ship_via'], true ) ) {

				$zones = \WC_Shipping_Zones::get_zones();

				foreach ( $zones as $zone_id => $zone_data ) {

					$zone    = \WC_Shipping_Zones::get_zone( $zone_id );
					$methods = $zone->get_shipping_methods( true );

					// enabled shipping methods exist for a zone
					if ( ! empty( $methods ) ) {

						$may_be_shipped = true;
						break;
					}
				}
			}
		}

		return $may_be_shipped;
	}


}
