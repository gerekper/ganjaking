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

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Field component to select a pickup location for a cart item.
 *
 * @since 2.0.0
 */
class Cart_Item_Pickup_Location_Field extends Pickup_Location_Field {


	/** @var string $cart_item_key the ID of the cart item for this field */
	private $cart_item_key;


	/**
	 * Field constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $cart_item_key the current cart item key
	 */
	public function __construct( $cart_item_key ) {

		$this->object_type   = 'cart-item';
		$this->cart_item_key = $cart_item_key;
		$this->data_store    = new Cart_Item_Pickup_Data( $cart_item_key );
	}


	/**
	 * Get the cart item ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|int
	 */
	public function get_cart_item_id() {
		return $this->cart_item_key;
	}


	/**
	 * Get the cart item pickup data, if set.
	 *
	 * TODO remove this method by version 3.0.0 or November 2020 {DM 2019-11-20}
	 *
	 * @since 2.0.0
	 * @deprecated since 2.7.0
	 *
	 * @param string $piece optionally get a specific pickup data key instead of the whole array (default)
	 * @return string|int|\WC_Local_Pickup_Plus_Pickup_Location|array
	 */
	protected function get_pickup_data( $piece = '' ) {

		wc_deprecated_function( __METHOD__, '2.7.0', '\SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data::get_pickup_data' );

		return $this->get_data_store()->get_pickup_data( $piece );
	}


	/**
	 * Save pickup data to session.
	 *
	 * TODO remove this method by version 3.0.0 or November 2020 {DM 2019-11-20}
	 *
	 * @since 2.0.0
	 * @deprecated since 2.7.0
	 *
	 * @param array $pickup_data
	 */
	protected function set_pickup_data( array $pickup_data ) {

		wc_deprecated_function( __METHOD__, '2.7.0', '\SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data::set_pickup_data' );

		$this->get_data_store()->set_pickup_data( $pickup_data );
	}


	/**
	 * Reset pickup data for the cart item (defaults to shipping).
	 *
	 * TODO remove this method by version 3.0.0 or November 2020 {DM 2019-11-20}
	 *
	 * @since 2.0.0
	 * @deprecated since 2.7.0
	 */
	protected function delete_pickup_data() {

		wc_deprecated_function( __METHOD__, '2.7.0', '\SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data::delete_pickup_data' );

		$this->get_data_store()->delete_pickup_data();
	}


	/**
	 * Get the field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html        = '';
		$cart_item_id      = '';
		$product           = null;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (    $local_pickup_plus->is_per_item_selection_enabled() // only display the item location select if enabled
		     && ( $product = $this->data_store->get_product() )
		     && wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {

			$cart_item_id        = $this->get_cart_item_id();
			$pickup_data         = $this->data_store->get_pickup_data();
			$should_be_picked_up = ( isset( $pickup_data['handling'] ) && 'pickup' === $pickup_data['handling'] ) || ! $this->data_store->can_be_shipped();
			$must_be_picked_up   = wc_local_pickup_plus_product_must_be_picked_up( $product );

			if ( ! empty( $pickup_data['pickup_location_id'] ) ) {
				$chosen_pickup_location = wc_local_pickup_plus_get_pickup_location( (int) $pickup_data['pickup_location_id'] );
			} else {
				$chosen_pickup_location = $this->get_user_default_pickup_location();
			}

			// sanity check
			if (      $local_pickup_plus->is_per_item_selection_enabled()
			     && ! wc_local_pickup_plus_product_can_be_picked_up( $product, $chosen_pickup_location ) ) {

				$chosen_pickup_location = null;
			}

			ob_start();

			?>
			<div
				id="pickup-location-field-for-<?php echo esc_attr( $cart_item_id ); ?>"
				class="pickup-location-field pickup-location-cart-item-field"
				data-pickup-object-id="<?php echo esc_attr( $cart_item_id ); ?>">

				<?php // if the item is set to be shipped, hide the select instead of removing it, to preserve the chosen location ?>
				<div style="display: <?php echo $must_be_picked_up || $should_be_picked_up ? 'block' : 'none'; ?>;">
					<?php echo $this->get_location_select_html( $cart_item_id, $chosen_pickup_location, $this->data_store->get_product() ); ?>
				</div>

			</div>
			<?php

			$field_html .= ob_get_clean();
		}

		/**
		 * Filter the cart item pickup location field HTML.
		 *
		 * @since 2.0.0
		 *
		 * @param string $field_html HTML
		 * @param string $cart_item_id the current cart item ID
		 * @param \WC_Product|null $product the cart item product
		 */
		return apply_filters( 'wc_local_pickup_plus_get_pickup_location_cart_item_field_html', $field_html, $cart_item_id, $product );
	}


	/**
	 * Determines if the current product can be picked up, or must be shipped.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location to check
	 * @return bool
	 */
	protected function can_be_picked_up( $pickup_location ) {
		return $this->data_store->get_product() ? wc_local_pickup_plus_product_can_be_picked_up( $this->data_store->get_product(), $pickup_location ) : true;
	}


	/**
	 * Determines if the current product can be shipped, depending on the available shipping methods.
	 *
	 * If there are no shipping methods/rates available for the item's package, the item should be picked up instead.
	 *
	 * TODO remove this method by version 3.0.0 or November 2020 {DM 2019-11-20}
	 *
	 * @since 2.3.1
	 * @deprecated since 2.7.0
	 *
	 * @return bool
	 */
	protected function can_be_shipped() {

		wc_deprecated_function( __METHOD__, '2.7.0', '\SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data::can_be_shipped' );

		return $this->get_data_store()->can_be_shipped();
	}


	/**
	 * Determines whether the item handling toggle should be hidden to customers in frontend.
	 *
	 * TODO remove this method by version 3.0.0 or November 2020 {DM 2019-11-20}
	 *
	 * @since 2.2.0
	 * @deprecated since 2.7.0
	 *
	 * @return bool
	 */
	protected function hiding_item_handling_toggle() {

		wc_deprecated_function( __METHOD__, '2.7.0', 'Cart_Item_Handling_Toggle::hiding_item_handling_toggle' );

		return false;
	}


}

class_alias( Cart_Item_Pickup_Location_Field::class, 'WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field' );
