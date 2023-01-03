<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Composite Products
 * https://woocommerce.com/products/composite-products/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Composite {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Composite|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );

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

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );
		add_action( 'woocommerce_composited_product_add_to_cart', [ $this, 'tm_composited_display_support' ], 11, 3 );
		add_filter( 'woocommerce_composite_button_behaviour', [ $this, 'tm_woocommerce_composite_button_behaviour' ], 50, 1 );
		add_action( 'woocommerce_composite_products_remove_product_filters', [ $this, 'tm_woocommerce_composite_products_remove_product_filters' ], 99999 );
		add_filter( 'woocommerce_composite_cart_permalink_args', [ $this, 'woocommerce_composite_cart_permalink_args' ], 99, 3 );
		add_filter( 'wc_epo_tm_post_class_no_options', [ $this, 'wc_epo_tm_post_class_no_options' ], 10, 2 );
		add_filter( 'wc_epo_options_min_price', [ $this, 'wc_epo_options_min_price' ], 10, 2 );
		add_filter( 'wc_epo_options_max_price', [ $this, 'wc_epo_options_max_price' ], 10, 2 );
		add_filter( 'wc_epo_woocommerce_available_variation_check', [ $this, 'wc_epo_woocommerce_available_variation_check' ], 10, 1 );
	}

	/**
	 * Override woocommerce_available_variation check
	 *
	 * @param boolean $ret To run the check or not.
	 * @since 6.1
	 */
	public function wc_epo_woocommerce_available_variation_check( $ret = true ) {
		if ( isset( $_REQUEST['wc-ajax'] ) && 'woocommerce_show_composited_product' === $_REQUEST['wc-ajax'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Alter catalog price
	 *
	 * @param string|float  $price The price to alter.
	 * @param string|Object $product The prodcut object.
	 * @since 5.0.12.11
	 */
	public function wc_epo_options_min_price( $price = '', $product = '' ) {
		if ( is_callable( [ $product, 'get_composite_regular_price' ] ) ) {
			$regular_price_min = $product->get_composite_regular_price( 'min', true );

			$price = floatval( $price ) + floatval( $regular_price_min );
		}

		return $price;
	}

	/**
	 * Alter catalog price
	 *
	 * @param string|float  $price The price to alter.
	 * @param string|Object $product The prodcut object.
	 * @since 5.0.12.11
	 */
	public function wc_epo_options_max_price( $price = '', $product = '' ) {
		if ( is_callable( [ $product, 'get_composite_regular_price' ] ) ) {
			$regular_price_max = $product->get_composite_regular_price( 'max', true );

			$price = floatval( $price ) + floatval( $regular_price_max );
		}
		return $price;
	}

	/**
	 * Search composite products for extra options
	 *
	 * @param array    $array Array of classes.
	 * @param int|null $post_id The post id.
	 * @since 5.0.12.9
	 */
	public function wc_epo_tm_post_class_no_options( $array = [], $post_id = null ) {

		$terms        = get_the_terms( $post_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		$has_epo      = THEMECOMPLETE_EPO_API()->has_options( $post_id );
		if ( ( 'bto' === $product_type || 'composite' === $product_type )
			&& ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo )
			&& 'yes' !== THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all
		) {

			// search components for options.
			$product = wc_get_product( $post_id );
			if ( is_callable( [ $product, 'get_composite_data' ] ) ) {
				$composite_data = $product->get_composite_data();

				foreach ( $composite_data as $component_id => $component_data ) {

					$component_options = [];

					if ( class_exists( 'WC_CP_Component' ) && method_exists( 'WC_CP_Component', 'query_component_options' ) ) {
						$component_options = WC_CP_Component::query_component_options( $component_data );
					} elseif ( function_exists( 'WC_CP' ) ) {
						$component_options = WC_CP()->api->get_component_options( $component_data );
					} else {
						global $woocommerce_composite_products;
						if ( is_object( $woocommerce_composite_products ) && function_exists( 'WC_CP' ) ) {
							$component_options = WC_CP()->api->get_component_options( $component_data );
						} else {
							if ( isset( $component_data['assigned_ids'] ) && is_array( $component_data['assigned_ids'] ) ) {
								$component_options = $component_data['assigned_ids'];
							}
						}
					}

					foreach ( $component_options as $key => $pid ) {
						$has_options = THEMECOMPLETE_EPO_API()->has_options( $pid );
						if ( THEMECOMPLETE_EPO_API()->is_valid_options_or_variations( $has_options ) ) {
							$array[] = 'tm-no-options-composite';

							return $array;
						}
					}
				}
			}
		}

		return $array;
	}
	/**
	 * Enable options when editing cart for composite products
	 *
	 * @param array  $args Array of arguments.
	 * @param array  $cart_item The cart item.
	 * @param object $composite_product The composite product.
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
			wp_enqueue_script( 'themecomplete-comp-composite-products', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-composite.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}

	/**
	 * Set bto flag
	 *
	 * @return void
	 */
	public function tm_woocommerce_composite_products_remove_product_filters() {
		THEMECOMPLETE_EPO()->is_bto = false;
	}

	/**
	 * Hide or disable the add-to-cart button if the composite has any components pending user input.
	 *
	 * @param string $type new or old.
	 * @return string
	 */
	public function tm_woocommerce_composite_button_behaviour( $type = '' ) {
		if ( isset( $_REQUEST['cpf_bto_price'] ) && ( isset( $_REQUEST['add-product-to-cart'] ) || isset( $_REQUEST['wccp_component_selection'] ) ) && isset( $_REQUEST['item_quantity'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$type = 'old';
		}

		return $type;
	}

	/**
	 * Include options in the composite product
	 *
	 * @param object|boolean $product The product.
	 * @param string         $component_id The component id.
	 * @param object|null    $composite_product The composite product.
	 * @return void
	 * @since 1.0
	 */
	public function tm_composited_display_support( $product = false, $component_id = '', $composite_product = null ) {
		if ( $product ) {
			THEMECOMPLETE_EPO()->set_tm_meta( themecomplete_get_id( $product ) );
			THEMECOMPLETE_EPO()->is_bto = true;
			if ( ( 'normal' === THEMECOMPLETE_EPO()->tm_epo_display || 'normal' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] ) && 'action' !== THEMECOMPLETE_EPO()->tm_meta_cpf['override_display'] ) {
				THEMECOMPLETE_EPO_DISPLAY()->frontend_display( themecomplete_get_id( $product ), $component_id );
			}
			return;
		}
		// something went wrong. wrong product id??
		// if you get here the plugin will not work.
	}
}
