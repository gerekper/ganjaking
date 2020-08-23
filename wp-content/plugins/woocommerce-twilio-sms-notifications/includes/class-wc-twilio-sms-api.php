<?php
/**
 * WooCommerce Twilio SMS Notifications
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Twilio_SMS\Message_Length_Calculator;

/**
 * Twilio SMS API class.
 *
 * Wrapper for the Twilio SMS API.
 *
 * @link https://www.twilio.com/docs/api/rest/sending-sms
 *
 * @since 1.0
 */
class WC_Twilio_SMS_API {


	/** @var string API host */
	private $host = 'https://api.twilio.com/2010-04-01/Accounts/';

	/** @var string API endpoint */
	private $endpoint;

	/** @var string phone number to send SMS messages from */
	private $from_number;

	/** @var string $asid The Alphanumeric Sender ID */
	private $asid;

	/** @var string SMS uri */
	private $sms_uri = '/Messages.json';

	/** @var string usage records for today uri */
	private $todays_usage_uri = '/Usage/Records/Today.json';

	/** @var string twilio account sid */
	private $account_sid;

	/** @var string twilio auth token */
	private $auth_token;

	/** @var string|array response string or array  */
	private $response;

	/** @var array args to use with wp_safe_remote_*() functions */
	private $wp_remote_http_args = [
		'method'      => '',
		'timeout'     => '10',
		'redirection' => 0,
		'httpversion' => '1.0',
		'sslverify'   => true,
		'blocking'    => true,
		'headers'     => [
			'Authorization' => '',
		],
		'body'        => '',
		'cookies'     => [],
	];


	/**
	 * API constructor.
	 *
	 * @since 1.0
	 *
	 * @param string $account_sid required
	 * @param string $auth_token required
	 * @param string $from_number required - number to send SMS messages from
	 * @param array $options optional API options
	 */
	public function __construct( $account_sid, $auth_token, $from_number, $options = array() ) {

		$this->account_sid = $account_sid;
		$this->auth_token  = $auth_token;
		// don't format to e.164, as the country code is not available
		$this->from_number = preg_replace( '[\D]', '', $from_number );

		// Format the Alphanumeric Sender ID if set
		if ( isset( $options['asid'] ) ) {
			$asid = preg_replace( '/[^a-zA-Z0-9 ]/', '', $options['asid'] );
			$asid = Framework\SV_WC_Helper::str_truncate( $asid, 11, '' );
			$this->asid = $asid;
		}

		// Set Basic HTTP Auth
		$this->wp_remote_http_args['headers']['Authorization'] = sprintf( 'Basic %s', base64_encode( $this->account_sid . ':' . $this->auth_token ) );
	}


	/**
	 * Sends an SMS message.
	 *
	 * POST /Messages.json
	 *
	 * @since 1.0
	 *
	 * @param string $to required destination phone number
	 * @param string $body required body of message
	 * @param string $country_code (optional) country code in ISO_3166-1_alpha-2 format - will only be used if country code cannot be determined from the destination phone number itself
	 * @throws Exception if required information is blank
	 * @return array parsed response
	 */
	public function send( $to, $body, $country_code = null ) {

		if ( ! $to || ! $body ) {
			throw new \Exception( __( 'Send SMS: To number / Body is blank!', 'woocommerce-twilio-sms-notifications' ) );
		}

		// format number to E.164 format
		$to = $this->format_e164( $to, $country_code );

		// try to determine the phone number's country code from the formatted number - this helps prevent
		// issues with Alphanumeric Sender ID not being used when the order billing country does not
		// match the phone number's country, or when the country code is not provided at all
		// (as is the case when sending a test SMS from the plugin settings screen)
		if ( Framework\SV_WC_Helper::str_starts_with( $to, '+' ) ) {

			/** @type string $code */
			foreach( $this->get_country_codes() as $code => $prefix ) {

				if ( Framework\SV_WC_Helper::str_starts_with( $to, '+' . $prefix ) ) {
					$country_code = $code;
					break;
				}
			}
		}

		// use the Alphanumeric Sender ID if supported by the receiving country
		if ( ! empty( $this->asid ) && in_array( $country_code, $this->get_asid_country_codes(), false ) ) {
			$from = $this->asid;
		} else {
			$from = $this->from_number;
		}

		// set request body
		$this->wp_remote_http_args['body'] = http_build_query( array(
			'From' => $from,
			'To'   => $to,
			'Body' => $this->truncate_message_body( trim( $body ) ),
		), '', '&' );

		// set SMS endpoint
		$this->set_endpoint( $this->sms_uri );

		// send the POST request
		$this->http_request( 'POST' );

		// parse the response JSON
		$this->parse_response();

		// return parsed response
		return $this->response;
	}


	/**
	 * Maybe truncates the message body according to settings and GSM specifications.
	 *
	 * @link https://www.twilio.com/docs/glossary/what-sms-character-limit
	 *
	 * @since 1.12.2
	 *
	 * @param string $body message content
	 * @return string (maybe) truncated message content
	 */
	private function truncate_message_body( $body ) {

		$concatenate = 'yes' === get_option( 'wc_twilio_sms_allow_concatenate_messages', 'no' );
		$segments    = Message_Length_Calculator::get_message_segments_count( $body );

		if ( ! $concatenate || $segments >= 10 ) {
			$body = Framework\SV_WC_Helper::str_truncate( $body, Message_Length_Calculator::get_characters_count_limit( $body, $concatenate ) );
		}

		return $body;
	}


	/**
	 * Get SMS usage records for today
	 *
	 * GET /Usage/Records/Today.json
	 *
	 * @since  1.1
	 * @return array parsed response
	 */
	public function get_sms_usage() {

		// set usage endpoint
		$this->set_endpoint( add_query_arg( array( 'Category' => 'sms' ), $this->todays_usage_uri ) );

		// send the request
		$this->http_request( 'GET' );

		// parse response JSON
		$this->parse_response();

		// return response
		return $this->response;
	}


	/**
	 * Formats a given number to e164 format.
	 *
	 * e164 format = +<1-3 digit country code><12-14 digit country number>
	 *
	 * @link http://en.wikipedia.org/wiki/E.164
	 *
	 * @since 1.0
	 *
	 * @param string $number number to format
	 * @param string $country_code (optional) country code in ISO_3166-1_alpha-2 format (WC uses this as standard) - only used if country code cannot be determined from the number itself
	 * @return string number in e164 format
	 */
	private function format_e164( $number, $country_code = null ) {

		// if the number starts with a +, assume the customer has entered the country code already, so just strip non-digit characters and return
		if ( ! strncmp( $number, '+', 1 ) ) {
			return '+' . preg_replace( '[\D]', '', $number );
		}

		// remove any non-number characters
		$number = preg_replace( '[\D]', '', $number );

		$country_calling_code = null;

		// TODO: consider supporting other international call prefixes as well, see https://en.wikipedia.org/wiki/List_of_international_call_prefixes {IT 2017-12-27}

		// number has international call prefix (00)
		if ( 0 === strpos( $number, '00' ) ) {

			// remove international dialing code
			$number = substr( $number, 2 );

			// determine if the number has a country calling code entered
			foreach ( $this->get_country_codes() as $code => $prefix ) {

				if ( 0 === strpos( $number, $prefix ) ) {

					$country_calling_code = $prefix;
					break;
				}
			}
		}

		// get the phone country code for given country, if not determined from phone number
		if ( ! $country_calling_code && $country_code ) {

			// add the country code
			$country_calling_code = $this->get_country_calling_code( $country_code );
			$number               = $country_calling_code . $number;
		}

		// if no country calling code can be determined, just return the number as-is
		if ( ! $country_calling_code ) {
			return $number;
		}

		// remove any leading zeroes after the country code
		// but only once, some numbers (like Taiwan) can have mobile numbers that
		// start with the same digits as the country code (e.g. +886 09-88606403) ಠ_ಠ
		if ( '0' === substr( $number, strlen( $country_calling_code ), 1 ) ) {
			$number = preg_replace( "/{$country_calling_code}0/", $country_calling_code, $number, 1 );
		}

		// prepend +
		$number = '+' . $number;

		return $number;
	}


	/**
	 * Get the calling code of a given country
	 *
	 * @link http://en.wikipedia.org/wiki/List_of_country_calling_codes
	 * @since 1.0
	 * @param string $country ISO_3166-1_alpha-2 country code
	 * @return string country calling code
	 */
	private function get_country_calling_code( $country ) {

		$country = strtoupper( $country );

		$country_codes = $this->get_country_codes();

		// return valid country code if country exists or blank country code if not found
		return ( isset( $country_codes[ $country ] ) ) ? $country_codes[ $country ] : '';
	}


	/**
	 * Get the country codes that allow Alphanumeric Sender IDs.
	 *
	 * @since 1.6.0
	 * @return array $country_codes The supported country codes.
	 */
	private function get_asid_country_codes() {

		$country_codes = array_keys( $this->get_country_codes() );

		// The countries that don't allow an ASID
		$non_asid_country_codes = array(
			'AF',
			'AR',
			'AZ',
			'BD',
			'BE',
			'BR',
			'CA',
			'CD',
			'CG',
			'CL',
			'CN',
			'CO',
			'CR',
			'DO',
			'DZ',
			'EC',
			'GF',
			'GH',
			'GT',
			'GU',
			'HN',
			'HR',
			'HU',
			'IQ',
			'IR',
			'KE',
			'KG',
			'KR',
			'KW',
			'KY',
			'KZ',
			'LA',
			'LK',
			'MA',
			'MC',
			'ML',
			'MM',
			'MX',
			'MY',
			'MZ',
			'NA',
			'NI',
			'NP',
			'NR',
			'NZ',
			'PA',
			'PK',
			'PR',
			'QA',
			'RO',
			'SV',
			'SY',
			'TR',
			'US',
			'UY',
			'VE',
			'VN',
			'ZA',
		);

		$country_codes = array_diff( $country_codes, $non_asid_country_codes );

		return $country_codes;
	}


	/**
	 * Gets available country codes and their corresponding calling codes.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] $country_codes
	 */
	private function get_country_codes() {

		$country_codes = array(
			'AC' => '247',
			'AD' => '376',
			'AE' => '971',
			'AF' => '93',
			'AG' => '1268',
			'AI' => '1264',
			'AL' => '355',
			'AM' => '374',
			'AO' => '244',
			'AQ' => '672',
			'AR' => '54',
			'AS' => '1684',
			'AT' => '43',
			'AU' => '61',
			'AW' => '297',
			'AX' => '358',
			'AZ' => '994',
			'BA' => '387',
			'BB' => '1246',
			'BD' => '880',
			'BE' => '32',
			'BF' => '226',
			'BG' => '359',
			'BH' => '973',
			'BI' => '257',
			'BJ' => '229',
			'BL' => '590',
			'BM' => '1441',
			'BN' => '673',
			'BO' => '591',
			'BQ' => '599',
			'BR' => '55',
			'BS' => '1242',
			'BT' => '975',
			'BW' => '267',
			'BY' => '375',
			'BZ' => '501',
			'CA' => '1',
			'CC' => '61',
			'CD' => '243',
			'CF' => '236',
			'CG' => '242',
			'CH' => '41',
			'CI' => '225',
			'CK' => '682',
			'CL' => '56',
			'CM' => '237',
			'CN' => '86',
			'CO' => '57',
			'CR' => '506',
			'CU' => '53',
			'CV' => '238',
			'CW' => '599',
			'CX' => '61',
			'CY' => '357',
			'CZ' => '420',
			'DE' => '49',
			'DJ' => '253',
			'DK' => '45',
			'DM' => '1767',
			'DO' => '1809',
			'DZ' => '213',
			'EC' => '593',
			'EE' => '372',
			'EG' => '20',
			'EH' => '212',
			'ER' => '291',
			'ES' => '34',
			'ET' => '251',
			'EU' => '388',
			'FI' => '358',
			'FJ' => '679',
			'FK' => '500',
			'FM' => '691',
			'FO' => '298',
			'FR' => '33',
			'GA' => '241',
			'GB' => '44',
			'GD' => '1473',
			'GE' => '995',
			'GF' => '594',
			'GG' => '44',
			'GH' => '233',
			'GI' => '350',
			'GL' => '299',
			'GM' => '220',
			'GN' => '224',
			'GP' => '590',
			'GQ' => '240',
			'GR' => '30',
			'GT' => '502',
			'GU' => '1671',
			'GW' => '245',
			'GY' => '592',
			'HK' => '852',
			'HN' => '504',
			'HR' => '385',
			'HT' => '509',
			'HU' => '36',
			'ID' => '62',
			'IE' => '353',
			'IL' => '972',
			'IM' => '44',
			'IN' => '91',
			'IO' => '246',
			'IQ' => '964',
			'IR' => '98',
			'IS' => '354',
			'IT' => '39',
			'JE' => '44',
			'JM' => '1',
			'JO' => '962',
			'JP' => '81',
			'KE' => '254',
			'KG' => '996',
			'KH' => '855',
			'KI' => '686',
			'KM' => '269',
			'KN' => '1869',
			'KP' => '850',
			'KR' => '82',
			'KW' => '965',
			'KY' => '1345',
			'KZ' => '7',
			'LA' => '856',
			'LB' => '961',
			'LC' => '1758',
			'LI' => '423',
			'LK' => '94',
			'LR' => '231',
			'LS' => '266',
			'LT' => '370',
			'LU' => '352',
			'LV' => '371',
			'LY' => '218',
			'MA' => '212',
			'MC' => '377',
			'MD' => '373',
			'ME' => '382',
			'MF' => '590',
			'MG' => '261',
			'MH' => '692',
			'MK' => '389',
			'ML' => '223',
			'MM' => '95',
			'MN' => '976',
			'MO' => '853',
			'MP' => '1670',
			'MQ' => '596',
			'MR' => '222',
			'MS' => '1664',
			'MT' => '356',
			'MU' => '230',
			'MV' => '960',
			'MW' => '265',
			'MX' => '52',
			'MY' => '60',
			'MZ' => '258',
			'NA' => '264',
			'NC' => '687',
			'NE' => '227',
			'NF' => '672',
			'NG' => '234',
			'NI' => '505',
			'NL' => '31',
			'NO' => '47',
			'NP' => '977',
			'NR' => '674',
			'NU' => '683',
			'NZ' => '64',
			'OM' => '968',
			'PA' => '507',
			'PE' => '51',
			'PF' => '689',
			'PG' => '675',
			'PH' => '63',
			'PK' => '92',
			'PL' => '48',
			'PM' => '508',
			'PR' => '1787',
			'PS' => '970',
			'PT' => '351',
			'PW' => '680',
			'PY' => '595',
			'QA' => '974',
			'QN' => '374',
			'QS' => '252',
			'QY' => '90',
			'RE' => '262',
			'RO' => '40',
			'RS' => '381',
			'RU' => '7',
			'RW' => '250',
			'SA' => '966',
			'SB' => '677',
			'SC' => '248',
			'SD' => '249',
			'SE' => '46',
			'SG' => '65',
			'SH' => '290',
			'SI' => '386',
			'SJ' => '47',
			'SK' => '421',
			'SL' => '232',
			'SM' => '378',
			'SN' => '221',
			'SO' => '252',
			'SR' => '597',
			'SS' => '211',
			'ST' => '239',
			'SV' => '503',
			'SX' => '1721',
			'SY' => '963',
			'SZ' => '268',
			'TA' => '290',
			'TC' => '1649',
			'TD' => '235',
			'TG' => '228',
			'TH' => '66',
			'TJ' => '992',
			'TK' => '690',
			'TL' => '670',
			'TM' => '993',
			'TN' => '216',
			'TO' => '676',
			'TR' => '90',
			'TT' => '1868',
			'TV' => '688',
			'TW' => '886',
			'TZ' => '255',
			'UA' => '380',
			'UG' => '256',
			'UK' => '44',
			'US' => '1',
			'UY' => '598',
			'UZ' => '998',
			'VA' => '39',
			'VC' => '1784',
			'VE' => '58',
			'VG' => '1284',
			'VI' => '1340',
			'VN' => '84',
			'VU' => '678',
			'WF' => '681',
			'WS' => '685',
			'XC' => '991',
			'XD' => '888',
			'XG' => '881',
			'XL' => '883',
			'XN' => '857',
			'XP' => '878',
			'XR' => '979',
			'XS' => '808',
			'XT' => '800',
			'XV' => '882',
			'YE' => '967',
			'YT' => '262',
			'ZA' => '27',
			'ZM' => '260',
			'ZW' => '263',
		);

		return $country_codes;
	}


	/**
	 * Performs HTTP Request
	 *
	 * @since  1.0
	 * @see wp_safe_remote_request()
	 * @param string $method HTTP method to use for request
	 * @throws Exception for Blank/invalid endpoint or HTTP method, WP HTTP API error
	 */
	private function http_request( $method ) {

		// Check for blank endpoint or method
		if ( ! $this->endpoint || ! $method ) {
			throw new \Exception( __( 'Endpoint and / or HTTP Method is blank.', 'woocommerce-twilio-sms-notifications' ) );
		}

		// Check that method is a valid http method
		if ( ! in_array( $method, array( 'GET', 'POST', 'PUT', 'DELETE' ) ) ) {
			throw new \Exception( __( 'Requested HTTP Method is invalid.', 'woocommerce-twilio-sms-notifications' ) );
		}

		// set the method
		$this->wp_remote_http_args['method'] = $method;

		// perform HTTP request with endpoint / args
		$this->response = wp_safe_remote_request( esc_url_raw( $this->endpoint ), $this->wp_remote_http_args );

		// WP HTTP API error like network timeout, etc
		if ( is_wp_error( $this->response ) ) {
			throw new \Exception( $this->response->get_error_message() );
		}

		// Check for proper response / body
		if ( ! isset( $this->response['response'] ) ) {
			throw new \Exception( __( 'Empty Response', 'woocommerce-twilio-sms-notifications' ) );
		}

		if ( ! isset( $this->response['body'] ) ) {
			throw new \Exception( __( 'Empty Body', 'woocommerce-twilio-sms-notifications' ) );
		}
	}


	/**
	 * Checks response HTTP status code according to Twilio API Documentation
	 * JSON decodes body as an associative array for return by calling function
	 *
	 * @link https://www.twilio.com/docs/api/rest/tips
	 * @since 1.0
	 * @throws Exception If HTTP status code other than 200/201 or if HTTP status code not set
	 */
	private function parse_response() {

		if ( isset( $this->response['response']['code'] ) ) {

			// 200 is returned for successful GET requests and 201 is returned for successful POST requests
			if ( $this->response['response']['code'] != 200 && $this->response['response']['code'] != 201 ) {

				$this->response = json_decode( $this->response['body'], true );

				throw new \Exception(
					sprintf(
						__( '%1$s See: %2$s', 'woocommerce-twilio-sms-notifications' ),
						( isset( $this->response['message'] ) ) ? $this->response['message'] : __( 'No Message Available', 'woocommerce-twilio-sms-notifications' ),
						( isset( $this->response['more_info'] ) ) ? $this->response['more_info'] : __( 'N/A', 'woocommerce-twilio-sms-notifications' )
					)
				);
			}

			// JSON decode the body into an associative array
			$this->response = json_decode( $this->response['body'], true );

		} else {

			throw new \Exception( __( 'Response HTTP Code Not Set', 'woocommerce-twilio-sms-notifications' ) );
		}
	}


	/**
	 * Set Endpoint URI
	 *
	 * @since 1.0
	 * @param string $uri specific API URI
	 */
	private function set_endpoint( $uri ) {

		$this->endpoint = $this->host . $this->account_sid . $uri;
	}


}
