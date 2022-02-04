<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAPL_Single_label
 *
 * WAPL single label class, load single label config.
 *
 * @class       WAPL_Single_label
 * @version     1.0.0
 * @author      Jeroen Sormani
 */
class WAPL_Single_Labels {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add the product tabs
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_label_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_label_tab_settings' ) );

		// Update meta from the above settings
		add_action( 'woocommerce_process_product_meta', array( $this, 'update_product_tab_settings' ) );

		// Hook in on te product title
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_label_template_hook' ), 15 );

		// Add labels on product detail page
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_label_template_hook' ), 9 );
	}


	/**
	 * Label products tab.
	 *
	 * Display 'Product labels' tab on edit product page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $tabs Existing tabs.
	 * @return array       Modified settings tabs, containing 'Product label'.
	 */
	public function add_product_label_tab( $tabs ) {
		$tabs['labels'] = array(
			'label'  => __( 'Product label', 'woocommerce-advanced-product-labels' ),
			'target' => 'woocommerce_advanced_product_labels',
			'class'  => array( 'woocommerce_advanced_product_labels' ),
		);

		return $tabs;
	}


	/**
	 * Settings in 'Product label' tab.
	 *
	 * Configure and display the settings in the 'Product data' meta box
	 *
	 * @since 1.0.0
	 */
	public function product_label_tab_settings() {
		$label = $this->get_label_data();
		$GLOBALS['product'] = wc_get_product();

		require 'includes/admin/views/html-product-tab.php';
	}


	/**
	 * Update single product label
	 *
	 * @since 1.0.0
	 */
	public function update_product_tab_settings() {
		global $post;

		// Save each field in separate post meta, needed for WC
		$meta_keys = array(
			'_wapl_label_type',
			'_wapl_label_text',
			'_wapl_label_style',
			'_wapl_label_align',
			'_wapl_custom_bg_color',
			'_wapl_custom_text_color',
			'_wapl_custom_image',
			'_wapl_position',
		);

		foreach ( $meta_keys as $meta ) {
			if ( isset( $_POST[ $meta ] ) ) {
				update_post_meta( $post->ID, $meta, wc_clean( $_POST[ $meta ] ) );
			}
		}

		if ( isset( $_POST['_wapl_label_exclude'] ) ) {
			update_post_meta( $post->ID, '_wapl_label_exclude', 'yes' );
		} else {
			update_post_meta( $post->ID, '_wapl_label_exclude', 'no' );
		}
	}


	/**
	 * Hook label in product loop.
	 *
	 * Echo's the product label @hook 'woocommerce_before_shop_loop_item_title'.
	 *
	 * @since 1.0.0
	 */
	public function product_label_template_hook() {

		if ( get_option( 'show_wapl_on_detail_pages', 'no' ) == 'no' && is_singular( 'product' ) ) {
			return;
		}

		$label = $this->get_label_data();
		if ( ! empty( $label ) ) {
			echo wapl_get_label_html( $label );
		}
	}


	/**
	 * Return label data.
	 *
	 * @since 1.0.0
	 *
	 * @param  int   $product_id
	 * @return array
	 */
	public function get_label_data( $product_id = null ) {

		if ( ! $product_id ) {
			global $post;
			$product_id = $post->ID;
		}

		$data = array(
			'id'                => $product_id,
			'exclude'           => get_post_meta( $product_id, '_wapl_label_exclude', true ),
			'type'              => get_post_meta( $product_id, '_wapl_label_type', true ),
			'text'              => get_post_meta( $product_id, '_wapl_label_text', true ),
			'style'             => get_post_meta( $product_id, '_wapl_label_style', true ),
			'align'             => get_post_meta( $product_id, '_wapl_label_align', true ),
			'custom_bg_color'   => get_post_meta( $product_id, '_wapl_custom_bg_color', true ),
			'custom_text_color' => get_post_meta( $product_id, '_wapl_custom_text_color', true ),
			'custom_image'      => get_post_meta( $product_id, '_wapl_custom_image', true ),
			'position'          => get_post_meta( $product_id, '_wapl_position', true ),
		);

		if ( empty( $data['custom_bg_color'] ) ) {
			$data['custom_bg_color'] = '#D9534F';
		}
		if ( empty( $data['custom_text_color'] ) ) {
			$data['custom_text_color'] = '#fff';
		}
		if ( empty( $data['position'] ) ) {
			$data['position'] = array( 'left' => 0, 'top' => 0 );
		}

		if ( $data['text'] == '' && $data['type'] !== 'custom' ) {
			$data = array();
		}

		return $data;
	}


}
