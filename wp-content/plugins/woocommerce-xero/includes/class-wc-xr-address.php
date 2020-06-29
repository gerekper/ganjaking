<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Address {

	private $type = 'POBOX';
	private $line_1 = '';
	private $line_2 = '';
	private $city = '';
	private $region = '';
	private $postal_code = '';
	private $country = '';

	/**
	 * @return string
	 */
	public function get_type() {
		return apply_filters( 'woocommerce_xero_address_type', $this->type, $this );
	}

	/**
	 * @param string $type
	 */
	public function set_type( $type ) {
		$this->type = htmlspecialchars( $type );
	}

	/**
	 * @return string
	 */
	public function get_line_1() {
		return apply_filters( 'woocommerce_xero_address_line_1', $this->line_1, $this );
	}

	/**
	 * @param string $line_1
	 */
	public function set_line_1( $line_1 ) {
		$this->line_1 = htmlspecialchars( $line_1 );
	}

	/**
	 * @return string
	 */
	public function get_line_2() {
		return apply_filters( 'woocommerce_xero_address_line_2', $this->line_2, $this );
	}

	/**
	 * @param string $line_2
	 */
	public function set_line_2( $line_2 ) {
		$this->line_2 = htmlspecialchars( $line_2 );
	}

	/**
	 * @return string
	 */
	public function get_city() {
		return apply_filters( 'woocommerce_xero_address_city', $this->city, $this );
	}

	/**
	 * @param string $city
	 */
	public function set_city( $city ) {
		$this->city = htmlspecialchars( $city );
	}

	/**
	 * @return string
	 */
	public function get_region() {
		return apply_filters( 'woocommerce_xero_address_region', $this->region, $this );
	}

	/**
	 * @param string $region
	 */
	public function set_region( $region ) {
		$this->region = htmlspecialchars( $region );
	}

	/**
	 * @return string
	 */
	public function get_postal_code() {
		return apply_filters( 'woocommerce_xero_address_postal_code', $this->postal_code, $this );
	}

	/**
	 * @param string $postal_code
	 */
	public function set_postal_code( $postal_code ) {
		$this->postal_code = htmlspecialchars( $postal_code );
	}

	/**
	 * @return string
	 */
	public function get_country() {
		return apply_filters( 'woocommerce_xero_address_country', $this->country, $this );
	}

	/**
	 * @param string $country
	 */
	public function set_country( $country ) {
		$this->country = htmlspecialchars( $country );
	}

	/**
	 * Return XML of address
	 *
	 * @return string
	 */
	public function to_xml() {
		$xml = '<Address>';

		// Address Type
		$xml .= '<AddressType>' . $this->get_type() . '</AddressType>';

		// Line 1
		$xml .= '<AddressLine1>' . $this->get_line_1() . '</AddressLine1>';

		// Line 2
		if ( '' !== $this->get_line_2() ) {
			$xml .= '<AddressLine2>' . $this->get_line_2() . '</AddressLine2>';
		}

		// City
		$xml .= '<City>' . $this->get_city() . '</City>';

		// Region
		$xml .= '<Region>' . $this->get_region() . '</Region>';

		// PostalCode
		$xml .= '<PostalCode>' . $this->get_postal_code() . '</PostalCode>';

		// Country
		$xml .= '<Country>' . $this->get_country() . '</Country>';

		$xml .= '</Address>';

		return $xml;
	}


}