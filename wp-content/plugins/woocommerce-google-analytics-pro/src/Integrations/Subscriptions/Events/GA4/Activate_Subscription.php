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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Contracts\Subscription_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Traits\Tracks_Subscription_Events;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use WC_Order;

defined( 'ABSPATH' ) or exit;

/**
 * The "activate subscription" event.
 *
 * Tracks subscription activations (only after successful payment for subscription).
 *
 * @since 2.0.0
 */
class Activate_Subscription extends GA4_Event implements Subscription_Event {

	use Tracks_Subscription_Events;


	/** @var string the event ID */
	public const ID = 'activate_subscription';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'subscriptions_activated_for_order';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Activate Subscription', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer activates their subscription.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'activate_subscription';
	}


	/**
	 * @inheritdoc
	 *
	 * @param WC_Order $order order instance
	 */
	public function track( $order = null ): void {

		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order );
		}

		$subscriptions = wcs_get_subscriptions_for_order( $order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		$identities = Order_Helper::get_order_identities( $order );

		foreach ( $subscriptions as $subscription ) {

			$this->track_subscription_event( $subscription, $identities, [
				'currency' => $subscription->get_currency(),
				'value'    => $subscription->get_total_initial_payment()
			] );
		}
	}


}
