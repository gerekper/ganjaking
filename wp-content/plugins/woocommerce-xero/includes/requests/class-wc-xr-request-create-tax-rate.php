<?php
/**
 * Create tax rate request
 *
 * @package WooCommerce Xero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// https://developer.xero.com/documentation/api/tax-rates
// Note: To avoid overwriting tax rates with the same name but a different
// rate, we use PUT to create a tax rate instead of POST now.

// This avoids two tax rates with the same name in WooCommerce (but different
// rates) from fighting each other (e.g. WA Sales Tax of 8.9 percent in one
// location, but WA Sales Tax (same name) of 9.1 percent in another, or
// VAT of 10% for certain products but (reduced) VAT of 7% for other products).

// We have to do this since WooCommerce does not require rows in the Tax
// tables to have unique Names.

/**
 * Request to create tax rate
 */
class WC_XR_Request_Create_Tax_Rate extends WC_XR_Request {

	/**
	 * Constructor
	 *
	 * @param WC_XR_Settings $settings Settings instance.
	 * @param array          $rate Tax rate.
	 */
	public function __construct( WC_XR_Settings $settings, $rate ) {
		parent::__construct( $settings );

		$this->set_method( 'PUT' );
		$this->set_endpoint( 'TaxRates' );
		$this->set_body( $this->get_xml( $rate ) );
	}

	/**
	 * Flush Tax Rates cache before updating
	 *
	 * @return void
	 */
	protected function before_cache_set() {
		$cache_key = 'api_' . $this->get_endpoint();
		wp_cache_delete( $cache_key, 'wc_xero' );
	}

	/**
	 * Wrap tax rate into XML
	 *
	 * @param array $rate Tax rate.
	 * @return string
	 */
	public function get_xml( $rate ) {
		$xml  = '<TaxRate>';
		$xml .= '<Name>' . $rate['label'] . '</Name>';
		if ( array_key_exists( 'report_tax_type', $rate ) ) {
			$xml .= '<ReportTaxType>' . $rate['report_tax_type'] . '</ReportTaxType>';
		}
		$xml .= '<Status>ACTIVE</Status>';
		$xml .= '<TaxComponents>';
		$xml .= '<TaxComponent>';
		$xml .= '<Name>' . $rate['label'] . '</Name>';
		$xml .= '<Rate>' . $rate['rate'] . '</Rate>';
		$xml .= '<IsCompound>' . ( ( 'yes' === $rate['compound'] ) ? 'true' : 'false' ) . '</IsCompound>';
		$xml .= '</TaxComponent>';
		$xml .= '</TaxComponents>';
		$xml .= '</TaxRate>';
		return $xml;
	}

}
