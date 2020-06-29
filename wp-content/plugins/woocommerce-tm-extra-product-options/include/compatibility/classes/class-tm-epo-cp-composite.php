<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Composite Products (https://woocommerce.com/products/composite-products/)
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_composite {

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
		if ( ! class_exists( 'WC_Composite_Products' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );

		add_action( 'woocommerce_composite_product_add_to_cart', array( $this, 'tm_bto_display_support' ), 11, 2 );
		add_action( 'woocommerce_composited_product_add_to_cart', array( $this, 'tm_composited_display_support' ), 11, 3 );
		add_filter( 'woocommerce_composite_button_behaviour', array( $this, 'tm_woocommerce_composite_button_behaviour' ), 50, 2 );
		add_action( 'woocommerce_composite_products_remove_product_filters', array( $this, 'tm_woocommerce_composite_products_remove_product_filters' ), 99999 );
		add_filter( 'woocommerce_composite_cart_permalink_args', array( $this, 'woocommerce_composite_cart_permalink_args' ), 99, 3 );
	}

	/**
	 * Enable options when editing cart for composite products
	 *
	 * @since 5.0
	 */
	public function woocommerce_composite_cart_permalink_args( $args, $cart_item, $composite_product ) {
		if ( isset( $cart_item['tmcartepo_bto'] ) && count( $cart_item['tmcartepo_bto'] ) ) {
			$args = array_merge( $args, $cart_item['tmcartepo_bto'] );
		}

		return $args;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-composite-products', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-composite.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
		}
	}

	public function tm_woocommerce_composite_products_remove_product_filters() {
		THEMECOMPLETE_EPO()->is_bto = FALSE;
	}

	public function tm_woocommerce_composite_button_behaviour( $type = "", $product = "" ) {
		if ( isset( $_POST ) && isset( $_POST['cpf_bto_price'] ) && ( isset( $_POST['add-product-to-cart'] ) || isset( $_POST['wccp_component_selection'] ) ) && isset( $_POST['item_quantity'] ) ) {
			$type = 'posted';
		}

		return $type;
	}

	/**
	 * Include options in the composite product (older versions)
	 *
	 * @since 1.0
	 */
	public function tm_bto_display_support( $product_id = "", $item_id = "" ) {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $product_id );
		}
		if ( ! $product ) {
			// something went wrong. wrong product id??
			// if you get here the plugin will not work :(
		} else {
			THEMECOMPLETE_EPO()->set_tm_meta( $product_id );
			THEMECOMPLETE_EPO()->is_bto = TRUE;

			if ( ( THEMECOMPLETE_EPO()->tm_epo_display == 'normal' || THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] == 'normal' ) && THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] != 'action' ) {
				THEMECOMPLETE_EPO_DISPLAY()->frontend_display( $product_id, $item_id );
			}
		}
	}

	/**
	 * Include options in the composite product
	 *
	 * @since 1.0
	 */
	public function tm_composited_display_support( $product = FALSE, $component_id = "", $composite_product ) {
		if ( ! $product ) {
			// something went wrong. wrong product id??
			// if you get here the plugin will not work :(
		} else {
			THEMECOMPLETE_EPO()->set_tm_meta( themecomplete_get_id( $product ) );
			THEMECOMPLETE_EPO()->is_bto = TRUE;
			if ( ( THEMECOMPLETE_EPO()->tm_epo_display == 'normal' || THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] == 'normal' ) && THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] != 'action' ) {
				THEMECOMPLETE_EPO_DISPLAY()->frontend_display( themecomplete_get_id( $product ), $component_id );
			}
		}
	}
}


