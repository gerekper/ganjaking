<?php
/**
 * Plugin menu items class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 2.4.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Items' ) ) {
	/**
	 * Items class.
	 * The class manage all plugin endpoints items.
	 *
	 * @since 2.4.0
	 */
	class YITH_WCMAP_Items {

		/**
		 * Items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $_items = array();

		/**
		 * Default items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $_default_items = array();

		/**
		 * Plugins items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $_plugins_items = array();

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  2.4.0
		 */
		public function __construct() {
		}

		/**
		 * Get items method
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_items() {
			return apply_filters( 'yith_wcmap_get_items', $this->_items );
		}

		/**
		 * Get default items method
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_default_items() {
			return apply_filters( 'yith_wcmap_get_default_items', $this->_default_items );
		}

		/**
		 * Get plugins items method
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_plugins_items() {
			return apply_filters( 'yith_wcmap_get_plugins_items', $this->_plugins_items );
		}

		/**
		 * Get a plugin item by key
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 * @param string $key
		 * @return array
		 */
		public function get_plugin_item_by_key( $key ) {
			$plugins_items = $this->get_plugins_items();
			return isset( $plugins_items[ $key ] ) ? $plugins_items[ $key ] : array();
		}

		/**
		 * Init default items
		 *
		 * @since  2.4.0
		 * @access protected
		 * @author Francesco Licandro
		 */
		protected function init_default_items() {

			$endpoints_slugs = array(
				'orders'          => get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' ),
				'downloads'       => get_option( 'woocommerce_myaccount_downloads_endpoint', 'downloads' ),
				'edit-address'    => get_option( 'woocommerce_myaccount_edit_address_endpoint', 'edit-address' ),
				'payment-methods' => get_option( 'woocommerce_myaccount_payment_methods_endpoint', 'payment-methods' ),
				'edit-account'    => get_option( 'woocommerce_myaccount_edit_account_endpoint', 'edit-account' ),
				'customer-logout' => get_option( 'woocommerce_logout_endpoint', 'customer-logout' ),
			);

			$endpoints = array(
				'dashboard'       => __( 'Dashboard', 'yith-woocommerce-customize-myaccount-page' ),
				'orders'          => __( 'My Orders', 'yith-woocommerce-customize-myaccount-page' ),
				'downloads'       => __( 'My Downloads', 'yith-woocommerce-customize-myaccount-page' ),
				'edit-address'    => __( 'Edit Address', 'yith-woocommerce-customize-myaccount-page' ),
				'payment-methods' => __( 'Payment Methods', 'yith-woocommerce-customize-myaccount-page' ),
				'edit-account'    => __( 'Edit Account', 'yith-woocommerce-customize-myaccount-page' ),
				'customer-logout' => __( 'Logout', 'yith-woocommerce-customize-myaccount-page' ),
			);

			$menu_items_endpoint = apply_filters( 'woocommerce_account_menu_items', $endpoints, $endpoints_slugs );
			! is_array( $menu_items_endpoint ) && $menu_items_endpoint = array();
			$endpoints = array_merge( $endpoints, $menu_items_endpoint );

			if ( class_exists( 'WC_Subscriptions' ) ) {
				unset( $endpoints['subscriptions'] );
			}

			$registered_endpoint  = WC()->query->get_query_vars();
			$this->_default_items = array();

			// populate endpoints array with options
			foreach ( $endpoints as $endpoint_key => $endpoint_label ) {

				// always exclude customer logout or endpoint not in menu if are not wc default
				if ( $endpoint_key == 'customer-logout' || ( $endpoint_key != 'dashboard' && ! array_key_exists( $endpoint_key, $registered_endpoint ) ) ) {
					continue;
				}

				$slug    = isset( $registered_endpoint[ $endpoint_key ] ) ? $registered_endpoint[ $endpoint_key ] : $endpoint_key;
				$options = yith_wcmap_get_default_endpoint_options( $slug );
				// set label
				$options['label'] = $endpoint_label;

				switch ( $endpoint_key ) {
					case 'orders':
						$options['icon'] = 'file-text-o';
						break;
					case 'edit-account':
					case 'edit-address':
						$options['icon'] = 'pencil-square-o';
						break;
					case 'downloads':
						$options['icon'] = 'download';
						break;
					case 'dashboard':
						$options['icon'] = 'tachometer';
						break;
					case 'payment-methods' :
						$options['icon'] = 'money';
						break;
					default:
						break;
				}

				$this->_default_items[ $endpoint_key ] = $options;
			}
		}

		/**
		 * Maybe init default items
		 *
		 * @since  2.4.0
		 * @access protected
		 * @author Francesco Licandro
		 */
		protected function maybe_init_default_items() {
			empty( $this->_default_items ) && $this->init_default_items();
		}

		/**
		 * Init plugin items
		 *
		 * @since  2.4.0
		 * @access protected
		 * @author Francesco Licandro
		 */
		protected function init_plugins_items() {
			if ( file_exists( YITH_WCMAP_DIR . 'plugin-options/plugins-endpoints.php' ) ) {
				$this->_plugins_items = include( YITH_WCMAP_DIR . 'plugin-options/plugins-endpoints.php' );
			}
		}

		/**
		 * Maybe init plugin items
		 *
		 * @since  2.4.0
		 * @access protected
		 * @author Francesco Licandro
		 */
		protected function maybe_init_plugins_items() {
			empty( $this->_plugins_items ) && $this->init_plugins_items();
		}

		/**
		 * Get items slug
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_items_slug() {
			$slugs = array();
			foreach ( $this->get_items() as $key => $field ) {
				isset( $field['slug'] ) && $slugs[ $key ] = $field['slug'];
				if ( isset( $field['children'] ) ) {
					foreach ( $field['children'] as $child_key => $child ) {
						isset( $child['slug'] ) && $slugs[ $child_key ] = $child['slug'];
					}
				}
			}

			return $slugs;
		}

		/**
		 * Get items keys
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_items_keys() {
			$keys = array();
			foreach ( $this->get_items() as $items_key => $item ) {
				$keys[] = $items_key;
				if ( isset( $item['children'] ) ) {
					foreach ( $item['children'] as $child_key => $child ) {
						$keys[] = $child_key;
					}
				}
			}

			return $keys;
		}

		/**
		 * Init items
		 *
		 * @since  2.4.0
		 * @author Francesco Licandro
		 */
		public function init() {

			// get saved endpoints order
			$fields = get_option( 'yith_wcmap_endpoint', '' );
			$fields = json_decode( $fields, true );

			// set empty array is false or null
			( ! $fields || is_null( $fields ) ) && $fields = array();

			$this->_items = array();

			// get default endpoints
			$this->maybe_init_default_items();
			$this->maybe_init_plugins_items();
			$defaults = array_merge( $this->_default_items, $this->_plugins_items );

			if ( empty( $fields ) ) {
				$this->_items = $defaults;
			} else {

				foreach ( $fields as $id => $field_option ) {

					// build return array
					$this->_items[ $id ] = array();

					$options = get_option( 'yith_wcmap_endpoint_' . $id, array() );

					if ( is_array( $options ) ) {

						empty( $field_option['type'] ) && $field_option['type'] = 'endpoint';
						$options_default = call_user_func( "yith_wcmap_get_default_{$field_option['type']}_options", $id );
						// is empty check on default endpoint
						( empty( $options ) && isset( $defaults[ $id ] ) ) && $options = $defaults[ $id ];
						// always merge with default
						$options = array_merge( $options_default, $options );

						if ( isset( $field_option['children'] ) ) {

							$children = array();

							foreach ( $field_option['children'] as $child_id => $child ) {
								$child_options   = get_option( 'yith_wcmap_endpoint_' . $child_id, array() );
								$options_default = call_user_func( "yith_wcmap_get_default_{$child['type']}_options", $child_id );
								// is empty check on default endpoint
								( empty( $child_options ) && isset( $defaults[ $id ] ) ) && $child_options = $defaults[ $id ];
								// always merge with default
								$children[ $child_id ] = is_array( $child_options ) ? array_merge( $options_default, $child_options ) : $options_default;

								// check child on default plugin
								unset( $defaults[ $child_id ] );
							}

							$options['children'] = $children;
						}

						// unset on defaults
						unset( $defaults[ $id ] );

						$this->_items[ $id ] = $options;
					}
				}

				// merge with defaults again
				$this->_items = array_merge( $this->_items, $defaults );
			}
		}
	}
}