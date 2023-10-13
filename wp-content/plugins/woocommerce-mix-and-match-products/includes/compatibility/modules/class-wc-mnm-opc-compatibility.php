<?php
/**
 * One Page Checkout Compatibility
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_OPC_Compatibility Class.
 *
 * Adds compatibility with WooCommerce One Page Checkout.
 */
class WC_MNM_OPC_Compatibility {

	public static function init() {

		// OPC support.
		add_action( 'wcopc_mix-and-match_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_mnm' ) );
		add_filter( 'wcopc_allow_cart_item_modification', array( __CLASS__, 'opc_disallow_bundled_cart_item_modification' ), 10, 4 );
	}

	/**
	 * OPC Single-product mix and match add-to-cart template.
	 *
	 * @param  int  $opc_post_id
	 * @return void
	 */
	public static function opc_single_add_to_cart_mnm( $opc_post_id ) {

		global $product;

		// Enqueue script.
		wp_enqueue_script( 'wc-add-to-cart-mnm' );

		if ( doing_action( 'wcopc_mix-and-match_add_to_cart' ) ) {
			// If after_summary location, switch default.
			if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
				// Single product template for Mix and Match. Form location: After summary.
				remove_action( 'woocommerce_after_single_product_summary', 'wc_mnm_template_add_to_cart_after_summary', -1000 );
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'opc_add_to_cart_after_summary' ), -1000 );
				return;
			}
		}

		ob_start();

		// Load the add to cart template.
		wc_mnm_template_add_to_cart( $product );

		echo wp_kses_post( str_replace( array( '<form', '</form>', 'method="post"', 'enctype="multipart/form-data"' ), array( '<div', '</div>', '', '' ), ob_get_clean() ) );
	}

	/**
	 * OPC Single-product mix and match type "after_summary" add-to-cart template.
	 *
	 * @param  int  $opc_post_id
	 * @return void
	 */
	public static function opc_add_to_cart_after_summary() {
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( 'PP_One_Page_Checkout', 'modify_single_add_to_cart_text' ) );

		global $product;

		ob_start();

		// Load the after summary add to cart template.
		wc_mnm_template_add_to_cart_after_summary( $product );

		echo wp_kses_post( str_replace( array( '<form', '</form>', 'method="post"', 'enctype="multipart/form-data"' ), array( '<div', '</div>', '', '' ), ob_get_clean() ) );

		remove_filter( 'woocommerce_product_single_add_to_cart_text', array( 'PP_One_Page_Checkout', 'modify_single_add_to_cart_text' ) );
	}

	/**
	 * Prevent OPC from managing child items.
	 *
	 * @param  bool   $allow
	 * @param  array  $cart_item
	 * @param  string $cart_item_key
	 * @param  string $opc_id
	 * @return bool
	 */
	public static function opc_disallow_bundled_cart_item_modification( $allow, $cart_item, $cart_item_key, $opc_id ) {

		if ( ! empty( $cart_item['mnm_container'] ) ) {
			return false;
		}

		return $allow;
	}
}

WC_MNM_OPC_Compatibility::init();
