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
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Traits\Has_Deferred_AJAX_Trigger;
use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The "add shipping info" event.
 *
 * @link https://developers.google.com/analytics/devguides/collection/ga4/reference/events?client_type=gtag#add_shipping_info
 *
 * @since 2.0.0
 */
class Add_Shipping_Info_Event extends GA4_Event implements Deferred_AJAX_Event {


	use Has_Deferred_AJAX_Trigger;


	/** @var string the event ID */
	public const ID = 'add_shipping_info';

	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = true;

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';

	/** @var string the ajax action name  */
	protected string $ajax_action = 'wc_google_analytics_pro_add_shipping_info';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Add Shipping Info', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer selects a shipping method at checkout.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'add_shipping_info';
	}


	/**
	 * @inheritdoc
	 */
	public function get_trigger_js(): string {

		/**
		 * Filters whether the initial shipping method selection should be ignored.
		 *
		 * WooCommerce automatically selects a shipping method when the checkout page is loaded.
		 * Allow the tracking of this automatic selection to be enabled or disabled.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $ignore_initial_shipping_method_selection
		 */
		$ignore_initial_selection = apply_filters( 'wc_google_analytics_pro_ignore_initial_shipping_method_selection', true );

		$selected_method = $ignore_initial_selection ? <<<JS
		$( 'input[name^="shipping_method"]:checked' ).val()
		JS : 'null';

		// listen to shipping method selection event
		return <<<JS
		(() => {
			let tracked_shipping_method   = false;
			let selected_shipping_method = {$selected_method};

			function trackEvent( shipping_method ) {
				// noinspection JSAnnotator
				"__INSERT_AJAX_CALL_HERE__"({ shipping_method });
			}

			$( 'form.checkout' ).on( 'change', 'input[name^="shipping_method"]', function() {
				if ( selected_shipping_method !== this.value ) {
					tracked_shipping_method = true;
					selected_shipping_method = this.value;

					let trackCalled = false;

					function callTrackEvent () {
						if (!trackCalled) {
							trackEvent( selected_shipping_method )
							trackCalled = true;
						}
					}

					// wait for the AJAX cart update to complete, or track after 5 seconds
					$( document.body ).on( 'updated_checkout', callTrackEvent );

					setTimeout(callTrackEvent, 5000);
				}
			});

			// fall back to sending the shipping method on checkout_place_order (clicked place order)
			$( 'form.checkout' ).on( 'checkout_place_order', function() {
				if ( ! tracked_shipping_method ) {
					trackEvent( $( 'input[name^="shipping_method"]' ).val() )
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

		$shipping_method = Framework\SV_WC_Helper::get_posted_value( 'shipping_method' );
		$shipping_method = is_string( $shipping_method ) ? explode( ':', $shipping_method )[0] ?? null : null;

		if ( empty( $shipping_method ) ) {
			return;
		}

		$method = WC()->shipping()->get_shipping_methods()[ $shipping_method ] ?? null;

		$this->record_via_api( array_merge(
			[
				'category'      => 'Checkout',
				'shipping_tier' => $method ? html_entity_decode( wp_strip_all_tags( $method->get_method_title() ) ) : $method,
			],
			( new Cart_Event_Data_Adapter( WC()->cart ) )->convert_from_source(),
		) );
	}


}
