<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Line_Item {

	/**
	 * @var string
	 */
	private $description = '';

	/**
	 * @var string
	 */
	private $account_code = '';

	/**
	 * @var string
	 */
	private $item_code = '';

	/**
	 * @var float
	 */
	private $unit_amount = 0;

	/**
	 * @var int
	 */
	private $quantity = 0;

	/**
	 * @var float
	 */
	private $line_amount = null;

	/**
	 * @var float
	 */
	private $tax_amount = 0;

	/**
	 * @var array
	 */
	private $tax_rate = array(
		'rate'  => 0,
		'label' => '',
	);

	/**
	 * @var bool
	 */
	private $is_digital_good = false;

	/**
	 * Line item discount ammount.
	 *
	 * @var float $discount_amount Discount ammount.
	 */
	private $discount_amount = 0;

	/**
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Line_Item constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'woocommerce_xero_line_item_description', $this->description, $this );
	}

	/**
	 * @param string $description
	 */
	public function set_description( $description ) {
		$this->description = htmlspecialchars( $description );
	}

	/**
	 * @return string
	 */
	public function get_account_code() {
		return apply_filters( 'woocommerce_xero_line_item_account_code', $this->account_code, $this );
	}

	/**
	 * @param string $account_code
	 */
	public function set_account_code( $account_code ) {
		$this->account_code = $account_code;
	}

	/**
	 * @return string
	 */
	public function get_item_code() {
		return apply_filters( 'woocommerce_xero_line_item_item_code', $this->item_code, $this );
	}

	/**
	 * @param string $item_code
	 */
	public function set_item_code( $item_code ) {
		$this->item_code = $item_code;
	}

	/**
	 * @return float
	 */
	public function get_unit_amount() {
		return apply_filters( 'woocommerce_xero_line_item_unit_amount', $this->unit_amount, $this );
	}

	/**
	 * @param float $unit_amount
	 */
	public function set_unit_amount( $unit_amount ) {
		$this->unit_amount = round( floatval( $unit_amount ), 4 );
	}

	/**
	 * @return int
	 */
	public function get_quantity() {
		return apply_filters( 'woocommerce_xero_line_item_quantity', $this->quantity, $this );
	}

	/**
	 * @param int $quantity
	 */
	public function set_quantity( $quantity ) {
		$this->quantity = round( floatval( $quantity ), 4 );
	}

	/**
	 * @return float
	 */
	public function get_line_amount() {
		return apply_filters( 'woocommerce_xero_line_item_line_amount', $this->line_amount, $this );
	}

	/**
	 * @param float $line_amount
	 */
	public function set_line_amount( $line_amount ) {
		$this->line_amount = round( floatval( $line_amount ), 2 );
	}

	/**
	 * @return float
	 */
	public function get_tax_amount() {
		return apply_filters( 'woocommerce_xero_line_item_tax_amount', $this->tax_amount, $this );
	}

	/**
	 * @param float $tax_amount
	 */
	public function set_tax_amount( $tax_amount ) {
		$this->tax_amount = round( floatval( $tax_amount ), 2 );
	}

	/**
	 * @return array
	 */
	public function get_tax_rate() {
		return apply_filters( 'woocommerce_xero_line_item_tax_rate', $this->tax_rate, $this );
	}

	/**
	 * @param array $tax_rate
	 */
	public function set_tax_rate( $tax_rate ) {
		$this->tax_rate = $tax_rate;
	}

	/**
	 * @param bool $is_digital_good
	 */
	public function set_is_digital_good( $is_digital_good ) {
		$this->is_digital_good = $is_digital_good;
	}

	/**
	 * @version 1.7.38
	 * 
	 * @return float
	 */
	public function get_discount_rate() {
		$precision     = 'on' === $this->settings->get_option( 'four_decimals' ) ? 4 : 2;
		$discount_rate = round( $this->discount_amount / ( $this->unit_amount * $this->quantity ), $precision );

		return apply_filters( 'woocommerce_xero_line_item_discount_rate', $discount_rate, $this );
	}

	/**
	 * Get the discount amount of current line item.
	 * @since 1.7.38
	 * 
	 * @return float
	 */
	public function get_discount_amount() {
		return apply_filters( 'woocommerce_xero_line_item_discount_amount', $this->discount_amount, $this );
	}

	/**
	 * @version 1.7.38
	 * 
	 * @param float $discount_rate
	 */
	public function set_discount_rate( $discount_rate ) {
		wc_deprecated_function( __METHOD__, '1.7.38' );
	}

	/**
	 * Set the discount amount of current line item.
	 * 
	 * @since 1.7.38
	 *
	 * @param float $discount_amount Discount ammount.
	 */
	public function set_discount_amount( $discount_amount ) {
		$this->discount_amount = round( floatval( $discount_amount ), 4 );
	}

	/**
	 * Creates a new tax type in the XERO system if one doesn't exist
	 * otherwise it passes the existing one
	 *
	 * @since 1.6.11
	 * @version 1.7.7
	 *
	 * @return string	The tax type for the line item
	 */
	public function get_tax_type() {

		// Create the logger to capture our interactions with Xero and the merchant's tax settings
		$line_item_description = $this->get_description();
		$logger = new WC_XR_Logger( $this->settings );
		$logger->write( "Getting tax type for line item ($line_item_description)" );

		// OK, at this point we're going to have to consult Xero
		// to figure out the tax type.
		// Let's see if we have already fetched tax rates recently
		$xero_tax_rates = array();
		$transient_key = 'wc_xero_tax_rates';
		$transient_value = get_transient( $transient_key );
		if ( is_array( $transient_value ) ) {
			$xero_tax_rates = $transient_value;
		}

		// If we don't have tax rates, time to fetch them
		if ( empty( $xero_tax_rates ) ) {
			$logger->write( " - Found no tax rates in transient... fetching from Xero" );

			$tax_rates_request = new WC_XR_Request_Tax_Rate( $this->settings );
			$tax_rates_request->do_request();
			$xml_response = $tax_rates_request->get_response_body_xml();

			if ( empty ( $xml_response->TaxRates->TaxRate ) ) {
				$logger->write( " - Error - unable to retrieve tax rates from Xero" );
				$logger->write( " - Returning (default) tax type (OUTPUT)" );
				return 'OUTPUT';
			} else {
				// Prepare the rates for caching
				$logger->write( " - Successfully retrieved tax rates from Xero" );
				foreach ( $xml_response->TaxRates->children() as $key => $value ) {
					$name_to_add = $value->Name->__toString();
					$tax_type_to_add = $value->TaxType->__toString();
					$report_tax_type_to_add = isset( $value->ReportTaxType ) ? $value->ReportTaxType->__toString() : '';
					$effective_rate_to_add = floatval( $value->EffectiveRate->__toString() );
					$rate_status = $value->Status->__toString();

					if ( 'ACTIVE' === $rate_status ) {
						$logger->write( " - Caching Name ($name_to_add), TaxType ($tax_type_to_add), ReportTaxType ($report_tax_type_to_add), EffectiveRate ($effective_rate_to_add)" );
						$xero_tax_rates[] = array(
							'name' => $name_to_add,
							'tax_type' => $tax_type_to_add,
							'report_tax_type' => $report_tax_type_to_add,
							'effective_rate' => $effective_rate_to_add
						);
					} else {
						$logger->write( " - Skipping Name ($name_to_add), TaxType ($tax_type_to_add), ReportTaxType ($report_tax_type_to_add), EffectiveRate ($effective_rate_to_add), Status ($rate_status)" );
					}
				}

				set_transient( $transient_key, $xero_tax_rates, 1 * HOUR_IN_SECONDS );
			}
		}

		// Iterate over the tax rates looking for our rate (e.g. 10) and label/name (e.g. "GST")
		$tax_rate = $this->get_tax_rate();
		$rate_to_find = floatval( $tax_rate[ 'rate' ] );
		$report_tax_type = $this->get_report_tax_type_for_base_country();

		// Is this item tax exempt? Tax exempt Xero tax types vary by country.
		if ( $this->get_tax_amount() <= 0 ) {

			// Try to match what is defined in Xero (portal) settings.
			if( $this->settings->get_option( 'match_zero_vat_tax_rates' ) ) {
				$logger->write( " - Item has zero tax. Searching for Xero defined zero tax rates" );
				$logger->write( " - Searching rates for label ({$tax_rate['label']}) and rate ($rate_to_find)" );
				$tax_type = self::get_tax_type_for_label_rate_and_type( $xero_tax_rates, $tax_rate['label'], $rate_to_find, $report_tax_type, $logger );
				if ( ! empty( $tax_type) ) {
					$logger->write( " - Found and returning tax type ($tax_type)" );
					return $tax_type;
				}
				$logger->write( " - No Xero defined tax rate found - reverting to default" );
			}

			$tax_type = $this->get_tax_exempt_type_for_base_country();
			$logger->write( " - Item has zero tax. Returning tax (exempt) type ($tax_type)" );
			return $tax_type;
		}

		// It is possible the Tax Name (label) in WooCommerce > Settings > Tax Rates is empty.
		// If so, then choose a base location appropriate default
		if ( empty( $tax_rate['label'] ) ) {
			$base_country = WC()->countries->get_base_country();
			switch( $base_country ) {
				case 'AU':
				case 'NZ':
					$tax_rate['label'] = 'GST';
					break;
				case 'GB':
					$tax_rate['label'] = 'VAT';
					break;
				default:
					$tax_rate['label'] = 'Tax';
			}
			$logger->write( " - Rate ($rate_to_find) has an empty Tax Name. Will use label ({$tax_rate['label']}) by default." );
		}

		if ( apply_filters( 'woocommerce_xero_create_unique_tax_label', true ) ) {
			// Add the rate to the label to make it unique
			$tax_rate['label'] .= ' ' . sprintf( '(%.2F%%)', $rate_to_find );
		}

		$logger->write( " - Searching rates for label ({$tax_rate['label']}) and rate ($rate_to_find)" );

		$tax_type = self::get_tax_type_for_label_rate_and_type( $xero_tax_rates, $tax_rate['label'], $rate_to_find, $report_tax_type, $logger );
		if ( ! empty( $tax_type) ) {
			$logger->write( " - Found and returning tax type ($tax_type)" );
			return $tax_type;
		}

		$logger->write( " - Could not find a cached tax type for that label and rate. Attempting to add new one to Xero." );

		// If no tax rate was found, ask Xero to add one for us

		// First, see if we need a ReportTaxType
		if ( ! empty( $report_tax_type ) ) {
			$tax_rate['report_tax_type'] = $report_tax_type;
			$logger->write( " - Setting ReportTaxType to ($report_tax_type)" );
		}

		$tax_type_create_request = new WC_XR_Request_Create_Tax_Rate( $this->settings, $tax_rate );
		$tax_type_create_request->do_request();
		$xml_response = $tax_type_create_request->get_response_body_xml();

		if ( ! empty( $xml_response->TaxRates->TaxRate->TaxType ) ) {
			$tax_type = $xml_response->TaxRates->TaxRate->TaxType->__toString();

			// Delete our transient so the next fetch will store the new rate and type
			delete_transient( $transient_key );

			// Return the type to the caller
			$logger->write( " - Successfully added tax rate to Xero" );
			$logger->write( " - Returning tax type ($tax_type)" );
			return $tax_type;
		}

		// Log the error and return an empty string
		$logger->write( " - Error - unable to add rate to Xero  - Returning empty tax type ()" );
		$logger->write( print_r( $xml_response, true ) );
		return '';
	}

	/**
	 * Returns an appropriate tax type for tax-exempt line items based on the country, options setting
	 * and whether this is a shipping line item. For tax exempt items, Australia requires a tax type of
	 * EXEMPTOUTPUT for income items or of EXEMPTEXPENSES for expense items.  Since merchants may
	 * elect to treat shipping as an expense or as income, we need to take that into account too.
	 *
	 * NONE:			Appropriate for tax exempt items for all countries except AU
	 *
	 * EXEMPTOUTPUT: 	Line item would be output taxed (income, sometimes shipping),
	 * 					except this particular line item is exempt of tax (AU only)
	 * EXEMPTEXPENSES:	Line item would be input taxed (expense, typically services like shipping),
	 * 					except this particular line item is exempt of tax (AU only)
	 *
	 * @since 1.7.7
	 * @version 1.7.7
	 *
	 * @return string	NONE | EXEMPTOUTPUT | EXEMPTEXPENSES
	 */
	protected function get_tax_exempt_type_for_base_country() {
		$tax_rate = $this->get_tax_rate();

		$is_shipping_line_item = array_key_exists( 'is_shipping_line_item', $tax_rate ) && $tax_rate['is_shipping_line_item'];
		$is_fee_line_item      = array_key_exists( 'is_fee_line_item', $tax_rate ) && $tax_rate['is_fee_line_item'];

		$base_country = WC()->countries->get_base_country();

		$tax_exempt_type = 'NONE';

		if ( 'GB' === $base_country && $this->is_digital_good ) {
			$tax_exempt_type = 'ECZROUTPUTSERVICES';
		} elseif ( 'AU' === $base_country ) {
			$tax_exempt_type = 'EXEMPTOUTPUT';
			if ( $is_shipping_line_item ) {
				$treat_shipping_as = $this->settings->get_option( 'treat_shipping_as' );
				$tax_exempt_type = ( 'income' === $treat_shipping_as ) ? 'EXEMPTOUTPUT' : 'EXEMPTEXPENSES';
			}
			if ( $is_fee_line_item ) {
				$treat_fees_as   = $this->settings->get_option( 'treat_fees_as' );
				$tax_exempt_type = ( 'income' === $treat_fees_as ) ? 'EXEMPTOUTPUT' : 'EXEMPTEXPENSES';
			}
		}
		return $tax_exempt_type;
	}

	/**
	 * Returns an appropriate report tax type (if any) for the line item for the country. Since
	 * merchants may elect to treat shipping as an expense or as income, we need to take that into account too.
	 *
	 * Only AU, NZ and GB have report tax types
	 *
	 * OUTPUT:    Line item's report tax type should be income (and therefore output taxed)
	 *            Output taxes are ad valorem tax charged on the selling price of taxable items
	 *            Note: Shipping (esp flat rate) is treated as income by some merchants
	 * INPUT:     Line item's report tax type should be expense (and therefore input taxed)
	 *            Input taxes are taxes charged on services (e.g. shipping) which a business
	 *            consumes/uses in its operations
	 * MOSSSALES: Line item's report tax type should be MOSS sales. MOSS sales aren't included in
	 *            the VAT return in Xero, as they need to be reported separately in a VAT MOSS return
	 *
	 * @since 1.7.7
	 * @version 1.7.39
	 *
	 * @return string (empty) | OUTPUT | INPUT | MOSSSALES
	 */
	protected function get_report_tax_type_for_base_country() {
		$tax_rate = $this->get_tax_rate();

		$is_shipping_line_item = array_key_exists( 'is_shipping_line_item', $tax_rate ) && $tax_rate['is_shipping_line_item'];
		$is_fee_line_item      = array_key_exists( 'is_fee_line_item', $tax_rate ) && $tax_rate['is_fee_line_item'];

		$base_country = WC()->countries->get_base_country();

		$report_tax_type = '';
		if ( in_array( $base_country, array( 'AU', 'NZ', 'GB' ) ) ) {
			$report_tax_type = 'OUTPUT';
			if ( $is_shipping_line_item ) {
				$treat_shipping_as = $this->settings->get_option( 'treat_shipping_as' );
				$report_tax_type = ( 'income' === $treat_shipping_as ) ? 'OUTPUT' : 'INPUT';
			}
			if ( $is_fee_line_item ) {
				$treat_fees_as   = $this->settings->get_option( 'treat_fees_as' );
				$report_tax_type = ( 'income' === $treat_fees_as ) ? 'OUTPUT' : 'INPUT';
			}
		}

		// Support the MOSSSALES tax type
		// Note this relies on the tax rate label having "moss" in it
		if (
			! $is_shipping_line_item &&
			! empty( $tax_rate['label'] ) &&
			false !== strpos( strtolower( $tax_rate['label'] ), 'moss' )
		) {
			$report_tax_type = 'MOSSSALES';
		}

		return $report_tax_type;
	}

	/**
	 * Search an array of (active) tax rates from Xero for the one that matches the given label and rate in WooCommerce
	 *
	 * @deprecated 1.7.30
	 * @param array $tax_rates		An array of tax rates retrieved from Xero
	 * @param array $label_to_find	The name of the rate to find (i.e. the Tax Name from WooCommerce > Settings > Tax > Standard Rates)
	 * @param array $rate_to_find	The rate (in percent) to find
	 *
	 * @return wc_deprecated_function
	 */
	protected static function get_tax_type_for_label_and_rate( $tax_rates, $label_to_find, $rate_to_find, $logger ) {
		return wc_deprecated_function( __METHOD__, '1.7.30', 'WC_XR_Line_Item::get_tax_type_for_label_rate_and_type' );
	}

	/**
	 * Search an array of (active) tax rates from Xero for the one that matches the given label and rate in WooCommerce
	 *
	 * @param array        $tax_rates       An array of tax rates retrieved from Xero.
	 * @param string       $label_to_find   The name of the rate to find (i.e. the Tax Name from WooCommerce > Settings > Tax > Standard Rates).
	 * @param float        $rate_to_find    The rate (in percent) to find.
	 * @param string       $report_tax_type The reporting tax type e.g. OUTPUT, INPUT.
	 * @param WC_XR_Logger $logger          Instance of WC_XR_Logger.
	 *
	 * @return string The xero tax type found (e.g. "OUTPUT") or an empty string if not found.
	 */
	protected static function get_tax_type_for_label_rate_and_type( $tax_rates, $label_to_find, $rate_to_find, $report_tax_type, $logger ) {
		$tax_type = '';
		$tax_name = '';

		// Find all matches for rate and report tax type.
		$matches = array();
		foreach ( $tax_rates as $tax_rate ) {
			if ( abs( $rate_to_find - $tax_rate['effective_rate'] ) <= 0.0001 && $report_tax_type === $tax_rate['report_tax_type'] ) {
				$logger->write( " - Found match: Name ({$tax_rate['name']}), Rate ({$tax_rate[ 'effective_rate' ]}), TaxType ({$tax_rate[ 'tax_type' ]}) ReportTaxType ({$tax_rate[ 'report_tax_type' ]})" );
				$matches[] = $tax_rate;
			}
		}

		/**
		 * Check for exact label match for Zero tax rate
		 * Only search for exact label match IF.
		 *  - Tax rate is Zero.
		 *  - No matches found by Rate && ReportTaxType.
		 *
		 * @see https://github.com/woocommerce/woocommerce-xero/issues/266.
		 */
		if ( empty( $matches ) && $rate_to_find <= 0 ) {
			$zero_tax_type = false;
			$logger->write( 'No tax rates found for given Rate and ReportTaxType. Searching zero tax rates by exact label match' );
			foreach ( $tax_rates as $tax_rate ) {
				if ( abs( $rate_to_find - $tax_rate['effective_rate'] ) <= 0.0001 && strcasecmp( $tax_rate['name'], $label_to_find ) === 0 ) {
					$logger->write( " - Found Zero tax rate match: Name ({$tax_rate['name']}), Rate ({$tax_rate['effective_rate']}), TaxType ({$tax_rate['tax_type']}) ReportTaxType ({$tax_rate['report_tax_type']})" );
					$zero_tax_type = $tax_rate['tax_type'];
					break;
				}
			}
			if ( false !== $zero_tax_type ) {
				return $zero_tax_type;
			}
		}

		// Find closest matching name.
		$label_without_rate = preg_replace( '/\s*\([0-9.]+%\)/', '', $label_to_find );
		$lowest_difference  = PHP_INT_MAX;

		foreach ( $matches as $tax_rate ) {
			// If tax rate name does not contain a rate, then compare without it.
			$difference = levenshtein( $tax_rate['name'], strpos( $tax_rate['name'], '%' ) === false ? $label_without_rate : $label_to_find );

			if ( $difference < $lowest_difference ) {
				$lowest_difference = $difference;
				$tax_type          = $tax_rate['tax_type'];
				$tax_name          = $tax_rate['name'];
			}

			// Found an exact match, so break out of loop.
			if ( 0 === $difference ) {
				break;
			}
		}

		if ( ! empty( $tax_type ) ) {
			$logger->write( " - Matched: Name ($tax_name) TaxType ($tax_type)" );
		}

		return $tax_type;
	}

	/**
	 * Format the line item to XML and return the XML string
	 *
	 * @return string
	 */
	public function to_xml() {
		$xml = '<LineItem>';

		// Description
		if ( '' !== $this->get_description() ) {
			$xml .= '<Description>' . $this->get_description() . '</Description>';
		}

		// Account code
		if ( '' !== $this->get_account_code() ) {
			$xml .= '<AccountCode>' . $this->get_account_code() . '</AccountCode>';
		}

		// Check if there's an item code
		if ( '' !== $this->get_item_code() ) {
			$xml .= '<ItemCode>' . htmlspecialchars( $this->get_item_code(), ENT_XML1, 'UTF-8' ) . '</ItemCode>';
		}

		$xml .= '<UnitAmount>' . $this->get_unit_amount() . '</UnitAmount>';

		// Quantity
		$xml .= '<Quantity>' . $this->get_quantity() . '</Quantity>';

		// Tax Amount.
		$tax_type = wc_tax_enabled() ? $this->get_tax_type() : 'NONE';
		if ( ! empty( $tax_type ) ) {
			$xml .= '<TaxType>' . $tax_type . '</TaxType>';
		}
		$xml .= '<TaxAmount>' . $this->get_tax_amount() . '</TaxAmount>';

		// Discount?
		$discount_amount = $this->get_discount_amount();
		if ( 0.001 < abs( $discount_amount ) ) {
			$xml .= '<DiscountAmount>' . $discount_amount . '</DiscountAmount>';
		}

		$xml .= '</LineItem>';

		return $xml;
	}
}
