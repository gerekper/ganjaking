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
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Fields;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Package_Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Component to render items details for items in a shipping package.
 *
 * @since 2.7.0
 */
class Package_Pickup_Items_Field extends Field {


	/** @var int|string key index of current package this field is associated to */
	private $package_id;


	/**
	 * Field constructor.
	 *
	 * @since 2.7.0
	 *
	 * @param int|string $package_id the package key index
	 */
	public function __construct( $package_id ) {

		$this->object_type = 'package';
		$this->package_id  = $package_id;
		$this->data_store  = new Package_Pickup_Data( $package_id );
	}


	/**
	 * Get the ID of the package for this field.
	 *
	 * @since 2.7.0
	 *
	 * @return int|string
	 */
	private function get_package_id() {
		return $this->package_id;
	}


	/**
	 * Get the package cart items.
	 *
	 * This is useful later when submitting the checkout form to associate a order line items to a package and thus an order shipping item.
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_order_line_item_to_package()
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @since 2.0.0
	 *
	 * @return int[]|string[]
	 */
	private function get_cart_items() {

		$items   = [];
		$package = $this->data_store->get_package();

		if ( ! empty( $package['contents'] ) && is_array( $package['contents'] ) ) {
			foreach ( array_keys( $package['contents'] ) as $cart_item_key  ) {
				$items[] = $cart_item_key;
			}
		}

		return $items;
	}


	/**
	 * Get cart item details for the current package.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array of product names and quantities
	 */
	private function get_cart_items_details() {

		$items   = [];
		$package = $this->data_store->get_package();

		if ( ! empty( $package['contents'] ) && is_array( $package['contents'] ) ) {

			foreach ( $package['contents'] as $cart_item_key => $cart_item ) {

				if ( isset( $cart_item['data'], $cart_item['quantity'] ) ) {

					$item_product = $cart_item['data'] instanceof \WC_Product ? $cart_item['data'] : null;
					$item_qty     = max( 0, abs( $cart_item['quantity'] ) );

					if ( $item_product && $item_qty > 0 ) {

						/* translators: Placeholders: %1$s product name, %2$s product quantity - e.g. "Product name x2" */
						$items[ $cart_item_key ] = sprintf( __( '%1$s &times; %2$s', 'woocommerce-shipping-local-pickup-plus' ), $item_product->get_name(), $item_qty );
					}
				}
			}
		}

		/**
		 * Filter the pickup package details.
		 *
		 * @see Checkout::maybe_hide_pickup_package_item_details()
		 * @see \wc_cart_totals_shipping_html() for a similar filter in WooCommerce
		 *
		 * @since 2.0.0
		 *
		 * @param array $items an array of item keys and name/quantity details as strings
		 * @param array $package the package for pickup the details are meant for
		 */
		return apply_filters( 'wc_local_pickup_plus_shipping_package_details_array', $items, $package );
	}


	/**
	 * Get the field HTML.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field::get_html
	 *
	 * @since 2.7.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html      = '';
		$shipping_method = wc_local_pickup_plus_shipping_method();

		ob_start();

		?>
		<div
			id="pickup-items-field-for-<?php echo esc_attr( $this->get_package_id() ); ?>"
			class="pickup-location-field pickup-location-field-<?php echo sanitize_html_class( $shipping_method->pickup_selection_mode() ); ?> pickup-location-<?php echo sanitize_html_class( $this->get_object_type() ); ?>-field"
			data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>">

			<?php // display the item details list ?>
			<?php $item_details = $this->get_cart_items_details(); ?>
			<?php if ( ! empty( $item_details ) && is_array( $item_details ) ) : ?>
				<p class="woocommerce-shipping-contents"><small><?php echo esc_html( implode( ', ', $item_details ) ); ?></small></p>
			<?php endif; ?>

		</div>

		<?php // record cart items to pickup ?>

		<input
			type="hidden"
			name="wc_local_pickup_plus_pickup_items[<?php echo esc_attr( $this->get_package_id() ); ?>]"
			value="<?php echo implode( ',', $this->get_cart_items() ); ?>"
			data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
		/>

		<?php

		$field_html .= ob_get_clean();

		/**
		 * Filter the package items details HTML.
		 *
		 * @since 2.7.0
		 *
		 * @param string $field_html input field HTML
		 * @param int|string $package_id the current package identifier
		 * @param array $package the current package array
		 */
		return apply_filters( 'wc_local_pickup_plus_get_package_pickup_items_field_html', $field_html, $this->get_package_id(), $this->data_store->get_package() );
	}


}
