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
 * PostcodeAnywhere.co.uk Provider Class.
 *
 * Extends abstract provider class to provide postcode lookup via PostCodeAnywhere.co.uk API.
 *
 * TODO PCA Predict (formerly Postcode Anywhere) has been acquired by Loqate and this handler will require an update and name changes accordingly; but also other internal references, assets, etc. {FN 2018-08-06}
 *
 * @link https://www.loqate.com/
 *
 * @since 1.0
 */
class WC_Address_Validation_Provider_Postcode_Anywhere extends \WC_Address_Validation_Provider {


	/* API Endpoint */
	const API_ENDPOINT = 'http://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/RetrieveByParts/v1.00/json3.ws?';


	/**
	 * Setup id/title/description and declare country / feature support
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->id = 'postcode_anywhere';

		$this->title = __( 'Loqate (legacy - UK)', 'woocommerce-address-validation' );

		$this->countries = array( 'GB', 'GG', 'JE', 'IM' );

		$this->supports = array(
			'postcode_lookup',
		);

		// setup form fields
		$this->init_form_fields();

		// load settings
		$this->init_settings();

		$this->api_key      = $this->settings['api_key'];
		$this->api_username = $this->settings['api_username'];

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

		// set key and postcode GET args
		$args = array(
			'Key'      => $this->api_key,
			'Postcode' => urlencode( $postcode )
		);

		// add username if available
		if ( $this->api_username ) {
			$args[ 'UserName'] = $this->api_username;
		}

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
			$response = json_decode( $api_response['body'] );

			// setup locations if an error was not returned
			if ( isset( $response->Items ) && is_array( $response->Items ) && ! isset( $response->Items[0]->Error ) ) {

				foreach( $response->Items as $item_num => $item ) {

					$locations[ $item_num ] = array(
						'value'     => "location-{$item_num}",
						'company'   => $item->Company,
						'address_1' => $item->Line1,
						'address_2' => $item->Line2,
						'address_3' => $item->Line3,
						'city'      => $item->PostTown,
						'postcode'  => $item->Postcode,
						'state'     => $item->County,
						'name'      => "{$item->Company} {$item->Line1} {$item->Line2} {$item->Line3} {$item->PostTown}",
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
			wc_address_validation()->log( print_r( $response, true ) );
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

		$this->is_configured = ! empty( $this->api_key );

		return parent::is_configured();
	}


	/**
	 * Init settings
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'api_key'  => array(
				'title'    => __( 'API Key', 'woocommerce-address-validation' ),
				'type'     => 'text',
				'description' => __( 'Enter your API Key from the Loqate website.', 'woocommerce-address-validation' ),
				'default'  => '',
			),

			'api_username' => array(
				'title'    => __( 'API Username', 'woocommerce' ),
				'type'     => 'text',
				'description' => __( 'Enter your username associated with your Royal Mail account. This is not required, so leave blank if you do not have one.', 'woocommerce-address-validation' ),
				'default'  => ''
			),
		);
	}


}
