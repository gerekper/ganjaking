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
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "selected payment" method event.
 *
 * @since 2.0.0
 */
class Selected_Payment_Method_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'selected_payment_method';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Selected Payment Method', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer selects a payment method at checkout.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'selected payment method';
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		// bail if tracking is disabled
		if ( Tracking::do_not_track() ) {
			return;
		}

		// set event properties
		$properties = [
			'eventCategory' => 'Checkout',
			'eventLabel'    => '{$payment_method}',
		];

		$frontend = $this->get_frontend_handler_instance();

		// enhanced ecommerce tracking
		$handler_js = '';

		foreach ( WC()->cart->get_cart() as $item ) {

			// JS add product
			$handler_js .= $frontend->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
		}

		// JS checkout action
		$args = array('step' => 3, 'option' => '{$payment_method}');

		$handler_js .= $frontend->get_ec_action_js( 'checkout', $args, 'args' );

		// event
		$handler_js .= $frontend->get_event_tracking_js( $this->get_name(), $properties, 'args' );

		$js = '';

		/**
		 * Filters whether the initial payment method selection should be ignored.
		 *
		 * WooCommerce automatically selects a payment method when the checkout page is loaded.
		 * Allow the tracking of this automatic selection to be enabled or disabled.
		 *
		 * @param bool $ignore_initial_payment_method_selection
		 * @since 1.4.1
		 *
		 */
		if ( true === apply_filters( 'wc_google_analytics_pro_ignore_initial_payment_method_selection', true ) ) {
			$js .= 'wc_ga_pro.selected_payment_method = $( "input[name=\'payment_method\']:checked" ).val();';
		}

		// listen to payment method selection event
		$js .= sprintf( "$( 'form.checkout' ).on( 'click', 'input[name=\"payment_method\"]', function( e ) { if ( wc_ga_pro.selected_payment_method !== this.value ) { var args = { payment_method: wc_ga_pro.get_payment_method_title( this.value ) }; wc_ga_pro.payment_method_tracked = true; %s wc_ga_pro.selected_payment_method = this.value; } });", $handler_js );

		// fall back to sending the payment method on checkout_place_order (clicked place order)
		$js .= sprintf( "$( 'form.checkout' ).on( 'checkout_place_order', function() { if ( ! wc_ga_pro.payment_method_tracked ) { var args = { payment_method: wc_ga_pro.get_payment_method_title( $( 'input[name=\"payment_method\"]' ).val() ) }; %s } });", $handler_js );

		$frontend->enqueue_js( 'event', $js );
	}


}
