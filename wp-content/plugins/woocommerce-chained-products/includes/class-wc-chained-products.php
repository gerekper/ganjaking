<?php
/**
 * Main class to handle mainly frontend related chained products actions
 *
 * @since       2.5.0
 * @version     1.3.0
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Chained_Products' ) ) {

	/**
	 * WC Chained Products Frontend
	 */
	class WC_Chained_Products {

		/**
		 * The Chained Products unit.
		 *
		 * @var array
		 */
		public $cp_units = array();

		/**
		 * The Chained Product items.
		 *
		 * @var array
		 */
		public $chained_items = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();
			$this->cp_include_files();

			add_action( 'init', array( $this, 'load_chained_products' ) );

			// Filter for validating cart based on availability of chained products.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_chained_add_to_cart_validation' ), 10, 3 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_chained_update_cart_validation' ), 10, 4 );

			// Action to add or remove actions & filter specific to chained products.
			add_action( 'add_chained_products_actions_filters', array( $this, 'add_chained_products_actions_filters' ) );
			add_action( 'remove_chained_products_actions_filters', array( $this, 'remove_chained_products_actions_filters' ) );

			// Action for checking cart items including Chained products.
			add_action( 'woocommerce_check_cart_items', array( $this, 'woocommerce_chained_check_cart_items' ) );

			// Filter to hide "Add to cart" button if chained products are out of stock.
			add_filter( 'woocommerce_get_availability', array( $this, 'woocommerce_get_chained_products_availability' ), 10, 2 );

			// Action to add chained product to cart.
			add_action( 'woocommerce_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 6 );
			add_action( 'woocommerce_mnm_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 7 );
			add_action( 'woocommerce_bundled_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 99, 7 );
			add_action( 'woocommerce_composited_add_to_cart', array( $this, 'add_chained_products_to_cart' ), 10, 7 );

			// Action for updating chained product quantity in cart.
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'sa_after_cart_item_quantity_update' ), 1, 2 );
			if ( Chained_Products_WC_Compatibility::is_wc_gte_37() ) {
				add_action( 'woocommerce_remove_cart_item', array( $this, 'sa_before_cart_item_quantity_zero' ), 1 );
			} else {
				add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'sa_before_cart_item_quantity_zero' ), 1 );
			}

			add_action( 'woocommerce_cart_updated', array( $this, 'validate_and_update_chained_product_quantity_in_cart' ) );

			// Don't allow chained products to be removed or change quantity.
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'chained_cart_item_remove_link' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'chained_cart_item_quantity' ), 10, 2 );

			// Filter for getting cart item from session.
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_chained_cart_item_from_session' ), 10, 2 );

			// remove/restore chained cart items when parent is removed/restored.
			add_action( 'woocommerce_cart_item_removed', array( $this, 'chained_cart_item_removed' ), 10, 2 );
			add_action( 'woocommerce_cart_item_restored', array( $this, 'chained_cart_item_restored' ), 10, 2 );

			// Filters for manage stock availability and max value of input args.
			add_filter( 'woocommerce_get_availability', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_quantity_input_max', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_data_max', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );
			add_filter( 'woocommerce_quantity_input_args', array( $this, 'validate_stock_availability_of_chained_products' ), 10, 2 );

			// Action for removing price of chained products before calculating totals.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_before_chained_calculate_totals' ) );

			// Chained product list on shop page.
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_chained_products_for_variable_product' ) );
			add_action( 'wp_ajax_nopriv_get_chained_products_html_view', array( $this, 'get_chained_products' ) );
			add_action( 'wp_ajax_get_chained_products_html_view', array( $this, 'get_chained_products' ) );

			// Register Chained Products Shortcode.
			add_action( 'init', array( $this, 'register_chained_products_shortcodes' ) );

			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'sa_cart_chained_item_subtotal' ), 11, 3 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'sa_cart_chained_item_subtotal' ), 11, 3 );

			add_filter( 'woocommerce_cart_item_class', array( $this, 'sa_cart_chained_item_class' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'sa_cart_chained_item_name' ), 10, 3 );
			add_filter( 'woocommerce_admin_html_order_item_class', array( $this, 'sa_admin_html_chained_item_class' ), 10, 2 );

			add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'sa_order_chained_item_subtotal' ), 10, 3 );

			add_filter( 'woocommerce_order_item_class', array( $this, 'sa_order_chained_item_class' ), 10, 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'sa_order_chained_item_name' ), 10, 2 );

			// Show Chained Products on Cart.
			add_filter( 'woocommerce_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			// Show Chained Products on Mini Cart.
			add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			// Show Chained Products on Checkout.
			add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'sa_chained_item_visible' ), 10, 3 );
			// Show Chained Products on Order Received page + email order items.
			add_filter( 'woocommerce_order_item_visible', array( $this, 'sa_order_chained_item_visible' ), 10, 2 );

			add_action( 'admin_footer', array( $this, 'chained_products_admin_css' ) );
			add_action( 'wp_footer', array( $this, 'chained_products_frontend_css' ) );

			add_action( 'get_header', array( $this, 'sa_chained_theme_header' ) );

			$do_housekeeping = get_option( 'sa_chained_products_housekeeping', 'yes' );

			if ( 'yes' === $do_housekeeping ) {
				add_action( 'trashed_post', array( $this, 'sa_chained_on_trash_post' ) );
				add_action( 'untrashed_post', array( $this, 'sa_chained_on_untrash_post' ) );

				// Refactored for variation product deletion since varations are directly deleted instead of being trashed.
				add_action( 'woocommerce_before_delete_product_variation', array( $this, 'sa_chained_on_trash_post' ) );
				add_action( 'woocommerce_before_delete_product', array( $this, 'sa_chained_on_trash_post' ) );
			}

			add_filter( 'woocommerce_order_get_items', array( $this, 'sa_cp_ignore_chained_child_items_on_manual_pay' ), 99, 2 );

			add_filter( 'woocommerce_coupon_get_items_to_validate', array( $this, 'sa_exclude_chained_items_from_being_validated' ), 15, 2 );

			add_filter( 'woocommerce_cart_item_price', array( $this, 'sa_cp_set_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'sa_cp_set_cart_item_subtotal' ), 10, 3 );

			add_filter( 'woocommerce_show_variation_price', array( $this, 'sa_cp_show_variation_price' ), 10, 3 );

			// Display price for all products excepts variable product.
			add_filter( 'woocommerce_get_price_html', array( $this, 'sa_cp_set_price_html' ), 7, 2 );
			// Display price for variable product.
			add_filter( 'woocommerce_variable_price_html', array( $this, 'sa_cp_set_variable_price_html' ), 7, 2 );

			add_filter( 'woocommerce_bundled_item_raw_price', array( $this, 'sa_cp_set_bundled_item_raw_price' ), 20, 4 );

			add_filter( 'get_product_addons', array( $this, 'sa_cp_ignore_addons_for_chained_items' ), 10, 1 );

			// Control stock for chained parent based on chained items.
			add_filter( 'woocommerce_product_is_in_stock', array( $this, 'chained_products_is_in_stock' ), 10, 2 );

			// Action for WooCommerce v7.1 custom order tables related compatibility.
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		}

		/**
		 * Get plugins data
		 *
		 * @return array
		 */
		public static function get_plugin_data() {

			if ( ! function_exists( 'get_plugin_data' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return get_plugin_data( WC_CP_PLUGIN_FILE );
		}

		/**
		 * Define Constants.
		 *
		 * @return void
		 */
		public function define_constants() {

			if ( ! defined( 'WC_CP_LIST_LINKED_PRODUCTS_PER_PAGE' ) ) {
				define( 'WC_CP_LIST_LINKED_PRODUCTS_PER_PAGE', 5 );
			}

		}

		/**
		 * Load plugin Localization files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/woocommerce-chained-products/woocommerce-chained-products-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/woocommerce-chained-products-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', determine_locale(), 'woocommerce-chained-products' );

			unload_textdomain( 'woocommerce-chained-products' );
			load_textdomain( 'woocommerce-chained-products', WP_LANG_DIR . '/woocommerce-chained-products/woocommerce-chained-products-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-chained-products', false, WC_CP_PLUGIN_DIRNAME . '/languages' );
		}

		/**
		 * Function to exclude adding of addons for chained items. ( Compatibility with WooCommerce Product Add-ons )
		 *
		 * @param array $addons Associated product addons.
		 * @return array
		 */
		public function sa_cp_ignore_addons_for_chained_items( $addons = array() ) {
			return ! empty( $this->cp_cart_item_data ) ? array() : $addons;
		}

		/**
		 * Function to return the chained products result.
		 *
		 * @return void.
		 */
		public function get_chained_products() {
			// prevent processing requests external of the site.
			check_ajax_referer( 'wc-cp-get-products', 'security' );

			if ( ! empty( $_POST ) ) {
				echo $this->get_chained_products_html_view( wc_clean( wp_unslash( $_POST ) ) ); // phpcs:ignore
			}

			wp_die();
		}

		/**
		 * Function to set bundle price. This price will then be used to calculate bundle total including chained item prices ( Compatibility with WooCommerce Product Bundles )
		 *
		 * @param mixed           $bundled_item_price Bundle price html.
		 * @param WC_Product      $product Product object.
		 * @param mixed           $discount Discount amount.
		 * @param WC_Bundled_Item $bundled_item Bundle item object.
		 * @return mixed $bundled_item_price
		 */
		public function sa_cp_set_bundled_item_raw_price( $bundled_item_price, $product, $discount, $bundled_item ) {
			$this->cp_bundle_item_raw_price = $bundled_item_price;

			return $bundled_item_price;
		}

		/**
		 * Function to get child product details by the product ids.
		 *
		 * @param array $product_ids              The product ids.
		 * @param bool  $is_child                 Whether the derails for child items.
		 * @param bool  $existing_product_data    The existing data if the function executes for the child items.
		 *
		 * @global wpdb $wpdb        The WordPress database object.
		 *
		 * @return array.
		 */
		public function get_chained_products_details( $product_ids = array(), $is_child = false, $existing_product_data = array() ) {

			global $wpdb;

			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return $existing_product_data;
			}

			$chained_product_ids = array();

			$products_data = array();

			// Set product ids to cache key to get the details of nested chained products.
			$cache_key = ( true === $is_child ) ? 'sa_cp_details' . implode( '_', $product_ids ) : 'sa_cp_details';

			// Fetch chained products id from cache.
			$chained_product_ids = wp_cache_get( $cache_key, 'woocommerce-chained-products' );

			// Fetch chained products id if the data not exists in cache.
			if ( empty( $chained_product_ids ) ) {
				$chained_products_data = $wpdb->get_results( // phpcs:ignore
					"SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta
					WHERE meta_key = '_chained_product_detail'
					AND meta_value IS NOT NULL
					AND meta_value != ''
					AND meta_value != 'a:0:{}'
					AND post_id IN (" . implode( ',', $product_ids ) . ')', // phpcs:ignore
					ARRAY_A
				);

				if ( ! empty( $chained_products_data ) && is_array( $chained_products_data ) ) {
					$chained_product_ids = array_column( $chained_products_data, 'meta_value', 'post_id' );
					if ( ! empty( $chained_product_ids ) ) {
						wp_cache_set( $cache_key, $chained_product_ids, 'woocommerce-chained-products' );
					}
				}
			}

			if ( ! empty( $chained_product_ids ) ) {
				foreach ( $chained_product_ids as $product_id => $chained_item_data ) {
					$details = maybe_unserialize( $chained_item_data );
					if ( ! empty( $details ) && is_array( $details ) ) {
						$items      = array();
						$new_cp_ids = array();
						foreach ( $details as $id => $detail ) {
							if ( ! empty( $detail['priced_individually'] ) && 'yes' === $detail['priced_individually'] ) {

								$unit = ! empty( $detail['unit'] ) ? intval( $detail['unit'] ) : 1;

								$prev_unit             = ! empty( $this->cp_units[ $id ] ) ? intval( $this->cp_units[ $id ] ) : 0;
								$this->cp_units[ $id ] = $prev_unit + $unit;

								$prev_product_wise_unit              = ! empty( $products_data[ $product_id ][ $id ] ) ? intval( $products_data[ $product_id ][ $id ] ) : 0;
								$products_data[ $product_id ][ $id ] = $prev_product_wise_unit + $unit;
								$new_cp_ids[]                        = $id;
							}
						}

						if ( ! empty( $new_cp_ids ) && is_array( $new_cp_ids ) && ! empty( $products_data ) && ! empty( $products_data[ $product_id ] ) ) {
							$new_cp_ids  = array_filter( $new_cp_ids );
							$nested_data = $this->get_chained_products_details( $new_cp_ids, true, $products_data[ $product_id ] );

							if ( ! empty( $nested_data ) && is_array( $nested_data ) ) {

								if ( $is_child ) {
									return $nested_data + $existing_product_data;
								}

								$products_data[ $product_id ] = $products_data[ $product_id ] + $nested_data;
							}
						}
					}
				}
			}

			return $is_child ? $existing_product_data : $products_data;
		}

		/**
		 * Function to get child product prices.
		 *
		 * @param array $product_ids The Product ids.
		 * @global wpdb $wpdb        The WordPress database object.
		 *
		 * @return array
		 */
		public function get_chained_product_prices( $product_ids = array() ) {

			global $wpdb;

			if ( empty( $product_ids ) ) {
				return array();
			}
			$products_data = $this->get_chained_products_details( $product_ids );

			if ( empty( $this->cp_units ) || ! is_array( $this->cp_units ) ) {
				return array();
			}

			$cp_prices = $wpdb->get_results( // phpcs:ignore
				"SELECT meta_value, meta_key, post_id FROM {$wpdb->prefix}postmeta
					WHERE meta_key IN ('_price','_regular_price')
					AND meta_value IS NOT NULL
					AND meta_value != ''
					AND meta_value > 0
					AND post_id IN (" . implode( ',', array_unique( array_keys( $this->cp_units ) ) ) . ')', // phpcs:ignore
				ARRAY_A
			);

			$chained_items = array();

			if ( ! empty( $cp_prices ) ) {
				foreach ( $cp_prices as $cp_price ) {
					if ( empty( $cp_price['post_id'] ) || empty( $cp_price['meta_key'] ) ) {
						continue;
					}
					$chained_items[ $cp_price['post_id'] ]                          = ! empty( $chained_items[ $cp_price['post_id'] ] ) ? $chained_items[ $cp_price['post_id'] ] : array();
					$chained_items[ $cp_price['post_id'] ][ $cp_price['meta_key'] ] = ! empty( $cp_price['meta_value'] ) ? floatval( $cp_price['meta_value'] ) : 0; // phpcs:ignore
				}
			}

			return array(
				'chained_items' => $chained_items,
				'products_data' => $products_data,
			);
		}

		/**
		 * Function to product prices.
		 *
		 * @param array $product_ids The Product ids.
		 * @global wpdb $wpdb        The WordPress database object.
		 *
		 * @return array
		 */
		public function get_product_prices( $product_ids = array() ) {

			global $wpdb;

			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return array();
			}

			$prices = $wpdb->get_results( // phpcs:ignore
				"SELECT meta_value, meta_key, post_id FROM {$wpdb->prefix}postmeta
					WHERE meta_key IN ('_price','_regular_price')
					AND meta_value IS NOT NULL
					AND meta_value != ''
					AND meta_value > 0
					AND post_id IN (" . implode( ',', $product_ids ) . ')', // phpcs:ignore
				ARRAY_A
			);

			$products = array();

			if ( ! empty( $prices ) && is_array( $prices ) ) {
				foreach ( $prices as $price ) {
					if ( empty( $price['post_id'] ) || empty( $price['meta_key'] ) ) {
						continue;
					}
					$products[ $price['post_id'] ][ $price['meta_key'] ] = ! empty( $price['meta_value'] ) ? floatval( $price['meta_value'] ) : 0; // phpcs:ignore
				}
			}

			return $products;
		}

		/**
		 * Function to set html price for variable product.
		 *
		 * @param string     $price Product price html.
		 * @param WC_Product $product Product object.
		 *
		 * @return string    The html formatted price.
		 */
		public function sa_cp_set_variable_price_html( $price = '', $product = null ) {

			if ( empty( $product ) || false === $product instanceof WC_Product_Variable ) {
				return $price;
			}

			return $this->sa_cp_get_price_html( $price, $product );
		}

		/**
		 * Function to set html price for a Products except variable product.
		 *
		 * @param string     $price Product price html.
		 * @param WC_Product $product Product object.
		 *
		 * @return string    The html formatted price.
		 */
		public function sa_cp_set_price_html( $price = '', $product = null ) {

			if ( empty( $product ) || $product instanceof WC_Product_Variable ) {
				return $price;
			}

			return $this->sa_cp_get_price_html( $price, $product );
		}

		/**
		 * Function to set html price for a Chained Parent Product. When price individually option is checked for a chained item the bundle price
		 * needs to be recalculated.
		 *
		 * @param string     $price Product price html.
		 * @param WC_Product $product Product object.
		 *
		 * @return string    $price
		 */
		public function sa_cp_get_price_html( $price = '', $product = null ) {

			if ( empty( $product ) || ( false === Chained_Products_WC_Compatibility::is_wc_gte_30() ) || $product instanceof WC_Product_Subscription || $product instanceof WC_Product_Variable_Subscription ) {
				return $price;
			}

			$main_product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : 0;

			if ( empty( $main_product_id ) ) {
				return $price;
			}

			$product_ids = array();

			if ( $product instanceof WC_Product_Variable ) {
				if ( is_callable( array( $product, 'get_children' ) ) ) {
					$product_ids = $product->get_children();
				}
			} else {
				if ( is_callable( array( $product, 'get_id' ) ) ) {
					$product_ids[] = $product->get_id();
				}
			}

			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return $price;
			}

			$override_price = false;

			$chained_data = $this->get_chained_product_prices( $product_ids );

			$all_chained_items = ! empty( $chained_data['chained_items'] ) ? $chained_data['chained_items'] : array();
			$products_data     = ! empty( $chained_data['products_data'] ) ? $chained_data['products_data'] : array();
			$product_prices    = $this->get_product_prices( $product_ids );

			$regular_prices = array();
			$prices         = array();

			foreach ( $product_ids as $product_id ) {

				if ( empty( $products_data[ $product_id ] ) ) {
					continue;
				}

				$chained_items = $products_data[ $product_id ];

				$cp_regular_price = 0;
				$cp_price         = 0;

				foreach ( $chained_items as $cp_id => $unit ) {
					// Set regular prices.
					$chained_item_data = ! empty( $all_chained_items[ $cp_id ] ) ? $all_chained_items[ $cp_id ] : array();

					if ( empty( $chained_item_data ) || empty( $chained_item_data['_price'] ) ) {
						continue;
					}

						// Set regular prices.
					if ( ! empty( $chained_item_data['_regular_price'] ) ) {
						$cp_regular_price += ( floatval( $chained_item_data['_regular_price'] ) * floatval( $unit ) );
					}

						// Set sale prices.
					if ( ! empty( $chained_item_data['_price'] ) ) {
						$cp_price += ( floatval( $chained_item_data['_price'] ) * floatval( $unit ) );
					}
				}

				// Calculate with product regular price.
				$regular_prices[ $product_id ] = ! empty( $product_prices[ $product_id ]['_regular_price'] ) ? ( floatval( $product_prices[ $product_id ]['_regular_price'] ) + floatval( $cp_regular_price ) ) : floatval( $cp_regular_price );

				// Calculate with product price.
				$prices[ $product_id ] = ! empty( $product_prices[ $product_id ]['_price'] ) ? ( floatval( $product_prices[ $product_id ]['_price'] ) + floatval( $cp_price ) ) : floatval( $cp_price );

				// Enable override the price.
				$override_price = true;
			}

			$main_product_regular_price = is_callable( array( $product, 'get_regular_price' ) ) ? floatval( $product->get_regular_price() ) : 0;
			$main_product_price         = is_callable( array( $product, 'get_price' ) ) ? floatval( $product->get_price() ) : 0;

			if ( true === $override_price ) {
				if ( $product instanceof WC_Product_Variable ) {

					$regular_prices[ $main_product_id ] = $main_product_regular_price;
					$prices[ $main_product_id ]         = $main_product_price;

					$min_price = min( $prices );
					$max_price = max( $prices );

					$product->cp_show_variation_price = true;

					if ( $min_price === $max_price ) {
						return wc_price( $max_price );
					} else {
						return wc_format_price_range( $min_price, $max_price );
					}
				}

				$total_regular_price = array_sum( $regular_prices );
				$total_price         = array_sum( $prices );

				return ( $total_regular_price > $total_price ) ? wc_format_sale_price( $total_regular_price, $total_price ) : wc_price( $total_regular_price );
			}

			return $price;
		}

		/**
		 * Function to show variation price. WooCommerce by default doesn't show the price of a variation if it's set to zero. We need to override this in case any of the variation
		 * has chained items linked to it with priced indivudally option enabled.
		 *
		 * @param bool                 $show_price Show price or not.
		 * @param WC_Product           $product Product object.
		 * @param WC_Product_Variation $variation Variation object.
		 * @return bool $price
		 */
		public function sa_cp_show_variation_price( $show_price, $product, $variation ) {
			if ( $product instanceof WC_Product_Variable && isset( $product->cp_show_variation_price ) && true === $product->cp_show_variation_price ) {
				$show_price = true;
			}

			return $show_price;
		}

		/**
		 * Function to re-calculate Price/Subtotal for Chained Parent Product in cart in case the chained item is priced individually.
		 *
		 * @param string $type Calculation type 'price'|'subtotal'.
		 * @param string $amount_html Cart item amount html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $amount_html
		 */
		public function get_cp_cart_item_amount( $type = '', $amount_html = '', $cart_item = array(), $cart_item_key = array() ) {

			if ( ! empty( $cart_item ) ) {
				$product         = $cart_item['data'];
				$product_id      = $product->get_id();
				$unit            = ( 'subtotal' === $type ) ? $cart_item['quantity'] : 1;
				$bundle_price    = 0;
				$override_amount = false;

				if ( is_callable( 'wc_pb_is_bundle_container_cart_item' ) && wc_pb_is_bundle_container_cart_item( $cart_item ) ) { // Compatibility with WooCommerce Product Bundles.
					$bundle_price = ( isset( $this->cp_bundle_item_raw_price ) ) ? $this->cp_bundle_item_raw_price : 0;
				}

				$chained_items = $this->get_chained_product_data_by_product_id( $product_id );

				if ( is_array( $chained_items ) && 0 < count( $chained_items ) ) {

					$value = 0;

					foreach ( $chained_items as $chained_item_id => $chained_item_data ) {

						if ( isset( $chained_item_data['priced_individually'] ) && 'yes' === $chained_item_data['priced_individually'] ) {

							$chained_product = wc_get_product( $chained_item_id );

							if ( $chained_product instanceof WC_Product ) {
								$chained_product_price = $chained_product->get_price();
								if ( ! empty( $chained_product_price ) ) {
									$value += $chained_product_price * $chained_item_data['unit'];
								}
								$override_amount = true;
							}
						}
					}

					if ( $override_amount ) {
						$product_price = ( WC()->cart->display_prices_including_tax() ) ? wc_get_price_including_tax( $product ) : wc_get_price_excluding_tax( $product );
						$amount_html   = wc_price( ( $product_price + $value + $bundle_price ) * $unit );
					}
				}
			}

			return $amount_html;

		}

		/**
		 * Function to set cart item subtotal.
		 *
		 * @param string $cart_item_subtotal Cart item subtotal html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $cart_item_subtotal
		 */
		public function sa_cp_set_cart_item_subtotal( $cart_item_subtotal, $cart_item, $cart_item_key ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$cart_item_subtotal = $this->get_cp_cart_item_amount( 'subtotal', $cart_item_subtotal, $cart_item, $cart_item_key );
			}

			return $cart_item_subtotal;
		}

		/**
		 * Function to set cart item price.
		 *
		 * @param string $cart_item_price Cart item price html.
		 * @param array  $cart_item Cart item data.
		 * @param array  $cart_item_key Cart item key.
		 * @return string $cart_item_price
		 */
		public function sa_cp_set_cart_item_price( $cart_item_price, $cart_item, $cart_item_key ) {
			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$cart_item_price = $this->get_cp_cart_item_amount( 'price', $cart_item_price, $cart_item, $cart_item_key );
			}

			return $cart_item_price;
		}

		/**
		 * Function to exclude chained items from being validated while applying coupon
		 *
		 * @param array        $items Items to validate.
		 * @param WC_Discounts $discounts Discounts object.
		 * @return array $items
		 */
		public function sa_exclude_chained_items_from_being_validated( $items, $discounts ) {
			foreach ( $items as $cart_item_key => $item ) {
				$cart_item           = $item->object;
				$priced_individually = ( ! empty( $cart_item['priced_individually'] ) ) ? $cart_item['priced_individually'] : 'no';
				if ( 'no' === $priced_individually ) {
					if ( isset( $cart_item['chained_item_of'] ) && ! empty( $cart_item['chained_item_of'] ) ) {
						unset( $items[ $cart_item_key ] );
					}
				}
			}

			return $items;
		}

		/**
		 * Function to load Chained Products
		 */
		public function load_chained_products() {

			$current_db_version = get_option( '_current_chained_product_db_version' );

			if ( version_compare( $current_db_version, '1.3', '<' ) || empty( $current_db_version ) ) {
				$this->cp_do_db_update();
			}

			$this->load_plugin_textdomain();
		}

		/**
		 * Function to include requires files
		 */
		public function cp_include_files() {
			include_once 'class-chained-products-wc-compatibility.php';
			include_once 'class-wcvs-cp-compatibility.php'; // Compatibility File For WooCommerce Variation Swatches and Photos Plugin.

			include_once 'class-cp-admin-welcome.php';
			include_once 'class-wc-cp-admin-notices.php';

			require 'class-wc-admin-chained-products.php';
		}

		/**
		 * Function for database updation on activation of plugin
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global int $blog_id
		 */
		public function cp_do_db_update() {
			global $wpdb, $blog_id;

			// For multisite table prefix.
			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}", 0 ); // phpcs:ignore
			} else {
				$blog_ids = array( $blog_id );
			}

			foreach ( $blog_ids as $id ) {

				if ( is_multisite() ) {
					switch_to_blog( $id ); // @codingStandardsIgnoreLine
				}

				if ( false === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_3();
				}

				if ( '1.3' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_3_8();
				}

				if ( '1.3.8' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_for_1_4();
				}

				if ( '1.4' === get_option( '_current_chained_product_db_version' ) ) {

					$this->database_update_after_1_3_8();
				}

				if ( is_multisite() ) {
					restore_current_blog();
				}
			}

			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) { // phpcs:ignore
				set_transient( '_chained_products_activation_redirect', 1, 30 );
			}
		}

		/**
		 * Database updation after version 1.3 for quantity bundle feature
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_3() {

			global $wpdb, $wc_chained_products;

			$old_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_ids' ), 'ARRAY_A' ); // phpcs:ignore

			if ( ! empty( $old_results ) ) {

				foreach ( $old_results as $result ) {

					if ( empty( $result['post_id'] ) || empty( $result['meta_value'] ) ) {
						continue;
					}

					$chained_product_detail = array();

					foreach ( maybe_unserialize( $result['meta_value'] ) as $id ) {

						$product_title = $wc_chained_products->get_product_title( $id );

						if ( empty( $product_title ) ) {
							continue;
						}

						$chained_product_detail[ $id ] = array(
							'unit'         => 1,
							'product_name' => $product_title,
						);

					}

					if ( empty( $chained_product_detail ) ) {
						continue;
					}

					// For variable product - update all variation according to parent product.
					$variable_product = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = %d", $result['post_id'] ), 'ARRAY_A' ); // db call ok; no-cache ok.

					if ( empty( $variable_product ) ) {
						$product_obj = wc_get_product( intval( $result['post_id'] ) );
						$product_obj->update_meta_data( '_chained_product_detail', $chained_product_detail );
						$product_obj->save();
					} else {
						foreach ( $variable_product as $value ) {
							if ( empty( $value['ID'] ) ) {
								continue;
							}
							$variable_product_obj = wc_get_product( $value['ID'] );
							$variable_product_obj->update_meta_data( $value['ID'], '_chained_product_detail', $chained_product_detail );
							$variable_product_obj->save();
						}
					}
				}
			}

			update_option( '_current_chained_product_db_version', '1.3', 'no' );

		}

		/**
		 * Database updation to include shortcode in post_content when activated
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_3_8() {

			global $wpdb, $wc_chained_products;

			$results  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // phpcs:ignore
			$post_ids = array_map( 'current', $results );

			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {

					$cp_ids[] = $wc_chained_products->get_parent( $post_id );
				}

				$post_ids = implode( ',', array_unique( $cp_ids ) );

				$shortcode  = '<h3>' . __( 'Included Products', 'woocommerce-chained-products' ) . '</h3><br />';
				$shortcode .= __( 'When you order this product, you get all the following products for free!!', 'woocommerce-chained-products' );
				$shortcode .= '[chained_products]';

				$wpdb->query( // phpcs:ignore
					"UPDATE {$wpdb->prefix}posts
							SET post_content = concat( post_content , '$shortcode')
							WHERE ID IN( $post_ids )"
				); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching WordPress.DB.PreparedSQL.NotPrepared WPCS: unprepared SQL ok.
			}

			update_option( '_current_chained_product_db_version', '1.3.8', 'no' );
		}

		/**
		 * Database updation to restore shortcode after version 1.3.8
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_after_1_3_8() {

			global $wpdb, $wc_chained_products;

			$cp_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // phpcs:ignore

			if ( ! empty( $cp_results ) ) {

				foreach ( $cp_results as $value ) {

					$cp_ids[] = $wc_chained_products->get_parent( $value['post_id'] );
				}

				if ( ! ( is_array( $cp_ids ) && count( $cp_ids ) > 0 ) ) {
					return;
				}

				$cp_results = array_unique( $cp_ids );
				$sc_results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_shortcode' ), 'ARRAY_A' ); // phpcs:ignore
				$post_ids   = array_intersect( $cp_results, array_map( 'current', $sc_results ) );

				if ( ! empty( $post_ids ) ) {

					foreach ( $post_ids as $post_id ) {

						foreach ( $sc_results as $result ) {

							if ( $result['post_id'] === $post_id ) {

								$shortcode[ $post_id ] = $result['meta_value'];
								break;

							}
						}
					}

					$query_case = array();

					foreach ( $shortcode as $id => $meta_value ) {

						$query_case[] = 'WHEN ' . $id . " THEN CONCAT( post_content, '" . $wpdb->_real_escape( $meta_value ) . "')";

					}

					$shortcode_query = " UPDATE {$wpdb->prefix}posts
									SET post_content = CASE ID " . implode( "\n", $query_case ) . '
									END
									WHERE ID IN ( ' . implode( ',', $post_ids ) . ' )
									';

					$wpdb->query( // phpcs:ignore
									$shortcode_query // phpcs:ignore
					); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching WordPress.DB.PreparedSQL.NotPrepared.

				}

				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_shortcode' ) ); // phpcs:ignore
			}

			update_option( '_current_chained_product_db_version', '1.5', 'no' );
		}

		/**
		 * Add chained product's parent's information in order containing chained products
		 *
		 * @global wpdb $wpdb WordPress Database Object
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function database_update_for_1_4() {

			global $wpdb, $wc_chained_products;

			$cp_results  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ), 'ARRAY_A' ); // phpcs:ignore
			$product_ids = array_map( 'current', $cp_results );
			$inserted    = array();

			$order_items = $wpdb->get_results( // phpcs:ignore
				"SELECT order_id, meta_value, order_items.order_item_id
												FROM {$wpdb->prefix}woocommerce_order_items AS order_items
												JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
												WHERE order_items.order_item_id = order_itemmeta.order_item_id
												AND meta_key IN ('_product_id', '_variation_id' )
												AND meta_value",
				'ARRAY_A'
			);

			if ( ! empty( $order_items ) ) {

				foreach ( $order_items as $value ) {
					$order_unique_products[ $value['order_id'] ][ $value['order_item_id'] ] = $value['meta_value'];
				}

				foreach ( $product_ids as $chained_parent_id ) {
					$chained_product_detail = $wc_chained_products->get_all_chained_product_details( $chained_parent_id );
					$chained_product_ids    = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : array();

					if ( empty( $chained_product_ids ) ) {
						continue;
					}

					$orders_contains_parent_product = array();
					foreach ( $order_unique_products as $order_id => $value ) {

						if ( array_search( $chained_parent_id, $value, true ) !== false ) {
							$orders_contains_parent_product[] = $order_id;
						}
					}

					if ( empty( $orders_contains_parent_product ) ) {
						continue;
					}

					foreach ( $orders_contains_parent_product as $order_id ) {

						foreach ( $chained_product_ids as $chained_product_id ) {

							$order_item_id = array_search( $chained_product_id, $order_unique_products[ $order_id ], true );

							if ( empty( $order_item_id ) || array_search( $order_item_id, $inserted, true ) !== false ) {
								continue;
							}

							$inserted[] = $order_item_id;

							$cp_meta_value = $wpdb->get_var( // phpcs:ignore
								$wpdb->prepare(
									"SELECT meta_id
								FROM {$wpdb->prefix}woocommerce_order_itemmeta
								WHERE meta_key = '_chained_product_of'
								AND order_item_id = %d",
									$order_item_id
								)
							); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

							if ( ! empty( $cp_meta_value ) ) {
								continue;
							}

							$wpdb->query( // phpcs:ignore
								$wpdb->prepare(
									"INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta
										VALUES ( NULL ,  %d,  '_chained_product_of',  %d)
										",
									$order_item_id,
									$chained_parent_id
								)
							); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

						}
					}
				}
			}

			update_option( '_current_chained_product_db_version', '1.4', 'no' );

		}

		/**
		 * Function to modify cart count in themes header
		 *
		 * @param string|null $name Name of the specific header file to use. null for the default header.
		 */
		public function sa_chained_theme_header( $name ) {
			global $wc_chained_products;

			$chained_item_visible = $wc_chained_products->is_show_chained_items();

			if ( ! $chained_item_visible ) {
				add_filter( 'woocommerce_cart_contents_count', array( $this, 'sa_cp_get_cart_count' ) );
			}
		}

		/**
		 * Function to modify cart count in cart widget
		 *
		 * @param int $quantity Number of items in the cart.
		 * @return int $quantity Numberof items in the cart excluding chained items.
		 */
		public static function sa_cp_get_cart_count( $quantity = 0 ) {
			$cart_contents = WC()->cart->cart_contents;
			if ( ! empty( $cart_contents ) && is_array( $cart_contents ) ) {
				$quantity = 0;
				foreach ( $cart_contents as $cart_item_key => $data ) {

					if ( ! empty( $data ) && is_array( $data ) && ! empty( $data['quantity'] ) && ( false === array_key_exists( 'chained_item_of', $data ) ) ) {
						$quantity += intval( $data['quantity'] );
					}
				}
			}

			return $quantity;
		}

		/**
		 * Function to save chained-parent relationship in product when that product is trashed
		 *
		 * @param int $trashed_post_id Post ID being trashed.
		 */
		public function sa_chained_on_trash_post( $trashed_post_id = 0 ) {
			global $wpdb;

			if ( empty( $trashed_post_id ) ) {
				return;
			}

			$published_chained_data = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"SELECT pm.post_id AS post_id,
								 pm.meta_value AS meta_value
							FROM {$wpdb->prefix}postmeta AS pm
								INNER JOIN {$wpdb->prefix}posts AS p
									ON ( pm.post_id = p.ID )
							WHERE p.post_status = 'publish'
								AND ( p.post_type = 'product' OR p.post_type = 'product_variation' )
								AND pm.meta_key = %s
								AND pm.meta_value IS NOT NULL
								AND pm.meta_value != ''",
					'_chained_product_detail'
				),
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( ! empty( $published_chained_data ) && is_array( $published_chained_data ) ) {

				$product_detail = array();

				foreach ( $published_chained_data as $index => $data ) {
					$product_detail[ $data['post_id'] ] = maybe_unserialize( $data['meta_value'] );
				}

				$product_detail       = array_filter( $product_detail );
				$parent_id_to_restore = array();
				$update               = false;

				if ( empty( $product_detail ) || ! is_array( $product_detail ) ) {
					return;
				}

				foreach ( $product_detail as $post_id => $chained_data ) {

					if ( empty( $chained_data ) || ! is_array( $chained_data ) ) {
						continue;
					}

					foreach ( $chained_data as $chained_id => $data ) {

						if ( $chained_id === $trashed_post_id ) {
							$parent_id_to_restore[ $post_id ][ $chained_id ] = $data;
							unset( $product_detail[ $post_id ][ $chained_id ] );
							$update = true;
						}
					}
				}

				if ( $update ) {
					$chained_parent_product = wc_get_product( $trashed_post_id );
					$chained_parent_product->update_meta_data( '_parent_id_restore', $parent_id_to_restore );
					$chained_parent_product->save();

					foreach ( $product_detail as $post_id => $values ) {
						$product_obj = wc_get_product( $post_id );
						$product_obj->update_meta_data( '_chained_product_detail', $values );
						$product_obj->save();
					}
				}
			}
		}

		/**
		 * Function to restore chained-parent relationship after restoring trashed product
		 *
		 * @param int $untrashed_post_id POST ID being restored.
		 */
		public function sa_chained_on_untrash_post( $untrashed_post_id = 0 ) {

			if ( empty( $untrashed_post_id ) ) {
				return;
			}

			$untrashed_post = wc_get_product( $untrashed_post_id );

			$data_to_restore = $untrashed_post->get_meta( '_parent_id_restore', true );

			if ( ! empty( $data_to_restore ) ) {

				foreach ( $data_to_restore as $parent_id => $chained_array_data ) {

					if ( empty( $chained_array_data ) || ! is_array( $chained_array_data ) ) {
						return;
					}

					$parent_post          = wc_get_product( $parent_id );
					$present_chained_data = self::chained_product_details( $parent_post );
					$present_chained_data = ! empty( $present_chained_data ) && is_array( $present_chained_data ) ? ( $present_chained_data + $chained_array_data ) : $chained_array_data;

					$parent_post->update_meta_data( '_chained_product_detail', $present_chained_data );
					$parent_post->save();
				}

				$untrashed_post->delete_meta_data( '_parent_id_restore' );
				$untrashed_post->save();
			}
		}

		/**
		 * To ignore chained child items when Pay button is clicked
		 * This will prevent adding chained child item twice
		 *
		 * @param  array    $items Cart items.
		 * @param  WC_Order $order Order object.
		 * @return array $items Updated cart items.
		 */
		public function sa_cp_ignore_chained_child_items_on_manual_pay( $items, $order ) {
			if ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) && ! empty( $items ) ) { // phpcs:ignore
				foreach ( $items as $item_id => $item ) {
					if ( ! empty( $item['chained_product_of'] ) ) {
						unset( $items[ $item_id ] );
					}
				}
			}

			return $items;
		}

		/**
		 * Function for display chained products list for variable products
		 *
		 * @global object $woocommerce WooCommerce's main instance.
		 * @global WC_Product $product WooCommerce product's instance.
		 */
		public function woocommerce_chained_products_for_variable_product() {

			global $woocommerce, $product;

			// Adding filter to prevent executing following code as it needs re-factoring.
			// Possible approach to handle would be to check if CP shortcode is present in the product description. If yes then only js code should execute.
			$trigger = apply_filters( 'chained_products_show_for_variable', true, array( 'source' => $this ) );
			if ( false === $trigger ) {
				return;
			}

			$children                  = ( Chained_Products_WC_Compatibility::is_wc_gte_30() && $product instanceof WC_Product_Variable ) ? $product->get_visible_children() : $product->get_children( true );
			$is_chained_product_parent = false;
			if ( ! empty( $children ) ) {

				foreach ( $children as $chained_parent_id ) {
					$product_detail = self::chained_product_details( $chained_parent_id );

					if ( ! empty( $product_detail ) ) {
						$is_chained_product_parent = true;
						break;
					}
				}
			}

			if ( ! ( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) ) || ( $product->is_type( 'variable' ) && ! $is_chained_product_parent ) ) {
				return;
			}

			$plugin_data = self::get_plugin_data();

			wp_register_script(
				'wc-cp-main-scripts',
				WC_CP_PLUGIN_URL . '/assets/js/wc-cp-main-scripts' . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' ) . '.js',
				array( 'jquery' ),
				! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : WC()->version,
				true
			);

			wp_localize_script(
				'wc-cp-main-scripts',
				'cpVariationParams',
				array(
					'ajaxURL'     => admin_url( 'admin-ajax.php' ),
					'security'    => wp_create_nonce( 'wc-cp-get-products' ),
					'postPerPage' => apply_filters( 'wc_cp_post_per_page', get_option( 'wc_cp_post_per_page', WC_CP_LIST_LINKED_PRODUCTS_PER_PAGE ) ),
				)
			);

			wp_enqueue_script( 'wc-cp-main-scripts' );
		}

		/**
		 * Function to add actions & filters specific to Chained Products
		 */
		public function add_chained_products_actions_filters() {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_after_shop_loop_chained_item' ) );
			add_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_chained_product_is_visible' ), 20, 2 );
		}

		/**
		 * Function to remove action & filters specific to Chained products
		 */
		public function remove_chained_products_actions_filters() {
			remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_after_shop_loop_chained_item' ) );
			remove_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_chained_product_is_visible' ), 20, 2 );
		}

		/**
		 * Function to show chained products which are only searchable
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class.
		 * @param boolean $visible Product catalog visibility.
		 * @param int     $product_id Product ID.
		 * @return boolean
		 */
		public function woocommerce_chained_product_is_visible( $visible, $product_id ) {
			global $wc_chained_products;

			$parent_product_id  = $wc_chained_products->get_parent( $product_id );
			$is_chained_product = $wc_chained_products->is_chained_product( $parent_product_id );
			$product            = wc_get_product( $product_id );
			if ( $product instanceof WC_Product ) {
				$product_visibility = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_catalog_visibility() : $product->visibility;
			}

			if ( $is_chained_product && ( 'search' === $product_visibility || 'hidden' === $product_visibility ) ) {
				return true;
			}

			return $visible;
		}

		/**
		 * Function for removing price of chained products before calculating totals
		 *
		 * @param WC_Cart $cart_object Current cart object.
		 */
		public function woocommerce_before_chained_calculate_totals( $cart_object = null ) {
			global $wc_chained_products;

			if ( ! $cart_object instanceof $cart_object || empty( $cart_object->cart_contents ) ) {
				return;
			}

			foreach ( $cart_object->cart_contents as $value ) {
				$priced_individually = ( ! empty( $value['priced_individually'] ) ) ? $value['priced_individually'] : 'no';

				if ( empty( $value['chained_item_of'] ) || 'yes' === $priced_individually ) {
					continue;
				}

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					if ( $value['data'] instanceof WC_Product && is_callable( array( $value['data'], 'set_price' ) ) ) {
						$value['data']->set_price( 0 );
					}
				} else {
					$value['data']->price = 0;
				}
			}
		}

		/**
		 * Function for making chained product's price to zero
		 *
		 * @param array $session_data Cart item session data.
		 * @param array $values Product data.
		 * @return array $session_data
		 */
		public function get_chained_cart_item_from_session( $session_data, $values ) {
			$priced_individually = ( ! empty( $values['priced_individually'] ) ) ? $values['priced_individually'] : 'no';

			if ( isset( $values['chained_item_of'] ) && '' !== $values['chained_item_of'] && 'no' === $priced_individually ) {
				$session_data['chained_item_of'] = $values['chained_item_of'];

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$session_data['data']->set_price( 0 );
				} else {
					$session_data['data']->price = 0;
				}
			}

			return $session_data;
		}

		/**
		 * Remove chained cart items with parent
		 *
		 * @param string  $cart_item_key Removed cart item key.
		 * @param WC_Cart $cart Cart object.
		 */
		public function chained_cart_item_removed( $cart_item_key, $cart ) {
			if ( ! empty( $cart->removed_cart_contents[ $cart_item_key ] ) ) {

				foreach ( $cart->cart_contents as $item_key => $item ) {

					if ( ! empty( $item['chained_item_of'] ) && $item['chained_item_of'] === $cart_item_key ) {
						$cart->removed_cart_contents[ $item_key ] = $item;
						unset( $cart->cart_contents[ $item_key ] );
						do_action( 'woocommerce_cart_item_removed', $item_key, $cart );
					}
				}
			}
		}

		/**
		 * Restore chained cart items with parent
		 *
		 * @param string  $cart_item_key Restored cart item key.
		 * @param WC_Cart $cart Cart object.
		 */
		public function chained_cart_item_restored( $cart_item_key, $cart ) {
			if ( ! empty( $cart->cart_contents[ $cart_item_key ] ) && ! empty( $cart->removed_cart_contents ) ) {

				foreach ( $cart->removed_cart_contents as $item_key => $item ) {

					if ( ! empty( $item['chained_item_of'] ) && $item['chained_item_of'] === $cart_item_key ) {
						$cart->cart_contents[ $item_key ] = $item;
						unset( $cart->removed_cart_contents[ $item_key ] );
						do_action( 'woocommerce_cart_item_restored', $item_key, $cart );
					}
				}
			}
		}

		/**
		 * Function to validate & update chained product's qty in cart
		 */
		public function validate_and_update_chained_product_quantity_in_cart() {
			$cart_contents_modified = WC()->cart->cart_contents;

			if ( empty( $cart_contents_modified ) ) {
				return;
			}

			foreach ( $cart_contents_modified as $key => $value ) {

				if ( isset( $value['chained_item_of'] ) && ! isset( $cart_contents_modified[ $value['chained_item_of'] ] ) ) {
					WC()->cart->set_quantity( $key, 0 );
				}
			}
		}

		/**
		 * Function to manage chained product quantity in cart
		 *
		 * @param string $cart_item_key Cart item key.
		 */
		public function sa_before_cart_item_quantity_zero( $cart_item_key = '' ) {
			$this->update_chained_product_quantity_in_cart( $cart_item_key );
		}

		/**
		 * Function to manage chained product quantity in cart
		 *
		 * @param string $cart_item_key Cart item key.
		 * @param int    $quantity New quantity.
		 */
		public function sa_after_cart_item_quantity_update( $cart_item_key = '', $quantity = 0 ) {
			$this->update_chained_product_quantity_in_cart( $cart_item_key, $quantity );
		}

		/**
		 * Function for updating chained product quantity in cart
		 *
		 * @param string $cart_item_key Cart item key.
		 * @param int    $quantity Cart item quantity.
		 */
		public function update_chained_product_quantity_in_cart( $cart_item_key = '', $quantity = 0 ) {

			global $wc_chained_products;

			$cart_contents = WC()->cart->cart_contents;

			if ( ! empty( $cart_contents ) && ! empty( $cart_contents[ $cart_item_key ] ) ) {

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$product_id = $cart_contents[ $cart_item_key ]['data']->get_id();
				} else {
					$product_id = $cart_contents[ $cart_item_key ]['data'] instanceof WC_Product_Variation ? $cart_contents[ $cart_item_key ]['variation_id'] : $cart_contents[ $cart_item_key ]['product_id'];
				}

				$quantity = ( $quantity <= 0 ) ? 0 : $cart_contents[ $cart_item_key ]['quantity'];

				// Prevent if the quantity is zero where we do not need to calculate if the quantity is zero.
				if ( empty( $quantity ) ) {
					return;
				}

				$bundle_product_data = is_callable( array( $wc_chained_products, 'get_all_chained_product_details' ) ) ? $wc_chained_products->get_all_chained_product_details( $product_id, $quantity ) : array();

				foreach ( $cart_contents as $key => $value ) {
					if ( isset( $value['chained_item_of'] ) && $cart_item_key === $value['chained_item_of'] ) {

						if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
							$parent_product_id = $cart_contents[ $key ]['data']->get_id();
						} else {
							$parent_product_id = $cart_contents[ $key ]['data'] instanceof WC_Product_Variation ? $cart_contents[ $key ]['variation_id'] : $cart_contents[ $key ]['product_id'];
						}
						$chained_product_qty = ! empty( $bundle_product_data[ $parent_product_id ]['unit'] ) ? $bundle_product_data[ $parent_product_id ]['unit'] : 1;
						WC()->cart->set_quantity( $key, $chained_product_qty );
					}
				}
			}
		}

		/**
		 * Function for keeping chained products quantity same as parent product
		 *
		 * @param int    $quantity Cart item quantity.
		 * @param string $cart_item_key Cart item key.
		 * @return int $quantity
		 */
		public function chained_cart_item_quantity( $quantity, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return '<div class="quantity buttons_added">' . WC()->cart->cart_contents[ $cart_item_key ]['quantity'] . '</div>';
			}

			return $quantity;
		}

		/**
		 * Function for removing delete link for chained products
		 *
		 * @param string $link Cart item remove link.
		 * @param string $cart_item_key Cart item key.
		 * @return string $link
		 */
		public function chained_cart_item_remove_link( $link, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return '';
			}

			return $link;
		}

		/**
		 * Function to add chained product to cart
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param string $cart_item_key Cart item key.
		 * @param int    $product_id ID of the product being added to the cart.
		 * @param int    $quantity  Quantity of the item being added to the cart.
		 * @param int    $variation_id ID of the variation being added to the cart.
		 *
		 * @return void.
		 */
		public function add_chained_products_to_cart( $cart_item_key = '', $product_id = 0, $quantity = 0, $variation_id = 0 ) {
			global $wc_chained_products;

			$product_id              = ! empty( $variation_id ) ? intval( $variation_id ) : intval( $product_id );
			$chained_products_detail = is_callable( array( $wc_chained_products, 'get_all_chained_product_details' ) ) ? $wc_chained_products->get_all_chained_product_details( $product_id ) : array();

			if ( empty( $chained_products_detail ) ) {
				return;
			}

			$validation_result = $this->are_chained_products_available( $product_id, intval( $quantity ) );

			if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {
				return;
			}

			$chained_cart_item_data = array( 'chained_item_of' => $cart_item_key );

			foreach ( $chained_products_detail as $chained_products_id => $chained_products_data ) {

				$_product = wc_get_product( intval( $chained_products_id ) );
				if ( $_product instanceof WC_Product ) {
					$chained_variation_id = 0;

					if ( $_product instanceof WC_Product_Variation ) {
						$chained_variation_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() && is_callable( array( $_product, 'get_id' ) ) ) ? $_product->get_id() : ( ! empty( $_product->variation_id ) ? $_product->variation_id : 0 );
					}

					$chained_parent_id = ! empty( $chained_variation_id ) ? ( is_callable( array( $wc_chained_products, 'get_parent' ) ) ? $wc_chained_products->get_parent( intval( $chained_products_id ) ) : 0 ) : intval( $chained_products_id );

					$this->cp_cart_item_data = $chained_cart_item_data;

					$chained_variation_data = ( ! empty( $chained_variation_id ) && is_callable( array( $_product, 'get_variation_attributes' ) ) ) ? $_product->get_variation_attributes() : array();
					$chained_cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $chained_cart_item_data, $chained_parent_id, $chained_variation_id, $quantity );
					$priced_individually    = ( ! empty( $chained_products_data['priced_individually'] ) ) ? $chained_products_data['priced_individually'] : 'no';
					$chained_quantity       = intval( $quantity ) * ( ! empty( $chained_products_data['unit'] ) ? intval( $chained_products_data['unit'] ) : 1 );

					// Prepare for adding children to cart.
					do_action(
						'wc_before_chained_add_to_cart',
						intval( $chained_parent_id ),
						$chained_quantity,
						$chained_variation_id,
						$chained_variation_data,
						$chained_cart_item_data,
						( ! empty( $chained_products_data['unit'] ) ? intval( $chained_products_data['unit'] ) : 1 )
					);

					$chained_item_cart_key = $this->chained_add_to_cart( intval( $product_id ), intval( $chained_parent_id ), $chained_quantity, $chained_variation_id, $chained_variation_data, $chained_cart_item_data, $priced_individually );
					// Finish.
					do_action(
						'wc_after_chained_add_to_cart',
						intval( $chained_parent_id ),
						$chained_quantity,
						$chained_variation_id,
						$chained_variation_data,
						$chained_cart_item_data,
						$cart_item_key
					);
				}
			}
		}

		/**
		 * Add a chained item to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
		 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
		 *
		 * @param int    $parent_cart_key ID of the product being added to the cart.
		 * @param int    $product_id Parent ID of the chained product.
		 * @param string $quantity Quantity of the chained item being added to the cart.
		 * @param int    $variation_id Variation ID if the chained item is a variation.
		 * @param array  $variation Attribute values of chained item.
		 * @param array  $cart_item_data Extra cart item data passed to the chained item.
		 * @param string $priced_individually Allow chained item to be priced 'yes|no'.
		 * @return string|false
		 */
		public function chained_add_to_cart( $parent_cart_key = 0, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array(), $priced_individually = 'no' ) {

			// Load cart item data when adding to cart.
			$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
			$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// See if this product and its options is already in the cart.
			$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

			// Get the product.
			$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			if ( ! ( $product_data instanceof WC_Product ) ) {
				return $cart_item_key;
			}

			// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart().
			if ( ! $cart_item_key ) {

				$cart_item_key = $cart_id;

				// Add item after merging with $cart_item_data - allow plugins and wc_cp_add_cart_item_filter to modify cart item.
				WC()->cart->cart_contents[ $cart_item_key ] = apply_filters(
					'woocommerce_add_cart_item',
					array_merge(
						$cart_item_data,
						array(
							'product_id'          => $product_id,
							'variation_id'        => $variation_id,
							'variation'           => $variation,
							'quantity'            => $quantity,
							'data'                => $product_data,
							'priced_individually' => $priced_individually,
						)
					),
					$cart_item_key
				);

			}

			// use this hook for compatibility instead of the 'woocommerce_add_to_cart' action hook to work around the recursion issue.
			// when the recursion issue is solved, we can simply replace calls to 'mnm_add_to_cart()' with direct calls to 'WC_Cart::add_to_cart()' and delete this function.
			do_action( 'woocommerce_chained_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $parent_cart_key );

			return $cart_item_key;
		}

		/**
		 * Function to remove subtotal for chained items in cart
		 *
		 * @param string $subtotal Cart item price.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $subtotal
		 */
		public function sa_cart_chained_item_subtotal( $subtotal = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $subtotal ) || empty( $cart_item ) || empty( $cart_item_key ) || empty( $cart_item['chained_item_of'] ) ) {
				return $subtotal;
			}

			global $wc_chained_products;

			if ( $wc_chained_products->is_show_chained_item_price() ) {
				$priced_individually = ( ! empty( $cart_item['priced_individually'] ) ) ? $cart_item['priced_individually'] : 'no';

				if ( 'no' === $priced_individually ) {
					$called_by  = current_filter();
					$product_id = ( ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];
					$product    = wc_get_product( $product_id );
					$price      = '';
					if ( $product instanceof WC_Product ) {
						$price = $product->get_price();
					}
					if ( 'woocommerce_cart_item_subtotal' === $called_by ) {
						$price = $price * $cart_item['quantity'];
					}
					return '<del>' . wc_price( $price ) . '</del>';
				}

				return $subtotal;
			}

			return '';
		}

		/**
		 * Function to add css class for chained items in cart
		 *
		 * @param string $class Default class name for cart item.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $class
		 */
		public function sa_cart_chained_item_class( $class = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $cart_item ) || empty( $cart_item['chained_item_of'] ) ) {
				return $class;
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to add indent in chained item name in cart
		 *
		 * @param string $item_name Product name.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return string $item_name
		 */
		public function sa_cart_chained_item_name( $item_name = '', $cart_item = null, $cart_item_key = null ) {
			if ( empty( $cart_item ) || empty( $cart_item['chained_item_of'] ) ) {
				return $item_name;
			}

			return '&nbsp;&nbsp;' . $item_name;
		}

		/**
		 * Function to add css class in chained items of order admin page
		 *
		 * @param string                $class Default order item class name.
		 * @param WC_Order_Item_Product $item Order item object.
		 * @return string $class
		 */
		public function sa_admin_html_chained_item_class( $class = '', $item = null ) {
			if ( empty( $item ) || empty( $item['chained_product_of'] ) ) {
				return $class;
			}

			$priced_individually = ( ! empty( $item['cp_priced_individually'] ) ) ? $item['cp_priced_individually'] : 'no';

			if ( 'no' === $priced_individually ) {
				$class = $class . ' cp_hide_line_item_meta';
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to remove subtotal for chained items in order
		 *
		 * @param string   $subtotal Formatted line subtotal.
		 * @param array    $order_item Item to get total from.
		 * @param WC_Order $order Order object.
		 * @return string $subtotal
		 */
		public function sa_order_chained_item_subtotal( $subtotal = '', $order_item = null, $order = null ) {

			global $wc_chained_products;

			if ( empty( $subtotal ) || empty( $order_item ) || empty( $order ) || empty( $order_item['chained_product_of'] ) ) {

				$product = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_product' ) ) ) ? $order_item->get_product() : $order->get_product_from_item( $order_item );

				if ( $product instanceof WC_Product ) {

					$product_id     = $product->get_id();
					$chained_items  = $this->get_chained_product_data_by_product_id( $product_id );
					$override_total = false;

					if ( is_array( $chained_items ) && 0 < count( $chained_items ) ) {
						$quantity = $order_item['quantity'];
						$value    = 0;

						foreach ( $chained_items as $chained_item_id => $chained_item_data ) {
							$priced_individually = ( ! empty( $chained_item_data['priced_individually'] ) ) ? $chained_item_data['priced_individually'] : 'no';

							if ( 'yes' === $priced_individually ) {
								$chained_product = wc_get_product( $chained_item_id );
								if ( $chained_product instanceof WC_Product ) {
									$chained_product_price = $chained_product->get_price();
									if ( ! empty( $chained_product_price ) ) {
										$value += $chained_product_price * $chained_item_data['unit'];
									}
									$override_total = true;
								}
							}
						}
					}

					if ( true === $override_total ) {
						return wc_price( ( $product->get_price() + $value ) * $quantity );
					} else {
						return $subtotal;
					}
				}

				return $subtotal;
			}

			if ( $wc_chained_products->is_show_chained_item_price() ) {

				if ( 'no' === $order_item['cp_priced_individually'] ) {
					$product = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_product' ) ) ) ? $order_item->get_product() : $order->get_product_from_item( $order_item );
					$price   = $product->get_price();
					$price   = $price * $order_item['qty'];

					return '<del>' . wc_price( $price ) . '</del>';
				}

				return $subtotal;
			}

			return '&nbsp;';
		}

		/**
		 * Function to add css class for chained items in order
		 *
		 * @param string                $class Default class name for order item.
		 * @param WC_Order_Item_Product $order_item Order item object.
		 * @param WC_Order              $order Order object.
		 * @return string $class
		 */
		public function sa_order_chained_item_class( $class = '', $order_item = null, $order = null ) {
			if ( empty( $order_item ) || empty( $order_item['chained_product_of'] ) ) {
				return $class;
			}

			return $class . ' chained_item';
		}

		/**
		 * Function to add indent in chained item name in order
		 *
		 * @param string                $item_name Order item name.
		 * @param WC_Order_Item_Product $order_item Order item object.
		 * @return string $item_name
		 */
		public function sa_order_chained_item_name( $item_name = '', $order_item = null ) {
			if ( empty( $order_item ) || empty( $order_item['chained_product_of'] ) ) {
				return $item_name;
			}

			return '&nbsp;&nbsp;' . $item_name;
		}

		/**
		 * Function to modify visibility of chained items in cart, mini-cart & checkout
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products
		 * @param bool   $is_visible Product visibility.
		 * @param array  $cart_item Cart item data.
		 * @param string $cart_item_key Cart item key.
		 * @return bool $is_visible
		 */
		public function sa_chained_item_visible( $is_visible = true, $cart_item = null, $cart_item_key = null ) {
			if ( ! $is_visible || empty( $cart_item ) || empty( $cart_item_key ) || empty( $cart_item['chained_item_of'] ) ) {
				return $is_visible;
			}

			global $wc_chained_products;
			$bool = $wc_chained_products->is_show_chained_items();

			return $bool;
		}

		/**
		 * Function to modify visibility of chained items in order
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products.
		 * @param bool                  $is_visible Order item visibility.
		 * @param WC_Order_Item_Product $item Order item object.
		 * @return bool $is_visible
		 */
		public function sa_order_chained_item_visible( $is_visible = true, $item = null ) {
			if ( ! $is_visible || empty( $item ) || empty( $item['chained_product_of'] ) ) {
				return $is_visible;
			}

			global $wc_chained_products;
			$bool = $wc_chained_products->is_show_chained_items();

			return $bool;
		}

		/**
		 * Function to add css for frontend pages
		 */
		public function chained_products_frontend_css() {
			?>
			<style type="text/css" class="wcp-frontend">
			<?php
			$theme = wp_get_theme();
				// TODO: mentioned classes are not available for cart widget so need to add support for cart widget.
			if ( is_cart() || is_checkout() || is_wc_endpoint_url( 'order-received' ) ) {
				?>
					.chained_item td.product-name {
						font-size: 0.9em;
						padding-left: 2em !important;
				}
				<?php
			}

			if ( $theme instanceof WP_Theme && is_callable( array( $theme, 'get_template' ) ) && 'storefront' === $theme->get_template() ) {
				?>

				.products.wccp-grid-view{
					display: flex;
					flex-wrap: wrap;
				}

				<?php } ?>

				.chained_items_container ul.products > li{
					margin: 0 0.6rem !important;
				}

				</style>
				<?php
		}

		/**
		 * Function to add css for admin page
		 */
		public function chained_products_admin_css() {

			global $wc_chained_products;

			if ( ! is_callable( array( $wc_chained_products, 'wc_chained_products' ) ) || ! $wc_chained_products->is_wc_order_admin_page() ) {
				return;
			}
			?>
			<style type="text/css" class="wcp-admin">
				.chained_item td.name {
					font-size: 0.9em;
					padding-left: 2em !important;
				}
				.chained_item.cp_hide_line_item_meta td.item_cost div,
				.chained_item.cp_hide_line_item_meta td.line_cost div,
				.chained_item.cp_hide_line_item_meta td.line_tax div {
					display: none;
				}
			</style>
			<?php
		}

		/**
		 * Function to set the chained product ids.
		 *
		 * @param int|string $product_id The Product Id.
		 *
		 * @return void.
		 */
		public function set_chained_products_items_ids( $product_id = 0 ) {

			if ( empty( $product_id ) ) {
				return;
			}

			// Get the chained products data.
			$cp_details = $this->get_chained_product_data_by_product_id( $product_id );

			if ( ! empty( $cp_details ) && is_array( $cp_details ) ) {
				foreach ( $cp_details as $cp_id => $cp_detail ) {

					if ( isset( $this->chained_items[ $cp_id ] ) ) {
						continue;
					}

					// Set chained items.
					$this->chained_items[ $cp_id ] = $cp_detail;

					$this->set_chained_products_items_ids( $cp_id );
				}
			}

		}


		/**
		 * Function to hide "Add to cart" button if chained products are out of stock.
		 *
		 * @param array      $availability Availability of the product.
		 * @param WC_Product $_product Product object.
		 *
		 * @return array $availability
		 */
		public function woocommerce_get_chained_products_availability( $availability = array(), $_product = null ) {

			if ( ! $_product instanceof WC_Product ) {
				return $availability;
			}

			if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
				$product_id = is_callable( array( $_product, 'get_id' ) ) ? $_product->get_id() : 0;
			} else {
				$product_id = ( $_product instanceof WC_Product_Variation && ! empty( $_product->variation_id ) ) ? $_product->variation_id : ( ! empty( $_product->id ) ? $_product->id : 0 );
			}

			if ( empty( $product_id ) ) {
				return $availability;
			}

			$validation_result = $this->are_chained_products_available( $product_id );

			if ( ! empty( $validation_result ) ) {

				$stock_status = ! empty( $validation_result['stock_status'] ) ? $validation_result['stock_status'] : '';

				if ( ! empty( $stock_status ) && is_callable( array( $_product, 'set_stock_status' ) ) ) {
					$_product->set_stock_status( $stock_status );
				}

				// Hide parent product if chained product is out of stock.
				if ( 'outofstock' === $stock_status && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items', 'no' ) ) {
					if ( Chained_Products_WC_Compatibility::is_wc_gte_30() && is_callable( array( $_product, 'set_catalog_visibility' ) ) && is_callable( array( $_product, 'save' ) ) ) {
						$_product->set_catalog_visibility( 'hidden' );
						$_product->save();
					} else {
						$_product->visibility = 'hidden';
					}
				}

				$class           = 'in-stock';
				$cp_availability = '';

				if ( 'outofstock' === $stock_status ) {
					/* translators: 1: Chained item name(s) */
					$cp_availability = _x( 'Out of stock', 'out of stock availability text', 'woocommerce-chained-products' ) . ( ! empty( $validation_result['product_titles'] ) ? sprintf( _nx( ': %s does not have sufficient quantity in stock.', ': %s do not have sufficient quantity in stock.', count( $validation_result['product_titles'] ), 'chained products backorder message', 'woocommerce-chained-products' ), implode( ', ', $validation_result['product_titles'] ) ) : '' );
					$class           = 'out-of-stock';
				} elseif ( 'onbackorder' === $stock_status ) {
					/* translators: 1: Chained item name(s) */
					$cp_availability = _x( 'Available on backorder', 'backorder availability text', 'woocommerce-chained-products' ) . ( ! empty( $validation_result['product_titles'] ) ? sprintf( _nx( ': %s is available on backorder.', ': %s are available on backorder.', count( $validation_result['product_titles'] ), 'chained products backorder message', 'woocommerce-chained-products' ), implode( ', ', $validation_result['product_titles'] ) ) : '' );
					$class           = 'available-on-backorder';
				}

				return apply_filters(
					'wcp_get_chained_products_availability',
					array(
						'availability' => $cp_availability,
						'class'        => $class,
					),
					array(
						'stock_status'      => $stock_status,
						'validation_result' => $validation_result,
						'source'            => $this,
					)
				);
			}

			return $availability;
		}

		/**
		 * Function to display available variation below Product's name on shop front.
		 *
		 * @global WC_Product $product
		 * @global array $variation_titles
		 * @global int $chained_parent_id
		 * @global array $chained_product_detail
		 * @global array $shortcode_attributes
		 */
		public function woocommerce_after_shop_loop_chained_item() {
			global $product, $variation_titles, $chained_parent_id, $chained_product_details, $shortcode_attributes;

			$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;

			if ( isset( $variation_titles[ $product_id ] ) ) {
				$chained_product_detail = isset( $chained_product_details ) ? $chained_product_details : $this->get_chained_product_data_by_product_id( $chained_parent_id );

				foreach ( $variation_titles[ $product_id ] as $product_id => $variation_data ) {

					echo $variation_data; // phpcs:ignore

					if ( ! empty( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] && ! empty( $chained_product_detail[ $product_id ] ) && ! empty( $chained_product_detail[ $product_id ]['unit'] ) ) {
						echo ' ( &times; ' . esc_html( $chained_product_detail[ $product_id ]['unit'] ) . ' )<br />'; // phpcs:ignore
					}
				}
			}
		}

		/**
		 * Function set the max value of quantity input box based on stock availability of chained products
		 *
		 * @global object $post
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param int        $stock Availability of the product.
		 * @param WC_Product $_product Product Object.
		 * @return int $stock
		 */
		public function validate_stock_availability_of_chained_products( $stock = 0, $_product = null ) {
			global $post, $wc_chained_products;

			if ( $_product instanceof WC_Product ) {

				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$product_id = $_product->get_id();
				} else {
					$product_id = $_product instanceof WC_Product_Variation ? $_product->variation_id : $post->ID;
				}

				$post_id                  = isset( $_product ) ? $product_id : $post->ID;
				$chained_product_instance = $wc_chained_products->get_product_instance( $post_id );

				if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && 'yes' === $chained_product_instance->get_meta( '_chained_product_manage_stock', true ) && $chained_product_instance->is_in_stock() ) {
					$max_quantity = $chained_product_instance->get_stock_quantity();

					if ( ! empty( $max_quantity ) ) {
						for ( $max_count = 1; $max_count < $max_quantity; $max_count++ ) {
							$validation_result = $this->are_chained_products_available( $post_id, $max_count );
							if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {
								if ( isset( $stock['max_value'] ) ) {
									$stock['max_value'] = $max_count - 1;
								} elseif ( isset( $stock['availability'] ) ) {
									$stock['availability'] = ( $max_count - 1 ) . ' in stock';
								} else {
									$stock = $max_count - 1;
								}
								return $stock;
							}
						}
					}
				}
			}

			return $stock;
		}

		/**
		 * Function to display price of the chained products on shop page
		 *
		 * @global WC_Product $product
		 * @global int $chained_parent_id
		 * @global array $shortcode_attributes
		 * @global array $chained_product_details
		 */
		public function woocommerce_template_chained_loop_quantity_and_price() {
			global $product, $chained_parent_id, $shortcode_attributes, $chained_product_details;

			$html_price = '';
			$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;

			if ( ! empty( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] ) {
				$chained_product_details = isset( $chained_product_details ) ? $chained_product_details : $this->get_chained_product_data_by_product_id( $chained_parent_id );
				if ( ! empty( $chained_product_details[ $product_id ] ) ) {
					echo ' ( &times; ' . esc_html( $chained_product_details[ $product_id ]['unit'] ) . ' )<br />';
				}
			}

			if ( ! empty( $product_id ) ) {
				if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
					$priced_individually = ( isset( $chained_product_details[ $product_id ]['priced_individually'] ) ) ? $chained_product_details[ $product_id ]['priced_individually'] : 'no';
					$html_price          = ( 'no' === $priced_individually ) ? wc_format_sale_price( wc_price( $product->get_price() ), '' ) : wc_price( $product->get_price() );
				} else {
					$html_price = $product->get_price_html_from_to( wc_price( $product->get_price() ), '' );
				}
			}

			if ( isset( $shortcode_attributes['price'] ) && 'yes' === $shortcode_attributes['price'] ) {
				$price      = '';
				$price     .= ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text();
				$price     .= $html_price;
				$price_html = apply_filters( 'woocommerce_free_price_html', $price, $product );
				echo '<span class="price">' . $price_html . '</span>'; // phpcs:ignore
			}
		}

		/**
		 * Function to check whether store has sufficient quantity of chained products
		 *
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @global array $chained_product_detail
		 * @param int   $product_id Product ID.
		 * @param int   $main_product_quantity Parent product quantity.
		 * @param array $chained_products_in_cart Chained products already present in cart.
		 * @return mixed
		 */
		public function are_chained_products_available( $product_id = 0, $main_product_quantity = 1, $chained_products_in_cart = array() ) {

			global $wc_chained_products;

			if ( empty( $product_id ) ) {
				return null;
			}

			$parent_product = wc_get_product( $product_id );

			if ( ! ( $parent_product instanceof WC_Product ) ) {
				return null;
			}

			if ( 'yes' === get_option( 'woocommerce_manage_stock', 'no' ) && 'yes' === $parent_product->get_meta( '_chained_product_manage_stock', true ) ) {

				$this->set_chained_products_items_ids( $product_id );

				$parent_is_in_stock = is_callable( array( $parent_product, 'is_in_stock' ) ) ? $parent_product->is_in_stock() : false;

				if ( ! empty( $this->chained_items ) ) {
					$validation_result           = array();
					$product_titles              = array();
					$backorders_allowed_products = array();
					$chained_add_to_cart         = 'yes';

					foreach ( $this->chained_items as $chained_product_id => $details ) {

						$instance_cache_key       = 'sa_cp_product_instance_' . $chained_product_id;
						$chained_product_instance = wp_cache_get( $instance_cache_key, 'woocommerce-chained-products' );

						if ( empty( $chained_product_instance ) ) {
							$chained_product_instance = wc_get_product( $chained_product_id );
							wp_cache_set( $instance_cache_key, $chained_product_instance, 'woocommerce-chained-products' );
						}

						if ( ! $chained_product_instance instanceof WC_Product || ! is_callable( array( $chained_product_instance, 'get_id' ) ) || empty( $chained_product_instance->get_id() ) ) {
							continue;
						}

						$chained_product_in_stock = ( is_callable( array( $chained_product_instance, 'is_in_stock' ) ) ) && $chained_product_instance->is_in_stock();

						$product_title = is_callable( array( $wc_chained_products, 'get_product_title' ) ) ? $wc_chained_products->get_product_title( intval( $chained_product_id ) ) : '';

						// Note: Pass the default quantity 1 to `is_on_backorder()` method as per WooCommerce does while checking the availability check.
						if ( $parent_is_in_stock && $chained_product_in_stock && ( is_callable( array( $chained_product_instance, 'is_on_backorder' ) ) && $chained_product_instance->is_on_backorder( 1 ) ) ) {
							// Show the products available in the back orders if admin wants to notify about that product.
							if ( is_callable( array( $chained_product_instance, 'get_backorders' ) ) && 'notify' === $chained_product_instance->get_backorders() ) {
								$backorders_allowed_products[] = '"' . $product_title . '"';
							}
							continue;
						}

						$chained_product_quantity_in_cart = ! empty( $chained_products_in_cart[ $chained_product_id ] ) ? intval( $chained_products_in_cart[ $chained_product_id ] ) : 0;

						wp_cache_set( 'sa_cp_check_is_in_stock_' . $chained_product_instance->get_id(), 'yes', 'woocommerce-chained-products' );

						if ( ! $chained_product_in_stock ||
							( ( is_callable( array( $chained_product_instance, 'managing_stock' ) ) && $chained_product_instance->managing_stock() ) &&
								! ( is_callable( array( $chained_product_instance, 'is_downloadable' ) ) && $chained_product_instance->is_downloadable() ) &&
								! ( is_callable( array( $chained_product_instance, 'is_virtual' ) ) && $chained_product_instance->is_virtual() ) &&
								is_callable( array( $chained_product_instance, 'get_stock_quantity' ) ) &&
								( $chained_product_instance->get_stock_quantity() < ( ( intval( $main_product_quantity ) * intval( $details['unit'] ) ) + $chained_product_quantity_in_cart ) )
						) ) {
							if ( is_callable( array( $wc_chained_products, 'get_product_title' ) ) ) {
								$product_titles[] = '"' . $product_title . '"';
							}
							$chained_add_to_cart = 'no';
						}
					}
					if ( 'no' === $chained_add_to_cart ) {
						return array(
							'product_titles' => array_filter( $product_titles ),
							'valid'          => $chained_add_to_cart,
							'stock_status'   => 'outofstock',
						);
					}

					if ( ! empty( $backorders_allowed_products ) ) {
						return array(
							'product_titles' => array_filter( $backorders_allowed_products ),
							'valid'          => $chained_add_to_cart,
							'stock_status'   => 'onbackorder',
						);
					}
				}
			}
			return null;
		}

		/**
		 * Function to validate Add to cart based on stock quantity of chained products
		 *
		 * @global object $woocommerce - Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param boolean $add_to_cart Add item to cart or not.
		 * @param int     $product_id Product ID.
		 * @param int     $main_product_quantity Parent product quantity.
		 * @return boolean
		 */
		public function woocommerce_chained_add_to_cart_validation( $add_to_cart = true, $product_id = 0, $main_product_quantity = 0 ) {
			global $woocommerce, $wc_chained_products;

			if ( isset( $_GET['order_again'] ) && is_user_logged_in() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-order_again' ) ) { // phpcs:ignore
				$order = wc_get_order( absint( $_GET['order_again'] ) ); // phpcs:ignore

				foreach ( $order->get_items() as $item ) {

					if ( $item['product_id'] === $product_id && isset( $item['chained_product_of'] ) ) {
						return false;
					}
				}
				return $add_to_cart;
			}

			// Do not add chained products again for a resubscribe order.
			if ( isset( $_GET['resubscribe'] ) && isset( $_GET['_wpnonce'] ) ) { // phpcs:ignore
				$subscription = wcs_get_subscription( $_GET['resubscribe'] );  // phpcs:ignore

				foreach ( $subscription->get_items() as $item ) {
					if ( $item['product_id'] === $product_id && isset( $item['chained_product_of'] ) ) {
						return false;
					}
				}
				return $add_to_cart;
			}

			$product_id = ( isset( $_REQUEST['variation_id'] ) && $_REQUEST['variation_id'] > 0 ) ? $_REQUEST['variation_id'] : $product_id; // phpcs:ignore

			$chained_products_in_cart = $this->get_chained_products_present_in_cart( $product_id );

			$validation_result = $this->are_chained_products_available( $product_id, $main_product_quantity, $chained_products_in_cart );

			if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {

				wc_add_notice(
					sprintf(
						/* translators: 1: Parent product name 2: Chained item name(s) */
						_x( 'Can not add %1$s to cart as %2$s doesn\'t have sufficient quantity in stock.', 'cart validation notice', 'woocommerce-chained-products' ),
						is_callable( array( $wc_chained_products, 'get_product_title' ) ) ? $wc_chained_products->get_product_title( intval( $product_id ) ) : '',
						! empty( $validation_result['product_titles'] && is_array( $validation_result['product_titles'] ) ) ? implode( ', ', $validation_result['product_titles'] ) : _x( 'Chained products', 'chained products title', 'woocommerce-chained-products' )
					),
					'error'
				);

				return false;
			}
			return $add_to_cart;
		}

		/**
		 * Function to get quantity chained products already present in cart
		 *
		 * @param int $product_id ID of the parent product that is being added to the cart.
		 * @return array $chained_products_in_cart;
		 */
		public function get_chained_products_present_in_cart( $product_id = '' ) {

			$chained_products_in_cart = array();

			$cart_contents = WC()->cart->cart_contents;

			if ( ! empty( $product_id ) && ! empty( $cart_contents ) ) {
				$chained_product_detail = $this->get_chained_product_data_by_product_id( $product_id );

				if ( is_array( $chained_product_detail ) && count( $chained_product_detail ) > 0 ) {

					foreach ( $cart_contents as $cart_item_key => $cart_item ) {

						$in_cart_chained_product_id = ( isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];

						if ( array_key_exists( $in_cart_chained_product_id, $chained_product_detail ) ) {

							if ( array_key_exists( $in_cart_chained_product_id, $chained_products_in_cart ) ) {
								$chained_products_in_cart[ $in_cart_chained_product_id ] += $cart_item['quantity'];
							} else {
								$chained_products_in_cart[ $in_cart_chained_product_id ] = $cart_item['quantity'];
							}
						}
					}
				}
			}

			return $chained_products_in_cart;
		}

		/**
		 * Function to validate updation of cart based on stock quantity of chained products
		 *
		 * @global object $woocommerce - Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param boolean $update_cart Passed validation.
		 * @param string  $cart_item_key Cart item key.
		 * @param array   $cart_item Cart item data.
		 * @param int     $main_product_quantity Parent product quantity.
		 * @return boolean $update_cart
		 */
		public function woocommerce_chained_update_cart_validation( $update_cart = true, $cart_item_key = '', $cart_item = array(), $main_product_quantity = 0 ) {
			global $woocommerce, $wc_chained_products;
			$product_id        = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0 ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			$validation_result = $this->are_chained_products_available( $product_id, $main_product_quantity );

			if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {
				wc_add_notice(
					sprintf(
						/* translators: 1: Parent product name 2: Chained item name(s) */
						_x( 'Can not increase quantity of %1$s because %2$s doesn\'t have sufficient quantity in stock.', 'cart validation message', 'woocommerce-chained-products' ),
						is_callable( array( $wc_chained_products, 'get_product_title' ) ) ? $wc_chained_products->get_product_title( intval( $product_id ) ) : '',
						! empty( $validation_result['product_titles'] && is_array( $validation_result['product_titles'] ) ) ? implode( ', ', $validation_result['product_titles'] ) : _x( 'Chained products', 'chained products title', 'woocommerce-chained-products' )
					),
					'error'
				);
				return false;
			}
			return $update_cart;
		}

		/**
		 * Function to validate cart when it is loaded
		 *
		 * @global object $woocommerce Main instance of WooCommerce
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 */
		public function woocommerce_chained_check_cart_items() {
			global $woocommerce, $wc_chained_products;
			$message = array();

			$cart = WC()->cart;
			if ( $cart instanceof WC_Cart ) {
				$cart_page_id = wc_get_page_id( 'cart' );
				foreach ( $cart->cart_contents as $cart_item_key => $cart_item_value ) {

					if ( isset( $cart_item_value['chained_item_of'] ) ) {
						continue;
					}

					$product_id        = ( isset( $cart_item_value['variation_id'] ) && $cart_item_value['variation_id'] > 0 ) ? $cart_item_value['variation_id'] : $cart_item_value['product_id'];
					$validation_result = $this->are_chained_products_available( $product_id, $cart_item_value['quantity'] );

					if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {
						$message[] = sprintf(
							/* translators: 1: Parent product name 2: Chained item name(s) */
							_x( 'Can not add %1$s to cart as %2$s doesn\'t have sufficient quantity in stock.', 'out of stock notice', 'woocommerce-chained-products' ),
							is_callable( array( $wc_chained_products, 'get_product_title' ) ) ? $wc_chained_products->get_product_title( intval( $product_id ) ) : '',
							! empty( $validation_result['product_titles'] && is_array( $validation_result['product_titles'] ) ) ? implode( ', ', $validation_result['product_titles'] ) : _x( 'Chained products', 'chained products title', 'woocommerce-chained-products' )
						);
						$cart->set_quantity( $cart_item_key, 0 );
						if ( $cart_page_id ) {
							if ( wp_safe_redirect( apply_filters( 'woocommerce_get_cart_url', get_permalink( $cart_page_id ) ) ) ) {
								exit;
							}
						}
					}
				}
				if ( count( $message ) > 0 ) {
					wc_add_notice( sprintf( __( implode( '. ', $message ), 'woocommerce-chained-products' ) ), 'message' ); // @codingStandardsIgnoreLine
				}
			}
		}

		/**
		 * Function for adding Chained Products Shortcode
		 */
		public function register_chained_products_shortcodes() {

			add_shortcode( 'chained_products', array( $this, 'get_chained_products_html_view' ) );
		}

		/**
		 * Get the parent id of the existing chained product in the cart items.
		 *
		 * @param int $product_id The chained product's id.
		 * @return int Return parent product id otherwise 0.
		 */
		public function get_parent_id_of_child_item_from_cart( $product_id = 0 ) {
			$cart = ! empty( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ? WC()->cart->get_cart() : array();

			// Return if the cart is empty.
			if ( empty( $cart ) ) {
				return 0;
			}

			foreach ( $cart as $cart_item ) {
				// Get the cart item data.
				$product = ! empty( $cart_item['data'] ) ? $cart_item['data'] : null;

				// Continue if the current chained product is not equal to the provided product.
				if ( empty( $cart_item['chained_item_of'] ) || ! $product instanceof WC_Product || ! is_callable( array( $product, 'get_id' ) ) || $product_id !== $product->get_id() ) {
					continue;
				}

				// Get the parent product item by the cart item key.
				$parent         = $cart[ $cart_item['chained_item_of'] ];
				$parent_product = ! empty( $parent['data'] ) ? $parent['data'] : null;

				return $parent_product instanceof WC_Product && is_callable( array( $parent_product, 'get_id' ) ) ? $parent_product->get_id() : 0;
			}
			return 0;
		}

		/**
		 * Function to get chained product data by product id.
		 *
		 * @param int|string $product_id The product id.
		 *
		 * @return array
		 */
		public function get_chained_product_data_by_product_id( $product_id = 0 ) {

			$cp_details = array();

			if ( empty( $product_id ) ) {
				return $cp_details;
			}

			$cp_all_products_details = wp_cache_get( 'sa_all_chained_products_details', 'woocommerce-chained-products' );

			if ( ! empty( $cp_all_products_details ) ) {
				$cp_details = ! empty( $cp_all_products_details[ $product_id ] ) ? $cp_all_products_details[ $product_id ] : array();
			} else {
				$cp_all_products_details = array();
			}

			if ( empty( $cp_details ) ) {

				$cp_details = maybe_unserialize( self::chained_product_details( $product_id ) );

				if ( ! empty( $cp_details ) ) {
					$cp_all_products_details[ $product_id ] = $cp_details;
					// Get the chained products data.
					wp_cache_set( 'sa_all_chained_products_details', $cp_all_products_details, 'woocommerce-chained-products' );
				}
			}

			return $cp_details;
		}

		/**
		 * Modify stock status of chained parent.
		 *
		 * @param boolean    $is_in_stock Is in stock.
		 * @param WC_Product $product The product object.
		 * @return boolean
		 */
		public function chained_products_is_in_stock( $is_in_stock = true, $product = null ) {

			if ( ( $product instanceof WC_Product ) && is_callable( array( $product, 'get_id' ) ) ) {
				return $is_in_stock;
			}

			if ( true === $is_in_stock ) {

				$check_is_in_stock_cache_key = 'sa_cp_check_is_in_stock_' . $product->get_id();
				$check_is_in_stock           = wp_cache_get( $check_is_in_stock_cache_key, 'woocommerce-chained-products' );

				if ( ! empty( $check_is_in_stock ) && ( 'yes' === $check_is_in_stock ) ) {
					wp_cache_delete( $check_is_in_stock_cache_key, 'woocommerce-chained-products' );
					return $is_in_stock;
				}

				$is_manage_stock = ( is_callable( array( $product, 'get_meta' ) ) ) ? $product->get_meta( '_chained_product_manage_stock' ) : 'no';
				if ( 'yes' === $is_manage_stock ) {

					$product_id = $product->get_id();
					// Get the chained products data.
					$chained_product_detail = $this->get_chained_product_data_by_product_id( $product_id );

					if ( ! empty( $chained_product_detail ) ) {
						foreach ( $chained_product_detail as $chained_product_id => $chained_product_data ) {
							$chained_product = wc_get_product( $chained_product_id );
							if ( is_object( $chained_product ) && is_callable( array( $chained_product, 'is_in_stock' ) ) ) {

								$chained_is_in_stock = is_callable( array( $chained_product, 'is_in_stock' ) ) ? $chained_product->is_in_stock() : false;
								if ( false === $chained_is_in_stock ) {
									$is_in_stock = $chained_is_in_stock;
									break;
								}
							}
						}
					}
				}
			} else {
				$cp_parent_id = $this->get_parent_id_of_child_item_from_cart( $product->get_id() );

				if ( ! empty( $cp_parent_id ) ) {
					$cp_parent   = wc_get_product( $cp_parent_id );
					$is_in_stock = ( 'no' === ( is_object( $cp_parent ) && is_callable( array( $cp_parent, 'get_meta' ) ) ? $cp_parent->get_meta( '_chained_product_manage_stock' ) : 'no' ) );
				}
			}
			return $is_in_stock;
		}

		/**
		 * Get chained parent ids.
		 * Added compatibility for WooCommerce Waitlist for The team at PIE.
		 *
		 * @param integer $product_id The product id.
		 * @return array $ids
		 */
		public function get_chained_parent_ids( $product_id = 0 ) {
			global $wpdb;
			$ids = array();
			if ( ! empty( $product_id ) ) {
				$parent_ids = $wpdb->get_col( // phpcs:ignore
					$wpdb->prepare(
						"SELECT post_id
							FROM {$wpdb->postmeta}
							WHERE meta_key = %s
								AND meta_value LIKE %s",
						'_chained_product_detail',
						'%' . $wpdb->esc_like( $product_id ) . '%'
					)
				);
				if ( ! empty( $parent_ids ) ) {
					foreach ( $parent_ids as $parent_id ) {
						$chained_product_detail = self::chained_product_details( $parent_id );
						$chained_product_ids    = ( ! empty( $chained_product_detail ) ) ? array_keys( $chained_product_detail ) : array();
						$chained_product_ids    = array_map( 'absint', $chained_product_ids );
						if ( in_array( absint( $product_id ), $chained_product_ids, true ) ) {
							$ids[] = absint( $parent_id );
						}
					}
				}
			}
			return $ids;
		}

		/**
		 * Function for Shortcode with included chained product detail and for Ajax response of chained product details in json encoded format
		 *
		 * @todo In some theme, appending the elements after to the last element not a new line for grid view.
		 * @todo Show Load more button for simple products.
		 *
		 * @global object $post
		 * @global array $variation_titles
		 * @global int $chained_parent_id
		 * @global array $shortcode_attributes
		 * @global WC_Admin_Chained_Products $wc_chained_products Main instance of Chained Products admin class
		 * @param array $chained_attributes Chained product attributes.
		 * @return string $chained_product_content
		 */
		public function get_chained_products_html_view( $chained_attributes = array() ) {

			global $post, $variation_titles, $chained_parent_id, $shortcode_attributes, $wc_chained_products, $chained_product_details;
			$chained_product_content = '';

			if ( ! empty( $chained_attributes['form_value']['variable_id'] ) ) {

				$chained_parent_id    = wc_clean( wp_unslash( $chained_attributes['form_value']['variable_id'] ) );
				$parent_product       = wc_get_product( $chained_parent_id );
				$shortcode_attributes = $chained_attributes['form_value'];

			} else {
				if ( empty( $post->ID ) ) {
					return '';
				}
				$chained_parent_id = $post->ID;
				$parent_product    = wc_get_product( $chained_parent_id );
				if ( ! ( $parent_product instanceof WC_Product ) ) {
					return '';
				}

				$shortcode_attributes['price']     = ! empty( $chained_attributes['price'] ) ? $chained_attributes['price'] : 'yes';
				$shortcode_attributes['quantity']  = ! empty( $chained_attributes['quantity'] ) ? $chained_attributes['quantity'] : 'yes';
				$shortcode_attributes['style']     = ! empty( $chained_attributes['style'] ) ? $chained_attributes['style'] : 'grid';
				$shortcode_attributes['css_class'] = ! empty( $chained_attributes['css_class'] ) ? $chained_attributes['css_class'] : '';

				$chained_item_css_class = apply_filters( 'chained_item_css_class', 'chained_items_container', $chained_parent_id );
				$chained_item_css_class = trim( $chained_item_css_class );

				$chained_product_content .= '<input type = "hidden" id = "show_price" value = "' . esc_attr( $shortcode_attributes['price'] ) . '"/>';
				$chained_product_content .= '<input type = "hidden" id = "show_quantity" value = "' . esc_attr( $shortcode_attributes['quantity'] ) . '"/>';
				$chained_product_content .= '<input type = "hidden" id = "select_style" value = "' . esc_attr( $shortcode_attributes['style'] ) . '"/>';
				$chained_product_content .= '<input type = "hidden" id = "wc_cp_page_number" value = "0"/>';
				$chained_product_content .= '<div class = "tab-included-products ' . $chained_item_css_class . ' ' . $shortcode_attributes['css_class'] . '">';

				if ( is_callable( array( $parent_product, 'is_type' ) ) && $parent_product->is_type( 'variable' ) ) {
					$chained_product_content .= '</div>';
					$chained_product_content .= sprintf( '<img style="display:none" id="%1$s" src="%2$s" />', esc_attr( 'wc_cp_load_more' ), apply_filters( 'wc_cp_loader_image', includes_url( 'images/spinner.gif' ) ) );
					$chained_product_content .= sprintf( '<a id="%s" href="" style="display:none" > %s </a>', esc_attr( 'wc_cp_load_more' ), esc_html_x( 'Load more', 'load more text', 'woocommerce-chained-products' ) );
					return $chained_product_content;
				}
			}

			$post_per_page           = ! empty( $shortcode_attributes['post_per_page'] ) ? intval( $shortcode_attributes['post_per_page'] ) : ( ! empty( $chained_attributes['form_value']['post_per_page'] ) ? intval( $chained_attributes['form_value']['post_per_page'] ) : -1 );
			$page                    = ! empty( $shortcode_attributes['page'] ) ? absint( $shortcode_attributes['page'] ) : ( ! empty( $chained_attributes['form_value']['page'] ) ? intval( $chained_attributes['form_value']['page'] ) : 0 );
			$total_chained_details   = is_callable( array( $wc_chained_products, 'get_all_chained_product_details' ) ) ? $wc_chained_products->get_all_chained_product_details( $chained_parent_id ) : array();
			$chained_product_details = $total_chained_details;
			if ( ! empty( $total_chained_details ) && $post_per_page > 0 ) {
				$index                   = intval( $page ) * intval( $post_per_page );
				$chained_product_details = array_slice( $total_chained_details, $index, $post_per_page, true );
			}
			$chained_product_ids = is_array( $chained_product_details ) ? array_keys( $chained_product_details ) : array();

			if ( ! empty( $chained_product_ids ) ) {

				$chained_product_instance = is_callable( array( $wc_chained_products, 'get_product_instance' ) ) ? $wc_chained_products->get_product_instance( $chained_parent_id ) : null;
				if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && 'yes' === $chained_product_instance->get_meta( '_chained_product_manage_stock', true ) && $chained_product_instance->is_in_stock() ) {

					if ( is_callable( array( $chained_product_instance, 'backorders_allowed' ) ) && ! $chained_product_instance->backorders_allowed() ) {
						$max_quantity = $chained_product_instance->get_stock_quantity();

						if ( ! empty( $max_quantity ) ) {
							for ( $max_count = 1; $max_count <= $max_quantity; $max_count++ ) {

								$validation_result = $this->are_chained_products_available( $chained_parent_id, $max_count );
								if ( ! empty( $validation_result ) && ! empty( $validation_result['stock_status'] ) && 'outofstock' === $validation_result['stock_status'] ) {
									break;
								}
							}
						}

						$stock_format = get_option( 'woocommerce_stock_format' );

						if ( ! empty( $max_quantity ) ) {
							if ( ! empty( $stock_format ) ) {
								$chained_product_content .= '';
							} else {
								$chained_product_content .= sprintf( '<div class="%1$s" data-stock="%2$s" hidden></div>', esc_attr( 'wccp-stock' ), esc_attr( $max_count - 1 ) );
							}
						} elseif ( empty( $max_quantity ) ) {
							$availability = is_callable( array( $chained_product_instance, 'get_availability' ) ) ? $chained_product_instance->get_availability() : array();
							if ( ! empty( $availability['class'] ) && 'out-of-stock' === $availability['class'] ) {
								$chained_product_content .= sprintf( '<div class="%1$s" data-stock="%2$s" hidden></div>', esc_attr( 'wccp-stock' ), 0 );
							}
						}
					}
				}

				// For list/grid view of included product.
				if ( isset( $shortcode_attributes['style'] ) && 'list' === $shortcode_attributes['style'] ) {

					$chained_product_content .= ( '<ul class="products wccp-list-view" ) >' );

					foreach ( $chained_product_details as $id => $product_data ) {

						$product = wc_get_product( $id );
						if ( ! ( $product instanceof WC_Product ) ) {
							continue;
						}

						if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
							$priced_individually = ( isset( $product_data['priced_individually'] ) ) ? $product_data['priced_individually'] : 'no';
							$price               = ( 'no' === $priced_individually ) ? wc_format_sale_price( wc_price( $product->get_price() ), '' ) : wc_price( $product->get_price() );
						} else {
							$price = $product->get_price_html_from_to( wc_price( $product->get_price() ), '' );
						}

						$price_html = apply_filters( 'woocommerce_free_price_html', $price, $product );

						if ( $product instanceof WC_Product_Simple ) {
							$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_id() : $product->id;
						} else {
							$product_id = ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) ? $product->get_parent_id() : $product->parent->id;
						}

						$chained_product_content .= ( "<li ><a href='" . get_permalink( $product_id ) . "' style='text-decoration: none;'>" . ( ! empty( $product_data['product_name'] ) ? $product_data['product_name'] : '' ) );
						$chained_product_content .= ( isset( $shortcode_attributes['quantity'] ) && 'yes' === $shortcode_attributes['quantity'] ) ? ' ( &times; ' . $product_data['unit'] . ' )' : '';
						$chained_product_content .= ( isset( $shortcode_attributes['price'] ) && 'yes' === $shortcode_attributes['price'] ) ? " <span class='price'>" . $price_html . '</span>' : '';
						$chained_product_content .= '</a></li>';

					}

					$chained_product_content .= '</ul>';

				} elseif ( isset( $shortcode_attributes['style'] ) && 'grid' === $shortcode_attributes['style'] ) {

					$atts             = array();
					$product_ids      = array();
					$variation_titles = array();

					foreach ( $chained_product_ids as $chained_product_id ) {

						$_product = wc_get_product( $chained_product_id );

						if ( $_product instanceof WC_Product_Variation ) {
							$parent_id      = is_callable( array( $_product, 'get_parent_id' ) ) ? intval( $_product->get_parent_id() ) : 0;
							$product_ids[]  = $parent_id;
							$variation_data = is_callable( array( $_product, 'get_variation_attributes' ) ) ? $_product->get_variation_attributes() : array();

							if ( ! empty( $variation_data ) ) {
								$variation_titles[ $parent_id ][ $chained_product_id ] = ' ( ' . wc_get_formatted_variation( $variation_data, true ) . ' )';
							}
						} else {
							$product_ids[] = $chained_product_id;
						}
					}

					$atts['ids'] = implode( ',', $product_ids );

					if ( empty( $atts ) ) {
						return;
					}

					$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

					// Get order + orderby args from string.
					$orderby_value = explode( '-', $orderby_value );
					$orderby       = esc_attr( $orderby_value[0] );
					$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'asc';

					extract( // @codingStandardsIgnoreLine
						shortcode_atts(
							array(
								'orderby' => strtolower( $orderby ),
								'order'   => strtoupper( $order ),
							),
							$atts
						)
					);

					$args = array(
						'post_type'      => array( 'product' ),
						'orderby'        => $orderby,
						'order'          => $order,
						'posts_per_page' => $post_per_page, // @codingStandardsIgnoreLine
					);

					if ( isset( $atts['ids'] ) ) {
						$args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
					}

					ob_start();

					$alter_shop_loop_item = has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

					remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

					if ( $alter_shop_loop_item ) {
						remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					}

					// For adding all visibility related actions & filters that are specific to Chained Products.
					do_action( 'add_chained_products_actions_filters' );
					add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'woocommerce_template_chained_loop_quantity_and_price' ) );

					if ( version_compare( WOOCOMMERCE_VERSION, '1.6', '<' ) ) {

						query_posts( $args ); // @codingStandardsIgnoreLine
						wc_get_template_part( 'loop', 'shop' ); // Depricated since version 1.6.

					} else {

						$products = new WP_Query( $args );

						if ( $products->have_posts() ) {

							while ( $products->have_posts() ) {
									$products->the_post();
									wc_get_template_part( 'content', 'product' );
							}

							$chained_product_content .= '<ul class="products wccp-grid-view">' . ob_get_clean() . '</ul>';

						}
					}

					remove_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'woocommerce_template_chained_loop_quantity_and_price' ), 10 );

					// For removing all visibility related actions & filters that are specific to Chained Products.
					do_action( 'remove_chained_products_actions_filters' );
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

					if ( $alter_shop_loop_item ) {
						add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					}

					wp_reset_query(); // @codingStandardsIgnoreLine

				}

				// Add the page for next pagination.
				if ( ! empty( $chained_attributes['form_value']['variable_id'] ) && ! empty( $post_per_page ) ) {
					if ( count( $chained_product_ids ) >= $post_per_page ) {
						$page++;
						$cp_ppg_component         = '<div class="wccp-page-no" data-page-number="' . esc_attr( $page ) . '" hidden></div>';
						$chained_product_content .= $cp_ppg_component;
					}
				}
			}

			$chained_product_content .= ( $parent_product instanceof WC_Product_Simple && $parent_product->is_type( 'simple' ) ) ? '</div>' : '';
			return sprintf( '<div id="%1$s"> %2$s </div>', esc_attr( 'wccp-list' ), wp_kses_post( $chained_product_content ) );
		}

		/**
		 * Method to check the HPOS is enabled or not.
		 *
		 * @return bool Return true of HPOS is enabled otherwise false.
		 */
		public static function wc_cp_is_hpos_enabled() {

			return class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && is_callable( array( '\Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled' ) ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}

		/**
		 * Get the chained details by product id or object.
		 * It will fetch only the first level chained products details.
		 *
		 * @param integer|WC_Product $product The product.
		 *
		 * @return array
		 */
		public static function chained_product_details( $product = 0 ) {

			if ( empty( $product ) ) {
				return array();
			}

			if ( ! $product instanceof WC_Product && is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			$details = is_callable( array( $product, 'get_meta' ) ) ? $product->get_meta( '_chained_product_detail', true ) : array();
			return ! empty( $details ) && is_array( $details ) ? $details : array();
		}

		/**
		 * Function to declare WooCommerce HPOS compatibility.
		 */
		public function declare_hpos_compatibility() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-chained-products/woocommerce-chained-products.php', true );
			}
		}
	}//end class
}
