<?php
/**
 * WC_MNM_Min_Max_Compatibility class
 *
 * @author   SomewhereWarm
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Min/Max Quantities Compatibility.
 *
 * @version  1.6.1
 */
class WC_MNM_Min_Max_Compatibility {

	/**
	 * The child item object whose qty input is being filtered.
	 * @var WC_Product
	 */
	public static $child_item = false;

	/**
	 * Unfiltered quantity input data used at restoration time.
	 * @var array
	 */
	public static $unfiltered_args = false;

	/**
	 * Initilize hooks.
	 */
	public static function init() {

		// Add admin option.
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'min_max_options' ), 50, 2 );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_mnm_min_max_data' ) );

		// Validate child items with min/max quantities.
		add_filter( 'woocommerce_mnm_item_add_to_cart_validation', array( __CLASS__, 'min_max_item_validation' ), 10, 5 );

		// Set global $child_item variable.
		add_action( 'woocommerce_mnm_child_item_details', array( __CLASS__, 'restore_quantities_set' ), -10, 2 );

		// Unset global $child_item variable.
		add_action( 'woocommerce_mnm_child_item_details', array( __CLASS__, 'restore_quantities_unset' ), 999 );

		// Restore child items quantities to the values they had before min/max interference.
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'save_quantity_input_args' ), 0, 2 );
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'restore_quantity_input_args' ), 11, 2 );

		// Ignore cart item quantity validation for child items.
		add_filter( 'wc_min_max_quantity_minmax_do_not_count', array( __CLASS__, 'ignore_child_items' ), 10, 4 );
		add_filter( 'wc_min_max_quantity_minmax_cart_exclude', array( __CLASS__, 'ignore_child_items' ), 10, 4 );
		add_filter( 'wc_min_max_cart_quantity_do_not_count', array( __CLASS__, 'ignore_child_items' ), 10, 4 );

		// Disable min/max/grouped cart item quantity validation for child items.
		add_filter( 'wc_min_max_quantity_minimum_allowed_quantity', array( __CLASS__, 'restore_allowed_quantity' ), 10, 4 );
		add_filter( 'wc_min_max_quantity_maximum_allowed_quantity', array( __CLASS__, 'restore_allowed_quantity' ), 10, 4 );
		add_filter( 'wc_min_max_quantity_group_of_quantity', array( __CLASS__, 'restore_allowed_quantity' ), 10, 4 );

		// Add child item and input cart quantity to the product.
		add_filter( 'woocommerce_cart_item_product', array( __CLASS__, 'add_child_item_to_product' ), 10, 3 );
	}

	/**
	 * Adds the MnM per-item shipping option.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.6.1
	 */
	public static function min_max_options( $post_id, $mnm_product_object ) {

		global $mnm_product_object;

		// Ignore Min/Max Quantities in Container.
		woocommerce_wp_radio( array(
			'id'          => '_mnm_ignore_min_max_rules',
			'label'       => __( 'WooCommmerce Min/Max Quantities compatibility', 'woocommerce-mix-and-match-products' ),
			'value'       => $mnm_product_object->get_meta( '_mnm_ignore_min_max_rules' ) == 'yes' ? 'ignore' : 'default',
			'description' => __( 'Select ignore to disregard the Min/Max Quantities plugin\'s rules for products in this container.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'	=> true,
			'options' 	  => array( 'default' => __( 'Apply rules in container', 'woocommerce-mix-and-match-products' ),
									'ignore' => __( 'Ignore rules in container', 'woocommerce-mix-and-match-products' ) )
		) );
	}

	/**
	 * Save meta data
	 *
	 * @param  WC_Product  $product
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.6.1
	 */
	public static function process_mnm_min_max_data( $product ) {
		if ( $product->is_type( 'mix-and-match' ) && ! defined( 'WC_MNM_UPDATING' ) ) {
			$ignore = wc_bool_to_string( isset( $_POST[ '_mnm_ignore_min_max_rules' ] ) && $_POST[ '_mnm_ignore_min_max_rules' ] == 'ignore' );
			$product->update_meta_data( '_mnm_ignore_min_max_rules', $ignore );
		}
	}


	/**
	 * Validate child items with min/max quantities.
	 * By default, Min/Max will catch the invalid item in the cart, but this will prevent the whole container from being added with an invalid configuration.
	 *
	 * @param bool $is_valid
	 * @param obj $container WC_Product_Mix_and_Match of parent container.
	 * @param obj $mnm_item WC_Product of child item.
	 * @param int $item_quantity Quantity of child item.
	 * @param int $container_quantity Quantity of parent container.
	 * @since  1.6.1
	 */
	public static function min_max_item_validation( $is_valid, $container, $mnm_item, $item_quantity, $container_quantity ) {

		if( ! self::contents_ignores_rules( $container ) ) {

			if( $mnm_item->get_parent_id() > 0 ){
				$product_id = $mnm_item->get_parent_id();
				$variation_id = $mnm_item->get_id();
			} else {
				$product_id = $mnm_item->get_id();
				$variation_id = 0;
			}

			$is_valid = WC_Min_Max_Quantities::get_instance()->add_to_cart( $is_valid, $product_id, $item_quantity * $container_quantity, $variation_id );

			if( ! $is_valid ) {
				// translators: %s product title.
				$notice = sprintf( __( '&quot;%s&quot; cannot  be added to the cart as configured.', 'woocommerce-mix-and-match-products' ), $container->get_title() );
				throw new Exception( $notice );
			}

		}

		return $is_valid;
	}


	/**
	 * Set global $child_item variable.
	 *
	 * @param  WC_Product  $child_item
	 * @param  WC_Product_Mix_and_Match $container
	 * @return void
	 */
	public static function restore_quantities_set( $child_item, $container ) {
		self::$child_item = self::contents_ignores_rules( $container ) ? $child_item : false;
	}

	/**
	 * Unset global $child_item variable.
	 *
	 * @param  WC_Product  $child_item
	 * @return void
	 */
	public static function restore_quantities_unset( $child_item ) {
		self::$child_item = false;
	}

	/**
	 * Save unmodified quantity args.
	 *
	 * @param  array   $data
	 * @param  object  $product
	 * @return array
	 */
	public static function save_quantity_input_args( $data, $product ) {

		if ( is_object( self::$child_item ) || isset( $product->wc_mmq_child_item ) ) {
			self::$unfiltered_args = $data;
		} else {
			self::$unfiltered_args = false;
		}

		return $data;
	}

	/**
	 * Restore min/max child item quantities to the values they had before min/max interference.
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
			$step 		  = 1;
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

			if ( isset( self::$unfiltered_args[ 'step' ] ) ) {
				$step = absint( self::$unfiltered_args[ 'step' ] );
			} elseif ( isset( $data[ 'step' ] ) ) {
				$step = absint( $data[ 'step' ] );
			}

			if ( ! isset( $product->wc_mmq_child_item ) ) {

				if ( $step > 1 ) {
					if ( $min_qty > $step ) {
						$modulo  = $min_qty % $step;
						$min_qty = $modulo ? $min_qty + $modulo : $min_qty;
					} elseif ( $min_qty > 0 ) {
						$min_qty = max( $min_qty, $step );
					}
				}

				$input_qty = max( $input_qty, $min_qty );
			}

			if ( empty( $max_qty ) || $max_qty >= $min_qty ) {
				$data[ 'min_value' ]   = $min_qty;
				$data[ 'max_value' ]   = $max_qty;
				$data[ 'input_value' ] = $input_qty;
				$data[ 'step' ]        = $step;
			} else {
				$data[ 'min_value' ]   = $min_qty;
				$data[ 'max_value' ]   = $min_qty;
				$data[ 'input_value' ] = $min_qty;
				$data[ 'step' ]        = $step;
			}
		}

		return $data;
	}

	/**
	 * Restore allowed min/max quantity for child items to empty, so min/max cart validation rules do not apply.
	 *
	 * @param  string  $is_ignored
	 * @param  string  $checking_id
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return string
	 */
	public static function ignore_child_items( $is_ignored, $checking_id, $cart_item_key, $cart_item ) {

		$container_item = wc_mnm_get_cart_item_container( $cart_item );

		if ( $container_item && self::contents_ignores_rules( $container_item['data'] ) ) {  
			$is_ignored = 'yes';
		}

		return $is_ignored;
	}

	/**
	 * Restore allowed min/max quantity for child items to empty, so min/max cart validation rules do not apply.
	 *
	 * @param  string  $qty_meta
	 * @param  string  $checking_id
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return array
	 */
	public static function restore_allowed_quantity( $qty_meta, $checking_id, $cart_item_key, $cart_item ) {

		$container_item = wc_mnm_get_cart_item_container( $cart_item );

		if ( $container_item && self::contents_ignores_rules( $container_item['data'] ) ) {  
			$qty_meta = '';
		}

		return $qty_meta;
	}

	/**
	 * Add child item and input cart quantity to the product.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 * @param  string      $cart_item_key
	 * @return WC_Product
	 */
	public static function add_child_item_to_product( $product, $cart_item, $cart_item_key ) {

		if ( wc_mnm_is_child_cart_item( $cart_item ) ) {

			if ( $container_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

				$container_product = $container_item[ 'data' ];

				if ( self::contents_ignores_rules( $container_product ) ) {
					$product->wc_mmq_child_item = $product;
				}
			}
		}

		return $product;
	}

	/**
	 * Validate child items with min/max quantities.
	 * By default, Min/Max will catch the invalid item in the cart, but this will prevent the whole container from being added with an invalid configuration.
	 *
	 * @param obj $container WC_Product_Mix_and_Match of parent container.
	 * @return bool
	 * @since  1.6.1
	 */
	public static function contents_ignores_rules( $container ) {
		return $container->is_type( 'mix-and-match' ) && wc_string_to_bool( $container->get_meta( '_mnm_ignore_min_max_rules' ) );
	}

}
WC_MNM_Min_Max_Compatibility::init();
