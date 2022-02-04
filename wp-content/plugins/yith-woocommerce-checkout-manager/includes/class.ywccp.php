<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @version 1.0.0
 */

defined( 'YWCCP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YWCCP' ) ) {
	/**
	 * Main class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YWCCP {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YWCCP
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YWCCP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YWCCP
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
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			// Load Plugin Framework.
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );
			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			if ( $this->is_admin() ) {
				require_once 'class.ywccp-admin.php';
				YWCCP_Admin();
			} else {
				require_once 'class.ywccp-front.php';
				YWCCP_Front();
			}

			add_action( 'init', array( $this, 'init_strings' ), 100, 1 );

			// GDPR compliance.
			add_filter( 'woocommerce_privacy_export_customer_personal_data', array( $this, 'export_customer_data' ), 10, 2 );
			add_filter( 'woocommerce_privacy_erase_personal_data_customer', array( $this, 'eraser_customer_data' ), 10, 2 );
			add_action( 'woocommerce_privacy_remove_order_personal_data', array( $this, 'remove_order_personal_data' ), 10, 1 );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Check if is admin
		 *
		 * @since  1.0.5
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_admin() {
			$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && 'frontend' === sanitize_text_field( wp_unslash( $_REQUEST['context'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return apply_filters( 'yith_wccp_is_admin_filter', is_admin() && ! $is_ajax );
		}

		/**
		 * Register strings for WPML translation
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function init_strings() {

			if ( ! defined( 'WPML_ST_VERSION' ) ) {
				return;
			}

			$fields = ywccp_get_all_checkout_fields();

			foreach ( $fields as $key => $field ) {
				// Register label.
				if ( isset( $field['label'] ) && $field['label'] ) {
					do_action( 'wpml_register_single_string', 'yith-woocommerce-checkout-manager', 'plugin_ywccp_' . $key . '_label', $field['label'] );
				}
				// Register placeholder.
				if ( isset( $field['placeholder'] ) && $field['placeholder'] ) {
					do_action( 'wpml_register_single_string', 'yith-woocommerce-checkout-manager', 'plugin_ywccp_' . $key . '_placeholder', $field['placeholder'] );
				}
				// Register tooltip.
				if ( isset( $field['custom_attributes']['data-tooltip'] ) && $field['custom_attributes']['data-tooltip'] ) {
					do_action( 'wpml_register_single_string', 'yith-woocommerce-checkout-manager', 'plugin_ywccp_' . $key . '_tooltip', $field['custom_attributes']['data-tooltip'] );
				}

				if ( ! empty( $field['options'] ) ) {
					foreach ( $field['options'] as $option_key => $option ) {
						if ( empty( $option ) ) {
							continue;
						}
						// Register single option.
						do_action( 'wpml_register_single_string', 'yith-woocommerce-checkout-manager', 'plugin_ywccp_' . $key . '_' . $option_key, $option );
					}
				}
			}
		}

		/**
		 * Export customer data
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param array       $personal_data An array of personal data for the customer.
		 * @param WC_Customer $customer      The customer to process.
		 * @return array
		 */
		public function export_customer_data( $personal_data, $customer ) {

			$billing  = ywccp_get_custom_fields( 'billing' );
			$shipping = ywccp_get_custom_fields( 'shipping' );
			$fields   = array_merge( $billing, $shipping );
			if ( ! empty( $fields ) ) {
				$id = $customer->get_id();
				foreach ( $fields as $field_key => $field ) {
					$value = get_user_meta( $id, $field_key, true );
					if ( $value ) {
						$personal_data[] = array(
							'name'  => isset( $field['label'] ) ? $field['label'] : $field_key,
							'value' => $value,
						);
					}
				}
			}

			return $personal_data;
		}

		/**
		 * Erase customer data
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param array       $response The response array.
		 * @param WC_Customer $customer The customer to process.
		 * @return array
		 */
		public function eraser_customer_data( $response, $customer ) {

			$billing  = ywccp_get_custom_fields( 'billing' );
			$shipping = ywccp_get_custom_fields( 'shipping' );
			$fields   = array_merge( $billing, $shipping );
			if ( ! empty( $fields ) ) {
				$id = $customer->get_id();
				foreach ( $fields as $field_key => $field ) {
					update_user_meta( $id, $field_key, '' );

					$label = isset( $field['label'] ) ? $field['label'] : $field_key;
					/* Translators: %s Prop name. */
					$response['messages'][]    = sprintf( __( 'Removed customer "%s"', 'woocommerce' ), $label );
					$response['items_removed'] = true;
				}
			}

			return $response;
		}

		/**
		 * Erase customer data
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param WC_Order $order The order object to precess.
		 */
		public function remove_order_personal_data( $order ) {

			$billing  = ywccp_get_custom_fields( 'billing' );
			$shipping = ywccp_get_custom_fields( 'shipping' );
			$fields   = array_merge( $billing, $shipping );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field_key => $field ) {
					if ( function_exists( 'wp_privacy_anonymize_data' ) ) {
						$anon_value = wp_privacy_anonymize_data( $field['type'] );
					} else {
						$anon_value = '';
					}

					$order->update_meta_data( '_' . $field_key, $anon_value );
				}

				$order->save();
			}
		}


		/**
		 * Register plugins for activation tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWCCP_DIR . '/plugin-fw/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YWCCP_INIT, YWCCP_SECRET_KEY, YWCCP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWCCP_DIR . '/plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YWCCP_SLUG, YWCCP_INIT );
		}
	}
}
/**
 * Unique access to instance of YWCCP class
 *
 * @since 1.0.0
 * @return YWCCP
 */
function YWCCP() { // phpcs:ignore
	return YWCCP::get_instance();
}
