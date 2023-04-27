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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Traits;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;
use WC_Subscription;

defined( 'ABSPATH' ) or exit;

/**
 * Trait for tracking subscription events.
 *
 * This trait can be used by both UA and GA4 events.
 *
 * @since 2.0.0
 */
trait Tracks_Subscription_Events {


	/**
	 * Enables tracking in situations where it would normally be disabled.
	 * i.e. subscription changes by an admin / shop manager in an admin context.
	 *
	 * @since 2.0.0
	 */
	protected function override_disabled_tracking(): void {

		add_filter( 'wc_google_analytics_pro_do_not_track', '__return_false' );
	}


	/**
	 * Tracks the subscription event in Universal Analytics.
	 *
	 * @since 2.0.0
	 *
	 * @UA
	 *
	 * @param WC_Subscription $subscription
	 * @param array $identities
	 * @param bool $is_user_interaction
	 * @param int|null $value
	 * @return void
	 */
	protected function track_subscription_event_in_ua( WC_Subscription $subscription, array $identities = [], bool $is_user_interaction = false, int $value = null ): void {

		$this->override_disabled_tracking();

		$identities = $identities ?: [
			'uid' => $subscription->get_user_id(),
			'cid' => Order_Helper::get_order_ga_identity( $subscription->get_id() ),
		];

		$properties = [
			'eventCategory'  => 'Subscriptions',
			'eventLabel'     => $subscription->get_id(),
			'eventValue'     => $value,
			'nonInteraction' => ! $is_user_interaction,
		];

		/** @see Universal_Analytics_Event::record_via_api() */
		$this->record_via_api( $properties, [], $identities );
	}


	/**
	 * Tracks the subscription event in GA4.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Subscription $subscription
	 * @param array $identities
	 * @param array $properties
	 * @return void
	 */
	protected function track_subscription_event( WC_Subscription $subscription, array $identities = [], array $properties = [] ): void {

		$this->override_disabled_tracking();

		$identities = $identities ?: [
			'uid' => $subscription->get_user_id(),
			'cid' => Order_Helper::get_order_ga_identity( $subscription->get_id() ),
		];

		$properties = array_merge( [
			'category'        => 'Subscriptions',
			'subscription_id' => $subscription->get_id(),
		], $properties );

		/** @see GA4_Event::record_via_api() */
		$this->record_via_api( $properties, $identities );
	}


}
