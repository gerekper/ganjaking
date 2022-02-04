<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Admin settings.
 *
 * Handle functions for admin settings page.
 *
 * @author		Jeroen Sormani
 * @version		1.0.0
 */
class WAPL_Settings {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add WC settings tab
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'settings_tab' ), 60 );

		// Settings page contents
		add_action( 'woocommerce_settings_tabs_labels', array( $this, 'settings_page' ) );

		// Save settings page
		add_action( 'woocommerce_update_options_labels', array( $this, 'update_options' ) );

		// Table field type
		add_action( 'woocommerce_admin_field_product_labels_table', array( $this, 'generate_table_field' ) );
	}


	/**
	 * Settings tab.
	 *
	 * Add a WooCommerce settings tab for the plugins settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $tabs Array Default tabs used in WC.
	 * @return array       All WC settings tabs including newly added.
	 */
	public function settings_tab( $tabs ) {
		$tabs['labels'] = __( 'Product Labels', 'woocommerce-advanced-product-labels' );

		return $tabs;
	}


	/**
	 * Settings page array.
	 *
	 * Get settings page fields array.
	 *
	 * @since 1.0.0
	 */
	public function get_settings() {

		$settings = apply_filters( 'woocommerce_wapl_settings', array(

			array(
				'title' => __( 'Advanced Product Labels', 'woocommerce-advanced-product-labels' ),
				'type'  => 'title',
			),

			array(
				'title'    => __( 'Enable/Disable', 'woocommerce-advanced-product-labels' ),
				'desc'     => __( 'Enable to display labels on the front-end. When disabled you can still add/modify labels.', 'woocommerce-advanced-product-labels' ),
				'id'       => 'enable_wapl',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'autoload' => false
			),

			array(
				'title'    => __( 'Show on the detail page', 'woocommerce-advanced-product-labels' ),
				'desc'     => __( 'Show the product labels also on product detail pages.', 'woocommerce-advanced-product-labels' ),
				'id'       => 'show_wapl_on_detail_pages',
				'default'  => 'no',
				'type'     => 'checkbox',
				'autoload' => false
			),

			array(
				'title' => __( 'Product Labels', 'woocommerce-advanced-product-labels' ),
				'type'  => 'product_labels_table',
			),

			array(
				'type' => 'sectionend',
			),

		) );

		return $settings;
	}


	/**
	 * Settings page content.
	 *
	 * Output settings page content via WooCommerce output_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {
		WC_Admin_Settings::output_fields( $this->get_settings() );
	}


	/**
	 * Save settings.
	 *
	 * Save settings based on WooCommerce save_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function update_options() {
		WC_Admin_Settings::save_fields( $this->get_settings() );
	}


	/**
	 * Table field type.
	 *
	 * Load and render table as a field type.
	 *
	 * @return string
	 */
	public function generate_table_field() {
		ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'views/html-labels-table.php';
		ob_end_flush();
	}
}
