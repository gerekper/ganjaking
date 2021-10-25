<?php
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */


	namespace MasterAddons\Inc\Templates\Classes;

	use MasterAddons\Inc\Templates;

	if( ! defined( 'ABSPATH' ) ) exit; // No access of directly access

	if ( ! class_exists( 'Master_Addons_Templates_API' ) ) {

		class Master_Addons_Templates_API {

			private $config     = array();

			private $enabled    = null;

			public function __construct() {
				$this->config  = Templates\master_addons_templates()->config->get( 'api' );
			}


			public function is_enabled() {

				if ( null !== $this->enabled ) {
					return $this->enabled;
				}

				if ( empty( $this->config['enabled'] ) || true !== $this->config['enabled'] ) {
					$this->enabled = false;
					return $this->enabled;
				}

				if ( empty( $this->config['base'] ) || empty( $this->config['path'] ) || empty( $this->config['endpoints'] ) ) {
					$this->enabled = false;
					return $this->enabled;
				}

				$this->enabled = true;

				return $this->enabled;
			}

			public function api_url( $flag ) {

				if ( ! $this->is_enabled() ) {
					return false;
				}

				if ( empty( $this->config['endpoints'][ $flag ] ) ) {
					return false;
				}

				return $this->config['base'] . $this->config['path'] . $this->config['endpoints'][ $flag ];
			}


			public function get_info( $key = '' ) {

				$api_url = $this->api_url( 'info' );

				if ( ! $api_url ) {
					return false;
				}

				$response = wp_remote_get( $api_url, $this->request_args() );

				$body = wp_remote_retrieve_body( $response );
				$body = json_decode( $body, true );

				if ( ! $body || ! isset( $body['success'] ) || true !== $body['success'] ) {
					return false;
				}

				if ( ! $key ) {
					unset( $body['success'] );
					return $body;
				}

				if ( is_string( $key ) ) {
					return isset( $body[ $key ] ) ? $body[ $key ] : false;
				}

				if ( is_array( $key ) ) {

					$result = array();

					foreach ( $key as $_key ) {
						$result[ $_key ] = isset( $body[ $_key ] ) ? $body[ $_key ] : false;
					}

					return $result;

				}

			}


			public function request_args() {
				return array(
					'timeout'   => 60,
					'sslverify' => false
				);
			}

		}

	}
