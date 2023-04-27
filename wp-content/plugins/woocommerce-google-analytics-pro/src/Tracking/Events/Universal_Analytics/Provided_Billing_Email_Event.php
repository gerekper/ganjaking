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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "provided billing" email event.
 *
 * @since 2.0.0
 */
class Provided_Billing_Email_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'provided_billing_email';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Provided Billing Email', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer provides a billing email at checkout.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'provided billing email';
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		// bail if tracking is disabled
		if ( Tracking::do_not_track() ) {
			return;
		}

		$frontend = $this->get_frontend_handler_instance();

		// set event properties
		$properties = [
			'eventCategory' => 'Checkout',
		];

		// enhanced ecommerce tracking
		$handler_js = '';

		foreach ( WC()->cart->get_cart() as $item ) {

			// JS add product
			$handler_js .= $frontend->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
		}

		// JS checkout action
		$args = [ 'step' => 2 ];

		$handler_js .= $frontend->get_ec_action_js( 'checkout', $args );

		// event
		$handler_js .= $frontend->get_event_tracking_js( $this->get_name(), $properties );

		$user_logged_in = is_user_logged_in();
		$billing_email  = $user_logged_in ? WC()->customer->get_billing_email() : '';

		// track the billing email only once for the logged-in user, if they have one
		if ( $user_logged_in && is_email( $billing_email ) && Tracking::not_page_reload() ) {
			$js = sprintf( 'if ( ! wc_ga_pro.payment_method_tracked ) { %s };', $handler_js );
		} elseif ( ! $user_logged_in ) {
			// track billing email once it's provided & valid
			$js = sprintf( "$( 'form.checkout' ).on( 'change', 'input#billing_email', function() { if ( ! wc_ga_pro.provided_billing_email && wc_ga_pro.is_valid_email( this.value ) ) { wc_ga_pro.provided_billing_email = true; %s } });", $handler_js );
		}

		if ( ! empty( $js ) ) {
			$frontend->enqueue_js( 'event', $js );
		}
	}


}
