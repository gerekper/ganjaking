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

if ( ! class_exists( 'YWSN_URL_Shortener' ) ) {

	/**
	 * Implements URL shorteners for YWSN plugin
	 *
	 * @class   YWSN_URL_Shortener
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_URL_Shortener {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSN_URL_Shortener
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSN_URL_Shortener
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Replace URLs with shorten URLs via callback
		 *
		 * @since   1.0.0
		 *
		 * @param   $text string
		 *
		 * @return  string
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
		 * @since   1.0.0
		 *
		 * @param   $text array
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		private function shorten_url( $text ) {

			$url     = reset( $text );
			$service = get_option( 'ywsn_url_shortening' );

			switch ( $service ) {

				case 'google':

					$short_url = $this->google_url_shortening( $url );
					break;

				case 'bitly':

					$short_url = $this->bitly_url_shortening( $url );
					break;

				default:

					$short_url = apply_filters( 'ywsn_custom_shortening_' . $service, $url );

			}

			return $short_url;

		}

		/**
		 * Shortens a URL via Bitly Shortener
		 *
		 * @since   1.0.0
		 *
		 * @param   $url string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		private function bitly_url_shortening( $url ) {

			$args = array(
				'access_token' => get_option( 'ywsn_bitly_access_token' ),
				'longUrl'      => esc_url( $url ),
				'format'       => 'json'
			);

			$reponse = wp_remote_get( add_query_arg( $args, 'https://api-ssl.bitly.com/v3/shorten' ) );
			$json    = json_decode( wp_remote_retrieve_body( $reponse ) );

			if ( isset( $json->status_code ) && $json->status_code == 200 && isset( $json->data->url ) ) {

				$url = $json->data->url;

			}

			return $url;

		}

		/**
		 * Shortens a URL via Google URL Shortener
		 *
		 * @since   1.0.0
		 *
		 * @param   $url string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		private function google_url_shortening( $url ) {

			$api_key  = get_option( 'ywsn_google_api_key' );
			$jsonData = json_encode( array( 'longUrl' => $url ) );
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

	}

	/**
	 * Unique access to instance of YWSN_URL_Shortener class
	 *
	 * @return \YWSN_URL_Shortener
	 */
	function YWSN_URL_Shortener() {
		return YWSN_URL_Shortener::get_instance();
	}

}