<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Fancy Product Designer 
 * https://codecanyon.net/item/fancy-product-designer-woocommercewordpress/6318393
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_fpd {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ) );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'Fancy_Product_Designer' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );
		add_filter( 'woocommerce_add_cart_item_data', array($this, 'woocommerce_add_cart_item_data'), 10, 2 );

	}

	/**
	 * Edit cart functionality
	 *
	 * @since 5.0
	 */
	public function woocommerce_add_cart_item_data($cart_item_meta, $product_id) {

		unset($cart_item_meta['fpd_data']['fpd_remove_cart_item']);

		return $cart_item_meta;

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-fpd', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-fpd.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
		}

	}

}
