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
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWCES_Mandrill' ) ) {


	/**
	 * Implements Mandrill for YWCES plugin
	 *
	 * @class   YWCES_Mandrill
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCES_Mandrill {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCES_Mandrill
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCES_Mandrill
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {
		}

		/**
		 * Send the email.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $to
		 * @param   string $subject
		 * @param   string $message
		 * @param   string $headers
		 * @param   string $attachments
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function send_email( $to, $subject, $message, $headers, $attachments ) {

			if ( ! class_exists( 'Mandrill' ) ) {
				require_once( YWCES_DIR . 'includes/third-party/Mandrill.php' );
			}

			$from_name = wp_specialchars_decode( esc_html( get_option( 'woocommerce_email_from_name' ) ), ENT_QUOTES );

			if ( ! isset( $from_name ) ) {
				$from_name = 'WordPress';
			}

			$from_email = sanitize_email( get_option( 'woocommerce_email_from_address' ) );

			if ( ! isset( $from_email ) ) {

				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );
				}

				$from_email = 'wordpress@' . $sitename;
			}

			$api_key = get_option( 'ywces_mandrill_apikey' );

			$headers_array = explode( '\r\n', $headers );

			$headers = array();

			foreach ( $headers_array as $item ) {

				$headers_row = explode( ': ', $item );

				$headers[ $headers_row[0] ] = $headers_row[1];

			}

			try {
				$mandrill = new Mandrill( $api_key );
				$message  = apply_filters( 'ywces_mandrill_send_mail_message', array(
					'html'        => $message,
					'subject'     => $subject,
					'from_email'  => apply_filters( 'wp_mail_from', $from_email ),
					'from_name'   => apply_filters( 'wp_mail_from_name', $from_name ),
					'to'          => array(
						array(
							'email' => $to,
							'type'  => 'to'
						)
					),
					'headers'     => $headers,
					'attachments' => $attachments
				) );

				$async   = apply_filters( 'ywces_mandrill_send_mail_async', false );
				$ip_pool = apply_filters( 'ywces_mandrill_send_mail_ip_pool', null );
				$send_at = apply_filters( 'ywces_mandrill_send_mail_send_at', null );

				$results = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );
				$return  = true;

				if ( ! empty( $results ) ) {
					foreach ( $results as $result ) {
						if ( ! isset( $result['status'] ) || in_array( $result['status'], array( 'rejected', 'invalid' ) ) ) {
							$return = false;
						}
					}
				}

				return $return;
			} catch ( Mandrill_Error $e ) {
				return false;
			}

		}

	}

	/**
	 * Unique access to instance of YWCES_Mandrill class
	 *
	 * @return \YWCES_Mandrill
	 */
	function YWCES_Mandrill() {
		return YWCES_Mandrill::get_instance();
	}

}

