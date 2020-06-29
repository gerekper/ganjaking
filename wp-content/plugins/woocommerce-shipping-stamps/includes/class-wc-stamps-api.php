<?php
/**
 * Stamps API wrapper class.
 *
 * @package WC_Stamps_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stamps API wrapper.
 *
 * Used to interact with the Stamps API
 */
class WC_Stamps_API {

	/**
	 * Instance of SoapClient.
	 *
	 * @var SoapClient
	 */
	private static $client = false;

	/**
	 * Authenticator.
	 *
	 * @var string|WP_Error.
	 */
	private static $authenticator = false;

	/**
	 * Instance of WC_Logger.
	 *
	 * @var WC_Logger
	 */
	private static $logger = false;

	/**
	 * Whether logging is enabled or not ('yes' or 'no').
	 *
	 * @var string
	 */
	private static $logging_enabled = null;

	/**
	 * Get rate name by type.
	 *
	 * @param string $type Type.
	 *
	 * @return string Rate name.
	 */
	public static function get_rate_type_name( $type ) {
		switch ( $type ) {
			case 'US-FC' :
				return 'First-Class Mail';
			break;
			case 'US-MM' :
				return 'Media Mail';
			break;
			case 'US-PP' :
				return 'Parcel Post';
			break;
			case 'US-PM' :
				return 'Priority Mail';
			break;
			case 'US-XM' :
				return 'Priority Mail Express';
			break;
			case 'US-EMI' :
				return 'Priority Mail Express International';
			break;
			case 'US-PMI' :
				return 'Priority Mail International';
			break;
			case 'US-FCI' :
				return 'First Class Mail International';
			break;
			case 'US-CM' :
				return 'Critical Mail';
			break;
			case 'US-PS' :
				return 'Parcel Select';
			break;
			case 'US-LM' :
				return 'Library Mail';
			break;
		}
	}

	/**
	 * Get addon name by type.
	 *
	 * @param string $type Type.
	 *
	 * @return string Addon name.
	 */
	public static function get_addon_type_name( $type ) {
		switch ( $type ) {
			case 'SC-A-HP' :
				return __( 'Stamps.com hidden postage', 'woocommerce-shipping-stamps' );
			break;
			case 'SC-A-INS' :
				return __( 'Stamps.com insurance', 'woocommerce-shipping-stamps' );
			break;
			case 'SC-A-INSRM' :
				return __( 'Stamps.com insurance for registered mail', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-CM' :
				return __( 'Certified mail', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-COD' :
				return __( 'Collect on delivery', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-DC' :
				return __( 'Delivery confirmation', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-ESH' :
				return __( 'Express sunday/holiday guaranteed', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-INS' :
				return __( 'USPS insurance', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-NDW' :
				return __( 'No delivery on saturdays', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-RD' :
				return __( 'Restricted delivery', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-REG' :
				return __( 'Registered mail', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-RR' :
				return __( 'Return reciept requested', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-RRM' :
				return __( 'Return reciept for merchandise', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-SC' :
				return __( 'Signature confirmation', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-SH' :
				return __( 'Fragile', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-PR' :
				return __( 'Perishable', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-WDS' :
				return __( 'Waive delivery signature', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-SR' :
				return __( 'Signature required', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-NDW' :
				return __( 'Do not deliver on saturday', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-ESH' :
				return __( 'Sunday/holiday guaranteed', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-NND' :
				return __( 'Notice of non-delivery', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-RRE' :
				return __( 'Electronic return reciept', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-LANS' :
				return __( 'Live animal no surcharge', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-LAWS' :
				return __( 'Live animal with surcharge', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-HM' :
				return __( 'Hazardous materials', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-CR' :
				return __( 'Cremated remains', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-1030' :
				return __( 'Deliver priority mail express by 10:30am', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-ASR' :
				return __( 'Adult signature required', 'woocommerce-shipping-stamps' );
			break;
			case 'US-A-ASRD' :
				return __( 'Adult signature restricted delivery', 'woocommerce-shipping-stamps' );
			break;
		}
	}

	/**
	 * Get SOAP client for Stamps service.
	 *
	 * @return SoapClient
	 */
	public static function get_client() {
		if ( ! self::$client ) {
			try {
				self::$client = new SoapClient( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wsdl/' . WC_STAMPS_INTEGRATION_WSDL_FILE, array( 'trace' => 1 ) );
			} catch ( SoapFault $e ) {
				self::log_soap_fault( $e, 'SoapFault during client construction' );

				// Work around in case the first attempt over ssl fails.
				self::$client = new SoapClient(
					plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wsdl/' . WC_STAMPS_INTEGRATION_WSDL_FILE,
					array(
						'trace'          => 1,
						'stream_context' => stream_context_create( array(
								'ssl' => array(
									'verify_peer'       => false,
									'verify_peer_name'  => true,
									'allow_self_signed' => false,
								),
							)
						),
					)
				);
			}
		}

		return self::$client;
	}

	/**
	 * Log a message.
	 *
	 * @param string $message Message to log.
	 */
	public static function log( $message ) {
		// Cache it, so we don't call `get_option()` everytime log is called.
		if ( is_null( self::$logging_enabled ) ) {
			self::$logging_enabled = get_option( 'wc_settings_stamps_logging', 'no' );
		}

		if ( 'yes' !== self::$logging_enabled ) {
			return;
		}

		if ( ! self::$logger ) {
			self::$logger = new WC_Logger();
		}

		self::$logger->add( 'stamps', $message );
	}

	/**
	 * Log SoapFault.
	 *
	 * @since 1.3.3
	 * @version 1.3.3
	 *
	 * @param SoapFault $e      Instance of SoapFault.
	 * @param string    $prefix Message prefix.
	 */
	private static function log_soap_fault( SoapFault $e, $prefix ) {
		self::log( $prefix . ' : ' . $e->getMessage() );
		self::log( 'Detailed exception:' );
		self::log( print_r( $e, true ) );
	}

	/**
	 * Make an API request.
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $request  Request params.
	 * @param bool   $retry    Whether to retry when failed.
	 *
	 * @return array|WP_Error Response on success, WP_Error if failed.
	 */
	public static function do_request( $endpoint, $request = array(), $retry = false ) {

		@ini_set( 'soap.wsdl_cache_enabled', 0 );

		try {

			if ( empty( $request['Authenticator'] ) ) {
				$authenticator = self::get_authenticator();
				if ( is_wp_error( $authenticator ) ) {
					return $authenticator;
				}
				$request['Authenticator'] = $authenticator;
			}

			$mask = array(
				'Address' => array(
					'FullName' => '*** ***',
					'Address1' => '***',
					'Address2' => '***',
					'City'     => '***',
					'State'    => '***',
					'ZIPCode'  => '***',
				),
				'Authenticator' => '***',
			);

			$obfuscated_request = array_merge( $request, array_intersect_key( $mask, $request ) );
			self::log( "Endpoint {$endpoint} Request: " . wc_print_r( $obfuscated_request, true ) );

			$client   = self::get_client();
			$response = $client->$endpoint( $request );

			$mask = array(
				'Address' => array(
					'FullName'     => '*** ***',
					'Address1'     => '***',
					'Address2'     => '***',
					'City'         => '***',
					'State'        => '***',
					'ZIPCode'      => '***',
					'ZIPCodeAddOn' => '***',
					'CleanseHash'  => '***',
					'OverrideHash' => '***',
				),
				'Authenticator' => '***',
			);

			$obfuscated_response = array_merge( (array) $response, array_intersect_key( $mask, (array) $response ) );
			self::log( "Endpoint {$endpoint} Response: " . print_r( $obfuscated_response, true ) );

			self::update_authenticator( $response );
			self::update_balance( $response );
			return $response;
		} catch ( SoapFault $e ) {
			self::log_soap_fault( $e, 'SoapFault during call to endpoint ' . $endpoint );

			// Try again if authenticator is bad.
			if ( ! $retry && isset( $e->detail->sdcerror ) && ( strstr( $e->detail->sdcerror, '002b0201' ) || strstr( $e->detail->sdcerror, '002b0202' ) || strstr( $e->detail->sdcerror, '002b0203' ) || strstr( $e->detail->sdcerror, '002b0204' ) ) ) {
				self::$authenticator      = false;
				$request['Authenticator'] = false;
				delete_transient( 'stamps_authenticator' );
				return self::do_request( $endpoint, $request, true );
			}

			return new WP_Error( $e->faultcode, $e->faultstring );
		}
	}

	/**
	 * Authenticate a user.
	 *
	 * @return string|WP_Error
	 */
	public static function authenticate() {
		$response = wp_remote_post( WC_STAMPS_INTEGRATION_AUTH_ENDPOINT, array(
			'method'      => 'POST',
			'timeout'     => 10,
			'httpversion' => '1.1',
			'user-agent'  => 'WooCommerce/' . WC_VERSION . '; ' . get_bloginfo( 'url' ),
			'body'        => array(
				'username' => get_option( 'wc_settings_stamps_username' ),
				'password' => get_option( 'wc_settings_stamps_password' ),
			),
		) );
		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) && ! strstr( $response['body'], 'error' ) ) {
			self::$authenticator = trim( $response['body'], '"' );
			set_transient( 'stamps_authenticator', self::$authenticator );
			return self::$authenticator;
		} elseif ( is_wp_error( $response ) ) {
			self::log( "Error getting stamps_authenticator: " . $response->get_error_message() );
			return new WP_Error( 'authentication_failed', $response->get_error_message() );
		} else {
			self::log( "Error getting stamps_authenticator: " . print_r( $response, true ) );
			$decoded_response = json_decode( $response['body'], true );
			if ( JSON_ERROR_NONE === json_last_error() && isset( $decoded_response['error'] ) ) {
				return new WP_Error( 'authentication_failed', $decoded_response['error'] );
			}
		}
		return new WP_Error( 'authentication_failed', 'Unable to authenticate with Stamps.com' );
	}

	/**
	 * Get authenticator for requests.
	 *
	 * @return string|WP_Error
	 */
	public static function get_authenticator() {
		if ( self::$authenticator ) {
			return self::$authenticator;
		} elseif ( ( $authenticator = get_transient( 'stamps_authenticator_v50' ) ) ) {
			return $authenticator;
		} else {
			$authenticator = self::authenticate();

			if ( is_wp_error( $authenticator ) || ! empty( $authenticator ) ) {
				return $authenticator;
			}
		}
		return new WP_Error( 'authentication_failed', 'Authentication Failed' );
	}

	/**
	 * Update authenticator after a request.
	 *
	 * @param object $response Response from SOAP request.
	 */
	public static function update_authenticator( $response ) {
		if ( isset( $response->Authenticator ) ) {
			self::$authenticator = $response->Authenticator;
			set_transient( 'stamps_authenticator', self::$authenticator );
		}
	}

	/**
	 * Update stamps balance.
	 *
	 * Called after every successful request (by self::do_request) to the stamps.com
	 * API.
	 *
	 * Note that this also kicks off the top-up code - that will result in
	 * postage being purchased for accounts that fall below the merchant's
	 * minimum (if any).
	 *
	 * @param object $response Response from SOAP request.
	 */
	public static function update_balance( $response ) {
		if ( isset( $response->PostageBalance ) ) {
			set_transient( 'wc_stamps_balance', $response->PostageBalance->AvailablePostage, DAY_IN_SECONDS );
			set_transient( 'wc_stamps_control_total', $response->PostageBalance->ControlTotal, DAY_IN_SECONDS );

			WC_Stamps_Balance::check_balance( $response->PostageBalance->AvailablePostage );
		}
	}

	/**
	 * Purchase postage on behalf of the user.
	 *
	 * @param int   $amount        Amount to top up. Must be an integer.
	 * @param float $control_total This is the amount of postage that the user
	 *                             has CONSUMED over the LIFETIME of the account.
	 *
	 * @return array|WP_Error
	 */
	public static function purchase_postage( $amount, $control_total ) {
		$request = array(
			'PurchaseAmount' => absint( $amount ),
			'ControlTotal'   => number_format( $control_total, 4, '.', '' ),
		);
		return self::do_request( 'PurchasePostage', $request );
	}

	/**
	 * Check purchase status.
	 *
	 * @param  string $transaction_id Transaction ID.
	 * @return array|WP_Error
	 */
	public static function get_purchase_status( $transaction_id ) {
		$request = array(
			'TransactionID' => $transaction_id,
		);
		return self::do_request( 'GetPurchaseStatus', $request );
	}

	/**
	 * Get account info.
	 *
	 * @return string|WP_Error
	 */
	public static function get_account_info() {
		return self::do_request( 'getAccountInfo' );
	}

	/**
	 * Checks to see if country is a US territory.
	 * This is needed because WC treats US territories
	 * such as Puerto Rico as a country rather than a
	 * US state.
	 *
	 * @since 1.3.15
	 * @param string $country The shipping country.
	 * @return bool
	 */
	public static function is_us_territory( $country ) {
		// List of US territories.
		$list = array(
			'PR',
		);

		return in_array( $country, $list, true ) ? true : false;
	}

	/**
	 * Verify an address.
	 *
	 * @param  WC_Order $order Order object.
	 * @return array
	 */
	public static function verify_address( $order ) {
		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

		$address = array(
			'FullName' => $pre_wc_30 ? $order->shipping_first_name . ' ' . $order->shipping_last_name : $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
			'Company'  => $pre_wc_30 ? $order->shipping_company : $order->get_shipping_company(),
			'Address1' => $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1(),
			'Address2' => $pre_wc_30 ? $order->shipping_address_2 : $order->get_shipping_address_2(),
			'City'     => $pre_wc_30 ? $order->shipping_city : $order->get_shipping_city(),
		);

		$shipping_country = $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country();
		$state            = $pre_wc_30 ? $order->shipping_state : $order->get_shipping_state();
		$postcode         = $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode();
		$is_us_territory  = self::is_us_territory( $shipping_country );

		if ( 'US' === $shipping_country || $is_us_territory ) {
			$postcode_pieces = explode( '-', $postcode );
			$zipcode         = $postcode_pieces[0];
			$zipcode_addon   = 1 < count( $postcode_pieces ) ? $postcode_pieces[1] : '';

			$address['State']   = $state;
			$address['ZIPCode'] = substr( $zipcode, 0, 5 );

			// Add in the ZIP+4 (ZIPCodeAddOn) if present in the address
			// Otherwise the "To Address Cleanse Hash" match will fail.
			if ( $zipcode_addon ) {
				$address['ZIPCodeAddOn'] = substr( $zipcode_addon, 0, 4 );
			}
		} else {
			$address['Province']   = $state;
			$address['PostalCode'] = $postcode;
			$address['Country']    = $shipping_country;
		}

		$request = array(
			'Address' => $address,
		);

		$result = self::do_request( 'CleanseAddress', $request );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $result->AddressMatch ) {
			// If we get a ZIP and a ZIP+4 returned by Stamps.com (which we usually will), pack
			// them into the postcode. We need both parts to use this cleansed address when
			// buying a label (otherwise the Cleanse Hash will not match).
			$zip_code       = isset( $result->Address->ZIPCode ) ? $result->Address->ZIPCode : '';
			$zip_code_addon = isset( $result->Address->ZIPCodeAddOn ) ? $result->Address->ZIPCodeAddOn : '';
			if ( ! empty( $zip_code_addon ) ) {
				$zip_code .= '-' . $zip_code_addon;
			}

			// Return address in our own format.
			$matched_result = array(
				'matched'      => true,
				'matched_zip'  => true,
				'hash'         => $result->Address->CleanseHash,
				'overide_hash' => $result->Address->OverrideHash,
				'address'      => array(
					'full_name' => $result->Address->FullName,
					'company'   => $result->Address->Company,
					'address_1' => $result->Address->Address1,
					'address_2' => $result->Address->Address2,
					'city'      => $result->Address->City,
					'state'     => isset( $result->Address->Province ) ? $result->Address->Province : $result->Address->State,
					'postcode'  => isset( $result->Address->PostalCode ) ? $result->Address->PostalCode : $zip_code,
					'country'   => isset( $result->Address->Country ) ? $result->Address->Country : '',
				),
			);
			return $matched_result;
		}

		if ( 'US' === ( $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country() ) ) {
			// User can proceed anyway.
			if ( $result->CityStateZipOK ) {
				return array(
					'matched'      => false,
					'matched_zip'  => true,
					'overide_hash' => $result->Address->OverrideHash,
				);
			}
		}

		return array(
			'matched'      => false,
			'matched_zip'  => false,
		);
	}

	/**
	 * Get rates for a package.
	 *
	 * @param  WC_Order $order Order object.
	 * @param  array    $args  Request args.
	 * @return array
	 */
	public static function get_rates( $order, $args ) {
		$pre_wc_30        = version_compare( WC_VERSION, '3.0', '<' );
		$shipping_country = $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country();
		$is_us_territory  = self::is_us_territory( $shipping_country );

		$request = array(
			'Rate' => array(
				'FromZIPCode'   => get_option( 'wc_settings_stamps_zip' ),
				'ToCountry'     => ( $is_us_territory ) ? 'US' : $shipping_country,
				'WeightLb'      => floor( $args['weight'] ),
				'WeightOz'      => number_format( ( $args['weight'] - floor( $args['weight'] ) ) * 16, 2 ),
				'ShipDate'      => $args['date'],
				'InsuredValue'  => $args['value'],
				'CODValue'      => $args['value'],
				'DeclaredValue' => $args['value'],
				'Length'        => $args['length'],
				'Width'         => $args['width'],
				'Height'        => $args['height'],
				'PackageType'   => $args['type'],
				'PrintLayout'   => 'Normal4X6',
			),
		);

		$postcode = $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode();

		if ( ! empty( $postcode ) ) {
			$request['Rate']['ToZIPCode'] = $postcode;
		}

		$result = self::do_request( 'GetRates', $request );

		if ( is_wp_error( $result ) ) {
			self::log( "Error getting rates for request: " . print_r( $request, true ) . '. Response: ' . print_r( $result, true ) );
			return $result;
		}

		// It is possible $results->Rates is empty or an empty stdClass Object, so let's test for both
		// A safe way to do so is to cast to array and then test for an empty array.
		$temp_array = (array) $result->Rates;
		if ( empty( $temp_array ) ) {
			return new WP_Error( 'no_rates', __( 'No rates were returned for the selected package type, weight and dimensions. Please select a different package type and try again.', 'woocommerce-shipping-stamps' ) );
		}

		if ( ! is_array( $result->Rates->Rate ) ) {
			$api_rates = array( $result->Rates->Rate );
		} else {
			$api_rates = $result->Rates->Rate;
		}

		foreach ( $api_rates as $rate ) {
			$rates[] = (object) array(
				'cost'          => $rate->Amount,
				'service'       => $rate->ServiceType,
				'package'       => $rate->PackageType,
				'name'          => self::get_rate_type_name( $rate->ServiceType ),
				'dim_weighting' => isset( $rate->DimWeighting ) ? $rate->DimWeighting : 0,
				'rate_object'   => $rate,
			);
		}

		return $rates;
	}

	/**
	 * Get (purchase) label for a rate.
	 *
	 * @version 1.3.2
	 *
	 * @todo The name for this should be `purchase_label` as `get_something`
	 *       should refers to a method to retrieve something that's already
	 *       stored / purchased.
	 *
	 * @param  WC_Order $order Order object.
	 * @param  array    $args Request args.
	 * @return array
	 */
	public static function get_label( $order, $args ) {
		$pre_wc_30        = version_compare( WC_VERSION, '3.0', '<' );
		$shipping_country = $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country();
		$is_us_territory  = self::is_us_territory( $shipping_country );

		$order_id = $pre_wc_30 ? $order->id : $order->get_id();
		$rate     = $args['rate'];
		$customs  = $args['customs'];
		$tx_id    = uniqid( 'wc_' . $order_id . '_' );

		if ( $pre_wc_30 ) {
			update_post_meta( $order_id, '_last_label_tx_id', $tx_id );
		} else {
			$order->update_meta_data( '_last_label_tx_id', $tx_id );
		}

		$request = array(
			'IntegratorTxID' => $tx_id,
			'Rate'           => $rate,
			'SampleOnly'     => get_option( 'wc_settings_stamps_sample_only', "yes" ) === "yes",
			'ImageType'      => get_option( 'wc_settings_stamps_image_type', "Pdf" ),
			'PaperSize'      => get_option( 'wc_settings_stamps_paper_size', 'Default' ),
			'From'           => array(
				'FullName'    => get_option( 'wc_settings_stamps_full_name' ),
				'Company'     => get_option( 'wc_settings_stamps_company' ),
				'Address1'    => get_option( 'wc_settings_stamps_address_1' ),
				'Address2'    => get_option( 'wc_settings_stamps_address_2' ),
				'City'        => get_option( 'wc_settings_stamps_city' ),
				'State'       => get_option( 'wc_settings_stamps_state' ),
				'ZIPCode'     => get_option( 'wc_settings_stamps_zip' ),
				'Country'     => 'US',
				'PhoneNumber' => get_option( 'wc_settings_stamps_phone' ),
			),
		);

		$request['To'] = array(
			'FullName'    => $pre_wc_30 ? $order->shipping_first_name . ' ' . $order->shipping_last_name : $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
			'Company'     => $pre_wc_30 ? $order->shipping_company : $order->get_shipping_company(),
			'Address1'    => $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1(),
			'Address2'    => $pre_wc_30 ? $order->shipping_address_2 : $order->get_shipping_address_2(),
			'City'        => $pre_wc_30 ? $order->shipping_city : $order->get_shipping_city(),
			'Country'     => ( $is_us_territory ) ? 'US' : $shipping_country,
		);

		// Figure out which tag to use for the address hash. We want to use
		// 'CleanseHash' if the merchant accepted stamps.com's changes to the To Address or
		// 'OverrideHash' if the merchant selected to "continue without changes" to the To Address
		// See also WC_Stamps_Order::ajax_override_address.
		$cleanse_hash = $pre_wc_30 ? get_post_meta( $order_id, '_stamps_hash', true ) : $order->get_meta( '_stamps_hash', true );
		$override_hash = $pre_wc_30 ? get_post_meta( $order_id, '_stamps_override_hash', true ) : $order->get_meta( '_stamps_override_hash', true );
		if ( $cleanse_hash === $override_hash ) {
			$request['To'] += array(
				'OverrideHash' => $override_hash,
			);
		} else {
			$request['To'] += array(
				'CleanseHash' => $cleanse_hash,
			);
		}

		$postcode = $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode();
		$state    = $pre_wc_30 ? $order->shipping_state : $order->get_shipping_state();

		if ( 'US' === $shipping_country || $is_us_territory ) {
			$postcode_pieces = explode( '-', $postcode );
			$zipcode         = $postcode_pieces[0];
			$zipcode_addon   = 1 < count( $postcode_pieces ) ? $postcode_pieces[1] : '';

			$request['To'] += array(
				'State'   => $state,
				'ZIPCode' => substr( $zipcode, 0, 5 ),
			);

			// Add in the ZIP+4 (ZIPCodeAddOn) if present in the address
			// Otherwise the "To Address Cleanse Hash" match will fail.
			if ( $zipcode_addon ) {
				$request['To']['ZIPCodeAddOn'] = substr( $zipcode_addon, 0, 4 );
			}
		} else {
			$request['To'] += array(
				'Province'    => $state,
				'PostalCode'  => $postcode,
				'PhoneNumber' => $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone(),
			);
		}

		if ( $customs ) {
			$request['Customs'] = $customs;
		}

		$result = self::do_request( 'CreateIndicium', $request );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		if ( empty( $result->URL ) ) {
			return new WP_Error( 'stamps-api', __( 'Cannot create a label for the package with the requested service.', 'woocommerce-shipping-stamps' ) );
		}

		$label_id = WC_Stamps_Labels::create_label( $order, $result );

		if ( is_wp_error( $label_id ) ) {
			return $label_id;
		}

		return new WC_Stamps_Label( $label_id );
	}

	/**
	 * Cancel a label.
	 *
	 * @param  WC_Order $order Order object.
	 * @param  string   $tx_id Transaction ID.
	 * @return bool|WP_Error true on success.
	 */
	public static function cancel_label( $order, $tx_id ) {
		$request = array(
			'StampsTxID' => $tx_id,
		);
		$result  = self::do_request( 'CancelIndicium', $request );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Get a URL to an account page.
	 *
	 * @param  string $endpoint Endpoint.
	 * @return string|bool
	 */
	public static function get_url( $endpoint ) {
		$request = array(
			'URLType'            => $endpoint,
			'ApplicationContext' => '',
		);
		$result  = self::do_request( 'GetURL', $request );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return esc_url_raw( $result->URL );
	}
}
