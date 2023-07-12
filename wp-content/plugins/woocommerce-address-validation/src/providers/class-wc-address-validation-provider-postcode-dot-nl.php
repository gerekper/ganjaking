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

use SkyVerge\WooCommerce\PluginFramework\v5_11_3 as Framework;

/**
 * Postcode.nl Provider Class
 *
 * Extends abstract provider class to provide postcode lookup via Postcode.nl API
 *
 * @link
 * @since 1.2.0
 */
class WC_Address_Validation_Provider_Postcode_Dot_Nl extends \WC_Address_Validation_Provider {


	/* API Endpoint */ // https://api.postcode.nl/rest/addresses/{postcode}/{houseNumber}/{houseNumberAddition}
	const API_ENDPOINT = 'https://api.postcode.nl/rest/addresses/';


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		$this->id = 'postcode_dot_nl';

		$this->title = __( 'Postcode.nl', 'woocommerce-address-validation' );

		$this->countries = array( 'NL' );

		$this->supports = array(
			'postcode_lookup'
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->app_key    = $this->settings[ 'app_key' ];
		$this->app_secret = $this->settings[ 'app_secret' ];

		if ( $this->app_key && $this->app_secret ) {
			$this->configured = true;
		}

		// Save settings
		add_action( 'wc_address_validation_update_provider_options_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Lookup postcode using API
	 *
	 * @since 1.2.0
	 * @param string $postcode
	 * @param string $house_number Optional. Used by Postcode.nl.
	 * @return array locations found
	 */
	public function lookup_postcode( $postcode, $house_number = '' ) {

		$locations = array();

		// remove spaces in postcode ('1234 AB' should be '1234AB')
		$postcode     = str_replace( ' ', '', trim( $postcode ) );
		$house_number = trim( $house_number );

		// check if house_number was provided
		if ( '' === $house_number || empty( $house_number ) ) {
			$locations = [ 'value' => 'none', 'name' => __( 'No addresses found, please enter your house number and try again.', 'woocommerce-address-validation' ) ];
		}

		// get the house number addition, if provided
		list( $house_number, $house_number_addition ) = $this->split_house_number( $house_number );

		// format URL
		$url = self::API_ENDPOINT . urlencode( $postcode ) . '/' . $house_number . '/' . $house_number_addition;

		// set app_key/app_secret GET args
		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->app_key . ':' . $this->app_secret )
			)
		);

		$this->maybe_log_request( $url, $args );

		// send GET request
		$api_response = wp_safe_remote_get( $url, $args );

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
			$response = json_decode( $api_response['body'] );

			// check for successful response
			if ( ! isset( $response->exception ) ) {

				$street                = $response->street;
				$house_number          = $response->houseNumber;
				$house_number_addition = $response->houseNumberAddition;

				$address_1 = $street . ' ' . $house_number . ( '' != $house_number_addition ? ' ' . $house_number_addition : '' );
				$city      = $response->city;
				$postcode  = $response->postcode;
				$state     = $response->province;

				// Postcode.nl only returns one result
				$locations[] = array(
					'value'     => "location-0",
					'company'   => '',
					'address_1' => $address_1,
					'address_2' => '',
					'city'      => $city,
					'postcode'  => $postcode,
					'state'     => $state,
					'name'      => "{$address_1} {$city} {$state}",
				);

			} else {

				/**
				 * Change the message displayed when a postcode lookup returns no addresses
				 *
				 * @since 1.2
				 * @param string $message the message to display
				 * @param string $postcode the postcode the user entered
				 */
				$locations = array( 'value' => 'none', 'name' => apply_filters( 'wc_address_validation_postcode_lookup_no_address_found_message', __( 'No addresses found, please check your postcode and try again.', 'woocommerce-address-validation' ), $postcode ) );

			}
		}

		if ( wc_address_validation()->is_debug_mode_enabled() ) {
			wc_address_validation()->log( print_r( $response, true ) );
		}

		return $this->prepare_lookup_data( $locations, $postcode, $args );
	}


	/**
	 * Checks if provider is configured correctly.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$this->is_configured = ! empty( $this->app_key ) && ! empty( $this->app_secret );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 1.2.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'app_key' => array(
				'title'    => __( 'Application Key', 'woocommerce-address-validation' ),
				'type'     => 'text',
				'desc_tip' => __( 'Log into your account or look at your sign-up email to find your application key.', 'woocommerce-address-validation' ),
				'default'  => '',
			),

			'app_secret' => array(
				'title'    => __( 'Application Secret', 'woocommerce-address-validation' ),
				'type'     => 'text',
				'desc_tip' => __( 'Log into your account or look at your sign-up email to find your application secret.', 'woocommerce-address-validation' ),
				'default'  => '',
			),
		);
	}


	/**
	 * Split house number helper function
	 *
	 * @since 1.2.0
	 * @param string $house_number
	 * @return array house number and house number addition
	 */

	public function split_house_number( $house_number ) {

		$house_number_addition = '';

		if ( preg_match( '~^(?<number>[0-9]+)(?:[^0-9a-zA-Z]+(?<addition1>[0-9a-zA-Z ]+)|(?<addition2>[a-zA-Z](?:[0-9a-zA-Z ]*)))?$~', $house_number, $match ) ) {

			$house_number = $match['number'];

			if ( isset( $match['addition2'] ) ) {

				$house_number_addition = $match['addition2'];

			} elseif ( isset( $match['addition1'] ) ) {

				$house_number_addition = $match['addition1'];

			}
		}

		return array( $house_number, $house_number_addition );
	}


}
