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

/**
 * Twilio SMS URL shortener helper class.
 *
 * Shortens URLs using external services.
 *
 * @since 1.11.0
 */
class WC_Twilio_SMS_URL_Shortener {


	/** @var bool whether shortened URLs should be used */
	private static $using_shortened_urls;


	/**
	 * Determines whether shortened URLs should be used.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	public static function using_shortened_urls() {

		if ( null === self::$using_shortened_urls ) {

			self::$using_shortened_urls = 'yes' === get_option( 'wc_twilio_sms_shorten_urls' );
		}

		return self::$using_shortened_urls;
	}


	/**
	 * Gets the ID of the chosen URL shortener service.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public static function get_url_shortener_service() {

		$service = trim( get_option( 'wc_twilio_sms_url_shortener_service', self::get_default_url_shortener_service() ) );

		// TODO remove this in a future version as Google URL Shortener support is dropped {FN 2019-06-12}
		if ( 'google-url-shortener' !== $service ) {
			update_option( 'wc_twilio_sms_keep_google_url_shortener_service', 'no' );
		}

		return $service;
	}


	/**
	 * Gets the default URL shortener service to use.
	 *
	 * @since 1.12.2
	 *
	 * @return string
	 */
	public static function get_default_url_shortener_service() {

		$services = self::get_url_shortener_services();

		return in_array( 'firebase-dynamic-links', $services, true ) ? 'firebase-dynamic-links' : current( $services );
	}


	/**
	 * Gets the available URL shortener services.
	 *
	 * @since 1.12.2
	 *
	 * @param bool $include_labels whether to include labels or not (default false)
	 * @return string[]|array array of service IDs or associative array of IDs and labels
	 */
	public static function get_url_shortener_services( $include_labels = false ) {

		$url_shortening_services = [
			'firebase-dynamic-links' => __( 'Firebase Dynamic Links', 'woocommerce-twilio-sms-notifications' ),
		];

		// TODO remove this in a future version as Google URL Shortener support is dropped {FN 2019-06-12}
		if ( 'yes' === get_option( 'wc_twilio_sms_keep_google_url_shortener_service', 'no' ) ) {
			$url_shortening_services['google-url-shortener'] = __( 'Google URL Shortener', 'woocommerce-twilio-sms-notifications' );
		}

		/**
		 * Filters the available URL shortening services.
		 *
		 * @since 1.11.0
		 *
		 * @param array $url_shortening_services associative array of service IDs and names
		 */
		$url_shortening_services = (array) apply_filters( 'wc_twilio_sms_url_shortening_services', $url_shortening_services );

		return $include_labels ? $url_shortening_services : array_keys( $url_shortening_services );
	}


	/**
	 * Checks which is the URL shortener service chosen.
	 *
	 * @since 1.11.0
	 *
	 * @param string $which URL shortener ID
	 *
	 * @return bool returns false if not using shortened URLs
	 */
	public static function is_shortener_service( $which ) {

		return self::using_shortened_urls() && $which === self::get_url_shortener_service();
	}


	/**
	 * Extracts URLs from SMS message and replace them with shorten URLs via callback.
	 *
	 * @see \WC_Twilio_SMS_URL_Shortener::shorten_url()
	 *
	 * @since  1.11.0
	 *
	 * @param string $sms_message SMS message
	 * @return string SMS message with URLs shortened
	 */
	public static function shorten_urls( $sms_message ) {

		// regex pattern source : http://daringfireball.net/2010/07/improved_regex_for_matching_urls
		$pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

		// find each URL and replacing using callback
		$shortener_message = preg_replace_callback( $pattern, [ __CLASS__, 'shorten_url' ], $sms_message );

		// return message with shortened URLs
		return $shortener_message;
	}


	/**
	 * Shortens URLs by using an external URL shortener service.
	 *
	 * By default, uses Google URL Shortener, but the service is being deprecated in favor of Firebase Dynamic Links (Google acquired Firebase).
	 *
	 * Callback for shorten_urls() preg_replace.
	 * @see \WC_Twilio_SMS_URL_Shortener::shorten_urls()
	 *
	 * @since 1.11.0
	 *
	 * @param string[] $matches matches found via preg_replace
	 * @return string shortened url
	 */
	private static function shorten_url( $matches ) {

		$url     = reset( $matches ); // get the first match
		$service = self::get_url_shortener_service();

		switch ( $service ) {

			case 'firebase-dynamic-links' :
				$shortened_url = self::firebase_shorten_url( $url );
			break;

			// TODO remove this in a future version as Google URL Shortener support is dropped {FN 2019-06-12}
			case 'google-url-shortener' :
				$shortened_url = self::google_shorten_url( $url );
			break;

			default :
				$shortened_url = $url;
			break;
		}

		/**
		 * Filters a shortened URL or a URL to be shortened.
		 *
		 * @since 1.8.2
		 *
		 * @param string $shortened_url the shortened URL
		 * @param string $url the original URL
		 * @param string $service URL shortening service to use
		 */
		return (string) apply_filters( 'wc_twilio_sms_shorten_url', $shortened_url, $url, $service );
	}


	/**
	 * Shortens a given URL via Google URL Shortener.
	 *
	 * This service is no longer supported.
	 *
	 * TODO remove this in a future version as Google URL Shortener support is dropped {FN 2019-06-12}
	 *
	 * @link https://developers.google.com/url-shortener/v1/getting_started
	 *
	 * @since 1.11.0
	 *
	 * @param string $url URL to shorten
	 * @return string shortened URL
	 */
	private static function google_shorten_url( $url ) {

		$shortened_url = $url;
		$api_key       = trim( get_option( 'wc_twilio_sms_google_url_shortener_api_key', '' ) );

		if ( is_string( $url ) && '' !== $api_key ) {

			$api_url   = add_query_arg( 'key', $api_key, 'https://www.googleapis.com/urlshortener/v1/url' );
			$post_args = [
				'method'      => 'POST',
				'timeout'     => '10',
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => true,
				'headers'     => [
					'content-type' => 'application/json',
				],
				'body'        => json_encode( [
					'longUrl' => $url,
				] ),
			];

			$shortened_url = self::parse_url_shortening_response( $url, wp_safe_remote_post( $api_url, $post_args ) );
		}

		return $shortened_url;
	}


	/**
	 * Shortens a given URL via Firebase Dynamic Links.
	 *
	 * @link https://firebase.google.com/products/dynamic-links/
	 * @link https://firebase.google.com/docs/dynamic-links/rest
	 *
	 * @since 1.11.0
	 *
	 * @param string $url URL to shorten
	 * @return string shortened URL
	 */
	private static function firebase_shorten_url( $url ) {

		$shortened_url = $url;
		$api_key       = trim( get_option( 'wc_twilio_sms_firebase_dynamic_links_api_key', '' ) );
		$domain        = trim( get_option( 'wc_twilio_sms_firebase_dynamic_links_domain',  '' ) );

		if ( is_string( $url ) && ! empty( $api_key ) && ! empty( $domain ) ) {

			$url         = untrailingslashit( $url );
			$domain      = str_replace( [ 'http://', 'https://' ], '', untrailingslashit( $domain ) );
			$api_url     = add_query_arg( 'key', $api_key, 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks' );
			$post_args   = [
				'method'      => 'POST',
				'timeout'     => '10',
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => true,
				'headers'     => [
					'content-type' => 'application/json',
				],
				/** @see https://firebase.google.com/docs/dynamic-links/rest for options */
				'body' => json_encode( [
					'longDynamicLink' => "https://{$domain}/?link={$url}",
					'suffix' => [
						'option' => 'SHORT',
					],
				] ),
			];

			$shortened_url = self::parse_url_shortening_response( $url, wp_safe_remote_post( $api_url, $post_args ) );
		}

		return $shortened_url;
	}


	/**
	 * Parses a response from a URL shortening service.
	 *
	 * @since 1.11.0
	 *
	 * @param string $long_url the original long URL to return in case of errors
	 * @param array|\WP_Error $response
	 * @return string URL (shortened on success, or original long form on failure, while logging errors)
	 */
	private static function parse_url_shortening_response( $long_url, $response ) {

		$shortened_url = $long_url;

		// request error
		if ( $response instanceof \WP_Error ) {

			self::log_url_shortener_error_message( $response->get_error_message() );

		// evaluate response
		} else {

			$error_message = '';
			$data          = json_decode( wp_remote_retrieve_body( $response ), true );

			// Google Shortener error
			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {

				if ( ! empty( $data['error']['errors'] ) ) {

					foreach ( $data['error']['errors'] as $error ) {

						// append an error message if it was provided
						if ( ! empty( $error['message'] ) ) {
							$error_message .= ': ' . $error['message'];
						}

						// append the reason code if it was provided
						if ( ! empty( $error['reason'] ) ) {
							$error_message .= ' (' . $error['reason'] . ')';
						}

						self::log_url_shortener_error_message( $error_message );
					}

				// unknown error or maybe Firebase error
				} else {

					self::log_url_shortener_error_message( is_array( $data['error'] ) && isset( $data['error']['message'] ) ? $data['error']['message'] : '' );
				}

			// Firebase error
			} elseif ( ! empty( $data['error'] ) ) {

				self::log_url_shortener_error_message( is_array( $data['error'] ) && isset( $data['error']['message'] ) ? $data['error']['message'] : $data['error'] );

			// Firebase: success
			} elseif ( ! empty( $data['shortLink'] ) && is_string( $data['shortLink'] ) ) {

				$shortened_url = $data['shortLink'];

			// Google Shortener: success
			} elseif ( ! empty( $data['id'] ) && is_string( $data['id'] ) ) {

				$shortened_url = $data['id'];

			// Unknown error
			} else {

				self::log_url_shortener_error_message();
			}
		}

		return $shortened_url;
	}


	/**
	 * Logs an error message from a URL shortener service.
	 *
	 * @since 1.11.0
	 *
	 * @param string $error_message
	 */
	private static function log_url_shortener_error_message( $error_message = null ) {

		if ( ! is_string( $error_message ) || '' === trim( $error_message ) ) {
			$error_message = esc_html__( 'Unknown error.', 'woocommerce-twilio-sms-notifications' );
		}

		wc_twilio_sms()->log( sprintf(
			/* translators: Placeholder: %s - error message to be logged */
			esc_html__( 'URL Shortener error: %s', 'woocommerce-twilio-sms-notifications' ),
			$error_message
		) );
	}


}
