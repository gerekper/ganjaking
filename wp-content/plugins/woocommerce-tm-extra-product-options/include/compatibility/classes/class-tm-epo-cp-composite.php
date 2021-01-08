<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Composite Products 
 * https://woocommerce.com/products/composite-products/
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
		add_filter( 'wc_epo_tm_post_class_no_options', array( $this, 'wc_epo_tm_post_class_no_options' ), 10, 2 );
		add_filter( 'wc_epo_options_min_price', array( $this, 'wc_epo_options_min_price' ), 10, 3 );
		add_filter( 'wc_epo_options_max_price', array( $this, 'wc_epo_options_max_price' ), 10, 3 );
	}

	/**
	 * Alter catalog price
	 *
	 * @since 5.0.12.11
	 */
	public function wc_epo_options_min_price( $price = '', $product = '' , $is_variable = '') {
		if ( is_callable( array( $product, 'get_composite_regular_price' ) ) ) {
			$regular_price_min = $product->get_composite_regular_price( 'min', true );

			$price = floatval($price) + floatval($regular_price_min);
		}

		return $price;
	}

	/**
	 * Alter catalog price
	 *
	 * @since 5.0.12.11
	 */
	public function wc_epo_options_max_price( $price = '', $product = '' , $is_variable = '') {
		if ( is_callable( array( $product, 'get_composite_regular_price' ) ) ) {
			$regular_price_max = $product->get_composite_regular_price( 'max', true );

			$price = floatval($price) + floatval($regular_price_max);
		}
		return $price;
	}

	/**
	 * Search composite products for extra options
	 *
	 * @since 5.0.12.9
	 */
	public function wc_epo_tm_post_class_no_options( $array = array(), $post_id = null ) {
		
		$terms = get_the_terms( $post_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );
		if ( ( $product_type == 'bto' || $product_type == 'composite' )
		     && ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo )
		     && THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all != "yes"
		) {

			// search components for options
			$product = wc_get_product( $post_id );
			if ( is_callable( array( $product, 'get_composite_data' ) ) ) {
				$composite_data = $product->get_composite_data();

				foreach ( $composite_data as $component_id => $component_data ) {

					$component_options = array();

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
	public function tm_composited_display_support( $product = FALSE, $component_id = "", $composite_product = null ) {
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


