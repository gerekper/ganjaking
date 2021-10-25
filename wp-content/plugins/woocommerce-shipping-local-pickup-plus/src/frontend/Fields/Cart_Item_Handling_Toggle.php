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

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Fields;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Cart_Item_Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * Field component to toggle shipping handling method for a cart item.
 *
 * @since 2.7.0
 */
class Cart_Item_Handling_Toggle extends Field {

	/** @var string $cart_item_key the ID of the cart item for this field */
	private $cart_item_key;


	/**
	 * Field constructor.
	 *
	 * @since 2.7.0
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
	 * @since 2.7.0
	 *
	 * @return string|int
	 */
	public function get_cart_item_id() {
		return $this->cart_item_key;
	}


	/**
	 * Get the field HTML.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field::get_html
	 *
	 * @since 2.7.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html        = '';
		$cart_item_id      = '';
		$product           = null;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (    ( $product = $this->data_store->get_product() )
		     && ! wc_local_pickup_plus_product_must_be_picked_up( $product ) ) {

			if ( wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {

				$cart_item_id        = $this->get_cart_item_id();
				$pickup_data         = $this->data_store->get_pickup_data();
				$should_be_picked_up = ( isset( $pickup_data['handling'] ) && 'pickup' === $pickup_data['handling'] ) || ! $this->data_store->can_be_shipped();

				ob_start();

				?>
				<div
					id="handling-toggle-for-<?php echo esc_attr( $cart_item_id ); ?>"
					class="pickup-location-field pickup-location-cart-item-field"
					data-pickup-object-id="<?php echo esc_attr( $cart_item_id ); ?>">

				<?php if ( ! $this->hiding_item_handling_toggle() ) : ?>

					<?php

					/**
					 * Filters the product handling links and their labels.
					 *
					 * @since 2.2.0
					 *
					 * @param array $item_handling_labels associative array of keys and HTML labels containing links
					 * @param string $enable_pickup_class CSS class expected to be in a link to set item for pickup
					 * @param string $disable_pickup_class CSS class expected to be in a link to set item for shipping
					 */
					$item_handling_labels = (array) apply_filters( 'wc_local_pickup_plus_item_handling_toggle_labels', [
						/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
						'set_for_pickup'   => sprintf( esc_html__( 'This item is set for shipping. %1$sClick here to pickup this item%2$s.', 'woocommerce-shipping-local-pickup-plus' ), '<a class="enable-local-pickup"  href="#">', '</a>' ),
						/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
						'set_for_shipping' => sprintf( esc_html__( 'This item is set for pickup. %1$sClick here to ship this item%2$s.', 'woocommerce-shipping-local-pickup-plus' ), '<a class="disable-local-pickup" href="#">', '</a>' ),
					], 'enable-local-pickup', 'disable-local-pickup' );

					?>

					<?php if ( isset( $item_handling_labels['set_for_pickup'], $item_handling_labels['set_for_shipping'] ) ) : ?>

						<small
							style="display: <?php echo $should_be_picked_up ? 'none' : 'block'; ?>;"><?php echo $item_handling_labels['set_for_pickup']; ?></small>
						<small
							style="display: <?php echo ! $should_be_picked_up ? 'none' : 'block'; ?>;"><?php echo $item_handling_labels['set_for_shipping']; ?></small>

					<?php endif; ?>

				<?php else : ?>

					<?php // if the customer control toggle to switch between ship and pickup is disabled, force handling into session
						$this->data_store->set_pickup_data( [
							'handling'           => $should_be_picked_up ? 'pickup' : $local_pickup_plus->get_default_handling(),
							'lookup_area'        => isset( $pickup_data['lookup_area'] ) ? $pickup_data['lookup_area'] : '',
							'pickup_location_id' => isset( $pickup_data['pickup_location_id'] ) ? $pickup_data['pickup_location_id'] : 0,
							'pickup_date'        => isset( $pickup_data['pickup_date'] ) ? $pickup_data['pickup_date'] : '',
							'appointment_offset' => isset( $pickup_data['appointment_offset'] ) ? $pickup_data['appointment_offset'] : '',
						] );
					?>

				<?php endif; ?>

				<?php

				// display if not forced to pick up and item handling links have not been displayed despite cart item susceptible to be shipped
				if ( empty( $item_handling_labels ) && $this->data_store->cart_item_may_have_shipping( $cart_item_id ) ) {

					$note_text    = __( 'Shipping may be available.', 'woocommerce-shipping-local-pickup-plus' );
					$note_tooltip = is_checkout() ? __( 'Enter or update your full address to see if shipping options are available.', 'woocommerce-shipping-local-pickup-plus' ) : __( 'Enter your full address on the checkout page to see if shipping is available.', 'woocommerce-shipping-local-pickup-plus' );

					printf( '<small>%1$s <span class="wc-lpp-help-tip" data-tip="%2$s"></span></small>', esc_html( $note_text ), esc_attr( $note_tooltip ) );
				}

				?>

				</div>
				<?php

				$field_html .= ob_get_clean();

			} elseif ( $product->needs_shipping() ) {

				// display a shipping handling notice only for non-virtual items
				$field_html .= '<br /><em><small>' . __( 'This item can only be shipped', 'woocommerce-shipping-local-pickup-plus' ) . '</small></em>';
			}
		}

		/**
		 * Filter the cart item handling toggle HTML.
		 *
		 * @since 2.7.0
		 *
		 * @param string $field_html HTML
		 * @param string $cart_item_id the current cart item ID
		 * @param \WC_Product|null $product the cart item product
		 */
		return apply_filters( 'wc_local_pickup_plus_get_cart_item_handling_toggle_html', $field_html, $cart_item_id, $product );
	}


	/**
	 * Determines whether the item handling toggle should be hidden to customers in frontend.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	protected function hiding_item_handling_toggle() {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();
		$hiding            = false;

		$is_automatic_handling = $local_pickup_plus
		                         && $local_pickup_plus->is_per_order_selection_enabled()
		                         && $local_pickup_plus->is_item_handling_mode( 'automatic' );

		// when you have a product that must be picked and a product that cannot be picked up,
		// a minimum of 2 shipping packages is required and automatic handling by itself is not enough,
		// so we allow each item to be toggled as well
		$shipping_and_pickup_required = wc_local_pickup_plus()->get_packages_instance()->are_shipping_and_pickup_required();

		if ( ! $this->data_store->can_be_shipped() || ( $is_automatic_handling && ! $shipping_and_pickup_required ) ) {
			$hiding = true;
		}

		return $hiding;
	}


}
