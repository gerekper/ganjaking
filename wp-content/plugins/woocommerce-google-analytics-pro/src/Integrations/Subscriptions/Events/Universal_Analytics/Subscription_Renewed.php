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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Contracts\Subscription_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Traits\Tracks_Subscription_Events;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "subscription renewed" event.
 *
 * @since 2.0.0
 */
class Subscription_Renewed extends Universal_Analytics_Event implements Subscription_Event {

	use Tracks_Subscription_Events;


	/** @var string the event ID */
	public const ID = 'subscription_renewed';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_renewal_order_payment_complete';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Subscription Renewed', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer is automatically billed for a subscription renewal.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'subscription billed';
	}


	/**
	 * @inheritdoc
	 *
	 * @param int|string $renewal_order_id
	 */
	public function track( $renewal_order_id = null ): void {

		$renewal_order = wc_get_order( $renewal_order_id );
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		foreach ( $subscriptions as $subscription ) {

			/* this filter is documented in class-wc-google-analytics-pro-integration.php */
			$use_cents = (bool) apply_filters( 'wc_google_analytics_pro_purchase_event_use_cents', true, $this->get_name(), $renewal_order );

			$this->track_subscription_event_in_ua(
				$subscription,
				[],
				false,
				$use_cents ? round( $renewal_order->get_total() * 100 ) : floor( $renewal_order->get_total() )
			);
		}
	}


}
