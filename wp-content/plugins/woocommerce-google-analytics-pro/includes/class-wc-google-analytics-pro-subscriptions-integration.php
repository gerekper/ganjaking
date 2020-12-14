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
* @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
* @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
*/

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
* Google Analytics Pro Subscriptions Integration
*
* Handles settings and functions needed to integrate with WooCommerce Subscriptions
*
* @since 1.5.0
*/
class WC_Google_Analytics_Pro_Subscriptions_Integration {


	/**
	 * Sets up the Subscriptions integration.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		add_filter( 'wc_google_analytics_pro_settings', array( $this, 'add_settings' ) );

		add_filter( 'wc_google_analytics_pro_do_not_track_completed_purchase', array( $this, 'set_subscription_ga_identity'), 10, 2 );

		add_filter( 'wcs_new_order_created', [ $this, 'add_order_placed_meta' ] );

		add_action( 'woocommerce_init', array( $this, 'init_hooks' ) );

		if ( is_admin() && ! is_ajax() ) {
			add_action( 'admin_init', array( $this, 'maybe_add_update_settings_notice' ) );
		}
	}


	/**
	 * Adds a notice if Subscriptions is active but Google Analytics Pro settings haven't
	 * been re-saved yet with the additional subscription-specific event names.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function maybe_add_update_settings_notice() {

		if ( ! isset( $this->get_integration()->settings['renewed_subscription_event_name'] ) ) {

			wc_google_analytics_pro()->get_admin_notice_handler()->add_admin_notice(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				sprintf( __( 'Please %1$supdate%2$s your Google Analytics Pro settings in order to start tracking Subscription events.', 'woocommerce-google-analytics-pro' ), '<a href="' . esc_url( wc_google_analytics_pro()->get_settings_url() ) . '">', '</a>' ),
				'subscriptions-update-settings',
				array( 'always_show_on_settings' => true, 'dismissible' => true )
			);
		}
	}



	/**
	 * Adds hooks for settings and events.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function init_hooks() {

		$event_hooks = array(
			'activated_subscription'           => 'subscriptions_activated_for_order',
			'reactivated_subscription'         => 'woocommerce_subscription_status_on-hold_to_active',
			'suspended_subscription'           => 'woocommerce_subscription_status_on-hold',
			'cancelled_subscription'           => 'woocommerce_subscription_status_cancelled',
			'subscription_trial_ended'         => 'woocommerce_scheduled_subscription_trial_end',
			'subscription_end_of_prepaid_term' => 'woocommerce_scheduled_subscription_end_of_prepaid_term',
			'subscription_expired'             => 'woocommerce_scheduled_subscription_expiration',
			'renewed_subscription'             => 'woocommerce_renewal_order_payment_complete',
		);

		foreach ( $event_hooks as $event_name => $hook ) {

			if ( $this->get_integration()->has_event( $event_name ) ) {

				$callback = array( $this, $event_name );

				add_action( $hook, $callback, 10, 1 );
			}
		}

	}


	/**
	 * Tracks subscription activations (only after successful payment for subscription).
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order order instance
	 */
	public function activated_subscription( $order ) {

		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( $order );
		}

		$subscriptions = wcs_get_subscriptions_for_order( $order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		foreach ( $subscriptions as $subscription ) {

			$identities = $this->get_integration()->get_order_identities( $order );

			$this->track_subscription_event( 'activated_subscription', $subscription, false, $identities, floor( $subscription->get_total_initial_payment() ) );
		}
	}


	/**
	 * Tracks subscription re-activations (on-hold to active status).
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Subscription $subscription
	 */
	public function reactivated_subscription( $subscription ) {
		$this->track_subscription_event( 'reactivated_subscription', $subscription, true );
	}


	/**
	 * Tracks subscription suspensions (on-hold status).
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Subscription $subscription
	 */
	public function suspended_subscription( $subscription ) {
		$this->track_subscription_event( 'suspended_subscription', $subscription, true );
	}


	/**
	 * Tracks subscription cancellations (cancelled status).
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Subscription $subscription
	 */
	public function cancelled_subscription( $subscription ) {
		$this->track_subscription_event( 'cancelled_subscription', $subscription, true );
	}


	/**
	 * Tracks subscription trial end.
	 *
	 * @since 1.5.0
	 *
	 * @param int|string $subscription_id
	 */
	public function subscription_trial_ended( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );

		$this->track_subscription_event( 'subscription_trial_ended', $subscription, true );

		// handle trial conversions - if a subscription has more than a single completed payment, assume it converted
		if ( version_compare( \WC_Subscriptions::$version, '2.6', '>=' ) ) {

			$payment_count = $subscription->get_payment_count();

		} else {

			$payment_count = $subscription->get_completed_payment_count();
		}

		if ( $payment_count > 1 ) {
			$this->track_subscription_event( 'subscription_trial_converted', $subscription, true );
		} else {
			$this->track_subscription_event( 'subscription_trial_cancelled', $subscription, true );
		}
	}


	/**
	 * Tracks the end of pre-paid term action for a subscription.
	 *
	 * This is triggered when a subscription is cancelled prior to the end date
	 * (e.g. cancelled 14 days into a monthly subscription, and the month has been paid for up-front).
	 *
	 * @since 1.5.0
	 *
	 * @param int|string $subscription_id
	 */
	public function subscription_end_of_prepaid_term( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );
		$this->track_subscription_event( 'subscription_end_of_prepaid_term', $subscription, true );
	}


	/**
	 * Tracks subscription expiration.
	 *
	 * @since 1.5.0
	 *
	 * @param int|string $subscription_id
	 */
	public function subscription_expired( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );
		$this->track_subscription_event( 'subscription_expired', $subscription, true );
	}


	/**
	 * Tracks subscription renewal payments.
	 *
	 * @since 1.5.0
	 *
	 * @param int|string $renewal_order_id
	 */
	public function renewed_subscription( $renewal_order_id ) {

		$this->enable_tracking();

		$renewal_order = wc_get_order( $renewal_order_id );
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		$event_name = 'renewed_subscription';

		foreach ( $subscriptions as $subscription ) {

			/* this filter is documented in class-wc-google-analytics-pro-integration.php */
			$use_cents = (bool) apply_filters( 'wc_google_analytics_pro_purchase_event_use_cents', true, $event_name, $renewal_order );

			$this->track_subscription_event(
				$event_name,
				$subscription,
				true,
				null,
				$use_cents ? round( $renewal_order->get_total() * 100 ) : floor( $renewal_order->get_total() )
			);
		}
	}


	/**
	 * Tracks a Subscriptions event.
	 *
	 * @since 1.5.0
	 *
	 * @param string $event_name the name of the event, also defaults as the eventAction
	 * @param \WC_Subscription $subscription the subscription object this event is related to
	 * @param bool $nonInteraction (optional) whether the event was caused by user-interaction or not
	 * @param array $identity (optional) array of identifying data to send
	 * @param int|string $value (optional) value to attribute to the event. Google Analytics only accepts integer values.
	 */
	protected function track_subscription_event( $event_name, $subscription, $nonInteraction = false, $identity = null, $value = null ) {

		$events = $this->get_integration()->event_name;

		if ( is_array( $events ) && isset( $events[ $event_name ] ) ) {

			$this->enable_tracking();

			$identity = $identity ?: [
				'uid' => $subscription->get_user_id(),
				'cid' => $this->get_integration()->get_order_ga_identity( $subscription->get_id() ),
			];

			$properties = [
				'eventCategory'  => 'Subscriptions',
				'eventLabel'     => $subscription->get_id(),
				'eventValue'     => $value,
				'nonInteraction' => $nonInteraction,
			];

			$this->get_integration()->api_record_event( $events[ $event_name ], $properties, array(), $identity );
		}
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
	public function set_subscription_ga_identity( $track_completed_purchase, $order_id ) {

		$subscriptions = wcs_get_subscriptions_for_renewal_order( $order_id );

		if ( ! empty( $subscriptions ) ) {

			foreach ( $subscriptions as $subscription ) {

				$subscription_id  = $subscription->get_id();
				$subscription_cid = $this->get_integration()->get_order_ga_identity( $subscription_id );

				if ( empty( $subscription_cid ) ) {
					$subscription_cid = $this->get_integration()->store_ga_identity( $subscription_id );
				}

				$related_orders = $subscription->get_related_orders( 'ids' );

				if ( $subscription_cid && ! empty( $related_orders ) ) {

					foreach ( $related_orders as $related_order_id ) {

						$order_cid = $this->get_integration()->get_order_ga_identity( $related_order_id );

						if ( empty( $order_cid ) ) {
							$this->get_integration()->store_ga_identity( $related_order_id, $subscription_cid );
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
	public function add_order_placed_meta( $order ) {

		$this->get_integration()->add_order_placed_meta( $order->get_id() );

		return $order;
	}


	/**
	 * Adds Subscriptions settings to the Google Analytics Pro settings page.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings
	 * @return array
	 */
	public function add_settings( $settings ) {

		$subscription_settings = array(
			'subscription_event_names_section'            => array(
				'title'       => __( 'Subscription Event Names', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Customize the event names for Subscription events. Leave a field blank to disable tracking of that event.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'title',
			),
			'activated_subscription_event_name'           => array(
				'title'       => __( 'Activated Subscription', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer activates their subscription.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'activated subscription',
			),
			'subscription_trial_ended_event_name'         => array(
				'title'       => __( 'Subscription Free Trial Ended', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a the free trial ends for a subscription.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'subscription trial ended',
			),
			'subscription_end_of_prepaid_term_event_name' => array(
				'title'       => __( 'Subscription End of Pre-Paid Term', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when the end of a pre-paid term for a previously cancelled subscription is reached.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'subscription prepaid term ended',
			),
			'subscription_expired_event_name'             => array(
				'title'       => __( 'Subscription Expired', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a subscription expires.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'subscription expired',
			),
			'suspended_subscription_event_name'           => array(
				'title'       => __( 'Suspended Subscription', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer suspends their subscription.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'suspended subscription',
			),
			'reactivated_subscription_event_name'         => array(
				'title'       => __( 'Reactivated Subscription', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer reactivates their subscription.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'reactivated subscription',
			),
			'cancelled_subscription_event_name'           => array(
				'title'       => __( 'Cancelled Subscription', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer cancels their subscription.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'cancelled subscription',
			),
			'renewed_subscription_event_name'             => array(
				'title'       => __( 'Renewed Subscription', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer is automatically billed for a subscription renewal.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'subscription billed',
			),
		);

		// try to append after default events
		if ( array_key_exists( 'reordered_event_name', $settings ) ) {
			$settings = Framework\SV_WC_Helper::array_insert_after( $settings, 'reordered_event_name', $subscription_settings );
		} else {
			$settings = array_merge( $settings, $subscription_settings );
		}

		return $settings;
	}


	/**
	 * Enables tracking in situations where it would normally be disabled.
	 * i.e. subscription changes by an admin / shop manager in an admin context.
	 *
	 * @since 1.5.0
	 */
	protected function enable_tracking() {
		add_filter( 'wc_google_analytics_pro_do_not_track', '__return_false' );
	}


	/**
	 * Gets the integration instance.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_Google_Analytics_Pro_Integration
	 */
	public function get_integration() {
		return wc_google_analytics_pro()->get_integration();
	}


}
