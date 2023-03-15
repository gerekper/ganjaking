<?php
/**
 * Xero Payment Manager class.
 *
 * @package WC_Xero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Xero Payment Manager.
 */
class WC_XR_Payment_Manager {

	/**
	 * Xero settings.
	 *
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Payment_Manager constructor.
	 *
	 * @param WC_XR_Settings $settings Xero settings.
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Set up callbacks to the hooks.
	 */
	public function setup_hooks() {
		// Check if we need to send payments when they're completed automatically.
		if ( 'on' === $this->settings->get_option( 'send_payments' ) ) {
			add_action( 'woocommerce_order_status_completed', array( $this, 'send_payment' ) );
		} elseif ( 'payment_completion' === $this->settings->get_option( 'send_payments' ) ) {
			add_action( 'woocommerce_payment_complete', array( $this, 'send_payment' ), 20 );

			// if `woocommerce_payment_complete` does not trigger, decide when to create payment.
			add_action( 'woocommerce_order_status_changed', array( $this, 'send_payment_on_order_change' ), 20, 3 );
		}

		add_filter( 'woocommerce_xero_order_payment_date', array( $this, 'cod_payment_set_payment_date_as_current_date' ), 10, 2 );

		// Cron events for invoice.
		add_action( 'woocommerce_xero_schedule_send_payment', array( $this, 'maybe_queue_send_payment' ), 10, 1 );
	}

	/**
	 * Perform instant payment send or schedule it if rate limit has been exceeded.
	 *
	 * @param int $order_id Order ID.
	 * @return bool|WP_Error
	 */
	public function maybe_queue_send_payment( $order_id ) {

		// Try to perform instant API request.
		$result = $this->send_payment( $order_id );

		$delay_in_seconds = apply_filters( 'woocommerce_xero_api_queue_delay', 1 * MINUTE_IN_SECONDS );

		// Schedule for later if rate limit error received.
		if ( is_wp_error( $result ) && WC_XERO_RATE_LIMIT_ERROR === $result->get_error_code() ) {
			$order = wc_get_order( $order_id );

			$timestamp = time() + $delay_in_seconds;
			$format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			// translators: schedule time.
			$order->add_order_note( sprintf( __( 'Schedule the next Xero Payment request for %s', 'woocommerce-xero' ), wp_date( $format, $timestamp ) ) );
			return as_schedule_single_action( time() + $delay_in_seconds, 'woocommerce_xero_schedule_send_payment', array( $order_id ), WC_XERO_AS_GROUP );
		}

		return $result;
	}

	/**
	 * Detect the payment method with order status change and create Xero payment.
	 *
	 * @since x.x.x
	 * @version 1.0.0
	 *
	 * @param int    $order_id Order ID.
	 * @param string $from Old order status.
	 * @param string $to New order status.
	 */
	public function send_payment_on_order_change( $order_id, $from, $to ) {

		// Decide when to create a payment according to the payment method and changed order status.
		$xero_payment_completion = array(
			'cheque' => array( 'processing' ),
		);

		/**
		 * Filter the order statuses according to the payment method array.
		 *
		 * @param array $xero_payment_completion Order statuses with their respective payment methods.
		 *
		 * @since x.x.x
		 */
		$xero_payment_completion = apply_filters( 'woocommerce_xero_payment_creation', $xero_payment_completion );

		// Get the order.
		$order = wc_get_order( $order_id );

		$current_payment_method = $order->get_payment_method();

		// Create a XERO payment for specified method and status.
		if ( isset( $xero_payment_completion[ $current_payment_method ] ) && in_array( $to, $xero_payment_completion[ $current_payment_method ], true ) ) {
			$this->send_payment( $order_id );
		}
	}

	/**
	 * Send the payment to the XERO API.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool|WP_Error
	 */
	public function send_payment( $order_id ) {
		do_action( 'wc_xero_send_payment', $order_id );

		// Get the order.
		$order = wc_get_order( $order_id );

		// Check for an invoice first.
		$invoice_id = $order->get_meta( '_xero_invoice_id', true );

		if ( ! $invoice_id ) {
			$order->add_order_note( __( 'Xero Payment not created: Invoice has not been sent.', 'woocommerce-xero' ) );
			return false;
		}

		if( 0 == $order->get_total() ) {
			$order->add_order_note( __( 'Xero Invoice amount is zero, no payment necessary.', 'woocommerce-xero' ) );
			return false;
		} 

		// Try to do the request.
		try {
			// Payment Request.
			$payment_request = new WC_XR_Request_Payment( $this->settings, $this->get_payment_by_order( $order ) );

			// Write exception message to log.
			$logger = new WC_XR_Logger( $this->settings );

			// Logging start.
			$logger->write( 'START XERO NEW PAYMENT. order_id=' . $order_id );

			// Do the request.
			$payment_request->do_request();

			// Parse XML Response.
			$xml_response = $payment_request->get_response_body_xml();

			// Check response status.
			if ( 'OK' === (string) $xml_response->Status ) {

				// Add post meta.
				$payment_id = (string) $xml_response->Payments->Payment[0]->PaymentID;
				$order->update_meta_data( '_xero_payment_id', $payment_id );
				$order->save_meta_data();

				// Write logger.
				$logger->write( 'XERO RESPONSE:' . "\n" . $payment_request->get_response_body() );

				// Add order note.
				$order->add_order_note( sprintf(
					/* translators: Payment ID from Xero. */
					__( 'Xero Payment created. Payment ID: %s', 'woocommerce-xero' ),
					(string) $xml_response->Payments->Payment[0]->PaymentID
				) );

			} else {

				// Logger write.
				$logger->write( 'XERO ERROR RESPONSE:' . "\n" . $payment_request->get_response_body() );

				// Error order note.
				$error_num = (string) $xml_response->ErrorNumber;
				$error_msg = (string) $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message;
				$order->add_order_note( sprintf(
					__( 'ERROR creating Xero payment. ErrorNumber: %1$s | Error Message: %2$s', 'woocommerce-xero' ),
					$error_num,
					$error_msg
				) );
			}
		} catch ( Exception $e ) {
			// Add Exception as order note.
			$order->add_order_note( $e->getMessage() );

			$logger->write( $e->getMessage() );

			if ( WC_XERO_RATE_LIMIT_ERROR === $e->getCode() ) {
				return new WP_Error( WC_XERO_RATE_LIMIT_ERROR, __( 'API Rate limit exceeded.', 'woocommerce-xero' ) );
			}

			return false;
		}

		// Logging end.
		$logger->write( 'END XERO NEW PAYMENT' );

		return true;
	}

	/**
	 * Get payment by order.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return WC_XR_Payment
	 */
	public function get_payment_by_order( $order ) {
		// Get the XERO invoice ID.
		$invoice_id = $order->get_meta( '_xero_invoice_id', true );

		// Get the XERO currency rate.
		$currency_rate = $order->get_meta( '_xero_currencyrate', true );

		// Date time object of order data.
		$order_date = $order->get_date_created()->date( 'Y-m-d H:i:s' );
		$date_parts = explode( ' ', $order_date );
		$order_ymd = $date_parts[0];

		// The Payment object.
		$payment = new WC_XR_Payment();

		$payment->set_order( $order );

		// Set the invoice ID.
		$payment->set_invoice_id( $invoice_id );

		// Set the Payment Account code.
		$payment->set_code( $this->settings->get_option( 'payment_account' ) );

		// Set the payment date.
		$payment->set_date( apply_filters( 'woocommerce_xero_order_payment_date', $order_ymd, $order ) );

		// Set the currency rate.
		$payment->set_currency_rate( $currency_rate );

		// Set the amount.
		$order_total = $order->get_total();
		$payment->set_amount( $order_total );

		return $payment;
	}

	/**
	 * If the payment gateway is set to COD, set the payment date as the current
	 * date instead of the order date.
	 *
	 * @since 1.0.0
	 * @version 1.7.10
	 *
	 * @param string   $order_date Order date.
	 * @param WC_Order $order      Order object.
	 *
	 * @return string Date.
	 */
	public function cod_payment_set_payment_date_as_current_date( $order_date, $order ) {
		$payment_method = $order->get_payment_method();
		if ( 'cod' !== $payment_method ) {
			return $order_date;
		}
		return date( 'Y-m-d', current_time( 'timestamp' ) );
	}
}
