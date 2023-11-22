<?php
/**
 * WooCommerce Address Validation
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * PostcodeSoftware.net Provider Class
 *
 * Extends abstract provider class to provide postcode lookup via PostcodeSoftware.net API
 *
 * @link
 * @since 1.0
 */
class WC_Address_Validation_Provider_Postcode_Software_Dot_Net extends \WC_Address_Validation_Provider {


	/* API Endpoint */
	const API_ENDPOINT = 'http://ws1.postcodesoftware.co.uk/lookup.asmx/getAddress?';


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->id = 'postcodesoftware_dot_net';

		$this->title = __( 'PostcodeSoftware.net', 'woocommerce-address-validation' );

		$this->countries = array( 'GB' );

		$this->supports = array(
			'postcode_lookup'
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->account_number = $this->settings[ 'account_number' ];
		$this->password       = $this->settings[ 'password' ];

		if ( $this->account_number && $this->password ) {
			$this->configured = true;
		}

		// Save settings
		add_action( 'wc_address_validation_update_provider_options_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Lookup postcode using API
	 *
	 * @since 1.0
	 * @param string $postcode
	 * @param string $house_number Optional. Used by Postcode.nl.
	 * @return array locations found
	 */
	public function lookup_postcode( $postcode, $house_number = '' ) {

		$locations = array();

		// set account/password/postcode GET args
		$args = array(
			'account'  => $this->account_number,
			'password' => $this->password,
			'postcode' => urlencode( $postcode )
		);

		$this->maybe_log_request( self::API_ENDPOINT, $args );

		// send GET request
		$api_response = wp_safe_remote_get( add_query_arg( $args, self::API_ENDPOINT ) );

		// check for network timeout, etc
		if ( is_wp_error( $api_response ) || empty( $api_response['body'] ) ) {

			// Assign API response to the response object for later debug log
			$response = $api_response;

			$locations = [
				'value' => 'none',
				'name'  => $this->get_lookup_provider_error_message( $api_response ),
			];

		} else {

			// decode response body
			$response = simplexml_load_string( $api_response['body'] );

			// setup locations if more than 1 exists and there's no error
			if ( isset( $response->ErrorNumber ) && '0' == (string) $response->ErrorNumber ) {

				$address_1 = (string) $response->Address1; // cast as string otherwise simpleXML elements are used
				$address_2 = (string) $response->Address2;
				$city      = (string) $response->Town;
				$postcode  = (string) $response->Postcode;
				$state     = (string) $response->County;

				// @see https://web.postcodesoftware.com/sdk for a breakdown of premise-level data
				if ( isset( $response->PremiseData ) ) {

					// premise data is stored in ';' separated format
					foreach ( explode( ';', $response->PremiseData ) as $location_num => $location ) {

						if ( ! $location ) {
							continue;
						}

						// individual premise details are stored in '|' separated format
						list( $company, $building_details, $building_number ) = explode( '|', $location );

						// start with a base of building number + building or location name, like "100 Lancaster" (building) or "Boughton Hill" (location)
						$line_1 = trim( "{$building_number} {$address_1}" );
						$line_2 = $address_2;

						// if there are specific building details, use that as line 1 and the number/location from above as line 2
						if ( $building_details ) {

							$line_2 = $line_1;

							// add the second address detail back to line 2 if available
							if ( $address_2 ) {
								$line_2 .= ', ' . $address_2;
							}

							// this is returned as {number}/{name}
							$line_1 = str_replace( '/', ' ', $building_details );
						}

						// build the name that will be displayed in the select dropdown
						$name = implode( ', ', array_filter( [ $company, $line_1, $line_2, $city ] ) );

						$locations[ $location_num ] = array(
							'value'     => "location-{$location_num}",
							'company'   => $company,
							'address_1' => $line_1,
							'address_2' => $line_2,
							'city'      => $city,
							'postcode'  => $postcode,
							'state'     => $state,
							'name'      => $name,
						);
					}
				} else {

					$locations[] = array(
						'value'     => "location",
						'address_1' => $address_1,
						'address_2' => $address_2,
						'city'      => $city,
						'postcode'  => $postcode,
						'state'     => $state,
						'name'      => "{$address_1} {$address_2} {$city}",
					);
				}

			} else {

				/**
				 * Change the message displayed when a postcode lookup returns no addresses
				 *
				 * @since 1.0.4
				 * @param string $message the message to display
				 * @param string $postcode the postcode the user entered
				 */
				$locations = array( 'value' => 'none', 'name' => apply_filters( 'wc_address_validation_postcode_lookup_no_address_found_message', __( 'No addresses found, please check your postcode and try again.', 'woocommerce-address-validation' ), $postcode ) );
			}
		}

		if ( wc_address_validation()->is_debug_mode_enabled() ) {
			wc_address_validation()->log( $response->asXML() );
		}

		return $this->prepare_lookup_data( $locations, $postcode, $args );
	}


	/**
	 * Checks if provider is configured correctly.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$this->is_configured = ! empty( $this->account_number ) && ! empty( $this->password );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'account_number' => array(
				'title'    => __( 'Account Number', 'woocommerce-address-validation' ),
				'type'     => 'text',
				'desc_tip' => __( 'Log into your account or look at your sign-up email to find your account number.', 'woocommerce-address-validation' ),
				'default'  => '',
			),

			'password' => array(
				'title'    => __( 'Password', 'woocommerce-address-validation' ),
				'type'     => 'text',
				'desc_tip' => __( 'Log into your account or look at your sign-up email to find your password.', 'woocommerce-address-validation' ),
				'default'  => '',
			),
		);
	}


}
