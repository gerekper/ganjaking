<?php

if ( ! class_exists( 'JWT' ) ) {
	include_once dirname( __FILE__ ) . '/libraries/json-web-token.php';
}

if ( ! class_exists( 'YLC_Token' ) ) {

	class YLC_Token {

		/**
		 * @var $credentials array credentials keys
		 */
		private $credentials = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 *
		 * @param $credentials array
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $credentials ) {

			$this->credentials = $credentials;

		}

		/**
		 * Get token
		 *
		 * @since   1.0.0
		 *
		 * @param $data    array
		 * @param $options array
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_token( $data, $options ) {

			$now_seconds = time();

			if ( isset( $this->credentials['secret'] ) ) {

				$payload  = array(
					'admin' => $options['admin'],
					'debug' => true,
					'd'     => $data,
					'iat'   => $now_seconds
				);
				$key      = $this->credentials['secret'];
				$encoding = 'HS256';

			} else {

				$payload  = array(
					'iss'    => $this->credentials['service_account'],
					'sub'    => $this->credentials['service_account'],
					'aud'    => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
					'iat'    => $now_seconds,
					'exp'    => $now_seconds + ( 60 * 60 ),  // Maximum expiration time is one hour
					'uid'    => $data['uid'],
					'claims' => array(
						'd'     => $data,
						'admin' => $options['admin'],
						'debug' => true

					),
				);
				$key      = $this->credentials['private_key'];
				$encoding = 'RS256';

			}

			return JWT::encode( $payload, $key, $encoding );

		}

	}

}

