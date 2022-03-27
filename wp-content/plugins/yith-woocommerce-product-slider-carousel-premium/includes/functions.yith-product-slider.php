<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
if ( ! function_exists( 'get_tab_categories' ) ) {

	/**
	 * Get_tab_categories
	 *
	 * Load all categories in Category chosen field
	 *
	 * @package YITH
	 * @since 1.0.0
	 * @return array
	 */
	function get_tab_categories() {

		$args = array( 'hide_empty' => 1 );

		$categories_term = wp_get_post_terms( 0, 'product_cat', $args );

		$categories = array();

		foreach ( $categories_term as $category ) {

			$categories[ $category->slug ] = '#' . $category->term_id . '-' . $category->name;
		}

		return $categories;

	}
}
if ( ! function_exists( 'ywcps_json_search_product_categories' ) ) {

	/**
	 * Ywcps_json_search_product_categories
	 *
	 * @param  mixed $x x.
	 * @param  mixed $taxonomy_types taxonomy types.
	 * @return void
	 */
	function ywcps_json_search_product_categories( $x = '', $taxonomy_types = array( 'product_cat' ) ) {

			global $wpdb;
			$term = (string) urldecode( stripslashes( wp_strip_all_tags( isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification
			$term = '%' . $term . '%';

			$query_cat = $wpdb->prepare(
				"SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.name LIKE %s",
				implode( ',', $taxonomy_types ),
				$term
			);

			$product_categories = $wpdb->get_results( $query_cat ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

			$to_json = array();

		foreach ( $product_categories as $product_category ) {

			$to_json[ $product_category->slug ] = '#' . $product_category->term_id . '-' . $product_category->name;
		}

		wp_send_json( $to_json );

	}
}
add_action( 'wp_ajax_yit_slider_json_search_product_categories', 'ywcps_json_search_product_categories', 10 );


if ( ! function_exists( 'ywcps_animations_list' ) ) {


	/**
	 * Ywcps_animations_list
	 *
	 * @return apply_filters
	 */
	function ywcps_animations_list() {

		$animations = array(
			'Fading Entrances' => array( 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig' ),
			'Fading Exits'     => array( 'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig' ),

		);

		return apply_filters( 'ywcps_animate_styles', $animations );
	}
}

if ( ! function_exists( 'YITH_Product_Slider_Type' ) ) {

	/**
	 * YITH_Product_Slider_Type
	 *
	 * @return YITH_Product_Slider_Type_Premium instance
	 */
	function YITH_Product_Slider_Type() { // phpcs:ignore WordPress.NamingConventions

		if ( ! defined( 'YWCPS_PREMIUM' ) ) {
			return YITH_Product_Slider_Type::get_instance();
		} else {
			return YITH_Product_Slider_Type_Premium::get_instance();
		}
	}
}

if ( ! function_exists( 'ywcps_add_gutenberg_block' ) ) {

	/**
	 * Ywcps_add_gutenberg_block
	 *
	 * @return void
	 */
	function ywcps_add_gutenberg_block() {

		global $YWC_Product_Slider; // phpcs:ignore WordPress.NamingConventions
		$all_sliders = $YWC_Product_Slider->get_productslider(); // phpcs:ignore WordPress.NamingConventions

		$options = array();

		foreach ( $all_sliders as $slider ) {
			$options[ $slider['value'] ] = $slider['text'];
		}
		$current_option = current( array_keys( $options ) );

		$block = array(
			'yith-wc-productslider' => array(
				'title'          => _x( 'Product Slider Carousel', '[gutenberg]: product slider carousel', 'yith-woocommerce-product-slider-carousel' ),
				'description'    => __( 'Show your Product Slider in sidebar!', 'yith-woocommerce-product-slider-carousel' ),
				'shortcode_name' => 'yith_wc_productslider',
				'do_shortcode'   => false,
				'keywords'       => array( __( 'Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ) ),
				'attributes'     => array(
					'id' => array(
						'type'    => 'select',
						'label'   => __( 'Slider', 'yith-woocommerce-product-slider-carousel' ),
						'options' => $options,
						'default' => $current_option,
					),
				),
			),
		);

		yith_plugin_fw_gutenberg_add_blocks( $block );

	}
}
