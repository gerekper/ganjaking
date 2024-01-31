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

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "completed purchase" event.
 *
 * @since 2.0.0
 */
class Completed_Purchase_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'completed_purchase';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Completed Purchase', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer completes a purchase.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'completed purchase';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'woocommerce_order_status_on-hold',    [ $this, 'purchase_on_hold' ] );

		add_action( 'woocommerce_payment_complete',        [ $this, 'track' ] );
		add_action( 'woocommerce_order_status_processing', [ $this, 'track' ] );
		add_action( 'woocommerce_order_status_completed',  [ $this, 'track' ] );

		// catch orders processed through payment gateways such as COD
		add_action( 'woocommerce_thankyou', [ $this, 'track' ] );
	}


	/**
	 * Checks 'On Hold' orders to see if we should record a completed transaction or not.
	 *
	 * Currently, the only reason we might want to do this is if PayPal returns On Hold
	 * from the IPN. This is usually due to an email address mismatch, and the payment has
	 * technically already been captured at this point.
	 *
	 * @see https://github.com/skyverge/wc-plugins/issues/2332
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id
	 */
	public function purchase_on_hold( $order_id ): void {

		$order = wc_get_order( $order_id );

		if ('paypal' === $order->get_payment_method()) {
			$this->track( $order_id );
		}
	}


	/**
	 * @inheritdoc
	 *
	 * @param int $order_id the order ID
	 */
	public function track( $order_id = null ): void {

		/**
		 * Filters whether the completed purchase event should be tracked or not.
		 *
		 * @since 1.1.5
		 *
		 * @param bool $do_not_track true to not track the event, false otherwise
		 * @param int $order_id the order ID
		 */
		if ( true === apply_filters( 'wc_google_analytics_pro_do_not_track_completed_purchase', false, $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// can't track an order that doesn't exist
		if ( ! $order instanceof \WC_Order) {
			return;
		}

		// only track orders with a 'paid' order status
		if ( ! $order->is_paid() ) {
			return;
		}

		// bail if tracking is disabled but not if the status is being manually changed by the admin
		if ( ! Tracking::is_tracking_enabled_for_user_role( $order->get_customer_id() ) ) {
			return;
		}

		// don't track order when its already tracked
		if ( Order_Helper::is_order_tracked_in_ua( $order_id ) ) {
			return;
		}

		// don't track order when we haven't tracked the 'placed' event - this prevents tracking old orders that were placed before GA Pro was active
		if ( ! Order_Helper::was_order_placed_while_ga_enabled( $order_id ) ) {
			return;
		}

		/**
		 * Toggles whether to use cents or dollars for purchase events value.
		 *
		 * @since 1.5.2
		 *
		 * @param bool $use_cents whether to use cents (default, true) or dollars (false)
		 * @param string $event_name the event name
		 * @param \WC_Order $order related order object for the event
		 */
		$use_cents = (bool) apply_filters( 'wc_google_analytics_pro_purchase_event_use_cents', true, 'completed_purchase', $order );

		// record purchase event
		$properties = array(
			'eventCategory' => 'Checkout',
			'eventLabel'    => $order->get_order_number(),
			'eventValue'    => $use_cents ? round( $order->get_total() * 100 ) : floor( $order->get_total() ),
		);

		// set to non-interaction if this is a renewal order
		if (class_exists( 'WC_Subscriptions_Renewal_Order' ) && ( wcs_order_contains_resubscribe( $order ) || wcs_order_contains_renewal( $order ) )) {
			$properties['nonInteraction'] = 1;
		}

		$ec = [ 'purchase' => [ 'order' => $order ] ];

		$identities = Order_Helper::get_order_identities( $order );

		if ( $this->record_via_api( $properties, $ec, $identities, true ) ) {

			// mark order as tracked
			Order_Helper::set_order_tracked_in_ua( $order->get_id() );
		}
	}


}
