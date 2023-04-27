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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "provide billing email" event.
 *
 * @since 2.0.0
 */
class Provide_Billing_Email_Event extends GA4_Event implements Deferred_Event {


	/** @var string the event ID */
	public const ID = 'provide_billing_email';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';

	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Provide Billing Email', 'woocommerce-google-analytics-pro' );
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

		return 'provide_billing_email';
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		$this->record_via_js( [ 'category' => 'Checkout' ] );
	}


	/**
	 * @inheritdoc
	 */
	public function get_trigger_js(): string {

		$user_logged_in = is_user_logged_in();
		$billing_email  = $user_logged_in ? WC()->customer->get_billing_email() : '';

		// track the billing email only once for the logged-in user, if they have one
		if ( $user_logged_in && is_email( $billing_email ) && Tracking::not_page_reload() ) {
			return <<<JS
			if ( ! wc_ga_pro.payment_method_tracked ) { "__INSERT_TRACKING_CALL_HERE__"; }
			JS;
		}

		if ( ! $user_logged_in ) {
			// track billing email once it's provided & valid
			return <<<JS
			$( 'form.checkout' ).on( 'change', 'input#billing_email', function() {
				if ( ! wc_ga_pro.provided_billing_email && wc_ga_pro.is_valid_email( this.value ) ) {
					wc_ga_pro.provided_billing_email = true;
					"__INSERT_TRACKING_CALL_HERE__";
				}
			});
			JS;
		}

		return '';
	}


}
