<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Invoice {

	/**
	 * @var string
	 */
	private $type = 'ACCREC';

	/**
	 * @var WC_XR_Contact
	 */
	private $contact = array();

	/**
	 * @var string
	 */
	private $date = '';

	/**
	 * @var string
	 */
	private $due_date = '';

	/**
	 * @var string
	 */
	private $invoice_number;

	/**
	 * @var WC_XR_Line_Item[]
	 */
	private $line_items = array();

	/**
	 * @var string
	 */
	private $currency_code = '';

	/**
	 * @var float
	 */
	private $total_tax = 0;

	/**
	 * @var float
	 */
	private $total = 0;

	/**
	 * @var WC_XR_Settings
	 */
	public $settings;

	/**
	 * @var WC_Order
	 */
	private $order = null;

	/**
	 * Construct
	 *
	 * @param WC_XR_Settings $settings
	 * @param WC_XR_Contact $contact
	 * @param string $date
	 * @param string $due_date
	 * @param string $invoice_number
	 * @param array $line_items
	 * @param string $currency_code
	 * @param float $total_tax
	 * @param float $total
	 */
	public function __construct( $settings, $contact, $date, $due_date, $invoice_number, $line_items, $currency_code, $total_tax, $total ) {
		$this->settings       = $settings;
		$this->contact        = $contact;
		$this->date           = $date;
		$this->due_date       = $due_date;
		$this->invoice_number = $invoice_number;
		$this->line_items     = $line_items;
		$this->currency_code  = $currency_code;
		$this->total_tax      = $total_tax;
		$this->total          = $total;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return apply_filters( 'woocommerce_xero_invoice_type', $this->type, $this );
	}

	/**
	 * @param string $type
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * @return WC_XR_Contact
	 */
	public function get_contact() {
		return apply_filters( 'woocommerce_xero_invoice_contact', $this->contact, $this );
	}

	/**
	 * @param WC_XR_Contact $contact
	 */
	public function set_contact( $contact ) {
		$this->contact = $contact;
	}

	/**
	 * @return string
	 */
	public function get_date() {
		return apply_filters( 'woocommerce_xero_invoice_date', $this->date, $this );
	}

	/**
	 * @param string $date
	 */
	public function set_date( $date ) {
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function get_due_date() {
		add_filter( 'woocommerce_xero_invoice_due_date', array( $this, 'set_org_default_due_date' ), 10, 2 );
		$due_date = apply_filters( 'woocommerce_xero_invoice_due_date', $this->due_date, $this );
		remove_filter( 'woocommerce_xero_invoice_due_date', array( $this, 'set_org_default_due_date' ) );
		return $due_date;
	}

	/**
	 * @param string $due_date
	 */
	public function set_due_date( $due_date ) {
		$this->due_date = $due_date;
	}

	/**
	 * @return string
	 */
	public function get_invoice_number() {

		// Load invoice prefix
		$prefix = trim( $this->settings->get_option( 'invoice_prefix' ) );

		// Set invoice number
		$invoice_number = $this->invoice_number;

		// Check prefix
		if ( $prefix !== '' ) {
			// Prefix invoice number with prefix
			$invoice_number = $prefix . $invoice_number;
		}

		return apply_filters( 'woocommerce_xero_invoice_invoice_number', $invoice_number, $this );
	}

	/**
	 * @param string $invoice_number
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->invoice_number = $invoice_number;
	}

	/**
	 * @return WC_XR_Line_Item[]
	 */
	public function get_line_items() {
		return apply_filters( 'woocommerce_xero_invoice_line_items', $this->line_items, $this );
	}

	/**
	 * @param array $line_items
	 */
	public function set_line_items( $line_items ) {
		$this->line_items = $line_items;
	}

	/**
	 * @return string
	 */
	public function get_currency_code() {
		return apply_filters( 'woocommerce_xero_invoice_currency_code', $this->currency_code, $this );
	}

	/**
	 * @param string $currency_code
	 */
	public function set_currency_code( $currency_code ) {
		$this->currency_code = $currency_code;
	}

	/**
	 * @return float
	 */
	public function get_total_tax() {
		return apply_filters( 'woocommerce_xero_invoice_total_tax', $this->total_tax, $this );
	}

	/**
	 * @param float $total_tax
	 */
	public function set_total_tax( $total_tax ) {
		$this->total_tax = floatval( $total_tax );
	}

	/**
	 * @return float
	 */
	public function get_total() {
		return apply_filters( 'woocommerce_xero_invoice_total', $this->total, $this );
	}

	/**
	 * @param float $total
	 */
	public function set_total( $total ) {
		$this->total = floatval( $total );
	}

	/**
	 * @return WC_Order
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @param WC_Order $order
	 */
	public function set_order( $order ) {
		$this->order = $order;
	}

	/**
	 * Checks to see if there is any organisation defaults for due date,
	 * otherwise the current date is used.
	 */
	public function set_org_default_due_date( $due_date, $wc_xr_invoice ) {
		$pmt_terms_type = '';
		$pmt_terms_day = '';

		// Check for transient for payment terms
		$transient_key = 'wc_xero_org_pmt_terms_' . md5( serialize( $this->settings ) );
		if ( get_transient( $transient_key ) ) {
			$pmt_terms = get_transient( $transient_key );
			if ( is_array( $pmt_terms ) && array_key_exists( 'type', $pmt_terms ) && array_key_exists( 'day', $pmt_terms ) ) {
				// Validate the transient's contents before using it
				if ( self::validate_payment_terms( $pmt_terms[ 'type' ], $pmt_terms[ 'day' ] ) ) {
					$pmt_terms_type = $pmt_terms[ 'type' ];
					$pmt_terms_day = $pmt_terms[ 'day' ];
				}
			}
		}

		// Nothing to work with? Ask Xero
		if ( empty( $pmt_terms_type ) || empty( $pmt_terms_day ) ) {
			$org_request = new WC_XR_Request_Organisation( $this->settings );
			$org_request->do_request();
			$xml_response = $org_request->get_response_body_xml();

			if ( ! empty ( $xml_response->Organisations->Organisation->PaymentTerms->Sales->Type ) ) {
				$type = $xml_response->Organisations->Organisation->PaymentTerms->Sales->Type->__toString();
				$day  = $xml_response->Organisations->Organisation->PaymentTerms->Sales->Day->__toString();

				if ( self::validate_payment_terms( $type, $day ) ) {
					$pmt_terms_type = $type;
					$pmt_terms_day = $day;

					// Save the terms so we don't need to fetch them again for an hour
					$pmt_terms = array(
						'type' => $pmt_terms_type,
						'day' => $pmt_terms_day
					);
					set_transient( $transient_key, $pmt_terms, 1 * HOUR_IN_SECONDS );
				}
			}
		}

		// Still nothing to work with? We are a filter, so just return what we were given
		if ( empty( $pmt_terms_type ) || empty( $pmt_terms_day ) ) {
			return $due_date;
		}

		// Use the type and day to calculate an appropriate due date for the invoice
		$pmt_terms_day = (int) $pmt_terms_day;
		$now = current_time( 'timestamp' );

		switch( $pmt_terms_type ) {
			// $day of the following month
			case 'OFFOLLOWINGMONTH':
				$year_due = (int) date( 'Y', $now );
				$month_due = 1 + (int) date( 'n', $now );
				if ( 12 < $month_due ) {
					$month_due = 1;
					$year_due += 1;
				}
				$last_day_month_due = (int) date( 't', strtotime( $year_due . '-' . $month_due . '-1' ) );
				$day_due = ( $pmt_terms_day > $last_day_month_due ) ? $last_day_month_due : $pmt_terms_day;
				$due_date = date( 'Y-m-d', strtotime( $year_due . '-' . $month_due . '-' . $day_due ) );
				break;
			// $day of the current month
			case 'OFCURRENTMONTH':
				$year_due = (int) date( 'Y', $now );
				$month_due = (int) date( 'n', $now );
				$last_day_month_due = (int) date( 't', strtotime( $year_due . '-' . $month_due . '-1' ) );
				$day_due = ( $pmt_terms_day > $last_day_month_due ) ? $last_day_month_due : $pmt_terms_day;
				$due_date = date( 'Y-m-d', strtotime( $year_due . '-' . $month_due . '-' . $day_due ) );
				break;
			// $day days after the end of the invoice month
			case 'DAYSAFTERBILLMONTH':
				$due_date = date( 'Y-m-d', strtotime( date( 'Y-m-t', $now ) . " +$pmt_terms_day days" ) );
				break;
			// $day days after the invoice date
			case 'DAYSAFTERBILLDATE':
				$due_date = date( 'Y-m-d', strtotime( $this->due_date . " +$pmt_terms_day days" ) );
				break;
		}

		// Return the due_date we calculated
		return $due_date;
	}

	/**
	 * Validate the payment terms type and day
	 *
	 * @since 1.7.6
	 * @param string $type
	 * @param string $day
	 *
	 * @return bool
	 */
	protected static function validate_payment_terms( $type, $day ) {
		$validates = true;

		$day_as_int = (int) $day;

		switch( $type ) {
			case 'OFFOLLOWINGMONTH':
			case 'OFCURRENTMONTH':
				if ( ! is_numeric( $day ) || $day_as_int < 1 || $day_as_int > 31 ) {
					$validates = false;
				}
				break;
			case 'DAYSAFTERBILLMONTH':
			case 'DAYSAFTERBILLDATE':
				if ( ! is_numeric( $day ) || $day_as_int < 0 || $day_as_int > 99 ) {
					$validates = false;
				}
				break;
			default:
				$validates = false;
		}

		return $validates;
	}

	/**
	 * Format the invoice to XML and return the XML string
	 *
	 * @return string
	 */
	public function to_xml() {

		// Start Invoice
		$xml = '<Invoice>';

		// Type
		$xml .= '<Type>' . $this->get_type() . '</Type>';

		// Add Contact
		if ( $this->get_contact()->get_id() ) {
			$xml .= $this->get_contact()->id_to_xml();
		} else {
			$xml .= $this->get_contact()->to_xml();
		}

		// Date
		$xml .= '<Date>' . $this->get_date() . '</Date>';

		// Due Date
		$xml .= '<DueDate>' . $this->get_due_date() . '</DueDate>';

		// Invoice Number
		$invoice_number = $this->get_invoice_number();
		if ( null !== $invoice_number ) {
			$xml .= '<InvoiceNumber>' . $invoice_number . '</InvoiceNumber>';
		}

		// Reference
		$order = $this->get_order();
		$reference_pieces = array();
		$payment_method   = esc_xml( $order->get_payment_method_title() );
		if ( ! empty( $payment_method ) ) {
			$reference_pieces[] = $payment_method;
		}
		$transaction_id = $order->get_transaction_id();
		if ( ! empty( $transaction_id ) ) {
			$reference_pieces[] = $transaction_id;
		}
		if ( 0 < count( $reference_pieces ) ) {
			$xml .= '<Reference>' . implode( ' ', $reference_pieces ) . '</Reference>';
		}

		// URL
		$order_id = $order->get_id();
		$path = '/post.php?post=' . esc_attr( intval( $order_id ) ) . '&amp;action=edit';
		$url =  admin_url( $path );
		// Check for port number (port numbers in URLs are not allowed by Xero)
		$port = parse_url( $url, PHP_URL_PORT );
		// Only add the Url to the XML if a port number is NOT present
		if ( empty( $port ) ) {
			$xml .= '<Url>' . esc_url( $url ) . '</Url>';
		}

		// Line Amount Types. Always send prices exclusive VAT.
		$line_amount_type = ( $this->settings->send_tax_inclusive_prices() ) ? 'Inclusive' : 'Exclusive';
		$xml             .= '<LineAmountTypes>' . $line_amount_type . '</LineAmountTypes>';

		// Get Line Items
		$line_items = $this->get_line_items();

		// Check line items
		if ( count( $line_items ) ) {

			// Line Items wrapper open
			$xml .= '<LineItems>';

			// Loop
			foreach ( $line_items as $line_item ) {

				// Add
				$xml .= $line_item->to_xml();

			}

			// Line Items wrapper close
			$xml .= '</LineItems>';
		}

		// Currency Code
		$xml .= '<CurrencyCode>' . $this->get_currency_code() . '</CurrencyCode>';

		// Status
		$xml .= '<Status>AUTHORISED</Status>';

		// Get branding theme template ID.
		$branding_theme = $this->settings->get_option( 'branding_theme' );

		/**
		 * Filter to change the branding theme template ID.
		 *
		 * @since 1.7.45
		 *
		 * `woocommerce_xero_branding_theme` is a filter hook.
		 * @var string $branding_theme is a branding theme ID.
		 * @var object $order Order object.
		 */
		$branding_theme = apply_filters( 'woocommerce_xero_branding_theme', $branding_theme, $this );
		if ( $branding_theme ) {

			// Only send branding theme if it is valid/exists.
			try {
				$org_request = new WC_XR_Request_Branding_Themes( $this->settings, $branding_theme );
				$org_request->do_request();
				$xml_response = $org_request->get_response_body_xml();

				if ( 'OK' === (string) $xml_response->Status ) {
					$xml .= '<BrandingThemeID>' . esc_html( $branding_theme ) . '</BrandingThemeID>';
				}
			} catch ( Exception $e ) {
				// Add Exception as order note.
				$order->add_order_note( 'BrandingThemeID is invalid, using the default template. ' . $e->getMessage() );
			}
		}

		// Total Tax
		$xml .= '<TotalTax>' . $this->get_total_tax() . '</TotalTax>';

		// Total
		$xml .= '<Total>' . $this->get_total() . '</Total>';

		// End Invoice
		$xml .= '</Invoice>';

		/**
		 * Filter the xml data returned by WC_XR_Invoice::to_xml()
		 * value is returned.
		 *
		 * @since 1.7.4 introduced
		 *
		 * @param string $xml
		 * @param WC_XR_Invoice $invoice_object
		 */
		return apply_filters( 'woocommerce_xero_invoice_to_xml', $xml, $this );
	}
}
