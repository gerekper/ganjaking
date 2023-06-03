<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Software Add-On Translator Class
 *
 * @since       2.7
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Software Add-On Translator
 */
if ( ! class_exists( 'WCAM_Software_Add_On_Translator' ) ) {

	class WCAM_Software_Add_On_Translator {

		private $request = array();

		/**
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Singleton.
		 *
		 * @static
		 * @return null|\WCAM_Software_Add_On_Translator
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		private function __construct() {
			// Disable Software Add-on, so it does not respond to query requests.
			$this->deactivate_plugin();

			add_action( 'woocommerce_api_software-api', array( $this, 'translate_request' ) );
		}

		/**
		 * Deactivate Software Add-on, so it does not respond to HTTP query requests.
		 *
		 * @since 2.7
		 */
		public function deactivate_plugin() {
			if ( defined( 'ABSPATH' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( function_exists( 'deactivate_plugins' ) ) {
				if ( is_plugin_active( 'woocommerce-software-add-on/woocommerce-software-add-on.php' ) ) {
					deactivate_plugins( 'woocommerce-software-add-on/woocommerce-software-add-on.php' );
				} elseif ( is_plugin_active( 'woocommerce-software-add-on/woocommerce-software.php' ) ) {
					deactivate_plugins( 'woocommerce-software-add-on/woocommerce-software.php' );
				}
			}
		}

		/**
		 * Translate HTTP query request.
		 *
		 * @since 2.7
		 *
		 * @param array $_REQUEST
		 */
		public function translate_request() {
			if ( isset( $_REQUEST[ 'request' ] ) ) {
				$this->request = $_REQUEST;

				$this->translate_keys();
				WC_AM_API_REQUESTS( $this->request );
			}
		}

		/**
		 * Migrate old keys to new keys and flatten array.
		 *
		 * @since 2.7
		 */
		private function translate_keys() {
			foreach ( $this->request as $key => $value ) {
				if ( $key == 'wc-api' ) {
					continue;
				}

				// Excess garbage
				if ( $key == 'woocommerce-login-nonce' ) {
					unset( $this->request[ $key ] );
				}

				// Excess garbage
				if ( $key == '_wpnonce' ) {
					unset( $this->request[ $key ] );
				}

				// Excess garbage
				if ( $key == 'woocommerce-reset-password-nonce' ) {
					unset( $this->request[ $key ] );
				}

				if ( $value == 'activation' ) {
					unset( $this->request[ $key ] );
					$this->request[ $key ] = 'activate';
				}

				if ( $value == 'deactivation' ) {
					unset( $this->request[ $key ] );
					$this->request[ $key ] = 'deactivate';
				}
				if ( $value == 'check' ) {
					unset( $this->request[ $key ] );
					$this->request[ $key ] = 'status';
				}

				if ( $key == 'software_id' ) {
					unset( $this->request[ $key ] );
					$this->request[ 'product_id' ] = WC_AM_LEGACY_PRODUCT_ID()->get_product_id_integer( $value, '_software_product_id' );
				}

				if ( $key == 'software_version' ) {
					unset( $this->request[ $key ] );
					$this->request[ 'version' ] = ! empty( $value ) ? $value : '';
				}

				if ( $key == 'licence_key' ) {
					unset( $this->request[ $key ] );
					$this->request[ 'api_key' ] = ! empty( $value ) ? $value : '';
				}

				if ( $key == 'license_key' ) {
					unset( $this->request[ $key ] );
					$this->request[ 'api_key' ] = ! empty( $value ) ? $value : '';
				}

				if ( $key == 'platform' ) {
					unset( $this->request[ $key ] );
					$this->request[ 'object' ] = ! empty( $value ) ? $value : '';
				}

				if ( $key == 'request' ) {
					$this->request[ 'wc_am_action' ] = $this->request[ $key ];
					unset( $this->request[ $key ] );
				}

				$this->request[ 'wcam_software_add_on_request' ] = 'yes';
			}
		}
	}
}