<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Phone {

	private $type = 'DEFAULT';
	private $number = '';

	/**
	 * Constructor of new Phone object
	 *
	 * @param $number
	 */
	public function __construct( $number ) {
		$this->set_number( $number );
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return apply_filters( 'woocommerce_xero_phone_type', $this->type, $this );
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
	public function get_number() {
		return apply_filters( 'woocommerce_xero_phone_number', $this->number, $this );
	}

	/**
	 * @param string $number
	 */
	public function set_number( $number ) {
		$this->number = htmlspecialchars( $number );
	}

	/**
	 * Generate Phone XML
	 *
	 * @return string
	 */
	public function to_xml() {
		$xml = '<Phone>';
		$xml .= '<PhoneType>' . $this->get_type() . '</PhoneType>';
		$xml .= '<PhoneNumber>' . $this->get_number() . '</PhoneNumber>';
		$xml .= '</Phone>';

		return $xml;
	}

}
