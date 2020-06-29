<?php
/**
 * @package		WooCommerce One Page Checkout
 * @subpackage	Name Your Price Extension Compatibility
 * @category	Compatibility Class
 * @version 	1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class to hold Name Your Price compat functionality
 */
class WCOPC_Compat_Name_Your_Price {

	const PREFIX = '-opc-';
	const SUFFIX = '-opc-';

	protected static $is_nyp_3 = false; 

	public static function init() {

		if ( class_exists( 'WC_Name_Your_Price' ) ) {

			self::$is_nyp_3 = is_callable( array( 'WC_Name_Your_Price_Compatibility', 'is_nyp_gte' ) ) ? WC_Name_Your_Price_Compatibility::is_nyp_gte( '3.0' ) : false;

			// Add NYP input to product_table and pricing_table templates.
			add_action( 'wcopc_before_add_to_cart_button', array( __CLASS__, 'opc_nyp_price_input' ) );

			// 3.0 Ready Filters.
			$suffix_filter = self::$is_nyp_3 ? 'wc_nyp_field_suffix'      : 'nyp_field_prefix';
			$price_filter  = self::$is_nyp_3 ? 'wc_nyp_get_posted_price'  : 'woocommerce_nyp_get_posted_price';

			// Filter the NYP suffix.
			add_filter( $suffix_filter, array( __CLASS__, 'nyp_cart_suffix' ), 10, 2 );
			
			// Make sure input has cart price since it may not be in $_REQUEST.	
			add_filter( $price_filter, array( __CLASS__, 'nyp_input_initial_value' ), 10, 2 );

			// Maybe swap on single product pages.
			add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'maybe_swap_nyp_price_input' ), 5 );

			// Swap on single product shortcodes.
			add_action( 'wcopc_single_add_to_cart', array( __CLASS__, 'swap_nyp_price_input' ), 5 );


			if ( isset( WC_Name_Your_Price()->display ) ) {
				// Load the NYP scripts with OPC scripts.
				add_action( 'wcopc_enqueue_scripts', array( WC_Name_Your_Price()->display, 'nyp_scripts' ) );
				add_action( 'wcopc_enqueue_scripts', array( WC_Name_Your_Price()->display, 'nyp_style' ) );
			}
		}
	}

	/**
	 * Maybe swap default price input with OPC function that adds prefix.
	 *
	 * @since	1.5.0
	 * 
	 * @param	obj $product
	 * @return	void
	 */
	public static function maybe_swap_nyp_price_input(){
		if ( is_wcopc_checkout() ) {
			self::swap_nyp_price_input();
		}
	}

	/**
	 * Swap default price input with OPC function that adds prefix.
	 *
	 * @since	1.6.0
	 * 
	 * @param	obj $product
	 * @return	void
	 */
	public static function swap_nyp_price_input(){
		remove_action( 'woocommerce_before_add_to_cart_button', array( WC_Name_Your_Price()->display, 'display_price_input' ), 9 );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'opc_nyp_price_input' ), 9 );

		// NYP 3.0's single product display is short-circuited if product is not a variable product, therefore, no need to remove it from single product hook.
		if ( self::$is_nyp_3 ) {
			remove_action( 'woocommerce_single_variation', array( WC_Name_Your_Price()->display, 'display_variable_price_input' ), 12 );
			add_action( 'woocommerce_single_variation', array( __CLASS__, 'opc_nyp_price_input' ), 12 );

		// NYP 2.9.0 changes display hook to woocommerce_single_variation for variable products.
		} else if ( version_compare( WC_Name_Your_Price()->version, '2.9.0', '>=' ) ) {
			remove_action( 'woocommerce_before_variations_form', array( WC_Name_Your_Price()->display, 'move_display_for_variable_product' ) );
			add_action( 'woocommerce_before_variations_form', array( __CLASS__, 'move_display_for_variable_product' ) );
		}

		
	}

	/**
	 * Fix price input position on variable products - This shows them before Product Addons.
	 *
	 * @since 1.6.0
	 */
	public static function move_display_for_variable_product() {
		remove_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'opc_nyp_price_input' ), 9 );
		add_action( 'woocommerce_single_variation', array( __CLASS__, 'opc_nyp_price_input' ), 12 );
	}

	/**
	 * Display Price Input in OPC templates.
	 *
	 * @since	1.5.0
	 * 
	 * @param	obj $product
	 * @return	void
	 */
	public static function opc_nyp_price_input( $product = false ) {

		if ( ! is_a( $product, 'WC_Product' ) ) {
			global $product;
		}

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$suffix = self::SUFFIX . WC_Name_Your_Price_Core_Compatibility::get_id( $product );

		WC_Name_Your_Price()->display->display_price_input( $product, $suffix );
	}

	/**
	 * Sets a unique suffix for unique NYP products in OPC templates. 
	 * The suffix is set and re-set globally before validating and adding to cart.
	 *
	 * @since	1.5.0
	 * 
	 * @param  	string  $suffix
	 * @param  	int     $nyp_id
	 * @return  string
	 */
	public static function nyp_cart_suffix( $suffix, $nyp_id ) {

		if ( PP_One_Page_Checkout::is_any_form_of_opc_page() ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['add_to_cart'] ) ) {
				$suffix = self::SUFFIX . $_REQUEST['add_to_cart'];
			} elseif ( isset( $_REQUEST['add-to-cart'] ) ) {
				$suffix = self::SUFFIX . $_REQUEST['add-to-cart'];
			}
		}

		return $suffix;
	}

	/**
	 * Modifies the price input if the item is in the OPC cart
	 *
	 * @since	1.7.5
	 * 	 
	 * @param  	string     $price
	 * @param  	WC_Product $product
	 * @return  string
	 */
	public static function nyp_input_initial_value( $price, $product ) {

		if ( PP_One_Page_Checkout::is_any_form_of_opc_page() && $product instanceof WP_Product ) {
			$cart_item = wcopc_get_products_prop( $product, 'cart_item' );

			if ( isset( $cart_item['nyp'] ) ) {
				$price = $cart_item['nyp'];
			}

		}

		return $price;
	}

	/**
	 * Sets a unique prefix for unique NYP products in OPC templates. 
	 * The prefix is set and re-set globally before validating and adding to cart.
	 *
	 * @since	1.5.0
	 * @deprecated 1.7.5
	 * 
	 * @param  	string  $prefix
	 * @param  	int     $nyp_id
	 * @return  string
	 * 
	 */
	public static function nyp_cart_prefix( $prefix, $nyp_id ) {
		wc_deprecated_function( __METHOD__, '1.7.5' );
		return self::nyp_cart_suffix( $prefix, $nyp_id );
	}
}
add_action( 'init', 'WCOPC_Compat_Name_Your_Price::init' );
