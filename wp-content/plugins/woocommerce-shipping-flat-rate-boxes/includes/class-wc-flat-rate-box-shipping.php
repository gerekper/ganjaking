<?php
/**
 * Main plugin class.
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Flat Rate Box Shipping Class.
 */
class WC_Flat_Rate_Box_Shipping {
	/**
	 * Constructor.
	 */
	public function __construct() {
		define( 'BOX_SHIPPING_DEBUG', defined( 'WP_DEBUG' ) && 'true' === WP_DEBUG && ( ! defined( 'WP_DEBUG_DISPLAY' ) || 'true' === WP_DEBUG_DISPLAY ) );
		$this->init();
	}

	/**
	 * Register method for usage.
	 *
	 * @param  array $shipping_methods List of shipping methods.
	 * @return array
	 */
	public function woocommerce_shipping_methods( $shipping_methods ) {
		$shipping_methods['flat_rate_boxes'] = 'WC_Shipping_Flat_Rate_Boxes';
		return $shipping_methods;
	}

	/**
	 * Init Flat Rate Boxes.
	 */
	public function init() {
		include_once __DIR__ . '/functions-ajax.php';
		include_once __DIR__ . '/functions-admin.php';

		/**
		 * Install check (for updates).
		 */
		if ( get_option( 'box_shipping_version' ) < WC_BOX_SHIPPING_VERSION ) {
			wc_shipping_flat_rate_boxes_install();
		}

		add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ) );

		// Hooks.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-flat-rate-boxes', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	/**
	 * Plugin row meta.
	 *
	 * @param  array  $links List of links.
	 * @param  string $file  Current plugin.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __DIR__ ) === $file ) {
			$row_meta = array(
				/**
				 * Allow modifying plugin documentation link.
				 *
				 * @param string $link Documentation link.
				 *
				 * @since 2.0.0
				 */
				'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_flat_rate_boxes_shipping_docs_url', 'https://woocommerce.com/document/flat-rate-box-shipping/' ) ) . '" title="' . esc_attr__( 'View Documentation', 'woocommerce-shipping-flat-rate-boxes' ) . '">' . esc_html__( 'Docs', 'woocommerce-shipping-flat-rate-boxes' ) . '</a>',
				/**
				 * Allow modifying plugin support link.
				 *
				 * @param string $link Support link.
				 *
				 * @since 2.0.0
				 */
				'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_flat_rate_boxes_support_url', 'https://support.woocommerce.com/' ) ) . '" title="' . esc_attr__( 'Visit Premium Customer Support Forum', 'woocommerce-shipping-flat-rate-boxes' ) . '">' . esc_html__( 'Premium Support', 'woocommerce-shipping-flat-rate-boxes' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Admin styles + scripts
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'woocommerce_shipping_flat_rate_boxes_styles', plugins_url( '/assets/css/admin.css', __DIR__ ), array(), WC_BOX_SHIPPING_VERSION );
		wp_register_script( 'woocommerce_shipping_flat_rate_box_rows', plugins_url( '/assets/js/flat-rate-box-rows.min.js', __DIR__ ), array( 'jquery', 'wp-util' ), WC_BOX_SHIPPING_VERSION, true );
		wp_localize_script(
			'woocommerce_shipping_flat_rate_box_rows',
			'woocommerce_shipping_flat_rate_box_rows',
			array(
				'i18n'             => array(
					'delete_rates' => __( 'Delete the selected boxes?', 'woocommerce-table-rate-shipping' ),
				),
				'delete_box_nonce' => wp_create_nonce( 'delete-box' ),
			)
		);
	}

	/**
	 * Load shipping class.
	 */
	public function shipping_init() {
		include_once __DIR__ . '/class-wc-shipping-flat-rate-boxes.php';
		include_once __DIR__ . '/class-wc-shipping-flat-rate-boxes-privacy.php';
	}
}
