<?php
/**
 * WC_PB_Min_Max_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Min/Max Quantities Compatibility.
 *
 * @version  6.16.1
 */
class WC_PB_Min_Max_Compatibility {

	/**
	 * The bundled item object whose qty input is being filtered.
	 * @var WC_Bundled_Item
	 */
	public static $bundled_item = false;

	/**
	 * Unfiltered quantity input data used at restoration time.
	 * @var array
	 */
	public static $unfiltered_args = false;

	/**
	 * Initilize hooks.
	 */
	public static function init() {

		/*
		 * Admin.
		 */

		// Filter Min/Max bundled item quantities based on "Group of" option on save.
		add_filter( 'woocommerce_bundles_process_bundled_item_admin_data', array( __CLASS__, 'filter_processed_bundled_item_data' ), 10, 4 );

		// Add a notice if Min/Max bundled item quantities are not compatible with the "Group of" option.
		add_action( 'admin_notices', array( __CLASS__, 'maybe_add_group_of_notice' ), 0 );

		// Filter Min/Max/Default quantity selector values when adding a new bundled item or editing an existing one.
		add_filter( 'woocommerce_add_bundled_product_item_data', array( __CLASS__, 'filter_add_bundled_product_item_data' ), 10, 3 );

		/*
		 * Cart.
		 */

		// Set global $bundled_item variable.
		add_action( 'woocommerce_after_bundled_item_cart_details', array( __CLASS__, 'restore_quantities_set' ), 9 );
		add_action( 'woocommerce_bundled_item_details', array( __CLASS__, 'restore_quantities_set' ), 34 );

		// Unset global $bundled_item variable.
		add_action( 'woocommerce_after_bundled_item_cart_details', array( __CLASS__, 'restore_quantities_unset' ), 11 );
		add_action( 'woocommerce_bundled_item_details', array( __CLASS__, 'restore_quantities_unset' ), 36 );

		// Restore bundled items quantities to the values they had before min/max interference.
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'save_quantity_input_args' ), 0, 2 );
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'restore_quantity_input_args' ), 11, 2 );

		// Double-check variation data quantities to account for "group of" option.
		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'bundled_variation_data' ), 15, 3 );

		// Disable min cart item quantity validation for bundled items.
		add_filter( 'wc_min_max_quantity_minimum_allowed_quantity', array( __CLASS__, 'restore_allowed_quantity' ), 10, 4 );

		// Disable max cart item quantity validation for bundled items.
		add_filter( 'wc_min_max_quantity_maximum_allowed_quantity', array( __CLASS__, 'restore_allowed_quantity' ), 10, 4 );

		// Add bundled item and input cart quantity to the product.
		add_filter( 'woocommerce_cart_item_product', array( __CLASS__, 'add_bundled_item_to_product' ), 10, 3 );

		// Filter Min bundled item Quantity based on "Group of" option on runtime.
		add_filter( 'woocommerce_bundled_item_quantity', array( __CLASS__, 'filter_bundled_item_min_quantity' ), 10, 3 );

		// Filter Default bundled item Quantity based on "Group of" option on runtime.
		add_filter( 'woocommerce_bundled_item_quantity_default', array( __CLASS__, 'filter_bundled_item_min_quantity' ), 10, 3 );

		// Filter Max bundled item Quantity based on "Group of" option on runtime.
		add_filter( 'woocommerce_bundled_item_quantity_max', array( __CLASS__, 'filter_bundled_item_max_quantity' ), 10, 3 );
	}

	/*
 	* Admin.
 	*/

	/**
	 * Filter Min/Max bundled item Quantity based on "Group of" option on save.
	 *
	 * @param  array  $item_data
	 * @param  array  $data
	 * @param  mixed  $item_id
	 * @param  mixed  $post_id
	 *
	 * @return array
	 */
	public static function filter_processed_bundled_item_data( $item_data, $data, $item_id, $post_id ) {

		if ( isset( $item_data[ 'allowed_variations' ] ) && is_array( $item_data[ 'allowed_variations' ] ) && 1 === count( $item_data[ 'allowed_variations' ] ) ) {
			$group_of_quantity = self::get_bundled_item_group_of_qty( $item_data[ 'allowed_variations' ][0] );
		} else {
			$group_of_quantity = self::get_bundled_item_group_of_qty( $item_data[ 'product_id' ] );
		}

		if ( $group_of_quantity ) {

			$adjusted_min_quantity     = WC_Min_Max_Quantities::adjust_min_quantity( $item_data[ 'quantity_min' ], $group_of_quantity );
			$adjusted_default_quantity = WC_Min_Max_Quantities::adjust_min_quantity( $item_data[ 'quantity_default' ], $group_of_quantity );
			$adjusted_max_quantity     = WC_Min_Max_Quantities::adjust_max_quantity( $item_data[ 'quantity_max' ], $group_of_quantity, $adjusted_min_quantity );

			if ( $item_data[ 'quantity_min' ] !== $adjusted_min_quantity ) {

				$item_data[ 'quantity_min' ] = $adjusted_min_quantity;

				/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
				WC_PB_Meta_Box_Product_Data::add_admin_error( sprintf( __( 'The <strong>Min Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Its value has been adjusted.', 'woocommerce-product-bundles' ), $item_data[ 'title' ], $group_of_quantity ) );
			}

			if ( $item_data[ 'quantity_default' ] !== $adjusted_default_quantity ) {

				$item_data[ 'quantity_default' ] = $adjusted_default_quantity;

				/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
				WC_PB_Meta_Box_Product_Data::add_admin_error( sprintf( __( 'The <strong>Default Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Its value has been adjusted.', 'woocommerce-product-bundles' ), $item_data[ 'title' ], $group_of_quantity ) );
			}

			if ( $item_data[ 'quantity_max' ] !== $adjusted_max_quantity ) {

				$item_data[ 'quantity_max' ] = $adjusted_max_quantity;

				/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
				WC_PB_Meta_Box_Product_Data::add_admin_error( sprintf( __( 'The <strong>Max Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Its value has been adjusted.', 'woocommerce-product-bundles' ), $item_data[ 'title' ], $group_of_quantity ) );
			}
		}

		return $item_data;
	}

	/**
	 * Add a notice if Min/Max bundled item quantities are not compatible with the "Group of" option.
	 *
	 * @param  mixed            $qty
	 * @param  WC_Bundled_Item  $bundled_item
	 * @param  array            $args
	 *
	 * @return mixed
	 */
	public static function maybe_add_group_of_notice() {

		global $post_id;

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		$product_type = WC_Product_Factory::get_product_type( $post_id );

		if ( 'bundle' !== $product_type ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return;
		}

		$bundled_items = $product->get_bundled_items();

		foreach ( $bundled_items as $bundled_item ) {

			$group_of_quantity = self::get_bundled_item_group_of_qty( $bundled_item );

			if ( $group_of_quantity ) {

				$item_data = $bundled_item->get_data();

				$min_quantity     = (int) $item_data[ 'quantity_min' ];
				$default_quantity = (int) $item_data[ 'quantity_default' ];
				$max_quantity     = (int) $item_data[ 'quantity_max' ];

				$adjusted_min_quantity     = WC_Min_Max_Quantities::adjust_min_quantity( $min_quantity, $group_of_quantity );
				$adjusted_default_quantity = WC_Min_Max_Quantities::adjust_min_quantity( $default_quantity, $group_of_quantity );
				$adjusted_max_quantity     = WC_Min_Max_Quantities::adjust_max_quantity( $max_quantity, $group_of_quantity );

				if ( $min_quantity !== $adjusted_min_quantity ) {
					/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
					WC_PB_Admin_Notices::add_notice( ( sprintf( __( 'The <strong>Min Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Please adjust its value and save your changes.', 'woocommerce-product-bundles' ), $bundled_item->get_title( true ), $group_of_quantity ) ), 'warning' );
				}

				if ( $default_quantity !== $adjusted_default_quantity ) {
					/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
					WC_PB_Admin_Notices::add_notice( ( sprintf( __( 'The <strong>Default Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Please adjust its value and save your changes.', 'woocommerce-product-bundles' ), $bundled_item->get_title( true ), $group_of_quantity ) ), 'warning' );
				}

				if ( $max_quantity !== $adjusted_max_quantity ) {
					/* translators: %1$s: Bundled product name, %2$s: Group of quantity */
					WC_PB_Admin_Notices::add_notice( ( sprintf( __( 'The <strong>Max Quantity</strong> of <strong>%1$s</strong> must be a multiple of <strong>%2$s</strong>. Please adjust its value and save your changes.', 'woocommerce-product-bundles' ), $bundled_item->get_title( true ), $group_of_quantity ) ), 'warning' );
				}
			}
		}
	}

	/**
	 * Filter Min/Max/Default quantity selector values when adding a new bundled item or editing an existing one.
	 *
	 * @param  array $item_data
	 * @param  int   $product_id
	 * @return array
	 */
	public static function filter_add_bundled_product_item_data( $item_data, $context, $product_id ) {

		if ( 'create' === $context ) {
			$group_of_quantity = self::get_bundled_item_group_of_qty( $product_id );
			if ( $group_of_quantity ) {
				$item_data[ 'quantity_min' ] = $item_data[ 'quantity_max' ] = $item_data[ 'quantity_default' ] = $group_of_quantity;
			}
		} elseif ( 'edit' === $context ) {
			$group_of_quantity = 0;

			if ( isset( $item_data[ 'allowed_variations' ] ) && is_array( $item_data[ 'allowed_variations' ] ) && 1 === count( $item_data[ 'allowed_variations' ] ) ) {
				$group_of_quantity = self::get_bundled_item_group_of_qty( $item_data[ 'allowed_variations' ][0] );
			} elseif ( isset( $item_data[ 'product_id' ] ) ) {
				$group_of_quantity = self::get_bundled_item_group_of_qty( $item_data[ 'product_id' ] );
			}
		}

		if ( $group_of_quantity ) {
			$item_data[ 'step' ] = $group_of_quantity;
		}

		return $item_data;
	}

	/*
	 * Cart.
	 */

	/**
	 * Set global $bundled_item variable.
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return void
	 */
	public static function restore_quantities_set( $bundled_item ) {
		self::$bundled_item = $bundled_item;
	}

	/**
	 * Unset global $bundled_item variable.
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return void
	 */
	public static function restore_quantities_unset( $bundled_item ) {
		self::$bundled_item = false;
	}

	/**
	 * Save unmodified quantity args.
	 *
	 * @param  array   $data
	 * @param  object  $product
	 * @return array
	 */
	public static function save_quantity_input_args( $data, $product ) {

		if ( is_object( self::$bundled_item ) || isset( $product->wc_mmq_bundled_item ) ) {
			self::$unfiltered_args = $data;
		} else {
			self::$unfiltered_args = false;
		}

		return $data;
	}

	/**
	 * Restore min/max bundled item quantities to the values they had before min/max interference.
	 *
	 * @param  array   $data
	 * @param  object  $product
	 * @return array
	 */
	public static function restore_quantity_input_args( $data, $product ) {

		if ( is_array( self::$unfiltered_args ) ) {

			$min_qty      = 0;
			$max_qty      = '';
			$input_qty    = 1;
			$group_of_qty = 0;

			if ( isset( self::$unfiltered_args[ 'min_value' ] ) ) {
				if ( self::$unfiltered_args[ 'min_value' ] > 0 || self::$unfiltered_args[ 'min_value' ] === 0 ) {
					$min_qty = absint( self::$unfiltered_args[ 'min_value' ] );
				}
			} elseif ( isset( $data[ 'min_value' ] ) && ( $data[ 'min_value' ] > 0 || $data[ 'min_value' ] === 0 ) ) {
				$min_qty = absint( $data[ 'min_value' ] );
			}

			if ( isset( self::$unfiltered_args[ 'max_value' ] ) ) {
				if ( self::$unfiltered_args[ 'max_value' ] > 0 || self::$unfiltered_args[ 'max_value' ] === 0 ) {
					$max_qty = absint( self::$unfiltered_args[ 'max_value' ] );
				}
			} elseif ( isset( $data[ 'max_value' ] ) && ( $data[ 'max_value' ] > 0 || $data[ 'max_value' ] === 0 ) ) {
				$max_qty = absint( $data[ 'max_value' ] );
			}

			if ( isset( self::$unfiltered_args[ 'input_value' ] ) ) {
				$input_qty = absint( self::$unfiltered_args[ 'input_value' ] );
			} elseif ( isset( $data[ 'input_value' ] ) ) {
				$input_qty = absint( $data[ 'input_value' ] );
			}

			if ( ! isset( $product->wc_mmq_bundled_item ) ) {
				// Single product page context.
				if ( isset( $data[ 'group_of' ] ) ) {
					$group_of_qty = $data[ 'group_of' ];
				} elseif ( $product instanceof WC_Product ) {
					$group_of_qty = self::get_bundled_item_group_of_qty( self::$bundled_item );
				}

				if ( $group_of_qty ) {
					$min_qty = WC_Min_Max_Quantities::adjust_min_quantity( $min_qty, $group_of_qty );
				}

				$input_qty = max( $input_qty, $min_qty );

			} else {
				// Cart context.
				$group_of_qty = self::get_bundled_item_group_of_qty( $product->wc_mmq_bundled_item ) * $product->container_quantity;

				if ( $group_of_qty ) {
					$min_qty = WC_Min_Max_Quantities::adjust_min_quantity( $min_qty, $group_of_qty );
				}
			}

			if ( empty( $max_qty ) || $max_qty >= $min_qty ) {
				$data[ 'min_value' ]   = $min_qty;
				$data[ 'max_value' ]   = $max_qty;
				$data[ 'input_value' ] = $input_qty;
				$data[ 'step' ]        = $group_of_qty;
			} else {
				$data[ 'min_value' ]   = $min_qty;
				$data[ 'max_value' ]   = $min_qty;
				$data[ 'input_value' ] = $min_qty;
				$data[ 'step' ]        = 1;
			}
		}

		return $data;
	}

	/**
	 * Double-check bundled variation data quantities to account for "group of" option.
	 *
	 * @param  array                 $variation_data
	 * @param  WC_Product            $bundled_product
	 * @param  WC_Product_Variation  $bundled_variation
	 * @return array
	 */
	public static function bundled_variation_data( $variation_data, $bundled_product, $bundled_variation ) {

		if ( ! isset( $variation_data[ 'is_bundled' ] ) ) {
			return $variation_data;
		}

		if ( $bundled_variation->get_meta( 'min_max_rules', true ) ) {
			$group_of_quantity = $bundled_variation->get_meta( 'variation_group_of_quantity', true );
		} else {
			$group_of_quantity = $bundled_product->get_meta( 'group_of_quantity', true );
		}

		if ( $group_of_quantity > 1 ) {

			$data = array(
				'group_of'    => absint( $group_of_quantity ),
				'min_value'   => $variation_data[ 'min_qty' ],
				'max_value'   => $variation_data[ 'max_qty' ],
				'input_value' => isset( $variation_data[ 'input_value' ] ) ? $variation_data[ 'input_value' ] : $variation_data[ 'min_qty' ]
			);

			self::$unfiltered_args = $data;

			$fixed_args = self::restore_quantity_input_args( $data, false );

			self::$unfiltered_args = false;

			$variation_data[ 'min_qty' ]     = $fixed_args[ 'min_value' ];
			$variation_data[ 'max_qty' ]     = $fixed_args[ 'max_value' ];
			$variation_data[ 'input_value' ] = $fixed_args[ 'input_value' ];
			$variation_data[ 'step' ]        = $fixed_args[ 'group_of' ];
		}

		return $variation_data;
	}

	/**
	 * Restore allowed min/max quantity for bundled items to empty, so min/max cart validation rules do not apply.
	 *
	 * @param  string  $qty_meta
	 * @param  string  $checking_id
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return array
	 */
	public static function restore_allowed_quantity( $qty_meta, $checking_id, $cart_item_key, $cart_item ) {

		if ( wc_pb_is_bundled_cart_item( $cart_item ) ) {
			$qty_meta = '';
		}

		return $qty_meta;
	}

	/**
	 * Add bundled item and input cart quantity to the product.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 * @param  string      $cart_item_key
	 * @return WC_Product
	 */
	public static function add_bundled_item_to_product( $product, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundled_cart_item( $cart_item ) ) {

			if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

				$bundle = $bundle_container_item[ 'data' ];

				if ( 'bundle' === $bundle->get_type() && $bundled_item = $bundle->get_bundled_item( $cart_item[ 'bundled_item_id' ] ) ) {
					$product->wc_mmq_bundled_item = $bundled_item;
					$product->container_quantity  = $bundle_container_item[ 'quantity' ];
				}
			}
		}

		return $product;
	}

	/**
	 * Filter Min bundled item Quantity based on "Group of" option on runtime.
	 *
	 * @since 6.16.0
	 *
	 * @param  mixed            $qty
	 * @param  WC_Bundled_Item  $bundled_item
	 * @param  array            $args
	 *
	 * @return mixed
	 */
	public static function filter_bundled_item_min_quantity( $qty, $bundled_item, $args ) {

		$group_of_quantity = self::get_bundled_item_group_of_qty( $bundled_item );

		if ( $group_of_quantity ) {
			$qty = WC_Min_Max_Quantities::adjust_min_quantity( $qty, $group_of_quantity );
		}

		return $qty;
	}

	/**
	 * Filter Max bundled item Quantity based on "Group of" option on runtime.
	 *
	 * @since 6.16.0
	 *
	 * @param  mixed            $qty
	 * @param  WC_Bundled_Item  $bundled_item
	 * @param  array            $args
	 *
	 * @return mixed
	 */
	public static function filter_bundled_item_max_quantity( $qty, $bundled_item, $args ) {

		$group_of_quantity = self::get_bundled_item_group_of_qty( $bundled_item );

		if ( $group_of_quantity ) {
			$qty = WC_Min_Max_Quantities::adjust_max_quantity( $qty, $group_of_quantity );
		}

		return $qty;
	}

	/*
	 * Helpers.
	 */

	/**
	 * Returns the "Group of" quantity of a bundled item.
	 *
	 * @since 6.16.0
	 *
	 * @param  WC_Bundled_Item|int  $bundled_item_or_product
	 *
	 * @return int
	 */
	public static function get_bundled_item_group_of_qty( $bundled_item_or_product ) {

		if ( is_a( $bundled_item_or_product, 'WC_Bundled_Item' ) ) {

			$product   = $bundled_item_or_product->get_product();
			$item_data = $bundled_item_or_product->get_data();

			if ( isset( $item_data[ 'allowed_variations' ] ) && is_array( $item_data[ 'allowed_variations' ] ) && 1 === count( $item_data[ 'allowed_variations' ] ) ) {
				// If there is only variation available in the bundled item, it will be the one with the minimum price.
				$bundled_variation = $bundled_item_or_product->get_product( array( 'what' => 'min', 'having' => 'price' ) );

				if ( $bundled_variation->get_meta( 'min_max_rules', true ) ) {
					$product = $bundled_variation;
				}
			}

		} elseif ( is_int( $bundled_item_or_product ) ) {

			$product_id = $bundled_item_or_product;
			$product    = WC_PB_Helpers::cache_get( 'mmq_product_instance_' . $product_id );

			if ( is_null( $product ) ) {
				$product = wc_get_product( $product_id );

				// If the individual variation doesn't have Min/Max rules, fall back to parent Min/Max rules.
				if ( $product->is_type( 'variation' ) ) {
					if ( ! $product->get_meta( 'min_max_rules', true ) ) {
						$parent_id = $product->get_parent_id();
						$product   = wc_get_product( $parent_id );
					}
				}
				WC_PB_Helpers::cache_set( 'mmq_product_instance_' . $product_id, $product );
			}
		} else {
			return 0; // Default Group of quantity when it is not set.
		}

		$group_of_quantity = absint( $product->get_meta( 'group_of_quantity', true ) );

		if ( $product->is_type( 'variation' ) ) {
			$group_of_quantity = absint( $product->get_meta( 'variation_group_of_quantity', true ) );
		}

		return $group_of_quantity;
	}

}

WC_PB_Min_Max_Compatibility::init();
