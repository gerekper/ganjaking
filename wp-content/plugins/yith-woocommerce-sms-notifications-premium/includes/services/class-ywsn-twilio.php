<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWSN_Twilio' ) ) {

	/**
	 * Implements Twilio API for YWSN plugin
	 *
	 * @class   YWSN_Twilio
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Twilio extends YWSN_SMS_Gateway {

		/** @var string twilio account sid */
		private $_twilio_sid;

		/** @var string twilio auth token */
		private $_twilio_auth_token;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_twilio_sid        = get_option( 'ywsn_twilio_sid' );
			$this->_twilio_auth_token = get_option( 'ywsn_twilio_auth_token' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @param   $to_phone     string
		 * @param   $message      string
		 * @param   $country_code string
		 *
		 * @return  void
		 * @throws  Exception for WP HTTP API error, no response, HTTP status code is not 201 or if HTTP status code not set
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$to_phone = ( '+' !== substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			if ( '' !== $this->_from_asid && '' !== $country_code && $this->country_support_asid( $country_code ) ) {
				$from = $this->_from_asid;
			} else {
				$from = ( '+' !== substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );
			}

			$wp_remote_http_args = array(
				'method'      => 'POST',
				'timeout'     => '10',
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => true,
				'blocking'    => true,
				'headers'     => array(
					'Authorization' => sprintf( 'Basic %s', base64_encode( $this->_twilio_sid . ':' . $this->_twilio_auth_token ) ),
				),
				'body'        => http_build_query(
					array(
						'From' => $from,
						'To'   => $to_phone,
						'Body' => $message,
					)
				),
				'cookies'     => array(),
			);

			$endpoint = str_replace( '{sid}', $this->_twilio_sid, 'https://api.twilio.com/2010-04-01/Accounts/{sid}/Messages.json' );

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['response'] ) || ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			if ( isset( $response['response']['code'] ) ) {
				if ( 201 !== (int) $response['response']['code'] && 200 !== (int) $response['response']['code'] ) {
					$response = json_decode( $response['body'], true );
					throw new Exception( ( isset( $response['message'] ) ) ? $response['message'] : esc_html__( 'An error has occurred while sending the sms', 'yith-woocommerce-sms-notifications' ) );
				}
			} else {
				throw new Exception( esc_html__( 'No answer code', 'yith-woocommerce-sms-notifications' ) );
			}

			return;

		}

		/**
		 * Check if customer country supports Alphanumeric Sender ID
		 *
		 * @param   $country_code string
		 *
		 * @return  boolean
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function country_support_asid( $country_code ) {

			$allowed_countries = array(
				'AC',
				'AD',
				'AG',
				'AI',
				'AL',
				'AM',
				'AO',
				'AQ',
				'AS',
				'AT',
				'AU',
				'AW',
				'AX',
				'BA',
				'BB',
				'BF',
				'BG',
				'BH',
				'BI',
				'BJ',
				'BL',
				'BM',
				'BN',
				'BO',
				'BQ',
				'BS',
				'BT',
				'BW',
				'BY',
				'BZ',
				'CC',
				'CF',
				'CH',
				'CI',
				'CK',
				'CM',
				'CV',
				'CW',
				'CX',
				'CY',
				'CZ',
				'DE',
				'DJ',
				'DK',
				'DM',
				'EE',
				'EH',
				'ER',
				'ES',
				'ET',
				'EU',
				'FI',
				'FJ',
				'FK',
				'FM',
				'FO',
				'FR',
				'GA',
				'GB',
				'GD',
				'GE',
				'GG',
				'GI',
				'GL',
				'GM',
				'GN',
				'GP',
				'GQ',
				'GR',
				'GW',
				'GY',
				'HK',
				'HT',
				'IE',
				'IL',
				'IM',
				'IO',
				'IS',
				'IT',
				'JE',
				'JM',
				'JO',
				'KH',
				'KI',
				'KM',
				'KN',
				'KP',
				'KY',
				'LB',
				'LC',
				'LI',
				'LR',
				'LS',
				'LT',
				'LU',
				'LV',
				'LY',
				'MD',
				'ME',
				'MF',
				'MG',
				'MH',
				'MK',
				'MN',
				'MO',
				'MP',
				'MQ',
				'MR',
				'MS',
				'MT',
				'MU',
				'MV',
				'MW',
				'NC',
				'NE',
				'NF',
				'NG',
				'NL',
				'NO',
				'NU',
				'OM',
				'PE',
				'PF',
				'PG',
				'PH',
				'PL',
				'PM',
				'PS',
				'PT',
				'PW',
				'PY',
				'QN',
				'QS',
				'QY',
				'RE',
				'RW',
				'SB',
				'SC',
				'SD',
				'SE',
				'SG',
				'SH',
				'SI',
				'SJ',
				'SK',
				'SL',
				'SM',
				'SN',
				'SO',
				'SR',
				'SS',
				'ST',
				'SX',
				'SZ',
				'TA',
				'TC',
				'TD',
				'TG',
				'TJ',
				'TK',
				'TL',
				'TM',
				'TO',
				'TT',
				'TV',
				'TZ',
				'UA',
				'UG',
				'UK',
				'UZ',
				'VA',
				'VC',
				'VG',
				'VI',
				'VU',
				'WF',
				'WS',
				'XC',
				'XD',
				'XG',
				'XL',
				'XN',
				'XP',
				'XR',
				'XS',
				'XT',
				'XV',
				'YE',
				'YT',
				'ZM',
				'ZW',
			);

			return in_array( $country_code, $allowed_countries, true );

		}

	}

}
