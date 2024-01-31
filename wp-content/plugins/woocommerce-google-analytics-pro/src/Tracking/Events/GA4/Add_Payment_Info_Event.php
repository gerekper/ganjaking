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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Cart_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_AJAX_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Traits\Has_Deferred_AJAX_Trigger;

defined( 'ABSPATH' ) or exit;

/**
 * The "add payment info" event.
 *
 * @link https://developers.google.com/analytics/devguides/collection/ga4/reference/events?client_type=gtag#add_payment_info
 *
 * @since 2.0.0
 */
class Add_Payment_Info_Event extends GA4_Event implements Deferred_AJAX_Event {


	use Has_Deferred_AJAX_Trigger;


	/** @var string the event ID */
	public const ID = 'add_payment_info';

	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = true;

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';

	/** @var string the ajax action name  */
	protected string $ajax_action = 'wc_google_analytics_pro_add_payment_info';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Add Payment Info', 'woocommerce-google-analytics-pro' );
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

		return 'add_payment_info';
	}


	/**
	 * @inheritdoc
	 */
	public function get_trigger_js(): string {

		/**
		 * Filters whether the initial payment method selection should be ignored.
		 *
		 * WooCommerce automatically selects a payment method when the checkout page is loaded.
		 * Allow the tracking of this automatic selection to be enabled or disabled.
		 *
		 * @since 1.4.1
		 *
		 * @param bool $ignore_initial_payment_method_selection
		 */
		$ignore_initial_selection = apply_filters( 'wc_google_analytics_pro_ignore_initial_payment_method_selection', true );

		$selected_method = $ignore_initial_selection ? <<<JS
		$( "input[name='payment_method']:checked" ).val()
		JS : 'null';

		// listen to payment method selection event
		return <<<JS
		(() => {
			let tracked_payment_method  = false;
			let selected_payment_method = {$selected_method};

			function trackEvent( payment_method ) {
				// noinspection JSAnnotator
				"__INSERT_AJAX_CALL_HERE__"({ payment_method });
			}

			$( 'form.checkout' ).on( 'click', 'input[name="payment_method"]', function() {
				if ( selected_payment_method !== this.value ) {
					tracked_payment_method = true;
					selected_payment_method = this.value;

					let trackCalled = false;

					function callTrackEvent () {
						if (!trackCalled) {
							trackEvent( selected_payment_method )
							trackCalled = true;
						}
					}

					// wait for the AJAX cart update to complete, or track after 5 seconds
					$( document.body ).on( 'updated_checkout', callTrackEvent );

					setTimeout(callTrackEvent, 5000);
				}
			});

			// fall back to sending the payment method on checkout_place_order (clicked place order)
			$( 'form.checkout' ).on( 'checkout_place_order', function() {
				if ( ! tracked_payment_method ) {
					trackEvent( $( 'input[name="payment_method"]' ).val() )
				}
			});
		})();
		JS;
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		check_ajax_referer( $this->ajax_action, 'security' );

		$payment_method = $_POST['payment_method'];
		$gateway        = WC()->payment_gateways->get_available_payment_gateways()[ $payment_method ] ?? null;

		$this->record_via_api( array_merge(
			[
				'category'     => 'Checkout',
				'payment_type' => $gateway ? html_entity_decode( wp_strip_all_tags( $gateway->get_title() ) ) : $payment_method,
			],
			( new Cart_Event_Data_Adapter( WC()->cart ) )->convert_from_source(),
		) );
	}


}
