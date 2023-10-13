<?php
/**
 * Class YITH_WCBK_Product_Data_Extension
 * Allow extending product data.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Product_Data_Extension' ) ) {
	/**
	 * Class YITH_WCBK_Product_Data_Extension
	 */
	abstract class YITH_WCBK_Product_Data_Extension {
		use YITH_WCBK_Multiple_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			// Admin product tabs and saving data.
			$tabs               = $this->get_tabs();
			$internal_meta_keys = $this->get_internal_meta_keys();

			if ( $tabs ) {
				add_filter( 'yith_wcbk_booking_product_sub_tabs', array( $this, 'filter_product_sub_tabs' ) );
				add_action( 'woocommerce_product_data_panels', array( $this, 'print_product_data_panels' ) );
			}

			add_action( 'yith_wcbk_process_bookable_product_meta', array( $this, 'set_product_meta_before_saving' ) );

			// Data store.
			add_filter( 'yith_wcbk_product_data_store_update_props', array( $this, 'update_product_props' ), 10, 3 );
			add_action( 'yith_wcbk_product_data_store_read_data', array( $this, 'read_product_data' ), 10, 1 );
			add_action( 'yith_wcbk_product_data_store_updated_props', array( $this, 'handle_product_updated_props' ), 10, 2 );
			if ( $internal_meta_keys ) {
				add_filter( 'yith_wcbk_product_data_store_internal_meta_keys', array( $this, 'filter_product_internal_meta_keys' ), 10, 1 );
			}

			// Delete.
			add_action( 'delete_post', array( $this, 'handle_delete_post' ), 10, 1 );
		}

		/*
		|--------------------------------------------------------------------------
		| Methods to override.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array();
		}

		/**
		 * Save booking product meta for resources.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Triggered before updating product props.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		protected function before_updating_product_props( WC_Product_Booking $product ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Triggered before updating product props.
		 *
		 * @param mixed              $value   The value.
		 * @param string             $prop    The prop.
		 * @param WC_Product_Booking $product The booking product.
		 *
		 * @return mixed The sanitized value.
		 */
		protected function sanitize_prop_value_before_saving( $value, string $prop, WC_Product_Booking $product ) {
			// Do nothing! You can use it by overriding.
			return $value;
		}

		/**
		 * Update product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 * @param bool               $force   Force flag.
		 *
		 * @return array Array of updated props.
		 */
		protected function update_product_extra_data( WC_Product_Booking $product, bool $force ): array {
			// Do nothing! You can use it by overriding.
			return array();
		}

		/**
		 * Read product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		protected function read_product_extra_data( WC_Product_Booking $product ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Handle updated props.
		 *
		 * @param WC_Product_Booking $product       The booking product.
		 * @param array              $updated_props The updated props.
		 */
		public function handle_product_updated_props( WC_Product_Booking $product, array $updated_props ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Handle product delete.
		 *
		 * @param int $product_id The product ID.
		 */
		protected function handle_product_delete( int $product_id ) {
			// Do nothing! You can use it by overriding.
		}

		/*
		|--------------------------------------------------------------------------
		| Settings
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get single settings.
		 *
		 * @param string      $key     The key.
		 * @param mixed|false $default Default value.
		 *
		 * @return false|mixed
		 */
		final protected function get_single_settings( string $key, $default = false ) {
			$settings = $this->get_settings();

			return $settings[ $key ] ?? $default;
		}

		/**
		 * Get tabs.
		 *
		 * @return array
		 */
		final protected function get_tabs(): array {
			return $this->get_single_settings( 'tabs', array() );
		}

		/**
		 * Get meta_keys_to_props.
		 *
		 * @return array
		 */
		final protected function get_meta_keys_to_props(): array {
			return $this->get_single_settings( 'meta_keys_to_props', array() );
		}

		/**
		 * Get internal_meta_keys.
		 *
		 * @return array
		 */
		final protected function get_internal_meta_keys(): array {
			return $this->get_single_settings( 'internal_meta_keys', array() );
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks handlers
		|--------------------------------------------------------------------------
		*/

		/**
		 * Add product tabs
		 *
		 * @param array $tabs Product tabs.
		 *
		 * @return array
		 */
		public function filter_product_sub_tabs( $tabs ) {
			$extended_tabs = $this->get_single_settings( 'tabs', array() );

			foreach ( $extended_tabs as $key => $extended_tab ) {
				$wc_key = $extended_tab['wc_key'] ?? '';
				$tab    = $extended_tab['tab'] ?? array();
				if ( $wc_key && $tab ) {
					$tabs[ $wc_key ] = $tab;
				}
			}

			return $tabs;
		}

		/**
		 * Add data panels to products
		 */
		public function print_product_data_panels() {
			$extended_tabs = $this->get_single_settings( 'tabs', array() );

			/**
			 * Product object.
			 *
			 * @var WC_Product $product_object
			 */
			global $post, $product_object;

			$prod_type = YITH_WCBK_Product_Post_Type_Admin::$prod_type;
			$args      = array(
				'post_id'         => $post->ID,
				'prod_type'       => $prod_type,
				'booking_product' => $product_object->is_type( $prod_type ) ? $product_object : false,
				'product_object'  => $product_object,
				'post'            => $post,
			);

			foreach ( $extended_tabs as $key => $extended_tab ) {
				$id     = $extended_tab['id'] ?? '';
				$module = $extended_tab['module'] ?? '';
				if ( $id ) {
					echo '<div id="' . esc_attr( $id ) . '" class="panel woocommerce_options_panel">';
					if ( $module ) {
						yith_wcbk_get_module_view( $module, 'product-tabs/' . $key . '-tab.php', $args );
					} else {
						yith_wcbk_get_view( 'product-tabs/html-' . $key . '-tab.php', $args );
					}
					echo '</div>';
				}
			}
		}

		/**
		 * Save booking product meta for resources.
		 *
		 * @param array              $updated_props Updated props.
		 * @param WC_Product_Booking $product       The booking product.
		 * @param bool               $force         Force flag.
		 */
		public function update_product_props( $updated_props, $product, $force ) {
			$this->before_updating_product_props( $product );

			$meta_keys_to_props   = $this->get_meta_keys_to_props();
			$props_to_update      = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );
			$custom_updated_props = array();

			foreach ( $props_to_update as $meta_key => $prop ) {
				if ( is_callable( array( $product, "get_$prop" ) ) ) {
					$value = $product->{"get_$prop"}( 'edit' );
					$value = $this->sanitize_prop_value_before_saving( $value, $prop, $product );

					$updated = update_post_meta( $product->get_id(), $meta_key, $value );

					if ( $updated ) {
						$custom_updated_props[] = $prop;
					}
				}
			}

			$extra_updated_props = $this->update_product_extra_data( $product, $force );

			if ( $extra_updated_props ) {
				$custom_updated_props = array_merge( $custom_updated_props, $extra_updated_props );
			}

			if ( $custom_updated_props ) {
				$updated_props = array_merge( $updated_props, $custom_updated_props );
			}

			return $updated_props;
		}

		/**
		 * Gets a list of props and meta keys that need updated based on change state
		 * or if they are present in the database or not.
		 *
		 * @param WC_Product_Booking $product           The product.
		 * @param array              $meta_key_to_props A mapping of meta keys => prop names.
		 * @param string             $meta_type         The internal WP meta type (post, user, etc).
		 *
		 * @return array                        A mapping of meta keys => prop names, filtered by ones that should be updated.
		 */
		protected function get_props_to_update( WC_Product_Booking $product, array $meta_key_to_props, string $meta_type = 'post' ): array {
			$props_to_update = array();
			$changed_props   = $product->get_changes();

			// Props should be updated if they are a part of the $changed array or don't exist yet.
			foreach ( $meta_key_to_props as $meta_key => $prop ) {
				if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $product->get_id(), $meta_key ) ) {
					$props_to_update[ $meta_key ] = $prop;
				}
			}

			return $props_to_update;
		}

		/**
		 * Read product data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function read_product_data( WC_Product_Booking $product ) {
			$meta_keys_to_props = $this->get_meta_keys_to_props();
			$post_meta_values   = get_post_meta( $product->get_id() );
			$props_to_set       = array();

			foreach ( $meta_keys_to_props as $meta_key => $prop ) {
				$meta_value            = $post_meta_values[ $meta_key ][0] ?? null;
				$props_to_set[ $prop ] = maybe_unserialize( $meta_value );
			}

			$product->set_props( $props_to_set );

			$this->read_product_extra_data( $product );
		}

		/**
		 * Add meta keys to internal ones for bookable products.
		 *
		 * @param array $internal_meta_keys The internal meta keys.
		 *
		 * @return array
		 */
		public function filter_product_internal_meta_keys( array $internal_meta_keys ): array {
			$to_add = $this->get_internal_meta_keys();

			return array_merge( $internal_meta_keys, $to_add );
		}

		/**
		 * Handle post delete
		 *
		 * @param int $id ID of post being deleted.
		 *
		 * @since 4.0.0
		 */
		public function handle_delete_post( $id ) {
			if ( ! $id ) {
				return;
			}

			if ( 'product' === get_post_type( $id ) ) {
				$this->handle_product_delete( $id );
			}
		}
	}
}
