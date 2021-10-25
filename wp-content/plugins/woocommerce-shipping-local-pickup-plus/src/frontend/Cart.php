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

namespace SkyVerge\WooCommerce\Local_Pickup_Plus;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Cart_Item_Handling_Toggle;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Cart_Item_Pickup_Location_Field;
use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * Cart handler.
 *
 * @since 2.0.0
 */
class Cart {


	/**
	 * Hook into cart.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// set the cart item keys as cart item properties
		add_action( 'template_redirect', [ $this, 'set_cart_item_keys' ] );

		// set the default handling data based on product-level settings
		add_action( 'woocommerce_get_item_data', [ $this, 'set_cart_item_pickup_handling' ], 10, 2 );

		// add a selector next to each product in cart to designate for pickup
		// note: this is normally a filter, we use an action to echo some content instead
		add_action( 'woocommerce_get_item_data', [ $this, 'add_cart_item_pickup_location_field' ], 999, 2 );

		// perhaps disable the shipping calculator if the first and sole item in the cart totals is for pickup
		add_filter( 'option_woocommerce_enable_shipping_calc', [ $this, 'disable_shipping_calculator' ] );

		// ensures that the cart totals are blocked until the document is ready to ensure that package handling is persisted in session via AJAX request
		add_action( 'woocommerce_after_template_part', [ $this, 'add_cart_totals_block_ui_script' ], 1 );
	}


	/**
	 * Blocks the cart totals UI until the document is ready.
	 *
	 * May address an issue that is more evident on slower connections (while testing you may need to throttle your network speed to see it).
	 * The cart page may not block the cart totals carrying the shipping options radio inputs while the document is being loaded.
	 * Hence, there's the possibility that the customer clicks on the inputs but the choice would not be persisted as the AJAX request won't fire on time.
	 * This will inject a script to be executed immediately, instructing to block the cart totals until document is ready.
	 *
	 * Note that we require jQuery and jQueryUI to be loaded in head when attaching Local Pickup Plus front end scripts:
	 * @see Frontend::load_scripts()
	 *
	 * @internal
	 *
	 * @since 2.8.3
	 *
	 * @param string $template_name the template being loaded
	 */
	public function add_cart_totals_block_ui_script( $template_name ) {

		// bail if not on the cart page
		if ( 'cart/shipping-calculator.php' !== $template_name || ! is_cart() ) {
			return;
		}

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		// bail if:
		// - Local Pickup Plus is not available
		// - if the pickup is one item per package: the radio inputs won't show
		if ( ! $local_pickup_plus || ! $local_pickup_plus->is_available() || ! $local_pickup_plus->is_per_order_selection_enabled() )  {
			return;
		}

		?>
		<script type="text/javascript">
			if ( typeof jQuery !== 'undefined' && jQuery.isFunction( jQuery.fn.block ) ) {

				var $cartTotals       = jQuery( 'div.cart_totals' );
				var unblockCartTotals = true;

				if ( $cartTotals && $cartTotals.length > 0 && ( ! $cartTotals.is( 'processing' ) && ! $cartTotals.parents( '.processing' ).length > 0 ) ) {
					$cartTotals.addClass( 'processing' ).block( {
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					} );
				} else {
					unblockCartTotals = false;
				}

				jQuery( document ).ready( function( $ ) {
					if ( unblockCartTotals ) {
						$( 'div.cart_totals' ).removeClass( 'processing' ).unblock()
					}
				} );
			}
		</script>
		<?php
	}


	/**
	 * Perhaps disables the cart page shipping calculator by toggling a WordPress option value.
	 *
	 * If in the cart totals there is only one package and is meant for pickup, we don't need the shipping calculator.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param string $default_setting the option default setting
	 * @return string 'yes' or 'no'
	 */
	public function disable_shipping_calculator( $default_setting ) {

		if ( 'no' !== $default_setting && is_cart() ) {

			$packages = WC()->cart->get_shipping_packages();
			$package  = count( $packages ) > 0 ? current( $packages ) : [];

			if ( isset( $package['ship_via'][0] ) && $package['ship_via'][0] === wc_local_pickup_plus_shipping_method_id() ) {

				$default_setting = 'no';
			}
		}

		return $default_setting;
	}


	/**
	 * Add the cart item key to the cart item data.
	 *
	 * We will need a copy of the cart key to associate pickup choices with the corresponding cart item later.
	 *
	 * @see Cart::add_cart_item_pickup_location_field()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function set_cart_item_keys() {

		if ( ( is_cart() || is_checkout() ) && ! WC()->cart->is_empty() ) {

			$cart_contents = WC()->cart->cart_contents;

			foreach ( array_keys( WC()->cart->cart_contents ) as $cart_item_key ) {

				if ( ! isset( $cart_contents[ $cart_item_key ]['cart_item_key'] ) ) {
					$cart_contents[ $cart_item_key ]['cart_item_key'] = $cart_item_key;
				}

				wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, [] );
			}

			WC()->cart->cart_contents = $cart_contents;
		}
	}


	/**
	 * Sets the pickup handling for cart items to respect their product-level
	 * settings.
	 *
	 * @since 2.1.0
	 *
	 * @param array $item_data the product item data (e.g. used in variations)
	 * @param array $cart_item the product as a cart item array
	 * @return array unfiltered item data (see method description)
	 */
	public function set_cart_item_pickup_handling( $item_data, $cart_item ) {

		if ( isset( $cart_item['cart_item_key'] ) ) {

			$product_id = ! empty( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
			$product    = wc_get_product( $product_id );

			if ( $product ) {

				if ( wc_local_pickup_plus_product_must_be_picked_up( $product ) ) {
					$handling = 'pickup';
				} elseif ( ! wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {
					$handling = 'ship';
				}

				// only update handling if there are product restrictions
				if ( ! empty( $handling ) ) {

					$pickup_data = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item['cart_item_key'] );

					// only update handling if it is different than the current value
					if ( empty( $pickup_data['handling'] )
					     || $handling !== $pickup_data['handling'] ) {

						$pickup_data['handling'] = $handling;
						wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item['cart_item_key'], $pickup_data );
					}
				}
			}
		}

		return $item_data;
	}


	/**
	 * Render the pickup location selection box on the cart summary.
	 *
	 * This callback is performed as an action rather than a filter to echo some content.
	 *
	 * @see Cart::set_cart_item_keys()
	 * @see Checkout::add_checkout_item_pickup_location_field()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $item_data the product item data (e.g. used in variations)
	 * @param array $cart_item the product as a cart item array
	 *
	 * @return array unfiltered item data (see method description)
	 */
	public function add_cart_item_pickup_location_field( $item_data, $cart_item ) {

		if ( isset( $cart_item['cart_item_key'] ) && in_the_loop() && is_cart() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( $local_pickup_plus->is_available() ) {

				$product_field = new Cart_Item_Pickup_Location_Field( $cart_item['cart_item_key'] );
				$product_field->output_html();

				$handling_toggle = new Cart_Item_Handling_Toggle( $cart_item['cart_item_key'] );
				$handling_toggle->output_html();
			}
		}

		return $item_data;
	}


}

class_alias( Cart::class, 'WC_Local_Pickup_Plus_Cart' );
