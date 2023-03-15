<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Invoice_Manager {

	/**
	 * @var WC_XR_Settings
	 */
	protected $settings;

	/**
	 * WC_XR_Invoice_Manager constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Method to setup the hooks
	 */
	public function setup_hooks() {

		// Check if we need to send invoices when they're completed automatically
		$option = $this->settings->get_option( 'send_invoices' );
		if ( 'creation' === $option ) {
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'send_invoice_if_no_changes' ) );
		} elseif ( 'completion' === $option || 'on' === $option ) {
			add_action( 'woocommerce_order_status_completed', array( $this, 'send_invoice_if_no_changes' ) );
		} elseif ( 'payment_completion' === $option ) {
			add_action( 'woocommerce_payment_complete', array( $this, 'send_invoice_if_no_changes' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'send_invoice_if_no_changes' ) );
		}

		// Invoice must be sent before payment can send.
		add_action( 'wc_xero_send_payment', array( $this, 'send_invoice_if_no_changes' ) );
		// Automatically set unpaid invoice to VOIDED if order is totally refunded.
		add_action( 'woocommerce_order_fully_refunded', array( $this, 'maybe_queue_void_invoice' ) );

		// Cron events for invoice.
		add_action( 'woocommerce_xero_schedule_invoice', array( $this, 'maybe_queue_invoice' ), 10, 2 );
		add_action( 'woocommerce_xero_schedule_void_invoice', array( $this, 'maybe_queue_void_invoice' ), 10, 1 );
	}

	/**
	 * Perform instant invoice update or schedule it if rate limit has been exceeded.
	 *
	 * @param int  $order_id Id of the order to send an invoice to.
	 * @param bool $force If false, the invoice won't be sent if there are no changes since the last time it was sent.
	 * @return bool|WP_Error
	 */
	public function maybe_queue_invoice( $order_id, $force = true ) {

		// Try to perform instant API request.
		$result = $this->send_invoice_core( $order_id, $force );

		$delay_in_seconds = apply_filters( 'woocommerce_xero_api_queue_delay', 1 * MINUTE_IN_SECONDS );

		// Schedule for later if rate limit error received.
		if ( is_wp_error( $result ) && WC_XERO_RATE_LIMIT_ERROR === $result->get_error_code() ) {
			$order = wc_get_order( $order_id );

			$timestamp = time() + $delay_in_seconds;
			$format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			// translators: schedule time.
			$order->add_order_note( sprintf( __( 'Schedule the next Xero Invoice request for %s', 'woocommerce-xero' ), wp_date( $format, $timestamp ) ) );
			return as_schedule_single_action( $timestamp, 'woocommerce_xero_schedule_invoice', array( $order_id, $force ), WC_XERO_AS_GROUP );
		}

		return $result;
	}

	/**
	 * Instantly void invoice or schedule it if rate limit has been exceeded.
	 *
	 * @param int $order_id Id of the order to void an invoice to.
	 * @return bool|WP_Error
	 */
	public function maybe_queue_void_invoice( $order_id ) {

		// Try to perform instant API request.
		$result = $this->maybe_void_invoice( $order_id );

		$delay_in_seconds = apply_filters( 'woocommerce_xero_api_queue_delay', 1 * MINUTE_IN_SECONDS );

		// Schedule for later if rate limit error received.
		if ( is_wp_error( $result ) && WC_XERO_RATE_LIMIT_ERROR === $result->get_error_code() ) {
			$order = wc_get_order( $order_id );

			$timestamp = time() + $delay_in_seconds;
			$format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			// translators: schedule time.
			$order->add_order_note( sprintf( __( 'Schedule the next Xero Void Invoice request for %s', 'woocommerce-xero' ), wp_date( $format, $timestamp ) ) );
			return as_schedule_single_action( time() + $delay_in_seconds, 'woocommerce_xero_schedule_void_invoice', array( $order_id ), WC_XERO_AS_GROUP );
		}

		return $result;
	}

	/**
	 * Send invoice to XERO API.
	 *
	 * @param int $order_id Id of the order to send an invoice to.
	 *
	 * @return bool
	 */
    public function send_invoice( $order_id ) {
        return $this->maybe_queue_invoice( $order_id, true );
    }

    /**
     * Send invoice to XERO API, but only if there aren't changes since the last time it was sent.
     *
     * @param int $order_id Id of the order to send an invoice to.
     * @return bool
     */
    public function send_invoice_if_no_changes( $order_id ) {
        return $this->maybe_queue_invoice( $order_id, false );
    }

    /**
     * Send invoice to XERO API.
     * Optionally, the invoice won't be sent if there are no changes since the last time it was sent.
     *
     * @param int $order_id Id of the order to send an invoice to.
     * @param bool $force If false, the invoice won't be sent if there are no changes since the last time it was sent.
     * @return bool|WP_Error
     */
	private function send_invoice_core( $order_id, $force = true ) {
		// Get the order
		$order = wc_get_order( $order_id );

		$xero_invoice_id = $this->get_order_meta( $order, '_xero_invoice_id', true );

		try {
			// Write exception message to log
			$logger = new WC_XR_Logger( $this->settings );

			// Get the invoice
			$invoice = $this->get_invoice_by_order( $order );

			$xero_invoice_hash = hash('sha1', $invoice->to_xml());
			$previous_xero_invoice_hash = $this->get_order_meta( $order, '_xero_invoice_hash', true );
			if( $xero_invoice_id && ! $force && ( $previous_xero_invoice_hash === $xero_invoice_hash ) ) {

				$logger->write( 'INVOICE HAS NOT CHANGED, NOT SENDING ORDER WITH ID ' . $order_id );

				$order->add_order_note( __( "Skipping sending Xero Invoice update since there are no changes. Invoice ID: " . $xero_invoice_id, 'woocommerce-xero' ) );

				return false;
			}

			// Check if the order total is 0 and if we need to send 0 total invoices to Xero
			// If this is a reasend we want to send even the 0 valued invoice.
			if ( 0 == $invoice->get_total() && 'on' !== $this->settings->get_option( 'export_zero_amount' ) && ! $xero_invoice_id ) {

				$logger->write( 'INVOICE HAS TOTAL OF 0, NOT SENDING ORDER WITH ID ' . $order_id );

				$order->add_order_note( __( "XERO: Didn't create invoice because total is 0 and send order with zero total is set to off.", 'woocommerce-xero' ) );

				return false;
			}

			// Invoice Request
			$invoice_request = new WC_XR_Request_Invoice( $this->settings, $invoice );

			// Logging
			if( $xero_invoice_id ) {
				$logger->write( 'START INVOICE UPDATE. order_id=' . $order_id . ' xero_invoice_id=' . $xero_invoice_id );
			} else {
				$logger->write( 'START XERO NEW INVOICE. order_id=' . $order_id );
			}

			// Do the request
			$invoice_request->do_request();

			// Parse XML Response
			$xml_response = $invoice_request->get_response_body_xml();

			// Check response status
			if ( 'OK' == $xml_response->Status ) {

				// Add order meta data
				$this->update_order_meta( $order,'_xero_invoice_id', (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
				$this->update_order_meta( $order,'_xero_currencyrate', (string) $xml_response->Invoices->Invoice[0]->CurrencyRate );
				$this->update_order_meta( $order,'_xero_invoice_hash', $xero_invoice_hash );
				$this->save_order_meta( $order );

				// Log response
				$logger->write( 'XERO RESPONSE:' . "\n" . $invoice_request->get_response_body() );

				// Add Order Note
				if( $xero_invoice_id ) {
					$order->add_order_note( __( 'Xero Invoice updated.  ', 'woocommerce-xero' ) . ' Invoice ID: ' . (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
				} else {
					$order->add_order_note( __( 'Xero Invoice created.  ', 'woocommerce-xero' ) . ' Invoice ID: ' . (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
				}

			} else { // XML reponse is not OK

				// Log reponse
				$logger->write( 'XERO ERROR RESPONSE:' . "\n" . $invoice_request->get_response_body() );

				// Format error message
				$error_message = $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message ? $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message : __( 'None', 'woocommerce-xero' );

				// Add order note
				$order->add_order_note( __( 'ERROR creating Xero invoice: ', 'woocommerce-xero' ) .
				                        __( ' ErrorNumber: ', 'woocommerce-xero' ) . $xml_response->ErrorNumber .
				                        __( ' ErrorType: ', 'woocommerce-xero' ) . $xml_response->Type .
				                        __( ' Message: ', 'woocommerce-xero' ) . $xml_response->Message .
				                        __( ' Detail: ', 'woocommerce-xero' ) . $error_message );
			}

		} catch ( Exception $e ) {
			// Add Exception as order note
			$order->add_order_note( $e->getMessage() );

			$logger->write( $e->getMessage() );

			if ( WC_XERO_RATE_LIMIT_ERROR === $e->getCode() ) {
				return new WP_Error( WC_XERO_RATE_LIMIT_ERROR, __( 'API Rate limit exceeded.', 'woocommerce-xero' ) );
			}

			return false;
		}
		if( $xero_invoice_id ) {
			$logger->write( 'END XERO INVOICE UPDATE' );
		} else {
			$logger->write( 'END XERO NEW INVOICE' );
		}

		return true;
	}

	/**
	 * Maybe void the invoice and return the XML string.
	 *
	 * @since 1.7.20
	 * @param int $order_id
	 * @return bool|WP_Error
	 */
	public function maybe_void_invoice( $order_id ) {

		if ( apply_filters( 'woocommerce_xero_disable_auto_void_invoices', false ) ) {
			return false;
		}

		// Get the order.
		$order = wc_get_order( $order_id );

		$invoice_id = $order->get_meta( '_xero_invoice_id', true );

		if ( ! $invoice_id ) {
			return false;
		}

		try {

			$logger = new WC_XR_Logger( $this->settings );

			// Get the invoice from the order.
			$invoice = $this->get_invoice_by_order( $order );

			$xml = '<Invoice>';

			// Invoice ID.
			$xml .= '<InvoiceID>' . $invoice_id . '</InvoiceID>';

			// Set to Voided
			$xml .= '<Status>VOIDED</Status>';

			$xml .= '</Invoice>';

			// Instantiate new request.
			$void_request = new WC_XR_Request_Void( $this->settings, $invoice, $xml );

			// Do the request.
			$void_request->do_request();

			// Parse XML Response.
			$xml_response = $void_request->get_response_body_xml();

			if ( 'OK' == $xml_response->Status ) {	
				$order->add_order_note( 'Fully refunded - voided Xero invoice' );
				$logger->write( 'XERO RESPONSE:' . "\n" . $void_request->get_response_body() );
			} else {

				// Log response.
				$logger->write( 'XERO ERROR RESPONSE:' . "\n" . $void_request->get_response_body() );

				// Format error message.
				$error_message = $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message ? $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message : __( 'None', 'woocommerce-xero' );

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

		return true;
	}

	/**
	 * Get invoice by order
	 *
	 * @param WC_Order $order
	 *
	 * @return WC_XR_Invoice
	 */
	public function get_invoice_by_order( $order ) {

		$order_date = $order->get_date_created()->date( 'Y-m-d H:i:s' );
		$date_parts = explode( ' ', $order_date );
		$order_ymd = $date_parts[0];

		// Line Item manager
		$line_item_manager = new WC_XR_Line_Item_Manager( $this->settings );

		// Contact Manager
		$contact_manager = new WC_XR_Contact_Manager( $this->settings );

		// Cart Tax
		$cart_tax = floatval( $order->get_cart_tax() );

		// Shipping Tax
		$shipping_tax = floatval( $order->get_shipping_tax() );

		// Order Total
		$order_total = floatval( $order->get_total() );

		// Order Currency
		$order_currency = $order->get_currency();

		// Create invoice
		$invoice = new WC_XR_Invoice(
			$this->settings,
			$contact_manager->get_contact_by_order( $order ),
			$order_ymd,
			$order_ymd,
			ltrim( $order->get_order_number(), '#' ),
			$line_item_manager->build_line_items( $order ),
			$order_currency,
			round( $cart_tax + $shipping_tax, 2 ),
			$order_total
		);

		$invoice->set_order( $order );

		// Return invoice
		return $invoice;
	}

	private function get_order_meta( $order, $key, $single = false ) {
		return $order->get_meta( $key, $single );
	}

	private function update_order_meta( $order, $key, $value ) {
		$order->update_meta_data( $key, $value );
	}

	private function save_order_meta( $order ) {
		$order->save_meta_data();
	}
}
