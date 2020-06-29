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

if ( ! class_exists( 'YWSN_Messages' ) ) {

	/**
	 * Implements SMS functions for YWSN plugin
	 *
	 * @class   YWSN_Messages
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Messages {

		/**
		 * @var array options that can be used to trigger some specific actions after sending
		 */
		private $_args;

		/**
		 * @var integer SMS length
		 */
		public $_sms_length = 160;

		/**
		 * @var YWSN_SMS_Gateway SMS gateway
		 */
		private $_sms_gateway;

		/**
		 * @var array order note
		 */
		public $_order_note = array();

		/**
		 * Constructor
		 *
		 * @param   $args array
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct( $args ) {

			$this->_args        = $args;
			$this->_sms_gateway = get_option( 'ywsn_sms_gateway' );
			$this->include_gateway_class();

		}

		/**
		 * Send SMS message to one or multiple numbers
		 *
		 * @param   $phone         string
		 * @param   $message       string
		 * @param   $sms_type      string
		 * @param   $country_code  string
		 *
		 * @return  boolean
		 * @throws  Exception
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function pre_send_sms( $phone, $message, $sms_type, $country_code = '' ) {

			$country_code = strtoupper( $country_code );
			$message      = $this->prepare_sms_content( $message );

			return $this->send( $phone, $message, $sms_type, $country_code );

		}

		/**
		 * Include active SMS gateway class
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function include_gateway_class() {

			$classname = str_replace( '_', '-', strtolower( $this->_sms_gateway ) );

			if ( 'YWSN_Void_Sender' !== $classname ) {
				include_once( YWSN_DIR . "includes/services/class-{$classname}.php" );
			} else {
				include_once( YWSN_DIR . 'includes/services/class-ywsn-void-sender.php' );
			}

		}

		/**
		 * Prepare SMS message
		 *
		 * @param   $message string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function prepare_sms_content( $message ) {

			if ( 'none' !== get_option( 'ywsn_url_shortening' ) ) {

				$message = ywsn_url_shortening( $message );

			}

			/**
			 * SUPPORTED ADDITIONAL CHARSETS
			 *
			 * cjk      => CJK Radicals Supplement, CJK Symbols and Punctuation, CJK Strokes, Enclosed CJK Letters and Months, CJK Compatibility, CJK Unified Ideographs Extension A, CJK Unified Ideographs, CJK Compatibility Ideographs and CJK Compatibility Forms sets
			 * greek    => Greek and Coptic set
			 * cyrillic => Cyrillic set
			 * armenian => Armenian set
			 * hebrew   => Hebrew set
			 * arabic   => Arabic, Arabic Supplement, Arabic Presentation Forms-A and Arabic Presentation Forms-A sets
			 * hangul   => Hangul Jamo, Hangul Compatibility Jamo and Hangul Syllables sets
			 * thai     => Thai set
			 * latinext => Latin-1 Supplement, Latin Extended-A and Latin Extended-B sets
			 */
			$additional_charsets = $this->get_special_charsets( apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) ) );

			//Remove non GSM characters
			$gsm_chars = '~[^A-Za-z0-9 \r\n@£$¥èéùìòÇØøÅå' . $additional_charsets . '\x{0394}_\x{03A6}\x{0393}\x{039B}\x{03A9}\x{03A0}\x{03A8}\x{03A3}\x{0398}\x{039E}ÆæßÉ!\"#$%&\'\(\)*+,\-.\/:;<=>;?¡ÄÖÑÜ§¿äöñüà’^{}\[\~\]\|\x{20AC}]~u';
			$message   = preg_replace( $gsm_chars, '', $message );

			return $message;

		}

		/**
		 * Manage additional charsets
		 *
		 * @param   $args array
		 *
		 * @return  string
		 * @since   1.0.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_special_charsets( $args ) {

			$charsets = array(
				'cjk'      => '\x{2E80}-\x{2EFF}\x{3000}-\x{303F}\x{31C0}-\x{31EF}\x{3200}-\x{32FF}\x{3300}-\x{33FF}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{F900}-\x{FAFF}\x{FE30}-\x{FE4F}',
				'greek'    => '\x{0370}-\x{03FF}',
				'cyrillic' => '\x{0400}-\x{04FF}',
				'armenian' => '\x{0530}-\x{058F}',
				'hebrew'   => '\x{0590}-\x{05FF}',
				'arabic'   => '\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}',
				'hangul'   => '\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}',
				'thai'     => '\x{0E00}-\x{0E7F}',
				'latinext' => '\x{0080}-\x{00FF}\x{0100}-\x{017F}\x{0180}-\x{024F}',
			);

			$charset_regex = '';

			if ( ! empty( $args ) ) {

				$this->_sms_length = 70;

				foreach ( $args as $charset ) {

					$charset_regex .= $charsets[ $charset ];

				}
			}

			return $charset_regex;

		}

		/**
		 * Send SMS
		 *
		 * @param   $phone        string
		 * @param   $message      string
		 * @param   $sms_type     string
		 * @param   $country_code string
		 *
		 * @return  boolean
		 * @throws  Exception
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function send( $phone, $message, $sms_type, $country_code ) {

			$sms_gateway = new $this->_sms_gateway();

			if ( get_option( 'ywsn_enable_sms_length', 'no' ) === 'yes' ) {
				$this->_sms_length = get_option( 'ywsn_sms_length', '160' );
			}

			$sms_limit = apply_filters( 'ywsn_sms_limit', $this->_sms_length );

			try {

				$calling_code   = $this->get_calling_code( $country_code );
				$phone          = $this->format_phone_number( $phone, $calling_code, $country_code );
				$message        = mb_substr( $message, 0, $sms_limit );
				$status_message = esc_html__( 'Sent', 'yith-woocommerce-sms-notifications' );
				$sms_gateway->send( $phone, $message, $country_code );
				$success = true;

			} catch ( Exception $e ) {

				$status_message = $e->getMessage();
				$success        = false;

			}

			$log_args = array(
				'type'           => $sms_type,
				'object'         => isset( $this->_args['object'] ) ? $this->_args['object'] : '',
				'success'        => $success,
				'status_message' => $status_message,
				'phone'          => $phone,
				'message'        => $message,
			);

			$sms_gateway->write_log( $log_args );

			if ( apply_filters( 'ywsn_save_send_log', false ) ) {
				$sms_gateway->print_log();
			}

			if ( isset( $this->_args['object'] ) && ( 'customer' === $sms_type || ( get_option( 'ywsn_save_vendor_note' ) === 'yes' && 'admin' === $sms_type ) ) ) {

				$timestamp = time();
				$datetime  = new DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
				$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
				$send_date = date_i18n( wc_date_format() . ' ' . wc_time_format(), $timestamp + $datetime->getOffset() );

				ob_start();
				?>
				<?php
				/* translators: %s SMS sending date */
				printf( esc_html__( 'SMS text message sent on %s', 'yith-woocommerce-sms-notifications' ), esc_html( $send_date ) );
				?>
				<br />
				<?php esc_html_e( 'Status', 'yith-woocommerce-sms-notifications' ); ?>: <?php echo esc_html( $status_message ); ?>
				<br />
				<?php esc_html_e( 'Content', 'yith-woocommerce-sms-notifications' ); ?>: <?php echo esc_html( $message ); ?>
				<?php

				$order_note = ob_get_clean();

				if ( $this->_args['object'] instanceof WC_Order ) {

					$order          = $this->_args['object'];
					$vendor_message = wp_get_post_parent_id( $order->get_id() ) !== 0;

					if ( 'admin' === $sms_type && $vendor_message ) {
						$parent_id = wp_get_post_parent_id( $order->get_id() );
						$order     = wc_get_order( $parent_id );
					}
					$this->_order_note['text'] = $order_note;
					$this->_order_note['id']   = $order->add_order_note( $order_note );

				} elseif ( $this->_args['object'] instanceof YITH_WCBK_Booking ) {

					$booking                   = $this->_args['object'];
					$this->_order_note['text'] = $order_note;
					$this->_order_note['id']   = $booking->add_note( 'admin', $order_note );

				}
			}

			return $success;

		}

		/**
		 * Format a number to E.164 format
		 *
		 * @param   $phone        string
		 * @param   $calling_code string
		 * @param   $country_code string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function format_phone_number( $phone, $calling_code, $country_code ) {

			if ( '' === $calling_code ) {
				return apply_filters( 'ywsn_format_phone_number', $phone, $calling_code );
			}

			// Check if number do not starts with '+'
			if ( '+' !== substr( $phone, 0, 1 ) ) {

				// remove leading zero
				$phone = preg_replace( '/^0/', '', $phone );
				$phone = $this->country_special_cases( $phone, $calling_code, $country_code );

				// Check if number has country code
				if ( substr( $phone, 0, strlen( $calling_code ) ) !== $calling_code ) {
					$phone = $calling_code . $phone;
				}
			}

			// remove any non-number characters
			$phone = preg_replace( '[\D]', '', $phone );

			// Check if the number starts with the expected country code, remove any zero which immediately follows the country code.
			if ( substr( $phone, 0, strlen( $calling_code ) ) === $calling_code ) {
				$phone = preg_replace( "/^{$calling_code}(\s*)?0/", $calling_code, $phone );
			}

			return apply_filters( 'ywsn_format_phone_number', $phone, $calling_code );

		}

		/**
		 * Check if some country has special cases
		 *
		 * @param   $phone        string
		 * @param   $calling_code string
		 * @param   $country_code string
		 *
		 * @return  string
		 * @since   1.0.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function country_special_cases( $phone, $calling_code, $country_code ) {

			switch ( $country_code ) {

				case 'IT':
					/**
					 * in Italy, the telephone prefixes released by "H3G" operator have the first two digits equal to the Italian international prefix.
					 * If the customer has entered the number without the country code, the sending of SMS can fail because of this similarity
					 */
					if ( strlen( $phone ) <= apply_filters( 'ywsn_italian_numbers_length', 10 ) ) {

						$mobile_prefixes = apply_filters( 'ywsn_italian_prefixes', array( '390', '391', '392', '393', '397' ) );

						if ( in_array( substr( $phone, 0, 3 ), $mobile_prefixes, true ) ) {
							$phone = $calling_code . $phone;
						}
					}
					break;

				case 'NO':
					/**
					 * in Norway, the newer telephone prefixes have the first two digits equal to the Norwegian international prefix.
					 * If the customer has entered the number without the country code, the sending of SMS can fail because of this similarity
					 */
					if ( strlen( $phone ) <= apply_filters( 'ywsn_norwegian_numbers_length', 8 ) ) {

						$mobile_prefixes = apply_filters( 'ywsn_norwegian_prefixes', array( '47' ) );

						if ( in_array( substr( $phone, 0, 2 ), $mobile_prefixes, true ) ) {
							$phone = $calling_code . $phone;
						}
					}
					break;

			}

			return $phone;

		}

		/**
		 * Get the calling code of a given country
		 *
		 * @param   $country_code string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_calling_code( $country_code ) {

			$calling_codes = array(
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
				'JM' => '1876',
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

			return ( isset( $calling_codes[ $country_code ] ) ) ? $calling_codes[ $country_code ] : '';

		}

		/**
		 * Format a number to E.164 format
		 *
		 * @param   $phone        string
		 * @param   $country_code string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_formatted_number( $phone, $country_code ) {

			$country_code = strtoupper( $country_code );
			$calling_code = $this->get_calling_code( $country_code );

			return $this->format_phone_number( $phone, $calling_code, $country_code );

		}

	}

}
