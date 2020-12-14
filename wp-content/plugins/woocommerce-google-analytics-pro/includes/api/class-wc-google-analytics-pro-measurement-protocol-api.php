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
 * The Measurement Protocol API wrapper class.
 *
 * A basic wrapper around the GA Measurement Protocol HTTP API used for making
 * server-side API calls to track events.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro_Measurement_Protocol_API extends Framework\SV_WC_API_Base {


	/** @var string endpoint for GA API */
	public $ga_url = 'https://ssl.google-analytics.com/collect';

	/** @var string Google Analytics tracking ID */
	private $tracking_id;


	/**
	 * Constructs the class.
	 *
	 * @param int $tracking_id the configured Google Tracking ID
	 * @since 1.0.0
	 */
	public function __construct( $tracking_id ) {

		$this->tracking_id    = $tracking_id;
		$this->request_uri    = $this->ga_url;
		$this->request_method = 'POST';
	}


	/**
	 * Tracks an event via the Measurement Protocol.
	 *
	 * @see \WC_Google_Analytics_Pro_Measurement_Protocol_API_Request::identify()
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_name the event name, used from settings page
	 * @param string[] $identities identity params
	 * @param string[] $properties (optional) event properties, such as `eventCategory` and `eventAction`
	 * @param array $ec (optional) enhanced ecommerce action and any associated args to be sent with the event
	 */
	public function track_event( $event_name, $identities, $properties = [], $ec = [] ) {

		try {

			// make sure tracking code exists
			if ( empty( $this->tracking_id ) ) {
				return;
			}

			$request = $this->get_new_request();

			$request->identify( $identities );
			$request->track_event( $event_name, $properties );

			// add enhanced ecommerce data to request
			if ( ! empty( $ec ) ) {

				// get th checkout action
				$args   = reset( $ec );
				$action = key( $ec );

				$order   = ! empty( $args['order'] )   ? $args['order']   : null;
				$product = ! empty( $args['product'] ) ? $args['product'] : null;

				switch ( $action ) {

					case 'checkout':

						$step   = ! empty( $args['step'] )   ? $args['step']   : '';
						$option = ! empty( $args['option'] ) ? $args['option'] : '';

						$request->track_ec_checkout( $order, $step, $option );

					break;

					case 'checkout_option':

						$step   = ! empty( $args['step'] )   ? $args['step']   : '';
						$option = ! empty( $args['option'] ) ? $args['option'] : '';

						$request->track_ec_checkout_option( $step, $option );

					break;

					case 'purchase':
						$request->track_ec_purchase( $order );
					break;

					case 'refund':

						$refunded_items = ! empty( $args['refunded_items'] ) ? $args['refunded_items'] : null;

						$request->track_ec_refund( $order, $refunded_items );

					break;

					case 'add_to_cart':

						$item_key = ! empty( $args['cart_item_key'] ) ? $args['cart_item_key'] : '';
						$quantity = ! empty( $args['quantity'] ) ? $args['quantity'] : 1;

						$request->track_ec_add_to_cart( $product, $quantity, $item_key );

					break;

					case 'remove_from_cart':

						$cart_item = ! empty( $args['cart_item'] ) ? $args['cart_item'] : [];

						$request->track_ec_remove_from_cart( $product, $cart_item );

					break;
				}
			}

			$this->set_response_handler( 'WC_Google_Analytics_Pro_Measurement_Protocol_API_Response' );

			$this->perform_request( $request );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			/* translators: Placeholders: %s - error message */
			$error = sprintf( __( 'Error tracking event: %s', 'woocommerce-google-analytics-pro' ), $e->getMessage() );

			if ( wc_google_analytics_pro()->get_integration()->debug_mode_on() ) {
				wc_google_analytics_pro()->log( $error );
			}
		}
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @param string $type unused
	 * @return \WC_Google_Analytics_Pro_Measurement_Protocol_API_Request the request object
	 */
	protected function get_new_request( $type = null ){

		return new WC_Google_Analytics_Pro_Measurement_Protocol_API_Request( $this->tracking_id );
	}


	/**
	 * Gets the request user agent.
	 *
	 * Checks for the presence of a browser to send to Google Analytics.
	 *
	 * @see \Framework\SV_WC_API_Base::get_request_user_agent() for the default
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function get_request_user_agent() {

		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$user_agent = parent::get_request_user_agent();
		}

		return $user_agent;
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @see \Framework\SV_WC_API_Base::get_plugin()
	 *
	 * @since 1.0.0
	 * @return \WC_Google_Analytics_Pro
	 */
	protected function get_plugin() {

		return wc_google_analytics_pro();
	}


}
