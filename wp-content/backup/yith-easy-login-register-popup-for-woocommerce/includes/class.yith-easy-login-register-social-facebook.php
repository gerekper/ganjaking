<?php
/**
 * Social Facebook class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Social_Facebook' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Social_Facebook extends YITH_Easy_Login_Register_Social {

		/**
		 * Google Client ID
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $facebook_app_secret = '';

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $options
		 * @return void
		 */
		public function __construct( $options ) {
			$this->social              = 'facebook';
			$this->app_id              = $options['app_id'];
			$this->facebook_app_secret = $options['app_secret'];
			$this->api_url             = 'https://graph.facebook.com/v4.0/';
			$this->options             = $options;

			if ( $this->facebook_app_secret && $this->app_id ) {
				parent::__construct();
			}
		}

		/**
		 * Validate Facebook token
		 * Return an array of user data on success, throw an Exception on failure
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $token
		 * @return array
		 * @throws Exception
		 */
		protected function validate_token_facebook( $token ) {

			if ( ! $token || ! ( $user_id = $this->_is_token_valid( $token ) ) ) {
				throw new Exception( _x( 'An error has occurred! Invalid token.', 'Form error message', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			// start building return array
			$return = [ 'user_id_social' => $user_id ];
			// get additional info
			// https://graph.facebook.com/me/?fields=email,first_name,last_name&access_token=$token
			$response = wp_remote_get( $this->api_url . "me/?fields=email,first_name,last_name&access_token={$token}" );
			if ( $this->is_valid_response( $response ) ) {
				$response = json_decode( $response['body'] );
				$return   = array_merge( $return, [
					'email'      => $response->email ?? '',
					'first_name' => $response->first_name ?? '',
					'last_name'  => $response->last_name ?? '',
				] );
			}

			return $return;
		}

		/**
		 * Check if given token is valid
		 * https://graph.facebook.com/debug_token?input_token=$token&access_token=$this->facebook_app_id|$this->facebook_app_secret
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $token
		 * @return boolean|string User id on success, false on failure
		 */
		private function _is_token_valid( $token ) {
			// build access token
			$access_token = $this->app_id . '|' . $this->facebook_app_secret;
			$response     = wp_remote_get( $this->api_url . "debug_token?input_token={$token}&access_token={$access_token}" );

			if ( ! $this->is_valid_response( $response ) ) {
				return false;
			}

			$response = json_decode( $response['body'] );
			$is_valid = isset( $response->data->app_id ) && $response->data->app_id == $this->app_id && isset( $response->data->is_valid ) && $response->data->is_valid;
			return $is_valid ? $response->data->user_id : false;
		}
	}
}
