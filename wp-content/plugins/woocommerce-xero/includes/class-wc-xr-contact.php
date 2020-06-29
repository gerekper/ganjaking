<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Contact {

	private $name          = '';
	private $id            = '';
	private $first_name    = '';
	private $last_name     = '';
	private $email_address = '';
	private $addresses     = array();
	private $phones        = array();
	private $tax_number    = '';

	/**
	 * @return string
	 */
	public function get_id() {
		return apply_filters( 'woocommerce_xero_contact_id', $this->id, $this );
	}

	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return apply_filters( 'woocommerce_xero_contact_name', $this->name, $this );
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = htmlspecialchars( $name );
	}

	/**
	 * @return string
	 */
	public function get_first_name() {
		return apply_filters( 'woocommerce_xero_contact_first_name', $this->first_name, $this );
	}

	/**
	 * @param string $first_name
	 */
	public function set_first_name( $first_name ) {
		$this->first_name = htmlspecialchars( $first_name );
	}

	/**
	 * @return string
	 */
	public function get_last_name() {
		return apply_filters( 'woocommerce_xero_contact_last_name', $this->last_name, $this );
	}

	/**
	 * @param string $last_name
	 */
	public function set_last_name( $last_name ) {
		$this->last_name = htmlspecialchars( $last_name );
	}

	/**
	 * @return string
	 */
	public function get_email_address() {
		return apply_filters( 'woocommerce_xero_contact_email_address', $this->email_address, $this );
	}

	/**
	 * @param string $email_address
	 */
	public function set_email_address( $email_address ) {
		$this->email_address = htmlspecialchars( $email_address );
	}

	/**
	 * @return array
	 */
	public function get_addresses() {
		return apply_filters( 'woocommerce_xero_contact_addresses', $this->addresses, $this );
	}

	/**
	 * @param array $addresses
	 */
	public function set_addresses( $addresses ) {
		$this->addresses = $addresses;
	}

	/**
	 * @return array
	 */
	public function get_phones() {
		return apply_filters( 'woocommerce_xero_contact_phones', $this->phones, $this );
	}

	/**
	 * @param array $phones
	 */
	public function set_phones( $phones ) {
		$this->phones = $phones;
	}

	/**
	 * Set the VAT/Tax number for a contact.
	 *
	 * @since 1.7.28
	 * @param string $tax_number
	 */
	public function set_tax_number( $tax_number ) {
		$this->tax_number = $tax_number;
	}

	/**
	 * Get the VAT/Tax number for a contact.
	 *
	 * @since 1.7.28
	 * @return string $tax_number
	 */
	public function get_tax_number() {
		return apply_filters( 'woocommerce_xero_contact_tax_number', $this->tax_number, $this );
	}

	/**
	 * Format just the contact id to XML
	 *
	 * @return string
	 */
	public function id_to_xml() {
		$xml = '<Contact>';
		$xml .= '<ContactID>' . $this->get_id() . '</ContactID>';
		$xml .= '</Contact>';

		return $xml;
	}

	/**
	 * Format contact to XML
	 *
	 * @return string
	 */
	public function to_xml() {
		$xml = '<Contact>';

		// Set the contact ID if one is found..
		if ( $this->get_id() ) {
			$xml .= '<ContactID>' . $this->get_id() . '</ContactID>';
		}

		// Name
		$xml .= '<Name>' . $this->get_name() . '</Name>';

		// FirstName
		$xml .= '<FirstName>' . $this->get_first_name() . '</FirstName>';

		// LastName
		$xml .= '<LastName>' . $this->get_last_name() . '</LastName>';

		// EmailAddress
		$xml .= '<EmailAddress>' . $this->get_email_address() . '</EmailAddress>';

		// Addresses
		$addresses = $this->get_addresses();

		// Check Addresses
		if ( count( $addresses ) > 0 ) {
			$xml .= '<Addresses>';

			// Add Address to XML
			foreach ( $addresses as $address ) {
				$xml .= $address->to_xml();
			}

			$xml .= '</Addresses>';
		}

		// Phones
		$phones = $this->get_phones();

		// Check Phones
		if ( count( $phones ) > 0 ) {
			$xml .= '<Phones>';

			// Add Phones to XML
			foreach ( $phones as $phone ) {
				$xml .= $phone->to_xml();
			}

			$xml .= '</Phones>';
		}

		// VAT/Tax Number.
		if ( $this->get_tax_number() ) {
			$xml .= '<TaxNumber>' . $this->get_tax_number() . '</TaxNumber>';
		}

		$xml .= '</Contact>';

		return $xml;
	}

}
