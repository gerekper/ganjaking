<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Woocommerce_Advanced_Product_Labels_Globals
 *
 * Handle the global product labels
 *
 * @class 		Woocommerce_Advanced_Product_Labels_Globals
 * @author		Jeroen Sormani
 * @package 	WooCommerce Advanced Product Labels
 * @version		1.0.0
 */
class WAPL_Global_Labels {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Double insurance to not load on admin. Using shortcodes like [products ids] can cause fatals.
		// @since NEWVERSION wp_is_json_request() added due to fatal error in gutenberg when using 'sale products' block
		if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) || wp_is_json_request() ) return;

		// Add labels on archive page
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'global_label_hook' ), 15 ); // executing global label function

		// Add labels on product detail page
		add_action( 'woocommerce_product_thumbnails', array( $this, 'global_label_hook' ), 9 );
	}


	/**
	 * Display labels.
	 *
	 * Hook into product loop to add the global product labels.
	 *
	 * @since 1.0.0
	 */
	public function global_label_hook() {

		/** @var $product WC_Product */
		global $product;

		// Stop if global labels are disabled
		if ( 'no' == get_option( 'enable_wapl', 'yes' ) ) {
			return;
		}

		// Ensure it only shows when setup on detail pages
		if ( get_option( 'show_wapl_on_detail_pages', 'no' ) == 'no' && is_singular( 'product' ) ) {
			return;
		}

		// Check if product is excluded from Global Labels
		if ( 'yes' == get_post_meta( $product->get_id(), '_wapl_label_exclude', true ) ) {
			return;
		}

		// Get all global labels
		if ( false === $global_labels = wp_cache_get( 'global_labels', 'woocommerce-advanced-product-labels' ) ) {
			$global_labels = wapl_get_advanced_product_labels( array( 'suppress_filters' => false ) );
			wp_cache_set( 'global_labels', $global_labels, 'woocommerce-advanced-product-labels' );
		}

		// Loop through each global label
		foreach ( $global_labels as $global_label ) {

			// Retrieve label data and conditions
			$label            = get_post_meta( $global_label->ID, '_wapl_global_label', true );
			$condition_groups = $label['conditions'];

			// if one of the condition groups match, echo the label
			if ( wpc_match_conditions( $condition_groups, array( 'context' => 'wapl' ) ) ) {

				$label['custom_bg_color']   = isset( $label['label_custom_background_color'] ) ? $label['label_custom_background_color'] : '#D9534F';
				$label['custom_text_color'] = isset( $label['label_custom_text_color'] ) ? $label['label_custom_text_color'] : '#fff';
				$label['style_attr']        = ( isset( $label['style'] ) && 'custom' == $label['style'] ) ? "style='background-color: {$label['custom_bg_color']}; color: {$label['custom_text_color']};'" : '';
				$label['id']                = $global_label->ID;

				echo wapl_get_label_html( $label );

			}
		}
	}


}
