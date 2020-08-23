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

/**
 * Twilio SMS Notification class
 *
 * Handle SMS sending Admin & Customer Notifications, as well as manual SMS messages from Order page
 *
 * @since 1.0
 */
class WC_Twilio_SMS_Notification  {


	/** @var \WC_Order order object for SMS sending  */
	private $order;


	/**
	 * Load new order object
	 *
	 * @since 1.0
	 * @param int $order_id the order ID
	 */
	public function __construct( $order_id ) {

		$this->order = wc_get_order( $order_id );

	}


	/**
	 * Send admin new order SMS notifications
	 *
	 * @since 1.0
	 */
	public function send_admin_notification() {

		// Check if sending admin SMS updates for new orders
		if ( 'yes' === get_option( 'wc_twilio_sms_enable_admin_sms' ) ) {

			// get message template
			$message = get_option( 'wc_twilio_sms_admin_sms_template', '' );

			// replace template variables
			$message = $this->replace_message_variables( $message );

			// shorten URLs if enabled
			if ( \WC_Twilio_SMS_URL_Shortener::using_shortened_urls() ) {
				$message = \WC_Twilio_SMS_URL_Shortener::shorten_urls( $message );
			}

			// get admin phone number (s)
			$recipients = explode( ',', trim( get_option( 'wc_twilio_sms_admin_sms_recipients' ) ) );

			// send the SMS to each recipient
			if ( ! empty( $recipients ) ) {

				foreach ( $recipients as $recipient ) {

					try {

						wc_twilio_sms()->get_api()->send( $recipient, $message, false );

					} catch ( Exception $e ) {

						wc_twilio_sms()->log( $e->getMessage() );
					}
				}
			}
		}
	}


	/**
	 * Sends customer SMS notifications on order status changes
	 *
	 * @since  1.0
	 */
	public function send_automated_customer_notification() {

		// get checkbox opt-in label
		$optin = get_option( 'wc_twilio_sms_checkout_optin_checkbox_label', '' );

		// check if opt-in checkbox is enabled
		if ( ! empty( $optin ) ) {

			// get opt-in meta for order
			$optin = $this->order->get_meta( '_wc_twilio_sms_optin' );

			// check if customer has opted-in
			if ( empty( $optin ) ) {
				// no meta set, so customer has not opted in
				return;
			}
		}

		// Check if sending SMS updates for this order's status
		if ( in_array( 'wc-' . $this->order->get_status(), get_option( 'wc_twilio_sms_send_sms_order_statuses' ) ) ) {

			// get message template
			$message = get_option( 'wc_twilio_sms_' . $this->order->get_status() . '_sms_template', '' );

			// use the default template if status-specific one is blank
			if ( empty( $message ) ) {
				$message = get_option( 'wc_twilio_sms_default_sms_template' );
			}

			// allow modification of message before variable replace (add additional variables, etc)
			$message = apply_filters( 'wc_twilio_sms_customer_sms_before_variable_replace', $message, $this->order );

			// replace template variables
			$message = $this->replace_message_variables( $message );

			// allow modification of message after variable replace
			$message = apply_filters( 'wc_twilio_sms_customer_sms_after_variable_replace', $message, $this->order );

			// allow modification of the "to" phone number
			$phone = apply_filters( 'wc_twilio_sms_customer_phone', $this->order->get_billing_phone( 'edit' ), $this->order );

			// shorten URLs if enabled
			if ( \WC_Twilio_SMS_URL_Shortener::using_shortened_urls() ) {
				$message = \WC_Twilio_SMS_URL_Shortener::shorten_urls( $message );
			}

			// send the SMS!
			$this->send_sms( $phone, $message );
		}
	}


	/**
	 * Sends SMS to customer from 'Send an SMS' metabox on Orders page
	 *
	 * @since 1.0
	 * @param string $message message to send customer
	 */
	public function send_manual_customer_notification( $message ) {

		// shorten URLs if enabled
		if ( \WC_Twilio_SMS_URL_Shortener::using_shortened_urls() ) {
			$message = \WC_Twilio_SMS_URL_Shortener::shorten_urls( $message );
		}

		// send the SMS!
		$this->send_sms( $this->order->get_billing_phone( 'edit' ), $message );
	}


	/**
	 * Create and send SMS message
	 *
	 * @since 1.0
	 * @param string $to
	 * @param string $message
	 * @param bool $customer_notification order note is added if true
	 */
	private function send_sms( $to, $message, $customer_notification = true ) {

		// Default status for SMS message, on error this is replaced with error message
		$status = __( 'Sent', 'woocommerce-twilio-sms-notifications' );

		// Timestamp of SMS is current time
		$sent_timestamp =  time();

		// error flag
		$error = false;

		try {

			// send the SMS via API
			$response = wc_twilio_sms()->get_api()->send( $to, $message, $this->order->get_billing_country( 'edit' ) );

			// use the timestamp from twilio if available
			$sent_timestamp = ( isset( $response['date_created'] ) ) ? strtotime( $response['date_created'] ) : $sent_timestamp;

			// use twilio formatted number if available
			$to = ( isset( $response['to'] ) ) ? $response['to'] : $to;

		} catch ( Exception $e ) {

			// Set status to error message
			$status = $e->getMessage();

			// set error flag
			$error = true;

			// log to PHP error log
			wc_twilio_sms()->log( $e->getMessage() );
		}

		// Add formatted order note
		if ( $customer_notification ) {
			$this->order->add_order_note( $this->format_order_note( $to, $sent_timestamp, $message, $status, $error ) );
		}
	}


	/**
	 * Replaces template variables in SMS message
	 *
	 * @since 1.0
	 * @param string $message raw SMS message to replace with variable info
	 * @return string message with variables replaced with indicated values
	 */
	private function replace_message_variables( $message ) {

		$replacements = array(
			'%shop_name%'       => Framework\SV_WC_Helper::get_site_name(),
			'%order_id%'        => $this->order->get_order_number(),
			'%order_count%'     => $this->order->get_item_count(),
			'%order_amount%'    => $this->order->get_total(),
			'%order_status%'    => ucfirst( $this->order->get_status() ),
			'%billing_name%'    => $this->order->get_formatted_billing_full_name(),
			'%shipping_name%'   => $this->order->get_formatted_shipping_full_name(),
			'%shipping_method%' => $this->order->get_shipping_method(),
			'%billing_first%'   => $this->order->get_billing_first_name( 'edit' ),
			'%billing_last%'    => $this->order->get_billing_last_name( 'edit' ),
		);

		/**
		 * Filter the notification placeholders and replacements.
		 *
		 * @since 1.6.0
		 * @param array $replacements {
		 *     The replacements in 'placeholder' => 'replacement' format.
		 *
		 *     @type string %shop_name%       The site name.
		 *     @type int    %order_id%        The order ID.
		 *     @type int    %order_count%     The total number of items ordered.
		 *     @type string %order_amount%    The order total.
		 *     @type string %order_status%    The order status.
		 *     @type string %billing_name%    The billing first and last name.
		 *     @type string %shipping_name%   The shipping first and last name.
		 *     @type string %shipping_method% The shipping method name.
	 	 * }
		 * @param WC_Twilio_SMS_Notification $notification The notification object.
		 */
		$replacements = apply_filters( 'wc_twilio_sms_message_replacements', $replacements, $this );

		return str_replace( array_keys( $replacements ), $replacements, $message );
	}


	/**
	 * Formats order note
	 *
	 * @since  1.0
	 * @param string $to number SMS message was sent to
	 * @param int $sent_timestamp integer timestamp for when message was sent
	 * @param string $message SMS message sent
	 * @param string $status order status
	 * @param bool $error true if there was an error sending SMS, false otherwise
	 * @return string HTML-formatted order note
	 */
	private function format_order_note( $to, $sent_timestamp, $message, $status, $error ) {

		try {

			// get datetime object from unix timestamp
			$datetime = new \DateTime( "@{$sent_timestamp}", new \DateTimeZone( 'UTC' ) );

			// change timezone to site timezone
			$datetime->setTimezone( new \DateTimeZone( wc_timezone_string() ) );

			// return datetime localized to site date/time settings
			$formatted_datetime = date_i18n( wc_date_format() . ' ' . wc_time_format(), $sent_timestamp + $datetime->getOffset() );

		} catch ( Exception $e ) {

			// log error and set datetime for SMS to 'N/A'
			wc_twilio_sms()->log( $e->getMessage() );
			$formatted_datetime = __( 'N/A', 'woocommerce-twilio-sms-notifications' );
		}

		ob_start();
		?>
		<p><strong><?php esc_html_e( 'SMS Notification', 'woocommerce-twilio-sms-notifications' ); ?></strong></p>
		<p><strong><?php esc_html_e( 'To', 'woocommerce-twilio-sms-notifications' ); ?>: </strong><?php echo esc_html( $to ); ?></p>
		<p><strong><?php esc_html_e( 'Date Sent', 'woocommerce-twilio-sms-notifications' ); ?>: </strong><?php echo esc_html( $formatted_datetime ); ?></p>
		<p><strong><?php esc_html_e( 'Message', 'woocommerce-twilio-sms-notifications' ); ?>: </strong><?php echo esc_html( $message ); ?></p>
		<p><strong><?php esc_html_e( 'Status', 'woocommerce-twilio-sms-notifications' ); ?>: <span style="<?php echo ( $error ) ? 'color: red;' : 'color: green;'; ?>"><?php echo esc_html( $status ); ?></span></strong></p>
		<?php

		return ob_get_clean();
	}


}
