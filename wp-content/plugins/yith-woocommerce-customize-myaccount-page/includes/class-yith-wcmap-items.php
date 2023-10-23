<?php
/**
 * Plugin menu items class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 2.4.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Items' ) ) {
	/**
	 * Items class.
	 * The class manage all plugin endpoints items.
	 *
	 * @since 2.4.0
	 */
	class YITH_WCMAP_Items {

		/**
		 * Item types
		 *
		 * @since 3.0.0
		 * @aconst array
		 * @deprecated
		 */
		const ITEM_TYPES = array();

		/**
		 * Items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $items = array();

		/**
		 * Default items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $default_items = array();

		/**
		 * Plugins items array
		 *
		 * @since 2.4.0
		 * @var array
		 */
		private $plugins_items = array();

		/**
		 * Items types
		 *
		 * @since 3.12.0
		 * @var array
		 */
		private $items_types = array( 'endpoint' );

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  2.4.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 20 );
			add_action( 'init', array( $this, 'add_custom_endpoints' ), 21 );
			add_action( 'init', array( $this, 'rewrite_rules' ), 22 );
		}

		/**
		 * Get items method
		 *
		 * @since  2.4.0
		 * @return array
		 */
		public function get_items() {
			/**
			 * APPLY_FILTERS: yith_wcmap_get_items
			 *
			 * Filters the items.
			 *
			 * @param array $items Items.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_items', $this->items );
		}

		/**
		 * Get default items method
		 *
		 * @since  2.4.0
		 * @return array
		 */
		public function get_default_items() {
			/**
			 * APPLY_FILTERS: yith_wcmap_get_default_items
			 *
			 * Filters the default items.
			 *
			 * @param array $default_items Default items.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_default_items', $this->default_items );
		}

		/**
		 * Get plugins items method
		 *
		 * @since  2.4.0
		 * @return array
		 */
		public function get_plugins_items() {
			/**
			 * APPLY_FILTERS: yith_wcmap_get_plugins_items
			 *
			 * Filters the plugins items.
			 *
			 * @param array $plugins_items Plugins items.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_plugins_items', $this->plugins_items );
		}

		/**
		 * Get item type array
		 *
		 * @since 3.12.0
		 * @return array
		 */
		public function get_items_types() {
			/**
			 * APPLY_FILTERS: yith_wcmap_get_items_types
			 *
			 * Filters the item types.
			 *
			 * @param array $item_types Item types.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_items_types', $this->items_types );
		}

		/**
		 * Get a single item
		 *
		 * @since  3.0.0
		 * @param string $key The item key.
		 * @param array  $items (Optional) An array of items where search.
		 * @return array
		 */
		public function get_single_item( $key, $items = array() ) {

			if ( empty( $items ) ) {
				$items = $this->get_items();
			}

			foreach ( $items as $item_key => $options ) {
				if ( $item_key === $key ) {
					return $options;
				}

				if ( ! empty( $options['children'] ) ) {
					$child = $this->get_single_item( $key, $options['children'] );
					if ( ! empty( $child ) ) {
						return $child;
					}
				}
			}

			return array();
		}

		/**
		 * Get items slug
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_items_slug() {
			$slugs = array();
			foreach ( $this->get_items() as $key => $field ) {
				if ( isset( $field['slug'] ) ) {
					$slugs[ $key ] = $field['slug'];
				}

				if ( isset( $field['children'] ) ) {
					foreach ( $field['children'] as $child_key => $child ) {
						if ( isset( $child['slug'] ) ) {
							$slugs[ $child_key ] = $child['slug'];
						}
					}
				}
			}

			return $slugs;
		}

		/**
		 * Get items keys
		 *
		 * @since  1.0.0
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
		 * Get plugins items method
		 *
		 * @since  2.4.0
		 * @param string $key The plugin item key.
		 * @param array  $data The plugin item data.
		 */
		public function register_plugin_item( $key, $data ) {
			$this->plugins_items[ $key ] = $data;
		}

		/**
		 * Init items
		 *
		 * @since  2.4.0
		 * @param boolean $force Force the init.
		 */
		public function init( $force = false ) {

			if ( ! empty( $this->items ) && ! $force ) {
				return;
			}

			// Init default items.
			$this->init_default_items();
			// Get saved endpoints order.
			$items = get_option( 'yith_wcmap_endpoint', array() );
			$items = ! empty( $items ) ? json_decode( $items, true ) : $this->get_default_endpoint_option();

			// Let's filter item before process.
			/**
			 * APPLY_FILTERS: yith_wcmap_get_before_initialization
			 *
			 * Filters the items before initialization.
			 *
			 * @param array $items Items.
			 *
			 * @return array
			 */
			$items = apply_filters( 'yith_wcmap_get_before_initialization', $items );
			// Double check items after filter to prevent errors.
			if ( empty( $items ) || ! is_array( $items ) ) {
				$items = array();
			}

			$this->items = array();
			// Get default endpoints.
			$defaults = array_merge( $this->default_items, $this->plugins_items );

			if ( empty( $items ) ) {
				$this->items = $defaults;
			} else {

				foreach ( $items as $id => $item_option ) {

					// Build return array.
					$this->items[ $id ] = array();

					$options = get_option( 'yith_wcmap_endpoint_' . $id, array() );

					if ( is_array( $options ) ) {

						if ( empty( $item_option['type'] ) ) {
							$item_option['type'] = 'endpoint';
						}
						$options_default = $this->get_item_default_options( $id, $item_option['type'] );
						// Is empty check on default endpoint.
						if ( empty( $options ) && isset( $defaults[ $id ] ) ) {
							$options = $defaults[ $id ];
						}
						// Always merge with default.
						$options = array_merge( $options_default, $options );

						if ( isset( $item_option['children'] ) ) {

							$children = array();

							foreach ( $item_option['children'] as $child_id => $child ) {
								$child_options   = get_option( 'yith_wcmap_endpoint_' . $child_id, array() );
								$options_default = $this->get_item_default_options( $child_id, $child['type'] );
								// Is empty check on default endpoint.
								if ( empty( $child_options ) && isset( $defaults[ $id ] ) ) {
									$child_options = $defaults[ $id ];
								}
								// Always merge with default.
								$children[ $child_id ] = is_array( $child_options ) ? array_merge( $options_default, $child_options ) : $options_default;
								// Check child on default plugin.
								unset( $defaults[ $child_id ] );
							}

							$options['children'] = $children;
						}

						// Unset on defaults.
						unset( $defaults[ $id ] );

						$this->items[ $id ] = $options;
					}
				}

				// Merge with defaults again.
				$this->items = array_merge( $this->items, $defaults );
			}
		}

		/**
		 * Get default item options
		 *
		 * @since 3.12.0
		 * @param string $id The item ID.
		 * @param string $type (Optional) The item type. Default is endpoint.
		 * @return array
		 */
		protected function get_item_default_options( $id, $type = 'endpoint' ) {
			return function_exists( "yith_wcmap_get_default_{$type}_options" ) ? call_user_func( "yith_wcmap_get_default_{$type}_options", $id ) : array();
		}

		/**
		 * Get the default endpoint
		 *
		 * @since 3.12.0
		 * @return array
		 */
		protected function get_default_endpoint_option() {
			$defaults = array_merge( $this->default_items, $this->plugins_items );
			if ( empty( $defaults ) ) {
				return array();
			}

			return array_fill_keys( array_keys( $defaults ), array( 'type' => 'endpoint' ) );
		}

		/**
		 * Init default items
		 *
		 * @since  2.4.0
		 * @access protected
		 */
		protected function init_default_items() {

			if ( ! empty( $this->default_items ) ) {
				return;
			}

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
			if ( ! is_array( $menu_items_endpoint ) ) {
				$menu_items_endpoint = array();
			}
			$endpoints = array_merge( $endpoints, $menu_items_endpoint );

			if ( class_exists( 'WC_Subscriptions' ) ) {
				unset( $endpoints['subscriptions'] );
			}

			$registered_endpoint = WC()->query->get_query_vars();
			$this->default_items = array();

			// Populate endpoints array with options.
			foreach ( $endpoints as $endpoint_key => $endpoint_label ) {

				// Always exclude customer logout or endpoint not in menu if are not wc default.
				if ( 'customer-logout' === $endpoint_key || ( 'dashboard' !== $endpoint_key && ! array_key_exists( $endpoint_key, $registered_endpoint ) ) ) {
					continue;
				}

				$slug    = isset( $registered_endpoint[ $endpoint_key ] ) ? $registered_endpoint[ $endpoint_key ] : $endpoint_key;
				$options = yith_wcmap_get_default_endpoint_options( $slug );
				// Set label.
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
					case 'payment-methods':
						$options['icon'] = 'money';
						break;
					default:
						break;
				}

				$this->default_items[ $endpoint_key ] = $options;
			}
		}

		/**
		 * Add custom endpoints to main WC array
		 *
		 * @access public
		 * @since  3.0.0
		 */
		public function add_custom_endpoints() {
			$slugs = $this->get_items_slug();
			if ( empty( $slugs ) || ! is_array( $slugs ) ) {
				return;
			}

			$mask = WC()->query->get_endpoints_mask();

			foreach ( $slugs as $key => $slug ) {
				/**
				 * APPLY_FILTERS: yith_wcmap_skip_add_rewrite_endpoint
				 *
				 * Filters whether to skip rewriting endpoint.
				 *
				 * @param bool   $skip_add_rewrite_endpoint Whether to skip rewriting endpoint or not.
				 * @param string $key                       Endpoint key.
				 *
				 * @return bool
				 */
				if ( apply_filters( 'yith_wcmap_skip_add_rewrite_endpoint', false, $key ) || 'dashboard' === $key || isset( WC()->query->query_vars[ $key ] ) ) {
					continue;
				}

				WC()->query->query_vars[ $key ] = $slug;
				add_rewrite_endpoint( $slug, $mask );
			}
		}

		/**
		 * Rewrite rules
		 *
		 * @access public
		 * @since  1.0.0
		 * @param boolean $force (Optional) True to force flush, false otherwise.
		 */
		public function rewrite_rules( $force = false ) {
			$do_flush = get_option( 'yith_wcmap_flush_rewrite_rules', 1 );

			if ( $do_flush || $force ) {
				// Change option.
				update_option( 'yith_wcmap_flush_rewrite_rules', 0 );
				// Flush rewrite rules.
				flush_rewrite_rules();
			}
		}

		/**
		 * Change Active status given item
		 *
		 * @access public
		 * @since 3.0.0
		 * @param string  $item_key The item key.
		 * @param boolean $active True if item is active, false otherwise.
		 * @return boolean
		 */
		public function change_status_item( $item_key, $active = true ) {

			// Check if given item key exists.
			if ( ! in_array( $item_key, $this->get_items_keys(), true ) ) {
				return false;
			}

			$options           = get_option( 'yith_wcmap_endpoint_' . $item_key );
			$options['active'] = $active;
			update_option( 'yith_wcmap_endpoint_' . $item_key, $options );
			// Re-init items.
			$this->init( true );

			return true;
		}

		/**
		 * Add a new item
		 *
		 * @access public
		 * @since  3.0.0
		 * @param string $item_type The item type.
		 * @param array  $options The item options.
		 * @return boolean
		 */
		public function add_item( $item_type, $options ) {

			$slug  = ! empty( $options['slug'] ) ? yith_wcmap_sanitize_item_key( $options['slug'] ) : '';
			$label = ! empty( $options['label'] ) ? stripslashes( $options['label'] ) : '';

			if ( ! in_array( $item_type, $this->get_items_types(), true ) || empty( $label ) || ( 'endpoint' === $item_type && empty( $slug ) ) ) {
				return false;
			}

			$item_key_base = $slug ? $slug : $label;
			$item_key      = $item_key_base ? $this->generate_unique_item_key( $item_key_base ) : '';
			if ( ! $item_key ) {
				return false;
			}

			// Merge data with default.
			$default = call_user_func( "yith_wcmap_get_default_{$item_type}_options", $item_key );
			$options = array_merge( $default, $options );
			// Save new item data.
			$this->save_item( $item_key, $item_type, $options, false );

			// Get saved endpoints order.
			$items = get_option( 'yith_wcmap_endpoint', '' );
			$items = json_decode( $items, true );

			$items[ $item_key ] = array(
				'type' => $item_type,
			);

			update_option( 'yith_wcmap_endpoint', wp_json_encode( $items ) );
			$this->init( true );

			return $item_key;
		}

		/**
		 * Get and save the endpoint options
		 *
		 * @access public
		 * @since  3.0.0
		 * @param string  $item_key The item key.
		 * @param string  $item_type The item type.
		 * @param array   $data The item data.
		 * @param boolean $init True for re-init items, false otherwise.
		 * @return boolean
		 */
		public function save_item( $item_key, $item_type, $data, $init = true ) {

			$item_key       = yith_wcmap_sanitize_item_key( $item_key );
			$data['label']  = ! empty( $data['label'] ) ? stripslashes( $data['label'] ) : '';
			$data['active'] = isset( $data['active'] );

			switch ( $item_type ) {
				case 'group':
					$data['open'] = isset( $data['open'] );
					break;
				case 'link':
					$data['url']          = ! empty( $data['url'] ) ? esc_url_raw( $data['url'] ) : '#';
					$data['target_blank'] = isset( $data['target_blank'] );
					break;
				default:
					$data['slug']    = ! empty( $data['slug'] ) ? yith_wcmap_sanitize_item_key( $data['slug'] ) : $item_key;
					$data['content'] = ! empty( $data['content'] ) ? $data['content'] : '';

					update_option( 'woocommerce_myaccount_' . str_replace( '-', '_', $item_key ) . '_endpoint', $data['slug'] );
					break;
			}

			update_option( 'yith_wcmap_endpoint_' . $item_key, $data );
			// Re-init if requested.
			$init && $this->init( true );

			return true;
		}

		/**
		 * Reset to default option the given item
		 *
		 * @since 3.12.0
		 * @param string $id The item id to reset.
		 * @return boolean True on success, false otherwise.
		 */
		public function reset_item( $id ) {
			if ( delete_option( 'yith_wcmap_endpoint_' . $id ) ) {
				// Delete wc options if any.
				delete_option( 'woocommerce_myaccount_' . str_replace( '-', '_', $id ) . '_endpoint' );
				return true;
			}

			return false;
		}

		/**
		 * Remove given item
		 *
		 * @access public
		 * @since 3.0.0
		 * @param string $remove_key The item key to remove.
		 * @return boolean
		 */
		public function remove_item( $remove_key ) {

			$this->reset_item( $remove_key );

			// Do not go further if given item is a default one or a plugin one.
			if ( array_key_exists( $remove_key, $this->get_default_items() ) || array_key_exists( $remove_key, $this->get_plugins_items() ) ) {
				return false;
			}

			// Get saved endpoints order.
			$items = get_option( 'yith_wcmap_endpoint', '' );
			$items = json_decode( $items, true );

			foreach ( $items as $item_key => &$item ) {
				if ( $item_key === $remove_key ) {
					unset( $items[ $item_key ] );
					break;
				}

				if ( isset( $item['children'] ) ) {
					foreach ( $item['children'] as $child_key => $child ) {
						if ( $child_key === $remove_key ) {
							unset( $item['children'][ $child_key ] );
							break;
						}
					}
				}
			}

			update_option( 'yith_wcmap_endpoint', wp_json_encode( $items ) );
			// Re-init items.
			$this->init( true );

			return true;
		}

		/**
		 * Generate an unique item key from a base
		 *
		 * @since 3.0.0
		 * @param string  $base The key base.
		 * @param integer $iteration The number of iterations.
		 * @return string
		 */
		public function generate_unique_item_key( $base, $iteration = 0 ) {
			$key = yith_wcmap_sanitize_item_key( $base );
			if ( in_array( $key, $this->get_items_keys(), true ) ) {
				$base .= ' ' . substr( 'abcdefghijklmnopqrstuvwxyz', wp_rand( 0, 26 ), $iteration );

				return $this->generate_unique_item_key( $base, ++ $iteration );
			}

			return $key;
		}
	}
}
