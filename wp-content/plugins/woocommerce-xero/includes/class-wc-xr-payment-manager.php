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
		}

		add_filter( 'woocommerce_xero_order_payment_date', array( $this, 'cod_payment_set_payment_date_as_current_date' ), 10, 2 );
	}

	/**
	 * Send the payment to the XERO API.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool
	 */
	public function send_payment( $order_id ) {
		do_action( 'wc_xero_send_payment', $order_id );

		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		// Get the order.
		$order = wc_get_order( $order_id );

		// Check for an invoice first.
		$invoice_id = $old_wc ? get_post_meta( $order_id, '_xero_invoice_id', true ) : $order->get_meta( '_xero_invoice_id', true );

		if ( ! $invoice_id ) {
			$order->add_order_note( __( 'Xero Payment not created: Invoice has not been sent.', 'wc-xero' ) );
			return false;
		}

		if( 0 == $order->get_total() ) {
			$order->add_order_note( __( 'Xero Invoice amount is zero, no payment necessary.', 'wc-xero' ) );
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
				if ( $old_wc ) {
					update_post_meta( $order_id, '_xero_payment_id', $payment_id );
				} else {
					$order->update_meta_data( '_xero_payment_id', $payment_id );
					$order->save_meta_data();
				}

				// Write logger.
				$logger->write( 'XERO RESPONSE:' . "\n" . $payment_request->get_response_body() );

				// Add order note.
				$order->add_order_note( sprintf(
					/* translators: Payment ID from Xero. */
					__( 'Xero Payment created. Payment ID: %s', 'wc-xero' ),
					(string) $xml_response->Payments->Payment[0]->PaymentID
				) );

			} else {

				// Logger write.
				$logger->write( 'XERO ERROR RESPONSE:' . "\n" . $payment_request->get_response_body() );

				// Error order note.
				$error_num = (string) $xml_response->ErrorNumber;
				$error_msg = (string) $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message;
				$order->add_order_note( sprintf(
					__( 'ERROR creating Xero payment. ErrorNumber: %1$s | Error Message: %2$s', 'wc-xero' ),
					$error_num,
					$error_msg
				) );
			}
		} catch ( Exception $e ) {
			// Add Exception as order note.
			$order->add_order_note( $e->getMessage() );

			$logger->write( $e->getMessage() );

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
		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		// Get the XERO invoice ID.
		$order_id = $old_wc ? $order->id : $order->get_id();
		$invoice_id = $old_wc ? get_post_meta( $order_id, '_xero_invoice_id', true ) : $order->get_meta( '_xero_invoice_id', true );

		// Get the XERO currency rate.
		$currency_rate = $old_wc ? get_post_meta( $order_id, '_xero_currencyrate', true ) : $order->get_meta( '_xero_currencyrate', true );

		// Date time object of order data.
		$order_date = $old_wc ? $order->order_date : $order->get_date_created()->date( 'Y-m-d H:i:s' );
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
		$order_total = $old_wc ? $order->order_total : $order->get_total();
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
		$payment_method = version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method();
		if ( 'cod' !== $payment_method ) {
			return $order_date;
		}
		return date( 'Y-m-d', current_time( 'timestamp' ) );
	}
}
