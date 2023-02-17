<?php
/**
 * WC_PB_Addons_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Addons Compatibility.
 *
 * @version  6.18.0
 */
class WC_PB_Addons_Compatibility {

	public static function init() {

		// Add Addons script as a dependency to the Product Bundles script.
		add_filter( 'woocommerce_pb_script_dependencies', array( __CLASS__, 'add_script_dependency' ), 10, 1 );

		// Support for Product Addons.
		add_action( 'woocommerce_bundled_product_add_to_cart', array( __CLASS__, 'addons_support' ), 10, 2 );
		add_action( 'woocommerce_bundled_single_variation', array( __CLASS__, 'addons_support' ), 15, 2 );

		// Prefix form fields.
		add_filter( 'product_addons_field_prefix', array( __CLASS__, 'addons_cart_prefix' ), 10, 2 );

		// Validate add to cart Addons.
		add_filter( 'woocommerce_bundled_item_add_to_cart_validation', array( __CLASS__, 'validate_bundled_item_addons' ), 10, 5 );

		// Add addons identifier to bundled item stamp.
		add_filter( 'woocommerce_bundled_item_cart_item_identifier', array( __CLASS__, 'bundled_item_addons_stamp' ), 10, 2 );

		// Add option to disable Addons at component level.
		add_action( 'woocommerce_bundled_product_admin_advanced_html', array( __CLASS__, 'display_addons_disable_option' ), 15, 4 );

		// Save option to disable Addons at component level.
		add_filter( 'woocommerce_bundles_process_bundled_item_admin_data', array( __CLASS__, 'process_addons_disable_option' ), 10, 4 );

		// Before and after add-to-cart handling.
		add_action( 'woocommerce_bundled_item_before_add_to_cart', array( __CLASS__, 'before_bundled_add_to_cart' ), 10, 5 );
		add_action( 'woocommerce_bundled_item_after_add_to_cart', array( __CLASS__, 'after_bundled_add_to_cart' ), 10, 5 );

		// Load child Addons data from the parent cart item data array.
		add_filter( 'woocommerce_bundled_item_cart_data', array( __CLASS__, 'get_bundled_cart_item_data_from_parent' ), 10, 2 );

		/*
		 * Aggregate add-ons costs and calculate them after PB has applied discounts.
		 * Also, do not charge anything for add-ons if Priced Individually is disabled and the 'filters' cart pricing method is in use.
		 */
		if ( 'filters' === WC_PB_Product_Prices::get_bundled_cart_item_discount_method() ) {

			// Aggregate add-ons costs and calculate them after PB has applied discounts.
			add_filter( 'woocommerce_bundled_cart_item', array( __CLASS__, 'preprocess_bundled_cart_item_addon_data' ), 0, 2 );

			// Do not let add-ons adjust prices when PB modifies them.
			add_filter( 'woocommerce_product_addons_adjust_price', array( __CLASS__, 'adjust_addons_price' ), 15, 2 );

			// Remove bundled item add-on prices in product bundle pages when bundled items are not Priced Individually.
			add_action( 'woocommerce_bundled_product_price_filters_added', array( __CLASS__, 'add_addon_price_zero_filter' ) );
			add_action( 'woocommerce_bundled_product_price_filters_removed', array( __CLASS__, 'remove_addon_price_zero_filter' ) );
		}
	}

	/**
	 * Add Addons script as a dependency to the Product Bundles script.
	 *
	 * @since  6.16.0
	 *
	 * @param  array  add_script_dependency
	 * @return array
	 */
	public static function add_script_dependency( $dependencies ) {

		$dependencies[] = 'woocommerce-addons';
		
		return $dependencies;
	}

	/**
	 * Used to tell if a product has (required) addons.
	 *
	 * @since  5.9.2
	 *
	 * @param  mixed    $product
	 * @param  boolean  $required
	 * @return boolean
	 */
	public static function has_addons( $product, $required = false ) {

		if ( is_object( $product ) && is_a( $product, 'WC_Product' ) ) {
			$product_id = $product->get_id();
		} else {
			$product_id = absint( $product );
		}

		$has_addons = false;
		$cache_key  = 'product_addons_' . $product_id;

		$addons = WC_PB_Helpers::cache_get( $cache_key );

		if ( is_null( $addons ) ) {
			$addons = WC_Product_Addons_Helper::get_product_addons( $product_id, false, false );
			WC_PB_Helpers::cache_set( $cache_key, $addons );
		}

		if ( ! empty( $addons ) ) {

			if ( $required ) {

				foreach ( $addons as $addon ) {

					$type = ! empty( $addon[ 'type' ] ) ? $addon[ 'type' ] : '';

					if ( 'heading' !== $type && isset( $addon[ 'required' ] ) && '1' == $addon[ 'required' ] ) {
						$has_addons = true;
						break;
					}
				}

			} else {
				$has_addons = true;
			}
		}

		return $has_addons;
	}

	/**
	 * Show option to disable bundled product addons.
	 *
	 * @param  int    $loop
	 * @param  int    $product_id
	 * @param  array  $item_data
	 * @param  int    $post_id
	 * @return void
	 */
	public static function display_addons_disable_option( $loop, $product_id, $item_data, $post_id ) {

		$disable_addons = isset( $item_data[ 'disable_addons' ] ) && 'yes' === $item_data[ 'disable_addons' ];

		?><div class="disable_addons">
			<div class="form-field">
				<label for="disable_addons"><?php echo __( 'Disable Add-Ons', 'woocommerce-product-bundles' ) ?></label>
				<input type="checkbox" class="checkbox"<?php echo ( $disable_addons ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][disable_addons]" <?php echo ( $disable_addons ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to disable any Product Add-Ons associated with this bundled product.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div><?php
	}

	/**
	 * Save option that disables bundled product addons.
	 *
	 * @param  array  $item_data
	 * @param  array  $data
	 * @param  mixed  $item_id
	 * @param  mixed  $post_id
	 */
	public static function process_addons_disable_option( $item_data, $data, $item_id, $post_id ) {

		if ( isset( $data[ 'disable_addons' ] ) ) {
			$item_data[ 'disable_addons' ] = 'yes';
		} else {
			$item_data[ 'disable_addons' ] = 'no';
		}

		return $item_data;
	}

	/**
	 * Support for bundled item addons.
	 *
	 * @param  int              $product_id
	 * @param  WC_Bundled_Item  $item
	 * @return void
	 */
	public static function addons_support( $product_id, $item ) {

		global $Product_Addon_Display, $product;

		if ( ! empty( $Product_Addon_Display ) ) {

			if ( doing_action( 'wp_ajax_woocommerce_configure_bundle_order_item' ) ) {
				return;
			}

			if ( $item->get_product()->is_type( 'variable' ) && doing_action( 'woocommerce_bundled_product_add_to_cart' ) ) {
				return;
			}

			if ( $item->disable_addons() ) {
				return;
			}

			$product_bak = isset( $product ) ? $product : false;
			$product     = $item->get_product();

			WC_PB_Compatibility::$addons_prefix          = $item->get_id();
			WC_PB_Compatibility::$compat_bundled_product = $item->get_product();

			$Product_Addon_Display->display( $product_id, false );

			WC_PB_Compatibility::$addons_prefix = WC_PB_Compatibility::$compat_bundled_product = '';

			if ( $product_bak ) {
				$product = $product_bak;
			}
		}
	}

	/**
	 * Sets a unique prefix for unique add-ons. The prefix is set and re-set globally before validating and adding to cart.
	 *
	 * @param  string   $prefix         unique prefix
	 * @param  int      $product_id     the product id
	 * @return string                   a unique prefix
	 */
	public static function addons_cart_prefix( $prefix, $product_id ) {

		if ( ! empty( WC_PB_Compatibility::$addons_prefix ) ) {
			$prefix = WC_PB_Compatibility::$addons_prefix . '-';
		}

		if ( ! empty( WC_PB_Compatibility::$bundle_prefix ) ) {
			$prefix = WC_PB_Compatibility::$bundle_prefix . '-' . WC_PB_Compatibility::$addons_prefix . '-';
		}

		return $prefix;
	}

	/**
	 * Add addons identifier to bundled item stamp, in order to generate new cart ids for bundles with different addons configurations.
	 *
	 * @param  array   $bundled_item_stamp
	 * @param  string  $bundled_item_id
	 * @return array
	 */
	public static function bundled_item_addons_stamp( $bundled_item_stamp, $bundled_item_id ) {

		global $Product_Addon_Cart;

		// Store bundled item addons add-ons config in stamp to avoid generating the same bundle cart id.
		if ( ! empty( $Product_Addon_Cart ) ) {

			$addon_data = array();

			// Set addons prefix.
			WC_PB_Compatibility::$addons_prefix = $bundled_item_id;

			$bundled_product_id = $bundled_item_stamp[ 'product_id' ];

			$addon_data = $Product_Addon_Cart->add_cart_item_data( $addon_data, $bundled_product_id );

			// Reset addons prefix.
			WC_PB_Compatibility::$addons_prefix = '';

			if ( ! empty( $addon_data[ 'addons' ] ) ) {
				$bundled_item_stamp[ 'addons' ] = $addon_data[ 'addons' ];
			}
		}

		return $bundled_item_stamp;
	}

	/**
	 * Validate bundled item addons.
	 *
	 * @param  bool  $add
	 * @param  int   $product_id
	 * @param  int   $quantity
	 * @return bool
	 */
	public static function validate_bundled_item_addons( $add, $bundle, $bundled_item, $quantity, $variation_id ) {

		// Ordering again? When ordering again, do not revalidate addons.
		$order_again = isset( $_GET[ 'order_again' ] ) && isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wpnonce' ] ), 'woocommerce-order_again' );

		if ( $order_again  ) {
			return $add;
		}

		$bundled_item_id = $bundled_item->get_id();
		$product_id      = $bundled_item->get_product_id();

		// Validate add-ons.
		global $Product_Addon_Cart;

		if ( ! empty( $Product_Addon_Cart ) ) {

			WC_PB_Compatibility::$addons_prefix = $bundled_item_id;

			if ( false === $bundled_item->disable_addons() && false === $Product_Addon_Cart->validate_add_cart_item( true, $product_id, $quantity ) ) {
				$add = false;
			}

			WC_PB_Compatibility::$addons_prefix = '';
		}

		return $add;
	}

	/**
	 * Runs before adding a bundled item to the cart.
	 *
	 * @param  int    $product_id
	 * @param  int    $quantity
	 * @param  int    $variation_id
	 * @param  array  $variations
	 * @param  array  $bundled_item_cart_data
	 * @return void
	 */
	public static function after_bundled_add_to_cart( $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data ) {

		global $Product_Addon_Cart;

		// Reset addons prefix.
		WC_PB_Compatibility::$addons_prefix = '';

		if ( ! empty ( $Product_Addon_Cart ) ) {
			add_filter( 'woocommerce_add_cart_item_data', array( $Product_Addon_Cart, 'add_cart_item_data' ), 10, 2 );
		}
	}

	/**
	 * Runs after adding a bundled item to the cart.
	 *
	 * @param  int    $product_id
	 * @param  int    $quantity
	 * @param  int    $variation_id
	 * @param  array  $variations
	 * @param  array  $bundled_item_cart_data
	 * @return void
	 */
	public static function before_bundled_add_to_cart( $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data ) {

		global $Product_Addon_Cart;

		// Set addons prefix.
		WC_PB_Compatibility::$addons_prefix = $bundled_item_cart_data[ 'bundled_item_id' ];

		// Add-ons cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing Addons to re-add it.
		// Not doing so results in issues with file upload validation.

		if ( ! empty ( $Product_Addon_Cart ) ) {
			remove_filter( 'woocommerce_add_cart_item_data', array( $Product_Addon_Cart, 'add_cart_item_data' ), 10, 2 );
		}
	}

	/**
	 * Retrieve child cart item data from the parent cart item data array, if necessary.
	 *
	 * @param  array  $bundled_item_cart_data
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function get_bundled_cart_item_data_from_parent( $bundled_item_cart_data, $cart_item_data ) {

		// Add-ons cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing Addons to re-add it.
		if ( isset( $bundled_item_cart_data[ 'bundled_item_id' ] ) && isset( $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'addons' ] ) ) {
			$bundled_item_cart_data[ 'addons' ] = $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'addons' ];
		}

		return $bundled_item_cart_data;
	}

	/**
	 * Aggregate add-ons costs and calculate them after PB has applied discounts.
	 *
	 * @since  6.0.4
	 *
	 * @param  array              $cart_item
	 * @param  WC_Product_Bundle  $bundle
	 * @return array
	 */
	public static function preprocess_bundled_cart_item_addon_data( $cart_item, $bundle ) {

		if ( empty( $cart_item[ 'addons' ] ) ) {
			return $cart_item;
		}

		$bundled_item    = WC_PB_Helpers::get_runtime_prop( $cart_item[ 'data' ], 'bundled_cart_item' );
		$bundled_item_id = $cart_item[ 'bundled_item_id' ];

		if ( is_null( $bundled_item ) ) {
			$bundled_item = $bundle->get_bundled_item( $bundled_item_id );
		}

		if ( ! $bundled_item ) {
			return $cart_item;
		}

		if ( $bundled_item->is_priced_individually() ) {

			// Let PAO handle things on its own.
			if ( ! $discount = $bundled_item->get_discount( 'cart' ) ) {
				return $cart_item;
			}

			$cart_item[ 'data' ]->bundled_price_offset_pct = array();
			$cart_item[ 'data' ]->bundled_price_offset     = 0.0;

			if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

				// Read original % values from parent item.
				$addons_data = ! empty( $bundle_container_item[ 'stamp' ][ $bundled_item_id ][ 'addons' ] ) ? $bundle_container_item[ 'stamp' ][ $bundled_item_id ][ 'addons' ] : array();

				foreach ( $addons_data as $addon_key => $addon ) {

					// See 'WC_Bundled_Item::filter_get_price'.
					if ( 'percentage_based' === $addon[ 'price_type' ] ) {
						$cart_item[ 'data' ]->bundled_price_offset_pct[] = $addon[ 'price' ];
						$cart_item[ 'addons' ][ $addon_key ][ 'price' ]  = 0.0;
					} elseif ( 'flat_fee' === $addon[ 'price_type' ] ) {
						$cart_item[ 'data' ]->bundled_price_offset += (float) $addon[ 'price' ] / $cart_item[ 'quantity' ];
					} else {
						$cart_item[ 'data' ]->bundled_price_offset += (float) $addon[ 'price' ];
					}
				}
			}

		} else {

			// Priced Individually disabled? Give add-ons for free.
			foreach ( $cart_item[ 'addons' ] as $addon_key => $addon_data ) {
				$cart_item[ 'addons' ][ $addon_key ][ 'price' ] = 0.0;
			}
		}

		return $cart_item;
	}

	/**
	 * Do not let add-ons adjust prices when PB modifies them.
	 *
	 * @since  6.0.4
	 *
	 * @param  bool   $adjust
	 * @param  array  $cart_item
	 * @return bool
	 */
	public static function adjust_addons_price( $adjust, $cart_item ) {

		if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$adjust       = false;
			$bundled_item = WC_PB_Helpers::get_runtime_prop( $cart_item[ 'data' ], 'bundled_cart_item' );

			if ( is_null( $bundled_item ) ) {
				$bundle          = $bundle_container_item[ 'data' ];
				$bundled_item_id = $cart_item[ 'bundled_item_id' ];
				$bundled_item    = $bundle->get_bundled_item( $bundled_item_id );
			}

			// Only let add-ons adjust prices if PB doesn't modify bundled item prices in any way.
			if ( $bundled_item && $bundled_item->is_priced_individually() && ! $bundled_item->get_discount( 'cart' ) ) {
				$adjust = true;
			}
		}

		return $adjust;
	}

	/**
	 * Adds filter that discards bundled item add-on prices in product bundle pages.
	 *
	 * @since  6.0.4
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 */
	public static function add_addon_price_zero_filter( $bundled_item ) {

		if ( ! $bundled_item->is_priced_individually() ) {
			add_filter( 'woocommerce_product_addons_price_raw', array( __CLASS__, 'option_price_raw_zero_filter' ) );
			add_filter( 'woocommerce_product_addons_option_price_raw', array( __CLASS__, 'option_price_raw_zero_filter' ) );
		}
	}

	/**
	 * Removes filter that discards bundled item add-on prices in product bundle pages.
	 *
	 * @since  6.0.4
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 */
	public static function remove_addon_price_zero_filter( $bundled_item ) {

		if ( ! $bundled_item->is_priced_individually() ) {
			remove_filter( 'woocommerce_product_addons_price_raw', array( __CLASS__, 'option_price_raw_zero_filter' ) );
			remove_filter( 'woocommerce_product_addons_option_price_raw', array( __CLASS__, 'option_price_raw_zero_filter' ) );
		}
	}

	/**
	 * Discards bundled item add-on prices in product bundle pages.
	 *
	 * @since  6.0.4
	 *
	 * @param  mixed  $price
	 */
	public static function option_price_raw_zero_filter( $price ) {
		return '';
	}
}

WC_PB_Addons_Compatibility::init();
