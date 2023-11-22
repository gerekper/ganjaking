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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Contracts\Subscription_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Activate_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Cancel_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Reactivate_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Renew_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Subscription_Expiration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Subscription_Free_Trial_End;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Subscription_Prepaid_Term_End;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4\Suspend_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Activated_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Cancelled_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Reactivated_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Subscription_End_Of_Prepaid_Term;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Subscription_Expired;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Subscription_Free_Trial_Ended;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Subscription_Renewed;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics\Suspended_Subscription;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4\Reorder_Event;
use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

defined( 'ABSPATH' ) or exit;

/**
* Google Analytics Pro Subscriptions Integration
*
* Handles settings and functions needed to integrate with WooCommerce Subscriptions
*
* @since 1.5.0
*/
class Subscriptions_Integration {


	/**
	 * Sets up the Subscriptions integration.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		add_filter( 'wc_google_analytics_pro_event_classes_to_load', [ $this, 'add_subscription_event_classes' ] );

		add_filter( 'wc_google_analytics_pro_settings', [ $this, 'add_settings' ], 10, 2 );

		add_filter( 'wc_google_analytics_pro_do_not_track_completed_purchase', [ $this, 'set_subscription_ga_identity' ], 10, 2 );

		add_filter( 'wcs_new_order_created', [ $this, 'add_order_placed_meta' ] );

		if ( is_admin() && ! wp_doing_ajax() ) {
			add_action( 'admin_init', [ $this, 'ensure_subscription_events_have_default_values' ] );
		}
	}


	/**
	 * Adds subscription event classes to the be loaded by the Event_Tracking class.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $classes
	 * @return array
	 */
	public function add_subscription_event_classes( array $classes ) : array {

		// add GA4 subscription events after GA4 custom events
		$index = array_search( Reorder_Event::class, $classes, true );

		array_splice( $classes, $index + 1, 0, [
			Activate_Subscription::class,
			Subscription_Free_Trial_End::class,
			Subscription_Prepaid_Term_End::class,
			Subscription_Expiration::class,
			Suspend_Subscription::class,
			Reactivate_Subscription::class,
			Cancel_Subscription::class,
			Renew_Subscription::class,
		] );

		// add UA events
		array_push(
			$classes,
			Activated_Subscription::class,
			Subscription_Free_Trial_Ended::class,
			Subscription_End_Of_Prepaid_Term::class,
			Subscription_Expired::class,
			Suspended_Subscription::class,
			Reactivated_Subscription::class,
			Cancelled_Subscription::class,
			Subscription_Renewed::class,
		);

		return $classes;
	}


	/**
	 * Adds Subscriptions settings to the Google Analytics Pro settings page.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings
	 * @param Integration $integration
	 * @return array
	 */
	public function add_settings( $settings, $integration ): array {

		if ( ! $integration->is_connected() ) {
			return $settings;
		}

		// GA4 events
		$subscription_settings = [
			'subscription_event_names_section' => [
				// TODO: remove the suffix when removing support for UA {@itambek 2023-03-21}
				'title'       => __( 'Subscription Events', 'woocommerce-google-analytics-pro' ) . ' (GA4)',
				'description' => __( 'Customize the event names for Subscription events. Leave a field blank to disable tracking of that event.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'title',
			],
		];

		// try to append after custom events
		if ( array_key_exists( 'reorder_event_name', $settings ) ) {
			$settings = Framework\SV_WC_Helper::array_insert_after( $settings, 'reorder_event_name', $subscription_settings );
		} else {
			$settings = array_merge( $settings, $subscription_settings );
		}

		// UA events
		$ua_subscription_settings = [
			'ua_subscription_event_names_section' => [
				'title'       => __( 'Subscription Events', 'woocommerce-google-analytics-pro' ) . ' ' . __( '(Universal Analytics)', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Customize the event names for Subscription events. Leave a field blank to disable tracking of that event.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'title',
			],
		];

		// try to append after default events
		if ( array_key_exists( '404_error_event_name', $settings ) ) {
			$settings = Framework\SV_WC_Helper::array_insert_after( $settings, '404_error_event_name', $ua_subscription_settings );
		} else {
			$settings = array_merge( $settings, $ua_subscription_settings );
		}

		return $settings;
	}


	/**
	 * Sets a client identity on a Subscription and related orders that do not have one.
	 *
	 * This is useful when dealing with renewals of subscriptions that were created before Google Analytics Pro was active.
	 *
	 * @internal
	 *
	 * @since 1.5.2
	 *
	 * @param bool $track_completed_purchase whether to track a completed purchase, this is irrelevant for this method's purpose
	 * @param int $order_id the related order ID
	 * @return bool
	 */
	public function set_subscription_ga_identity( $track_completed_purchase, $order_id ): bool {

		$subscriptions = wcs_get_subscriptions_for_renewal_order( $order_id );

		if ( ! empty( $subscriptions ) ) {

			foreach ( $subscriptions as $subscription ) {

				$subscription_id  = $subscription->get_id();
				$subscription_cid = Order_Helper::get_order_ga_identity( $subscription_id );

				if ( empty( $subscription_cid ) ) {
					$subscription_cid = Order_Helper::store_ga_identity( $subscription_id );
				}

				if ( $subscription_cid && ! empty( $related_orders = $subscription->get_related_orders( 'ids' ) ) ) {

					foreach ( $related_orders as $related_order_id ) {

						$order_cid = Order_Helper::get_order_ga_identity( $related_order_id );

						if ( empty( $order_cid ) ) {
							Order_Helper::store_ga_identity( $related_order_id, $subscription_cid );
						}
					}
				}
			}
		}

		return $track_completed_purchase;
	}


	/**
	 * Adds a meta to mark renewal or resubscribe orders as placed.
	 *
	 * The meta `_wc_google_analytics_pro_placed` helps prevent tracking completed orders that were placed before GA Pro was enabled.
	 *
	 * Subscriptions does not trigger standard checkout actions and filters when a renewal or resubscribe order is created.
	 * Actions and filters like 'woocommerce_checkout_update_order_meta' or 'woocommerce_checkout_create_order'.
	 * As a result, GA Pro is unable to add the `_wc_google_analytics_pro_placed` meta to renewal and resubscribe orders.
	 *
	 * We add the meta here to track those orders even if the subscription was created before the plugin was activated.
	 *
	 * @see \WC_Google_Analytics_Pro_Integration::add_order_placed_meta()
	 *
	 * @internal
	 *
	 * @since 1.8.8
	 *
	 * @param \WC_Order $order the order object
	 * @return \WC_Order
	 */
	public function add_order_placed_meta( \WC_Order $order ): \WC_Order {

		Order_Helper::add_order_placed_meta( $order->get_id() );

		return $order;
	}


	/**
	 * Ensures that Subscription events have default values.
	 *
	 * This method should only set default event names once to ensure the user does not have to go through the settings page to set them.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function ensure_subscription_events_have_default_values(): void {

		if ( ! $this->get_integration()->is_enabled() ) {
			return;
		}

		$settings = $this->get_integration()->settings;

		if ( isset( $settings['renewed_subscription_event_name'] ) || isset( $settings['renew_subscription_event_name'] ) ) {
			return;
		}

		foreach( wc_google_analytics_pro()->get_tracking_instance()->get_event_tracking_instance()->get_events() as $event ) {

			if ( ! $event instanceof Subscription_Event ) {
				continue;
			}

			$key = $event::ID . '_event_name';

			if ( isset( $settings[ $key ] ) ) {
				continue;
			}

			$settings[ $key ] = $event->get_default_name();
		}

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		$this->get_integration()->init_settings();
	}


	/**
	 * Gets the integration instance.
	 *
	 * @since 1.5.0
	 *
	 * @return Integration
	 */
	public function get_integration(): Integration {
		return wc_google_analytics_pro()->get_integration();
	}


}
