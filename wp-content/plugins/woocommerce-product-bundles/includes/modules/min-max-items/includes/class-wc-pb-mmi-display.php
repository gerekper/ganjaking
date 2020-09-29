<?php
/**
 * WC_PB_MMI_Display class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display-related functions and filters.
 *
 * @class    WC_PB_MMI_Display
 * @version  6.4.0
 */
class WC_PB_MMI_Display {

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Validation script.
		add_action( 'woocommerce_bundle_add_to_cart', array( __CLASS__, 'enqueue_script' ) );
		add_action( 'woocommerce_composite_add_to_cart', array( __CLASS__, 'enqueue_script' ) );

		// Add min/max data to template for use by validation script.
		add_action( 'woocommerce_before_bundled_items', array( __CLASS__, 'script_data' ) );
		add_action( 'woocommerce_before_composited_bundled_items', array( __CLASS__, 'script_data' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Filter/action hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Validation script.
	 */
	public static function enqueue_script() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-pb-min-max-items-add-to-cart', WC_PB()->plugin_url() . '/assets/js/frontend/add-to-cart-bundle-min-max-items' . $suffix . '.js', array( 'wc-add-to-cart-bundle' ), WC_PB()->version );
		wp_enqueue_script( 'wc-pb-min-max-items-add-to-cart' );

		$params = array(
			'i18n_min_zero_max_qty_error_singular' => __( 'Please choose an item.', 'woocommerce-product-bundles' ),
			'i18n_min_max_qty_error_singular'      => __( 'Please choose 1 item.%s', 'woocommerce-product-bundles' ),
			'i18n_min_qty_error_singular'          => __( 'Please choose at least 1 item.%s', 'woocommerce-product-bundles' ),
			'i18n_max_qty_error_singular'          => __( 'Please choose up to 1 item.%s', 'woocommerce-product-bundles' ),
			'i18n_min_qty_error_plural'            => sprintf( __( 'Please choose at least %1$s items.%2$s', 'woocommerce-product-bundles' ), '%q', '%s' ),
			'i18n_max_qty_error_plural'            => sprintf( __( 'Please choose up to %1$s items.%2$s', 'woocommerce-product-bundles' ), '%q', '%s' ),
			'i18n_min_max_qty_error_plural'        => sprintf( __( 'Please choose %1$s items.%2$s', 'woocommerce-product-bundles' ), '%q', '%s' ),
			'i18n_qty_error_plural'                => __( '%s items selected', 'woocommerce-product-bundles' ),
			'i18n_qty_error_singular'              => __( '1 item selected', 'woocommerce-product-bundles' ),
			'i18n_qty_error_status_format'         => _x( '<span class="bundled_items_selection_status">%s</span>', 'validation error status format', 'woocommerce-product-bundles' )
		);

		wp_localize_script( 'wc-pb-min-max-items-add-to-cart', 'wc_pb_min_max_items_params', $params );
	}

	/**
	 * Pass min/max container values to the single-product script.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function script_data( $the_product = false ) {

		global $product;

		if ( ! $the_product ) {
			$the_product = $product;
		}

		if ( is_object( $the_product ) && $the_product->is_type( 'bundle' ) ) {

			$min = $the_product->get_meta( '_wcpb_min_qty_limit', true );
			$max = $the_product->get_meta( '_wcpb_max_qty_limit', true );

			?><div class="min_max_items" data-min="<?php echo $min > 0 ? esc_attr( absint( $min ) ) : ''; ?>" data-max="<?php echo $max > 0 ? esc_attr( absint( $max ) ) : ''; ?>"></div><?php
		}
	}
}

WC_PB_MMI_Display::init();
