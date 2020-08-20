<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Measurement Price Calculator 
 * https://woocommerce.com/products/measurement-price-calculator/
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_measurement {

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
		add_action( 'plugins_loaded', array( $this, 'add_compatibility2' ), 2 );
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ) );
		add_action( 'init', array( $this, 'template_redirect' ), 11 );
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 11 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WC_Measurement_Price_Calculator' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );

		add_filter( 'wc_epo_add_cart_item_original_price', array( $this, 'wc_epo_add_cart_item_original_price' ), 10, 2 );
		add_filter( 'wc_epo_option_price_correction', array( $this, 'wc_epo_option_price_correction' ), 10, 2 );
		add_filter( 'woocommerce_tm_epo_price_on_cart', array( $this, 'woocommerce_tm_epo_price_on_cart' ), 10, 2 );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 4.9.12
	 */
	public function add_compatibility2() {

		if ( class_exists( 'WC_Measurement_Price_Calculator' ) || class_exists( 'WC_Measurement_Price_Calculator_Loader' ) ) {
			add_filter( 'wc_epo_get_settings', array( $this, 'wc_epo_get_settings' ), 10, 1 );
			add_filter( 'tm_epo_settings_headers', array( $this, 'tm_epo_settings_headers' ), 10, 1 );
			add_filter( 'tm_epo_settings_settings', array( $this, 'tm_epo_settings_settings' ), 10, 1 );
		}

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-measurement', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-measurement.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
			$args = array(
				'wc_measurement_qty_multiplier' => isset( THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode ) && ( THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode === "yes" ) ? 1 : 0,
				'wc_measurement_divide' => isset( THEMECOMPLETE_EPO()->tm_epo_measurement_divide ) && ( THEMECOMPLETE_EPO()->tm_epo_measurement_divide === "yes" ) ? 1 : 0,
			);
			wp_localize_script( 'themecomplete-comp-measurement', 'TMEPOMEASUREMENTJS', $args );
		}
	}

	/**
	 * Disable EPO price filters
	 *
	 * @since 1.0
	 */
	public function template_redirect() {
		remove_filter( 'woocommerce_get_price_html', array( THEMECOMPLETE_EPO(), 'get_price_html' ), 10 );
		remove_filter( 'woocommerce_product_get_price', array( THEMECOMPLETE_EPO(), 'tm_woocommerce_get_price' ), 1 );
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = array() ) {
		$headers["measurement"] = array( "tcfa tcfa-ruler-combined", esc_html__( 'WooCommerce Measurement Calculator', 'woocommerce-tm-extra-product-options' ) );

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = array() ) {
		$label                   = esc_html__( 'WooCommerce Measurement Calculator', 'woocommerce-tm-extra-product-options' );
		$settings["measurement"] = array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'    => esc_html__( 'Multiply options cost by area', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the calculated area.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_measurement_calculate_mode',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Divide price with measurement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will divide the original price with the needed measurement.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_measurement_divide',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			),

			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);

		return $settings;
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = array() ) {

		if ( class_exists( 'WC_Measurement_Price_Calculator' ) ) {
			$settings["tm_epo_measurement_calculate_mode"] = "no";
			$settings["tm_epo_measurement_divide"] = "no";
		}

		return $settings;

	}

	/**
	 * Alter price on cart
	 *
	 * @since 1.0
	 */
	public function woocommerce_tm_epo_price_on_cart( $price = "", $cart_item = "" ) {
		if ( isset( THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode ) && THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode == 'yes' ) {
			if ( is_array( $cart_item ) && isset( $cart_item['pricing_item_meta_data'] ) && ! empty( $cart_item['pricing_item_meta_data']['_quantity'] ) ) {
				$new_quantity   = $cart_item['quantity'] / $cart_item['pricing_item_meta_data']['_quantity'];
				$original_price = $price;
				$original_price = $original_price * $new_quantity;

				$price = $original_price;
			}
		}

		return $price;

	}

	/**
	 * Alter option prices
	 *
	 * @since 1.0
	 */
	public function wc_epo_option_price_correction( $price = "", $cart_item = "" ) {

		if ( isset( $cart_item['pricing_item_meta_data'] ) && !empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) && isset( THEMECOMPLETE_EPO()->tm_epo_measurement_divide ) && THEMECOMPLETE_EPO()->tm_epo_measurement_divide === 'yes' ) {
			$price = floatval( $price ) / floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
		}

		if ( isset( THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode ) && THEMECOMPLETE_EPO()->tm_epo_measurement_calculate_mode === 'yes' ) {

			if ( is_array( $cart_item ) && isset( $cart_item['pricing_item_meta_data'] ) && ! empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) ) {
				$price = floatval( $price ) * floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
			}

		}

		return $price;
	}

	/**
	 * Set original price
	 *
	 * @since 1.0
	 */
	public function wc_epo_add_cart_item_original_price( $price = "", $cart_item = "" ) {
		
		if ( isset( $cart_item['pricing_item_meta_data'] ) && isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
			$price = floatval( $cart_item['pricing_item_meta_data']['_price'] );
			if ( ! empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) && isset( THEMECOMPLETE_EPO()->tm_epo_measurement_divide ) && THEMECOMPLETE_EPO()->tm_epo_measurement_divide === 'yes' ) {
				$price = floatval( $price ) / floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
			}
		}

		return $price;
	}

}
