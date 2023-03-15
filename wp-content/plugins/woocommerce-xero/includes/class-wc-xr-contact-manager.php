<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Contact_Manager {

	/**
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Contact_Manager constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return WC_XR_Address
	 */
	public function get_address_by_order( $order ) {

		// Setup address object.
		$address = new WC_XR_Address();

		// Set line 1.
		$billing_address_1 = $order->get_billing_address_1();
		$address->set_line_1( $billing_address_1 );

		// Set city.
		$billing_city = $order->get_billing_city();
		$address->set_city( $billing_city );

		// Set region.
		$billing_state = $order->get_billing_state();
		$address->set_region( $billing_state );

		// Set postal code.
		$billing_postcode = $order->get_billing_postcode();
		$address->set_postal_code( $billing_postcode );

		// Set country.
		$billing_country = $order->get_billing_country();
		$address->set_country( $billing_country );

		// Set line 2.
		$billing_address_2 = $order->get_billing_address_2();
		if ( strlen( $billing_address_2 ) > 0 ) {
			$address->set_line_2( $billing_address_2 );
		}

		// Return address object.
		return $address;
	}

	/**
	 * Returns a xero contact ID based on an email address if one is found
	 * null otherwise
	 *
	 * @param  string $email Customer email.
	 * @return string|null
	 */
	public function get_id_by_email( $email ) {
		$contact = $this->get_contact_by_email( $email, '' );
		if ( ! empty( $contact ) && ! empty( $contact['id'] ) ) {
			return $contact['id'];
		}
		return null;
	}

	/**
	 * Returns a xero contact ID and Name based on an email address if one is found
	 * null otherwise
	 *
	 * @param string $email        Customer email.
	 * @param string $contact_name Contact/Invoice Name.
	 * @return array|null
	 */
	public function get_contact_by_email( $email, $contact_name ) {
		if ( ! $email ) {
			return null;
		}

		$contact_request = new WC_XR_Request_Contact( $this->settings, $email );

		$transient_key = 'wc_xero_contact_info_' . md5( $email );
		if ( get_transient( $transient_key ) ) {
			return get_transient( $transient_key );
		}
		$contact_request->do_request();
		$xml_response = $contact_request->get_response_body_xml();

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if (
			'OK' == $xml_response->Status &&
			! empty( $xml_response->Contacts ) &&
			$xml_response->Contacts->Contact->ContactID->__toString()
		) {
			$contact = array(
				'id'   => $xml_response->Contacts->Contact->ContactID->__toString(),
				'name' => $xml_response->Contacts->Contact->Name->__toString(),
			);

			/**
			 * Backward Compatibility.
			 *
			 * Xero Can have multiple contact with same email address.
			 * Compare generated old contact name with Xero contact name in case of multiple contacts.
			 */
			if ( $xml_response->Contacts->Contact->count() > 1 ) {
				foreach ( $xml_response->Contacts->Contact as $xero_contact ) {
					$id   = $xero_contact->ContactID->__toString();
					$name = $xero_contact->Name->__toString();
					if ( strtolower( $name ) === strtolower( $contact_name ) ) {
						$contact = array(
							'id'   => $id,
							'name' => $name,
						);
						break;
					}
				}
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			set_transient( $transient_key, $contact, 31 * DAY_IN_SECONDS );
			return $contact;
		}

		return null;
	}

	/**
	 * Returns a xero contact ID and email based on name if one is found
	 * null otherwise
	 *
	 * @param string $contact_name Contact/Invoice Name.
	 * @return array|null
	 */
	public function get_contact_by_name( $contact_name ) {
		if ( ! $contact_name ) {
			return null;
		}

		$contact_request = new WC_XR_Request_Contact( $this->settings, '', $contact_name );

		$transient_key = 'wc_xero_contact_name_info_' . md5( $contact_name );
		$cache_data    = get_transient( $transient_key );
		if ( $cache_data ) {
			return $cache_data;
		}

		$contact_request->do_request();
		$xml_response = $contact_request->get_response_body_xml();

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if (
			'OK' === $xml_response->Status->__toString() &&
			! empty( $xml_response->Contacts ) &&
			$xml_response->Contacts->Contact->ContactID->__toString()
		) {
			$contact = array(
				'id'    => $xml_response->Contacts->Contact->ContactID->__toString(),
				'email' => $xml_response->Contacts->Contact->EmailAddress->__toString(),
			);

			set_transient( $transient_key, $contact, 1 * DAY_IN_SECONDS );
			return $contact;
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return null;
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return WC_XR_Contact
	 */
	public function get_contact_by_order( $order ) {
		// Setup Contact object.
		$contact = new WC_XR_Contact();

		$billing_company    = $order->get_billing_company();
		$billing_first_name = $order->get_billing_first_name();
		$billing_last_name  = $order->get_billing_last_name();

		// Set Invoice name.
		if ( apply_filters( 'woocommerce_xero_use_company_name', true ) && strlen( $billing_company ) > 0 ) {
			$invoice_name = $billing_company;
		} else {
			$invoice_name = $billing_first_name . ' ' . $billing_last_name;
		}

		$billing_email       = $order->get_billing_email();
		$unique_invoice_name = $invoice_name . ' (' . $billing_email . ')';
		$xero_contact        = $this->get_contact_by_email( $billing_email, $invoice_name );
		$contact_id_only     = null;

		// Check if "use customer email in contact name" setting is enabled to avoid duplicate contact name.
		$append_email_to_contact = 'on' === $this->settings->get_option( 'append_email_to_contact' );

		// See if a previous contact exists.
		if ( ! empty( $xero_contact ) && ! empty( $xero_contact['id'] ) ) {
			$contact->set_id( $xero_contact['id'] );
			$contact_id_only = $contact;

			// Check if "use customer email in contact name" setting is enabled and xero contact contains email in name.
			if (
				strtolower( $unique_invoice_name ) === strtolower( $xero_contact['name'] ) &&
				$append_email_to_contact
			) {

				$invoice_name = $unique_invoice_name;

			} elseif (
				strtolower( $invoice_name ) !== strtolower( $xero_contact['name'] ) &&
				$append_email_to_contact
			) {
				$xero_contact_name = $this->get_contact_by_name( $invoice_name );
				if ( ! empty( $xero_contact_name ) && ! empty( $xero_contact_name['id'] ) && $billing_email !== $xero_contact_name['email'] ) {
					$invoice_name = $unique_invoice_name;
				}
			}
		} else {
			// Only append email to contact name if contact with same name exists.
			$xero_contact_name = $this->get_contact_by_name( $invoice_name );
			if (
				! empty( $xero_contact_name ) &&
				! empty( $xero_contact_name['id'] ) &&
				$billing_email !== $xero_contact_name['email'] &&
				$append_email_to_contact
			) {
				$invoice_name = $unique_invoice_name;
			}
		}

		// Set name.
		$contact->set_name( $invoice_name );

		// Set first name.
		$contact->set_first_name( $billing_first_name );

		// Set last name.
		$contact->set_last_name( $billing_last_name );

		// Set email address.
		$contact->set_email_address( $billing_email );

		// Set address.
		$contact->set_addresses( array( $this->get_address_by_order( $order ) ) );

		// Set phone.
		$billing_phone = $order->get_billing_phone();
		$contact->set_phones( array( new WC_XR_Phone( $billing_phone ) ) );

		// Set VAT/Tax Number.
		$vat_number_meta_key = apply_filters( 'woocommerce_xero_vat_number_meta_key', '_vat_number' );
		if ( $order->meta_exists( $vat_number_meta_key ) ) {
			$tax_number = $order->get_meta( $vat_number_meta_key );
			$contact->set_tax_number( $tax_number );
		}

		// Return contact.

		if ( ! is_null( $contact_id_only ) ) {

			$transient_key = 'wc_xero_contact_'. md5( serialize( $contact_id_only ) );
			if ( get_transient( $transient_key ) ) {
				return get_transient( $transient_key );
			}
			// Update a contact if we pulled info from a previous thing.
			$contact_request_update = new WC_XR_Request_Update_Contact( $this->settings, $xero_contact['id'], $contact );
			$contact_request_update->do_request();

			set_transient( $transient_key, $contact_id_only, 31 * DAY_IN_SECONDS );
			return $contact_id_only;
		}

		return $contact;
	}

}
