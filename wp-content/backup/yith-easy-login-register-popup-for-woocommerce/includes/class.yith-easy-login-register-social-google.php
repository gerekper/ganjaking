<?php
/**
 * Social Google class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Social_Google' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Social_Google extends YITH_Easy_Login_Register_Social {

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $options
		 * @return void
		 */
		public function __construct( $options ) {
			$this->social  = 'google';
			$this->api_url = 'https://oauth2.googleapis.com/';
			$this->options = $options;
			$this->app_id  = $this->set_client_id();

			if ( $this->app_id ) {
				parent::__construct();
			}
		}

		/**
		 * Set Google client ID
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		protected function set_client_id() {
			$id = $this->options['app_id'];
			if ( $id ) {
				// make sure .apps.googleusercontent.com isset, if not try to append it
				if ( strpos( $id, '.apps.googleusercontent.com' ) === false ) {
					$id .= '.apps.googleusercontent.com';
				}
			}

			return $id;
		}

		/**
		 * Validate Google token
		 * Return an array of user data on success, throw an Exception on failure
		 * https://oauth2.googleapis.com/tokeninfo?id_token=XYZ123
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $token
		 * @return array
		 * @throws Exception
		 */
		protected function validate_token_google( $token ) {

			if ( ! $token ) {
				throw new Exception( _x( 'An error has occurred! Invalid token.', 'Form error message', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			$response = wp_remote_get( $this->api_url . "tokeninfo?id_token={$token}" );
			$response = $this->is_valid_response( $response ) ? json_decode( $response['body'] ) : false;
			// validate response checking client ID
			if ( ! $response || ! isset( $response->aud ) || $response->aud != $this->app_id ) {
				throw new Exception( _x( 'An error has occurred! Invalid token.', 'Form error message', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			return [
				'user_id_social' => $response->sub,
				'email'          => $response->email,
				'first_name'     => $response->given_name,
				'last_name'      => $response->family_name,
			];
		}
	}
}
