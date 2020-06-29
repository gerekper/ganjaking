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
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'send_invoice' ) );
		} elseif ( 'completion' === $option || 'on' === $option ) {
			add_action( 'woocommerce_order_status_completed', array( $this, 'send_invoice' ) );
		} elseif ( 'payment_completion' === $option ) {
			add_action( 'woocommerce_payment_complete', array( $this, 'send_invoice' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'send_invoice' ) );
		}

		// Invoice must be sent before payment can send.
		add_action( 'wc_xero_send_payment', array( $this, 'send_invoice' ) );
		// Automatically set unpaid invoice to VOIDED if order is totally refunded.
		add_action( 'woocommerce_order_fully_refunded', array( $this, 'maybe_void_invoice' ) );
	}

	/**
	 * Send invoice to XERO API
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	public function send_invoice( $order_id ) {
		// Get the order
		$order = wc_get_order( $order_id );

		$xero_invoice_id = version_compare( WC_VERSION, '3.0', '<' ) ? get_post_meta( $order_id, '_xero_invoice_id', true ) : $order->get_meta( '_xero_invoice_id', true );

		try {
			// Write exception message to log
			$logger = new WC_XR_Logger( $this->settings );

			// Get the invoice
			$invoice = $this->get_invoice_by_order( $order );

			// Check if the order total is 0 and if we need to send 0 total invoices to Xero
			// If this is a reasend we want to send even the 0 valued invoice.
			if ( 0 == $invoice->get_total() && 'on' !== $this->settings->get_option( 'export_zero_amount' ) && ! $xero_invoice_id ) {

				$logger->write( 'INVOICE HAS TOTAL OF 0, NOT SENDING ORDER WITH ID ' . $order_id );

				$order->add_order_note( __( "XERO: Didn't create invoice because total is 0 and send order with zero total is set to off.", 'wc-xero' ) );

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
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					update_post_meta( $order_id, '_xero_invoice_id', (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
					update_post_meta( $order_id, '_xero_currencyrate', (string) $xml_response->Invoices->Invoice[0]->CurrencyRate );
				} else {
					// Use the new CRUD functions
					$order->update_meta_data( '_xero_invoice_id', (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
					$order->update_meta_data( '_xero_currencyrate', (string) $xml_response->Invoices->Invoice[0]->CurrencyRate );
					$order->save_meta_data();
				}

				// Log response
				$logger->write( 'XERO RESPONSE:' . "\n" . $invoice_request->get_response_body() );

				// Add Order Note
				if( $xero_invoice_id ) {
					$order->add_order_note( __( 'Xero Invoice updated.  ', 'wc-xero' ) . ' Invoice ID: ' . (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
				} else {
					$order->add_order_note( __( 'Xero Invoice created.  ', 'wc-xero' ) . ' Invoice ID: ' . (string) $xml_response->Invoices->Invoice[0]->InvoiceID );
				}

			} else { // XML reponse is not OK

				// Log reponse
				$logger->write( 'XERO ERROR RESPONSE:' . "\n" . $invoice_request->get_response_body() );

				// Format error message
				$error_message = $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message ? $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message : __( 'None', 'wc-xero' );

				// Add order note
				$order->add_order_note( __( 'ERROR creating Xero invoice: ', 'wc-xero' ) .
				                        __( ' ErrorNumber: ', 'wc-xero' ) . $xml_response->ErrorNumber .
				                        __( ' ErrorType: ', 'wc-xero' ) . $xml_response->Type .
				                        __( ' Message: ', 'wc-xero' ) . $xml_response->Message .
				                        __( ' Detail: ', 'wc-xero' ) . $error_message );
			}

		} catch ( Exception $e ) {
			// Add Exception as order note
			$order->add_order_note( $e->getMessage() );

			$logger->write( $e->getMessage() );

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
	 * @return string
	 */
	public function maybe_void_invoice( $order_id ) {

		if ( apply_filters( 'woocommerce_xero_disable_auto_void_invoices', false ) ) {
			return false;
		}

		// Get the order.
		$order = wc_get_order( $order_id );

		$invoice_id = ( version_compare( WC_VERSION, '3.0', '<' ) ) ? get_post_meta( $order_id, '_xero_invoice_id', true ) : $order->get_meta( '_xero_invoice_id', true );

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
				$error_message = $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message ? $xml_response->Elements->DataContractBase->ValidationErrors->ValidationError->Message : __( 'None', 'wc-xero' );

			}
		} catch ( Exception $e ) {
			// Add Exception as order note.
			$order->add_order_note( $e->getMessage() );

			$logger->write( $e->getMessage() );

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

		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		$order_date = $old_wc ? $order->order_date : $order->get_date_created()->date( 'Y-m-d H:i:s' );
		$date_parts = explode( ' ', $order_date );
		$order_ymd = $date_parts[0];

		// Line Item manager
		$line_item_manager = new WC_XR_Line_Item_Manager( $this->settings );

		// Contact Manager
		$contact_manager = new WC_XR_Contact_Manager( $this->settings );

		// Cart Tax
		$cart_tax = floatval( $old_wc ? $order->order_tax : $order->get_cart_tax() );

		// Shipping Tax
		$shipping_tax = floatval( $old_wc ? $order->order_shipping_tax : $order->get_shipping_tax() );

		// Order Total
		$order_total = floatval( $old_wc ? $order->order_total : $order->get_total() );

		// Order Currency
		$order_currency = $old_wc ? $order->get_order_currency() : $order->get_currency();

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

}
