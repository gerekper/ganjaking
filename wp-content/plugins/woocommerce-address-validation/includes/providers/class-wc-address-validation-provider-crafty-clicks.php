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
 * @author      SkyVerge & Crafty Clicks
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * CraftyClicks.co.uk Provider Class
 *
 * Extends abstract provider class to provide postcode lookup via CraftyClicks.co.uk API
 *
 * @link http://www.craftyclicks.co.uk/
 * @since 1.0.3
 */
class WC_Address_Validation_Provider_Crafty_Clicks extends \WC_Address_Validation_Provider {


	/* API Endpoint */
	const API_ENDPOINT = 'http://pcls1.craftyclicks.co.uk/xml/rapidaddress?';


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 1.0.3
	 */
	public function __construct() {

		$this->id = 'crafty_clicks';

		$this->title = __( 'Crafty Clicks', 'woocommerce-address-validation' );

		$this->countries = array( 'GB' );

		$this->supports = array(
			'postcode_lookup',
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->api_key = $this->settings['api_key'];

		// Save settings
		add_action( 'wc_address_validation_update_provider_options_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	/**
	 * Lookup postcode using API
	 *
	 * @since 1.0.3
	 * @param string $postcode
	 * @param string $house_number Optional. Used by Postcode.nl.
	 * @return array locations found
	 */
	public function lookup_postcode( $postcode, $house_number = '' ) {

		$locations = array();

		// set key and postcode GET args
		$args = array(
			'key'      => urlencode( $this->api_key ),
			'postcode' => urlencode( $postcode ),
			'response' => 'data_formatted',
			'lines'    => '2', // woocommerce checkout only contains 2 address lines by default
		);

		// send GET request
		$response = wp_safe_remote_get( add_query_arg( $args, self::API_ENDPOINT ) );

		// check for network timeout, etc
		if ( is_wp_error( $response ) || ( ! isset( $response['body'] ) ) ) {
			$locations = array( 'value' => 'none', 'name' => __( 'No addresses found, please check your postcode and try again.', 'woocommerce-address-validation' ) );
		}

		// decode response body
		$response = simplexml_load_string( $response['body'] );
		$result = null;

		// setup locations if more than 1 exists (1 or less indicates an error was returned or no matching locations were found)
		if ( isset( $response->address_data_formatted ) ) {

			$result = $response->address_data_formatted;
			$item_num = 0;

			foreach ( $result->delivery_point as $item ) {

				// build company field
				$organization = ucwords( strtolower( (string) $item->organisation_name ) );
				$department   = ucwords( strtolower( (string) $item->department_name ) );

				if ( $organization ) {
					$company = $organization . ( $department ? ', ' . $department : '' );
				} else {
					$company = '';
				}

				// build location
				$locations[ $item_num ] = array(
					'company'   => $company,
					'address_1' => ucwords( strtolower( $item->line_1 ) ),
					'address_2' => ucwords( strtolower( $item->line_2 ) ),
					'address_3' => ( empty( $item->line_3 ) ) ? '' : ucwords( strtolower( $item->line_3 ) ),
					'city'      => ucwords( strtolower( $result->town ) ),
					'postcode'  => (string) $result->postcode,
					'state'     => ucwords( strtolower( $result->traditional_county ) ),
				);

				// build location name
				$name = array();
				if ( ! empty( $company ) ) array_push( $name, $company );
				if ( ! empty( $locations[ $item_num ]['address_1'] ) ) array_push( $name, $locations[ $item_num ]['address_1'] );
				if ( ! empty( $locations[ $item_num ]['address_2'] ) ) array_push( $name, $locations[ $item_num ]['address_2'] );
				if ( ! empty( $locations[ $item_num ]['address_3'] ) ) array_push( $name, $locations[ $item_num ]['address_3'] );
				if ( ! empty( $locations[ $item_num ]['city'] ) ) array_push( $name, $locations[ $item_num ]['city'] );
				$locations[ $item_num ]['name'] = implode( ', ', $name );

				$item_num++;
			}

			// sort the results
			usort($locations, array( $this, 'address_compare' ) );

			// put in location values in the sorted order
			foreach( $locations as $item_num => $item ) {
				$locations[ $item_num ]['value'] = "location-{$item_num}";
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

		if ( 'yes' == get_option( 'wc_address_validation_debug_mode' ) ) {
			wc_address_validation()->log( print_r( $response, true ) );
			wc_address_validation()->log( print_r( $result, true ) );
		}

		return $locations;
	}


	/**
	 * Checks if provider is configured correctly.
	 *
	 * @since 1.0.3
	 *
	 * @return bool
	 */
	public function is_configured() {

		$this->is_configured = ! empty( $this->api_key );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 1.0.3
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'api_key' => array(
				'title'       => __( 'Access Token', 'woocommerce-address-validation' ),
				'type'        => 'text',
				'description' => __( 'Enter your Access Token from the Crafty Clicks website.', 'woocommerce-address-validation' ),
				'default'     => '',
			)
		);
	}


	/**
	 * Helper for sorting address results
	 *
	 * @since 1.0.3
	 */
	private function address_compare( $a, $b ) {

		// here we can devise any sorting decisions
		// easiest (and best!) is simply to sort on the first address line.
		$comparison = strnatcmp( $a['address_1'], $b['address_1'] );

		// if undecided, then sort by company name
		if ( 0 === $comparison ) {
			$comparison = strnatcmp( $a['company'], $b['company'] );
		}

		return $comparison;
	}


}
