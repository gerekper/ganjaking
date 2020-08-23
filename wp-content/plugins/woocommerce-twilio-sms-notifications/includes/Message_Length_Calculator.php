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

namespace SkyVerge\WooCommerce\Twilio_SMS;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined ( 'ABSPATH' ) or exit;

/**
 * SMS length calculator.
 *
 * Helps detecting if the message can be sent in standard GSM-7 charset or UCS-2 must be used.
 *
 * @link http://messente.com/documentation/sms-length-calculator adaptation
 * @link https://www.twilio.com/docs/glossary/what-sms-character-limit for more information on SMS standards in relation to message length limits
 *
 * @since 1.12.2
 */
class Message_Length_Calculator {


	/** @const int standard GSM-7 */
	const GSM_CHARSET_GSM7 = 0;

	/** @const int Unicode UCS-2 */
	const GSM_CHARSET_UCS2 = 2;

	/** @const string escape entity for GSM-7 extended characters */
	const GSM_7BIT_ESC = "\x1b";

	/** @var string default encoding for input strings */
	private static $UTF8 = 'UTF-8';


	/**
	 * Gets all characters supported by GSM-7.
	 *
	 * @since 1.12.2
	 *
	 * @return string[] list of characters
	 */
	private static function get_gsm_7bit_chars() {

		return [
			'@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'Ç', "\n", 'Ø', 'ø', "\r", 'Å', 'å',
			'Δ', '_', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ', 'Ξ', "\x1b", 'Æ', 'æ', 'ß', 'É',
			' ', '!', '"', '#', '¤', '%', '&', "'", '(', ')', '*', '+', ',', '-', '.', '/',
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?',
			'¡', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
			'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä', 'Ö', 'Ñ', 'Ü', '§',
			'¿', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
			'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ', 'ü', 'à'
		];
	}


	/**
	 * Gets characters supported by the GSM-7 standard when using escape character 0x1b.
	 *
	 * @since 1.12.2
	 *
	 * @return string[] list of characters
	 */
	private static function get_gsm_7bit_chars_ext() {

		return [
			"\f", '^', '{', '}', '\\', '[', '~', ']', '|', '€'
		];
	}


	/**
	 * Gets all characters supported by the GSM-7 standard.
	 *
	 * @since 1.12.2
	 *
	 * @return string[] list of characters
	 */
	private static function get_gsm_7bit_chars_all() {

		return array_merge( self::get_gsm_7bit_chars(), self::get_gsm_7bit_chars_ext() );
	}


	/**
	 * Detects a charset type from a piece of content.
	 *
	 * Note: this will use WordPress mb_* polyfills if the mbstring extension is unavailable.
	 *
	 * @since 1.12.2
	 *
	 * @param string $content piece of content
	 * @return int charset type
	 */
	public static function detect_charset( $content ) {

		$supported_characters = [];

		foreach ( self::get_gsm_7bit_chars_all() as $char ) {
			$supported_characters[ $char ] = '';
		}

		// intersects the content with supported GSM-7 characters
		$content = strtr( $content, $supported_characters );

		// if the string length is > 0 it means there are unsupported characters
		$length = mb_strlen( $content, self::$UTF8 );

		// return GSM-7 if no unsupported characters are found, or UCS-2 if unsupported characters are detected
		return 0 === $length ? self::GSM_CHARSET_GSM7 : self::GSM_CHARSET_UCS2;
	}


	/**
	 * Estimates the number of messages the content will have to be split into to comply with GSM-7 or UCS-2.
	 *
	 * Note: this will use WordPress mb_* polyfills if the mbstring extension is unavailable.
	 *
	 * @since 1.12.2
	 *
	 * @param string $content piece of content
	 * @return int number of segments the message may be split into
	 */
	public static function get_message_segments_count( $content ) {

		if ( self::GSM_CHARSET_UCS2 === self::detect_charset( $content ) ) {

			// in UCS-2 the limit is 70 characters per message or 67 characters when split in multiple messages
			$segments = mb_strlen( $content, self::$UTF8 ) <= 70 ? 1 : mb_strlen( $content, self::$UTF8 ) / 67;

		} else {

			$extended_characters = [];

			foreach ( self::get_gsm_7bit_chars_ext() as $char_ext ) {
				$extended_characters[ $char_ext ] = self::GSM_7BIT_ESC . $char_ext;
			}

			// add escape character to extended charset
			$content = strtr( $content, $extended_characters );

			if ( mb_strlen( $content, self::$UTF8 ) <= 160 ) {

				$segments = 1;

			} else {

				$sms_count  = ceil( mb_strlen( $content, self::$UTF8 ) / 153 );
				$free_chars = mb_strlen( $content, self::$UTF8 ) - floor( mb_strlen( $content, self::$UTF8 ) / 153 ) * 153;

				// we have enough free characters left, don't care about escape character at the end of sms part
				if ( $free_chars >= $sms_count -1 ) {

					$segments = $sms_count;

				} else {

					$sms_count = 0;

					while ( mb_strlen( $content, self::$UTF8 ) > 0 ) {

						$sms_count++;

						// check for trailing escape character
						if ( mb_substr( $content, 153, 1, self::$UTF8 ) === self::GSM_7BIT_ESC ) {
							$content = mb_substr( $content, 152, null, self::$UTF8 );
						} else {
							$content = mb_substr( $content, 153, null, self::$UTF8 );
						}
					}

					$segments = $sms_count;
				}
			}
		}

		return ceil( $segments );
	}


	/**
	 * Gets the total count of characters a content should be truncated to individually or when split into multiple segments.
	 *
	 * @link https://www.twilio.com/docs/glossary/what-sms-character-limit
	 *
	 * @since 1.12.2
	 *
	 * @param string $content piece of content
	 * @param bool $concatenate whether segment concatenation should be allowed
	 * @return int
	 */
	public static function get_characters_count_limit( $content, $concatenate = false ) {

		if ( self::GSM_CHARSET_UCS2 === self::detect_charset( $content ) ) {
			// if will use UCS-2 encoding, should truncate to 70 characters for a single message, or to 10x67 characters when split into up 10 messages
			$chars_count_limit = ! $concatenate ? 70 : 670;
		} else { // $charset === self::GSM_CHARSET_GSM7 default
			// if GSM-7 compatible, should truncate to 160 characters for a single message, or to 10x153 characters when split into up to 10 messages
			$chars_count_limit = ! $concatenate ? 160 : 1530;
		}

		/**
		 * Filters the number of characters to truncate a message to.
		 *
		 * @since 1.12.2
		 *
		 * @param int $chars_count_limit number of characters to truncate a message to
		 * @param string $content the message body to truncate
		 * @param bool $concatenate whether concatenating the message into multiple parts should be allowed
		 */
		return max( 1, absint( apply_filters( 'wc_twilio_sms_message_body_truncate_characters', $chars_count_limit, $content, $concatenate ) ) );
	}


}
