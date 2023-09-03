<?php
/**
 * WC_PB_ET_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.4.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Divi 3.0+ integration.
 *
 * @version  6.4.3
 */
class WC_PB_ET_Compatibility {

	public static function init() {
		// Add hooks if the active parent theme is Divi.
		add_action( 'after_setup_theme', array( __CLASS__, 'maybe_add_hooks' ) );
	}

	/**
	 * Add hooks if the active parent theme is Divi.
	 */
	public static function maybe_add_hooks() {

		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'form_displayed_twice' ), -1001 );
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'admin_disable_form_location' ) );
		add_filter( 'woocommerce_bundle_add_to_cart_form_location_options', array( __CLASS__, 'change_help_message' ), 10, 1 );
	}

	/**
	 * Unhook woocommerce_after_single_product_summary set inside /includes/wc-pb-template-hooks.php
	 * if the Divi builder is used. No need to check the value of the form location
	 */
	public static function form_displayed_twice() {

		/* @var WC_Product_Bundle $product */
		global $product;

		if ( ! wc_pb_is_product_bundle() ) {
			return; // Bail out early
		}

		$et_pb_use_builder = $product->get_meta( '_et_pb_use_builder' );
		if ( 'on' === $et_pb_use_builder ) {
			remove_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', -1000 );
		}

	}

	/**
	 * Check if builder is used and the product type is "bundle"
	 * If the Divi builder is used deactivate the form location
	 *
	 * @param WC_Product_Bundle $product_bundle_object
	 */
	public static function admin_disable_form_location( $product_bundle_object ) {

		$et_pb_use_builder = $product_bundle_object->get_meta( '_et_pb_use_builder' );
		if ( 'on' === $et_pb_use_builder ) {
			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( "
					jQuery( function ( $ ) {
						$( '#_wc_pb_add_to_cart_form_location' ).prop( 'disabled', true );
					} )
				" );
			}
		}
	}

	/**
	 * If the Divi builder is used change the help message that appears in (?)
	 * Checking the product type, causes an infinite loop and there is no need to check the type
	 * for what we're doing
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public static function change_help_message( $options ) {

		/* @var WC_Product_Bundle $product_bundle_object */
		global $product_bundle_object;

		if ( null === $product_bundle_object ) {
			return $options; // Bail out early.
		}

		$et_pb_use_builder = $product_bundle_object->get_meta( '_et_pb_use_builder' );
		if ( 'on' === $et_pb_use_builder ) {
			$msg_after_summary = __( 'Unavailable when using the Divi builder.', 'woocommerce-product-bundles' );

			$options[ 'after_summary' ][ 'description' ] .= ' ' . $msg_after_summary;
		}

		return $options;
	}
}

WC_PB_ET_Compatibility::init();
