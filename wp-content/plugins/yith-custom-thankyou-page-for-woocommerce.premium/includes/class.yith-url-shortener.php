<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */
if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_url_shortener' ) ) {
	/**
	 * Manage the Socials url shortening
	 *
	 * @class       YITH_url_shortener
	 * @package     YITH Custom ThankYou Page for Woocommerce
	 * @author      YITH
	 * @since       1.0.0
	 */
	class YITH_url_shortener {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_url_shortener
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_url_shortener
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self( $_REQUEST ); //phpcs:ignore
			}

			return self::$instance;

		}


		/**
		 * Replace URLs with shorten URLs via callback
		 *
		 * @param   string $text url to shorten.
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function url_shortening( $text ) {

			$pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

			$text = preg_replace_callback( $pattern, array( $this, 'shorten_url' ), $text );

			return $text;

		}

		/**
		 * Callback for shortening regex
		 *
		 * @param   string $text url to shorten.
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		private function shorten_url( $text ) {

			$url     = reset( $text );
			$service = get_option( 'ctpw_url_shortening' );

			switch ( $service ) {

				case 'google':
					$short_url = $this->google_url_shortening( $url );
					break;
				case 'bitly':
					$short_url = $this->bitly_url_shortening( $url );
					break;
				default:
					$short_url = apply_filters( 'ctpw_custom_shortening_' . $service, $url );

			}

			return $short_url;

		}


		/**
		 * Shortens a URL via Bitly Shortener
		 *
		 * @param   string $url url to shorten.
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		private function bitly_url_shortening( $url ) {

			$args = array(
				'access_token' => get_option( 'ctpw_bitly_access_token' ),
				'longUrl'      => esc_url( $url ),
				'format'       => 'json',
			);

			$reponse = wp_remote_get( add_query_arg( $args, 'https://api-ssl.bitly.com/v3/shorten' ) );
			$json    = json_decode( wp_remote_retrieve_body( $reponse ) );

			if ( isset( $json->status_code ) && 200 === $json->status_code && isset( $json->data->url ) ) {
				$url = $json->data->url;
			}

			return $url;

		}

		/**
		 * Shortens a URL via Google URL Shortener
		 *
		 * @param   string $url url to shorten.
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		private function google_url_shortening( $url ) {

			$api_key  = get_option( 'ctpw_google_api_key' );
			$jsonData = wp_json_encode( array( 'longUrl' => $url ) );
			$curlObj  = curl_init();

			curl_setopt( $curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $api_key );
			curl_setopt( $curlObj, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curlObj, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $curlObj, CURLOPT_HEADER, 0 );
			curl_setopt( $curlObj, CURLOPT_HTTPHEADER, array( 'Content-type:application/json' ) );
			curl_setopt( $curlObj, CURLOPT_POST, 1 );
			curl_setopt( $curlObj, CURLOPT_POSTFIELDS, $jsonData );

			$response = curl_exec( $curlObj );
			$json     = json_decode( $response );

			curl_close( $curlObj );

			if ( ! empty( $json ) && isset( $json->id ) ) {
				$url = $json->id;
			}
			return $url;

		}

	} // end class.

	/**
	 * Unique access to instance of YITH_url_shortener class
	 *
	 * @return \YITH_url_shortener
	 */
	function YITH_url_shortener() {
		return YITH_url_shortener::get_instance();
	}
}
