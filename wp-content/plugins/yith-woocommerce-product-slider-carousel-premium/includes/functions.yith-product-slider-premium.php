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
if ( ! function_exists( 'yith_wpml_get_translated_id' ) ) {
	/**
	 * Get the id of the current translation of the post/custom type
	 *
	 * @param int    $id id.
	 * @param string $post_type post type.
	 *
	 * @since  2.0.0
	 * @package YITH
	 */
	function yith_wpml_get_translated_id( $id, $post_type ) {

		if ( function_exists( 'icl_object_id' ) ) {

			$id = icl_object_id( $id, $post_type, true );

		}

		return $id;
	}
}

if ( ! function_exists( 'ywcps_json_search_product_tags' ) ) {

	/**
	 * Get product tags by terms
	 *
	 * @package YITH
	 * @since 1.0.0
	 */
	function ywcps_json_search_product_tags() {
		ywcps_json_search_product_categories( '', array( 'product_tag' ) );
	}
}
add_action( 'wp_ajax_yit_slider_json_search_product_tag', 'ywcps_json_search_product_tags', 10 );

if ( ! function_exists( 'ywcps_json_search_product_brands' ) ) {

	/**
	 * Get product brands by terms
	 *
	 * @package YITH
	 * @since 1.0.0
	 */
	function ywcps_json_search_product_brands() {
		ywcps_json_search_product_categories( '', array( YITH_WCBR::$brands_taxonomy ) );
	}
}
add_action( 'wp_ajax_yit_slider_json_search_product_brands', 'ywcps_json_search_product_brands', 10 );

/**
 * Function that returns an array containing the IDs of the products that are out of stock.
 *
 * @since 2.0
 * @access public
 * @return array
 */
function yith_wc_get_product_ids_out_of_stock() {
	global $wpdb;

	// Load from cache!
	$product_ids_out_of_stock = get_transient( 'yith_products_out_of_stock' );

	// Valid cache found!
	if ( false !== $product_ids_out_of_stock ) {
		return $product_ids_out_of_stock;
	}

	$product_ids_out_of_stock = $wpdb->get_results( //phpcs:ignore WordPress.DB.DirectDatabaseQuery
		"
			SELECT post.ID FROM `$wpdb->posts` AS post
			LEFT JOIN `$wpdb->postmeta` AS meta ON post.ID = meta.post_id
			WHERE post.post_type IN ( 'product' )
			AND post.post_status = 'publish'
			AND meta.meta_key = '_stock_status'
			AND meta.meta_value LIKE 'outofstock'
			"
	);

	$product_ids_out_of_stock = wp_list_pluck( $product_ids_out_of_stock, 'ID' );
	set_transient( 'yith_products_out_of_stock', $product_ids_out_of_stock, DAY_IN_SECONDS * 30 );

	return $product_ids_out_of_stock;
}

if ( ! function_exists( 'ywcps_get_term_id_by_slug' ) ) {

	/**
	 * Ywcps_get_term_id_by_slug
	 *
	 * @param mixed  $slug slug.
	 * @param string $taxonomy taxonomy.
	 * @return array;
	 */
	function ywcps_get_term_id_by_slug( $slug, $taxonomy = 'product_cat' ) {

		$slug = ! is_array( $slug ) ? array( $slug ) : $slug;

		$term_ids = array();

		foreach ( $slug as $single_slug ) {

			$term = get_term_by( 'slug', $single_slug, $taxonomy );

			if ( is_object( $term ) ) {

				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}
}

if ( ! function_exists( 'ywcps_get_layout_style' ) ) {
	/**
	 * Ywcps_get_layout_style
	 *
	 * @return $custom_css
	 */
	function ywcps_get_layout_style() {

		// Layout 1.
		$background1           = get_option( 'ywcps_layout1_box_bg_color', '#f7f7f7' );
		$border_color1         = get_option( 'ywcps_layout1_box_border_color', '#cccccc' );
		$text_color1           = get_option( 'ywcps_layout1_text_color', '#000000' );
		$background_nav1       = get_option( 'ywcps_layout1_background_color_arrow', '#f7f7f7' );
		$border_color_nav1     = get_option( 'ywcps_layout1_border_color_arrow', 'f7f7f7' );
		$text_color_nav1       = get_option( 'ywcps_layout1_text_color_arrow', '#a9a9a9' );
		$background_btn1       = get_option( 'ywcps_layout1_button_bg_color', '#f7f7f7' );
		$background_btn_hov1   = get_option( 'ywcps_layout1_button_bg_color_hover', '#7f7f7f' );
		$text_btn_color1       = get_option( 'ywcps_layout1_button_color', '#000' );
		$text_btn_color_hov1   = get_option( 'ywcps_layout1_button_color_hover', '#fff' );
		$border_btn_color1     = get_option( 'ywcps_layout1_border_button_color', '#f7f7f7' );
		$border_btn_color_hov1 = get_option( 'ywcps_layout1_border_button_color_hover', '#7f7f7f' );

		// Layout 2.
		$background2           = get_option( 'ywcps_layout2_box_bg_color', '#fff' );
		$border_color2         = get_option( 'ywcps_layout2_box_border_color', '#ededed' );
		$text_color2           = get_option( 'ywcps_layout2_text_color', '#000000' );
		$background_nav2       = get_option( 'ywcps_layout2_background_color_arrow', '#ededed' );
		$border_color_nav2     = get_option( 'ywcps_layout2_border_color_arrow', '#ededed' );
		$text_color_nav2       = get_option( 'ywcps_layout2_text_color_arrow', '#a4a4a4' );
		$background_btn2       = get_option( 'ywcps_layout2_button_bg_color', '#c2947c' );
		$background_btn_hov2   = get_option( 'ywcps_layout2_button_bg_color_hover', '#fff' );
		$text_btn_color2       = get_option( 'ywcps_layout2_button_color', '#fff' );
		$text_btn_color_hov2   = get_option( 'ywcps_layout2_button_color_hover', '#c2947c' );
		$border_btn_color2     = get_option( 'ywcps_layout2_border_button_color', '#c2947c' );
		$border_btn_color_hov2 = get_option( 'ywcps_layout2_border_button_color_hover', '#c2947c' );

		// Layout 3.
		$background3           = get_option( 'ywcps_layout3_box_bg_color', '#fff' );
		$border_color3         = get_option( 'ywcps_layout3_box_border_color', '#ededed' );
		$text_color3           = get_option( 'ywcps_layout3_text_color', '#000000' );
		$background_nav3       = get_option( 'ywcps_layout3_background_color_arrow', '#fff' );
		$border_color_nav3     = get_option( 'ywcps_layout3_border_color_arrow', '#ededed' );
		$text_color_nav3       = get_option( 'ywcps_layout3_text_color_arrow', '#8b8b8b' );
		$background_btn3       = get_option( 'ywcps_layout3_button_bg_color', '#828282' );
		$background_btn_hov3   = get_option( 'ywcps_layout3_button_bg_color_hover', '#434343' );
		$text_btn_color3       = get_option( 'ywcps_layout3_button_color', '#fff' );
		$text_btn_color_hov3   = get_option( 'ywcps_layout3_button_color_hover', '#fff' );
		$border_btn_color3     = get_option( 'ywcps_layout3_border_button_color', '#828282' );
		$border_btn_color_hov3 = get_option( 'ywcps_layout3_border_button_color_hover', '#434343' );

		$properties = array(
			'layout1' => array(
				'background'                => $background1,
				'border-box'                => $border_color1,
				'text-color'                => $text_color1,
				'background-nav'            => $background_nav1,
				'border-nav'                => $border_color_nav1,
				'text-color-nav'            => $text_color_nav1,
				'background-button'         => $background_btn1,
				'background-button-hover'   => $background_btn_hov1,
				'text-color-button'         => $text_btn_color1,
				'text-color-button-hover'   => $text_btn_color_hov1,
				'border-color-button'       => $border_btn_color1,
				'border-color-button-hover' => $border_btn_color_hov1,
			),
			'layout2' => array(
				'background'                => $background2,
				'border-box'                => $border_color2,
				'text-color'                => $text_color2,
				'background-nav'            => $background_nav2,
				'border-nav'                => $border_color_nav2,
				'text-color-nav'            => $text_color_nav2,
				'background-button'         => $background_btn2,
				'background-button-hover'   => $background_btn_hov2,
				'text-color-button'         => $text_btn_color2,
				'text-color-button-hover'   => $text_btn_color_hov2,
				'border-color-button'       => $border_btn_color2,
				'border-color-button-hover' => $border_btn_color_hov2,
			),
			'layout3' => array(
				'background'                => $background3,
				'border-box'                => $border_color3,
				'text-color'                => $text_color3,
				'background-nav'            => $background_nav3,
				'border-nav'                => $border_color_nav3,
				'text-color-nav'            => $text_color_nav3,
				'background-button'         => $background_btn3,
				'background-button-hover'   => $background_btn_hov3,
				'text-color-button'         => $text_btn_color3,
				'text-color-button-hover'   => $text_btn_color_hov3,
				'border-color-button'       => $border_btn_color3,
				'border-color-button-hover' => $border_btn_color_hov3,
			),
		);
		$custom_css = ":root {\n";

		foreach ( $properties as $layout => $rules ) {
			$key = '--ywcps-' . $layout;
			foreach ( $rules as $property => $value ) {
				$key_attr = $key . '-' . $property;

				$row         = $key_attr . ":{$value};\n";
				$custom_css .= $row;
			}
		}

		$custom_css .= '}';

		return $custom_css;
	}
}
