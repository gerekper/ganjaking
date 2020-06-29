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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Subscriptions integration class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Integration_Subscriptions {


	/**
	 * Initialize Subscriptions support.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// note: this filter must run later than Local Pickup Plus and Subscriptions
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'handle_packages_recurring_cart_keys' ), 20 );

		// add hidden input to copy additional pickup location data for the subscription recurring cart data
		add_filter( 'wc_local_pickup_plus_get_pickup_location_package_field_html', array( $this, 'handle_recurring_cart_pickup_location' ), 0, 3 );

		// handle recurring totals shipping methods
		add_action( 'woocommerce_subscriptions_after_recurring_shipping_rates', array( $this, 'handle_recurring_shipping_methods' ) );
	}


	/**
	 * Hide Local Pickup Plus as a choice from recurring totals.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function handle_recurring_shipping_methods() {

		wc_enqueue_js( "
			jQuery( document ).ready( function( $ ) {

				function handleRecurringTotals() {

					var subRecurringTotals = $( '.recurring-total' );

					if ( subRecurringTotals ) {
						subRecurringTotals.find( 'input[value=\"" . wc_local_pickup_plus_shipping_method_id() . "\"]' ).each( function() {						
							$( this ).prop( 'checked', false );
							$( this ).closest( 'li' ).hide();
						} );
					}
				}

				handleRecurringTotals();

				$( document.body ).on( 'updated_checkout update_cart_totals updated_shipping_method', function() {
					handleRecurringTotals();
				} );
			} );
		" );
	}


	/**
	 * Filter the packages array to change the package id of a package containing a subscription with a recurring cart key from Subscriptions.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $packages an array of shipping packages
	 * @return array
	 */
	public function handle_packages_recurring_cart_keys( $packages ) {

		$filter_packages = array();

		foreach ( $packages as $package_key => $package ) {

			// Subscriptions recurring shipping packages do not use the recurring cart key for the index in the packages array.
			// Subscriptions uses this key in the chosen shipping methods array though, so we change the array key to make up for this discrepancy.
			if ( isset( $package['recurring_cart_key'] ) && 'none' !== $package['recurring_cart_key'] ) {
				$package_key = \WC_Subscriptions_Cart::get_recurring_shipping_package_key( $package['recurring_cart_key'], $package_key );
			}

			$filter_packages[ $package_key ] = $package;
		}

		return $filter_packages;
	}


	/**
	 * Check if the package contains at least one subscription product.
	 *
	 * @since 2.0.0
	 *
	 * @param array $package associative array
	 * @return bool
	 */
	private function package_contains_subscription( $package ) {

		$is_subscription  = false;
		$package_contents = isset( $package['contents'] ) ? $package['contents'] : array();

		foreach ( $package_contents as $content ) {

			$product         = isset( $content['data'] ) ? $content['data'] : null;
			$is_subscription = $product instanceof \WC_Product && $product->is_type( array( 'subscription', 'subscription_variation' ) );

			if ( $is_subscription ) {
				break;
			}
		}

		return $is_subscription;
	}


	/**
	 * Handle pickup of recurring shipping.
	 *
	 * Subscriptions adds recurrent shipping information that will be used in renewals.
	 * However this will trigger complaints from Local Pickup Plus as the added special package won't be accompanied by pickup data unselectable by the user.
	 * This filter hook will add some hidden inputs that will trick the validation to pass and will be discarded thereafter.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $html the HTML text that would either pass form components or a message
	 * @param int|string $package_key the package key may have been altered with the recurring cart key by Subscriptions
	 * @param array $package the current package array, whose key no longer matches (and might be as well be empty in this context)
	 * @return string HTML
	 */
	public function handle_recurring_cart_pickup_location( $html, $package_key, $package ) {

		// subscription package keys are never numerical, normally they consist of an underscore-separated string with recurrence information
		if ( ! is_numeric( $package_key ) ) {

			$lookup = esc_html__( 'Please choose a pickup location', 'woocommerce-shipping-local-pickup-plus' );

			if ( false !== strpos( $html, $lookup ) ) {

				$packages              = WC()->shipping()->get_packages();
				$recurring_package_key = explode( '_', $package_key );
				$recurring_package_key = ! empty( $recurring_package_key ) ? end( $recurring_package_key ) : null;
				$recurring_package     = isset( $packages[ $recurring_package_key ] ) ? $packages[ $recurring_package_key ] : null;

				if ( $recurring_package && isset( $packages[ $recurring_package_key ]['pickup_location_id'] ) && $this->package_contains_subscription( $recurring_package ) ) {

					$pickup_location_id = $packages[ $recurring_package_key ]['pickup_location_id'];
					$pickup_date        = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $recurring_package_key, 'pickup_date' );
					$pickup_date        = empty( $pickup_date ) && 'required' === wc_local_pickup_plus_appointments_mode() ? date( 'Y-m-d', current_time( 'timestamp' ) ) : $pickup_date;

					ob_start();

					// we trick validation to pass by adding hidden fields and populating them with the expected local pickup plus data ?>
					<tr class="pickup_location">
						<th colspan="1"></th>
						<td>
							<input
								type="hidden"
								name="_shipping_method_pickup_location_id[<?php echo esc_attr( $package_key ); ?>]"
								value="<?php echo esc_attr( $pickup_location_id ); ?>"
								data-package-id="<?php echo esc_attr( $package_key ); ?>"
							/>
							<input
								type="hidden"
								name="_shipping_method_pickup_date[<?php echo esc_attr( $package_key ); ?>]"
								value="<?php echo esc_attr( $pickup_date ); ?>"
								data-location-id="<?php echo esc_attr( $pickup_location_id ); ?>"
								data-package-id="<?php echo esc_attr( $package_key ); ?>"
							/>
						</td>
					</tr>
					<?php

					$html = ob_get_clean();
				}
			}
		}

		return $html;
	}


}
