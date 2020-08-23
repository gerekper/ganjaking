<?php
/**
 * WooCommerce Twilio SMS Notifications
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Twilio_SMS\Message_Length_Calculator;

/**
 * Twilio SMS AJAX class
 *
 * Handles all AJAX actions
 *
 * @since 1.0
 */
class WC_Twilio_SMS_AJAX {


	/**
	 * Adds required wp_ajax_* hooks
	 *
	 * @since  1.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_woocommerce_twilio_sms_send_test_sms', array( $this, 'send_test_sms' ) );

		// Process 'Toggle automated updates' meta-box action
		add_action( 'wp_ajax_wc_twilio_sms_toggle_order_updates', array( $this, 'toggle_order_updates' ) );

		// Process 'Send an SMS' meta-box action
		add_action( 'wp_ajax_wc_twilio_sms_send_order_sms', array( $this, 'send_order_sms' ) );
	}

	/**
	 * Handle test SMS AJAX call
	 *
	 * @since  1.0
	 */
	public function send_test_sms() {

		$this->verify_request( $_POST['security'], 'wc_twilio_sms_send_test_sms' );

		// sanitize input
		$mobile_number = trim( $_POST['mobile_number'] );
		$message       = sanitize_text_field( $_POST['message'] );

		try {

			// shorten URLs if enabled
			if ( \WC_Twilio_SMS_URL_Shortener::using_shortened_urls() ) {
				$message = \WC_Twilio_SMS_URL_Shortener::shorten_urls( $message );
			}

			// truncate message to simulate settings output
			$concatenate = 'yes' === get_option( 'wc_twilio_sms_allow_concatenate_messages', 'no' );
			$segments    = Message_Length_Calculator::get_message_segments_count( $message );

			if ( ! $concatenate || $segments >= 10 ) {
				$message = Framework\SV_WC_Helper::str_truncate( $message, Message_Length_Calculator::get_characters_count_limit( $message, $concatenate ) );
			}

			wc_twilio_sms()->get_api()->send( $mobile_number, $message );

			exit( __( 'Test message sent successfully', 'woocommerce-twilio-sms-notifications' ) );

		} catch ( Exception $e ) {

			die( sprintf( __( 'Error sending SMS: %s', 'woocommerce-twilio-sms-notifications' ), $e->getMessage() ) );
		}
	}


	/**
	 * Toggle automated SMS messages from the edit order page
	 *
	 * @since 1.6.0
	 */
	public function toggle_order_updates() {

		$this->verify_request( $_POST['security'], 'wc_twilio_sms_toggle_order_updates' );

		$order_id = ( is_numeric( $_POST['order_id'] ) ) ? absint( $_POST['order_id'] ) : null;

		if ( $order_id && $order = wc_get_order( $order_id ) ) {

			$current_status = $order->get_meta( '_wc_twilio_sms_optin' );

			if ( empty( $current_status ) ) {
				$order->update_meta_data( '_wc_twilio_sms_optin', 1 );
			} else {
				$order->delete_meta_data( '_wc_twilio_sms_optin' );
			}

			$order->save_meta_data();
		}

		exit();
	}


	/**
	 * Send an SMS from the edit order page
	 *
	 * @since 1.1.4
	 */
	public function send_order_sms() {

		$this->verify_request( $_POST['security'], 'wc_twilio_sms_send_order_sms' );

		// sanitize message
		$message = sanitize_text_field( $_POST[ 'message' ] );

		$order_id = ( is_numeric( $_POST['order_id'] ) ) ? absint( $_POST['order_id'] ) : null;

		if ( ! $order_id ) {
			return;
		}

		$notification = new \WC_Twilio_SMS_Notification( $order_id );

		// send the SMS
		$notification->send_manual_customer_notification( $message );

		exit( __( 'Message Sent', 'woocommerce-twilio-sms-notifications' ) );
	}


	/**
	 * Verifies AJAX request is valid
	 *
	 * @since  1.0
	 * @param string $nonce
	 * @param string $action
	 * @return void|bool
	 */
	private function verify_request( $nonce, $action ) {

		if ( ! is_admin() || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-twilio-sms-notifications' ) );
		}

		if( ! wp_verify_nonce( $nonce, $action ) ) {
			wp_die( __( 'You have taken too long, please go back and try again.', 'woocommerce-twilio-sms-notifications' ) );
		}

		return true;
	}


}
