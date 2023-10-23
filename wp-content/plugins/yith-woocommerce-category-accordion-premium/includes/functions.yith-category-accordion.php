<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ywcca_json_search_wc_categories' ) ) {

	/**
	 * Ywcca_json_search_wc_categories
	 *
	 * @param mixed $x x.
	 * @param mixed $taxonomy_types taxonomy types.
	 *
	 * @return void
	 */
	function ywcca_json_search_wc_categories( $x = '', $taxonomy_types = array( 'product_cat' ) ) {

		global $wpdb;
		$term      = (string) urldecode( stripslashes( wp_strip_all_tags( isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification
		$term      = '%' . $term . '%';
		$query_cat = $wpdb->prepare(
			"SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                          FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                          WHERE {$wpdb->term_taxonomy}.taxonomy IN ( %s ) AND {$wpdb->terms}.name LIKE %s",
			implode( ',', $taxonomy_types ),
			$term
		);

		$to_json            = array();
		$product_categories = $wpdb->get_results( $query_cat ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

		foreach ( $product_categories as $product_category ) {

			$to_json[ $product_category->term_id ] = '#' . $product_category->term_id . '-' . $product_category->name;
		}

		wp_send_json( $to_json );

	}
}

if ( ! function_exists( 'ywcca_json_search_wp_posts' ) ) {

	/**
	 * Ywcca_json_search_wp_posts
	 *
	 * @param mixed $x x.
	 * @param mixed $post_type post type.
	 *
	 * @return void
	 */
	function ywcca_json_search_wp_posts( $x = '', $post_type = array( 'post' ) ) {

		global $wpdb;

		$term = (string) urldecode( stripslashes( wp_strip_all_tags( isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification
		$term = '%' . $term . '%';

		$query = $wpdb->prepare(
			"SELECT {$wpdb->posts}.ID, {$wpdb->posts}.post_title
                                        FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type IN (%s) AND {$wpdb->posts}.post_title LIKE %s",
			implode( ',', $post_type ),
			$term
		);

		$posts = $wpdb->get_results( $query ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

		$to_json = array();

		foreach ( $posts as $post ) {

			$to_json[ $post->ID ] = $post->post_title;
		}

		wp_send_json( $to_json );

	}
}


add_action( 'wp_ajax_yith_category_accordion_json_search_wc_categories', 'ywcca_json_search_wc_categories', 10 );

if ( ! function_exists( 'ywcca_json_search_wp_categories' ) ) {

	/**
	 * Ywcca_json_search_wp_categories
	 *
	 * @return void
	 */
	function ywcca_json_search_wp_categories() {
		ywcca_json_search_wc_categories( '', array( 'category' ) );

	}
}
add_action( 'wp_ajax_yith_json_search_wp_categories', 'ywcca_json_search_wp_categories', 10 );


add_action( 'wp_ajax_yith_json_search_wp_posts', 'ywcca_json_search_wp_posts', 10 );


if ( ! function_exists( 'ywcca_json_search_wp_pages' ) ) {
	/**
	 * Ywcca_json_search_wp_pages
	 *
	 * @return void
	 */
	function ywcca_json_search_wp_pages() {
		ywcca_json_search_wp_posts( '', array( 'page' ) );

	}
}

add_action( 'wp_ajax_yith_json_search_wp_pages', 'ywcca_json_search_wp_pages', 10 );


if ( ! function_exists( 'yith_get_navmenu' ) ) {

	/**
	 * Yith_get_navmenu
	 *
	 * @return array $options
	 */
	function yith_get_navmenu() {

		$nav_menus = wp_get_nav_menus();
		$options   = array();

		foreach ( $nav_menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}
}
/**
 * Call the style options function
 *
 * @param $style
 * @param $postid
 * @return array|array[]
 */
function ywcca_get_style( $style, $postid ){
	$style_options = array();
	switch ($style){
		case 'style1':
			$style_options = ywcca_get_style_1( $style, $postid );
			break;
		case 'style2':
			$style_options = ywcca_get_style_2( $style, $postid );
			break;
		case 'style3':
			$style_options = ywcca_get_style_3( $style , $postid );
			break;
		case 'style4':
			$style_options = ywcca_get_style_4( $style, $postid );
			break;
		case 'style5':
			$style_options = ywcca_get_style_5( $style , $postid );
			break;
		case 'style6':
			$style_options = ywcca_get_style_6( $style, $postid );
			break;
	}

	return $style_options;
}

/**
 * Get the Style 1 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_1( $style, $postid ){

	// Get count style !
	$old_count_style = ! empty( get_option( 'ywcca_' . substr( $style, 0, 5 ) . '_' . substr( $style, - 1 ) . '_count' ) ) ? get_option( 'ywcca_' . substr( $style, 0, 5 ) . '_' . substr( $style, - 1 ) . '_count' ) : '';
	switch ( $old_count_style ) {
		case 'rect':
			$new_count_style = 'square_style';
			break;
		case 'round':
			$new_count_style = 'circle_style';
			break;
		case 'default':
			$new_count_style = 'simple_style';
			break;
		default:
			$new_count_style = 'simple_style';
	}

	$title_tipography        = ! empty ( get_option( 'ywcca_' . $style . '_title_typography' ) ) ? get_option( 'ywcca_' . $style . '_title_typography' ) : array(
		'color'     => 'rgb(72, 72, 72)',
		'style'     => 'bold',
		'size'      => 14,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);

	$title_parent_tipography = ! empty ( get_option( 'ywcca_' . $style . '_parent_typography' ) ) ? get_option( 'ywcca_' . $style . '_parent_typography' ) : array(
		'color'     => 'rgb(72, 72, 72)',
		'style'     => 'regular',
		'size'      => 13,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);

	$title_child_tipography  = ! empty ( get_option( 'ywcca_' . $style . '_child_typography' ) ) ? get_option( 'ywcca_' . $style . '_child_typography' ) : array(
		'color'     => 'rgb(144, 144, 144)',
		'style'     => 'regular',
		'size'      => 13,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);

	return array(
		'style_1' => array(
			'_ywcacc_count_style' => $new_count_style,
			// General setings !
			'_ywcacc_container_border' =>  ! empty( get_option( 'ywcca_' . $style . '_general_bg' ) ) ? get_option( 'ywcca_' . $style . '_general_bg' ) : 'rgb(207, 207, 207)',
			'_ywcacc_container_bg' => ! empty( get_option( 'ywcca_' . $style . '_title_bg' ) ) ? get_option( 'ywcca_' . $style . '_title_bg' ) : 'rgb(245, 245, 245)',
			'_ywcacc_border_radius' => array(
				'dimensions' => array(
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				),
				'linked' => 'yes',
				'unit' => 'px',
			),
			'_ywcacc_count_colors' => array(
				'text_color' => 'rgb(124, 124, 124)',
				'border_color' => ! empty( get_option( 'ywcca_' . $style . '_border_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_border_rect_count' ) : 'rgb(124, 124, 124)',
				'background_color' => ! empty( get_option( 'ywcca_' . $style . '_back_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_back_rect_count' ) : 'rgb(245, 245, 245)',
				),
			'_ywcacc_toggle_icon_style' => 'circle_style',
			'_ywcacc_toggle_icon' => 'plus_icon',
			'_ywcacc_toggle_colors' => array(
				'icon_color' => 'rgb(117, 117, 117)',
				'border_color' => 'rgb(228, 228, 228)',
				'background_color' => 'rgb(255, 255, 255)',
				'icon_hover_color' => 'rgb(117, 117, 117)',
				'border_hover_color' => 'rgb(228, 228, 228)',
				'background_hover_color' => 'rgb(117, 117, 117)',
			),
			'_ywcacc_toggle_icon_position' => 'right',

			// Rest of title options !
			'_ywcacc_color_title' => $title_tipography['color'],
			'_ywcacc_font_weight' => $title_tipography['style'],
			'_ywcacc_font_size' => array(
				'font_size' => $title_tipography['size'],
				'type_font_size' => $title_tipography['unit'],
			),
			'_ywcacc_alignment' => 'center',
			'_ywcacc_text_transform' => $title_tipography['transform'],
			'_ywcacc_border_style' => 'no_border',
			'_ywcacc_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) : 'rgb(245, 245, 245)',

			// Parent Category Options !
			'_ywcacc_parent_color' => array(
				'parent_text_color' => $title_parent_tipography['color'],
				'parent_hover_color' => $title_parent_tipography['color'],
			),
			'_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
			'_ywcacc_parent_font_size' => array(
				'font_size' =>$title_parent_tipography['size'],
				'type_font_size' => $title_parent_tipography['unit'],
			),
			'_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
			'_ywcacc_parent_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) : 'rgb(255, 255, 255)',
			'_ywcacc_parent_bg_color' => array(
				'parent_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) : 'rgb(245, 245, 245)',
				'parent_hover_color' => 'rgb(245, 245, 245)',
			),

			// Child Category Options !
			'_ywcacc_child_color' => array(
				'child_text_color' => $title_child_tipography['color'],
				'child_hover_color' => $title_child_tipography['color'],
			),
			'_ywcacc_child_font_weight' => $title_child_tipography['style'],
			'_ywcacc_child_font_size' => array(
				'font_size' =>  $title_child_tipography['size'],
				'type_font_size' => $title_child_tipography['unit'],
			),
			'_ywcacc_child_text_transform' => $title_child_tipography['transform'],
			'_ywcacc_child_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) : 'rgb(233, 233, 233)',
			'_ywcacc_child_bg_color' => array(
				'child_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) : 'rgb(255, 255, 255)',
				'child_hover_color' => 'rgb(255, 255, 255)',
			),
		),
	);
}

/**
 * Get the Style 2 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_2( $style, $postid ){
	// Get count style !
	$old_count_style = ! empty( get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) ) ? get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) : '';
	switch ( $old_count_style ) {
		case 'rect':
			$new_count_style = 'square_style';
			break;
		case 'round':
			$new_count_style = 'circle_style';
			break;
		case 'default':
			$new_count_style = 'simple_style';
			break;
		default:
			$new_count_style = 'square_style';
	}

	$title_tipography        = ! empty ( get_option( 'ywcca_' . $style . '_title_typography' ) ) ? get_option( 'ywcca_' . $style . '_title_typography' ) : array(
		'color'     => 'rgb(72, 72, 72)',
		'style'     => 'bold',
		'size'      => 14,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);
	$title_parent_tipography = ! empty ( get_option( 'ywcca_' . $style . '_parent_typography' ) ) ? get_option( 'ywcca_' . $style . '_parent_typography' ) : array(
		'color'     => 'rgb(144, 144, 144)',
		'style'     => 'regular',
		'size'      => 13,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);
	$title_child_tipography  = ! empty ( get_option( 'ywcca_' . $style . '_child_typography' ) ) ? get_option( 'ywcca_' . $style . '_child_typography' ) : array(
		'color'     => 'rgb(144, 144, 144)',
		'style'     => 'regular',
		'size'      => 12,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);


	return array(

		'style_2' => array(
			'_ywcacc_count_style' => $new_count_style,
			// General setings
			'_ywcacc_container_border' =>  ! empty( get_option( 'ywcca_' . $style . '_general_bg' ) ) ? get_option( 'ywcca_' . $style . '_general_bg' ) : 'rgb(255, 255, 255)',
			'_ywcacc_container_bg' => ! empty( get_option( 'ywcca_' . $style . '_title_bg' ) ) ? get_option( 'ywcca_' . $style . '_title_bg' ) : 'rgb(255, 255, 255)',
			'_ywcacc_border_radius' => array(
				'dimensions' => array(
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					),
				'linked' => 'yes',
				'unit' => 'px',
				),
			'_ywcacc_count_colors' => array(
				'text_color' => ' ',
				'border_color' => ! empty( get_option( 'ywcca_' . $style . '_border_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_border_rect_count' ) : ' ',
				'background_color' => ! empty( get_option( 'ywcca_' . $style . '_back_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_back_rect_count' ) : ' ',
				),
			'_ywcacc_toggle_icon_style' => 'square_style',
			'_ywcacc_toggle_icon' => 'plus_icon',
			'_ywcacc_toggle_colors' => array(
				'icon_color' => 'rgb(144, 144, 144)',
				'border_color' => 'rgb(230, 230, 230)',
				'background_color' => 'rgb(255, 255, 255)',
				'icon_hover_color' => 'rgb(144, 144, 144)',
				'border_hover_color' => 'rgb(230, 230, 230)',
				'background_hover_color' => 'rgb(255, 255, 255)',
				),
			'_ywcacc_toggle_icon_position' => 'right',

			// Rest of title options !
			'_ywcacc_color_title' => $title_tipography['color'],
			'_ywcacc_font_weight' => $title_tipography['style'],
			'_ywcacc_font_size' => array(
				'font_size' => $title_tipography['size'],
				'type_font_size' => $title_tipography['unit'],
				),
			'_ywcacc_alignment' => 'left',
			'_ywcacc_text_transform' => $title_tipography['transform'],
			'_ywcacc_border_style' => 'double_lines',
			'_ywcacc_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) : 'rgb(204, 204, 204)',

			// Parent Category Options !
			'_ywcacc_parent_color' => array(
				'parent_text_color' => $title_parent_tipography['color'],
				'parent_hover_color' => '',
				),
			'_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
			'_ywcacc_parent_font_size' => array(
				'font_size' =>$title_parent_tipography['size'],
				'type_font_size' => $title_parent_tipography['unit'],
				),
			'_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
			'_ywcacc_parent_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) : 'rgb(236, 236, 236)',
			'_ywcacc_parent_bg_color' => array(
				'parent_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) : 'rgb(255, 255, 255)',
				'parent_hover_color' => 'rgb(255, 255, 255)',
				),

			// Child Category Options !
			'_ywcacc_child_color' => array(
				'child_text_color' => $title_child_tipography['color'],
				'child_hover_color' => ' ',
				),
			'_ywcacc_child_font_weight' => $title_child_tipography['style'],
			'_ywcacc_child_font_size' => array(
				'font_size' =>  $title_child_tipography['size'],
				'type_font_size' => $title_child_tipography['unit'],
				),
			'_ywcacc_child_text_transform' => $title_child_tipography['transform'],
			'_ywcacc_child_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) : 'rgb(255, 255, 255)',
			'_ywcacc_child_bg_color' => array(
				'child_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) : 'rgb(255, 255, 255)',
				'child_hover_color' => 'rgb(255, 255, 255)',
				),
			),
		);
}

/**
 * Get the Style 3 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_3($style, $postid){
	// Get count style !
	$old_count_style = ! empty( get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) ) ? get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) : ' ';
	switch ( $old_count_style ) {
		case 'rect':
			$new_count_style = 'square_style';
			break;
		case 'round':
			$new_count_style = 'circle_style';
			break;
		case 'default':
			$new_count_style = 'simple_style';
			break;
		default:
			$new_count_style = 'square_style';
	}

	$title_tipography        = ! empty ( get_option( 'ywcca_' . $style . '_title_typography' ) ) ? get_option( 'ywcca_' . $style . '_title_typography' ) : array(
		'color'     => 'rgb(72, 72, 72)',
		'style'     => 'bold',
		'size'      => 14,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);
	$title_parent_tipography = ! empty ( get_option( 'ywcca_' . $style . '_parent_typography' ) ) ? get_option( 'ywcca_' . $style . '_parent_typography' ) : array(
		'color'     => 'rgb(144, 144, 144)',
		'style'     => 'regular',
		'size'      => 13,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);
	$title_child_tipography  = ! empty ( get_option( 'ywcca_' . $style . '_child_typography' ) ) ? get_option( 'ywcca_' . $style . '_child_typography' ) : array(
		'color'     => 'rgb(144, 144, 144)',
		'style'     => 'regular',
		'size'      => 12,
		'unit'      => 'px',
		'transform' => 'uppercase'
	);
	return array(

		'style_3' => array(
			'_ywcacc_count_style' => $new_count_style,
			// General setings !
			'_ywcacc_container_border' =>  ! empty( get_option( 'ywcca_' . $style . '_general_bg' ) ) ? get_option( 'ywcca_' . $style . '_general_bg' ) : 'rgb(224, 224, 224)',
			'_ywcacc_container_bg' => ! empty( get_option( 'ywcca_' . $style . '_title_bg' ) ) ? get_option( 'ywcca_' . $style . '_title_bg' ) : 'rgb(255, 255, 255)',
			'_ywcacc_border_radius' => array(
				'dimensions' => array(
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					),
				'linked' => 'yes',
				'unit' => 'px',
				),
			'_ywcacc_count_colors' => array(
				'text_color' => 'rgb(224, 224, 224)',
				'border_color' => ! empty( get_option( 'ywcca_' . $style . '_border_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_border_rect_count' ) : 'rgb(224, 224, 224)',
				'background_color' => ! empty( get_option( 'ywcca_' . $style . '_back_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_back_rect_count' ) : 'rgb(240, 240, 240)',
				),
			'_ywcacc_toggle_icon_style' => 'square_style',
			'_ywcacc_toggle_icon' => 'arrow_icon',
			'_ywcacc_toggle_colors' => array(
				'icon_color' => 'rgb(118, 118, 118)',
				'border_color' => 'rgb(239, 239, 239)',
				'background_color' => 'rgb(255, 255, 255)',
				'icon_hover_color' => 'rgb(118, 118, 118)',
				'border_hover_color' => 'rgb(239, 239, 239)',
				'background_hover_color' => 'rgb(255, 255, 255)',
				),
            '_ywcacc_toggle_icon_position' => 'left',

			// Rest of title options !
			'_ywcacc_color_title' => $title_tipography['color'],
			'_ywcacc_font_weight' => $title_tipography['style'],
			'_ywcacc_font_size' => array(
				'font_size' => $title_tipography['size'],
				'type_font_size' => $title_tipography['unit'],
				),
			'_ywcacc_alignment' => 'left',
			'_ywcacc_text_transform' => $title_tipography['transform'],
			'_ywcacc_border_style' => 'thick_line',
			'_ywcacc_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) : 'rgb(255, 255, 255)',

			// Parent Category Options !
			'_ywcacc_parent_color' => array(
				'parent_text_color' => $title_parent_tipography['color'],
				'parent_hover_color' => $title_parent_tipography['color'],
				),
			'_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
			'_ywcacc_parent_font_size' => array(
				'font_size' =>$title_parent_tipography['size'],
				'type_font_size' => $title_parent_tipography['unit'],
				),
			'_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
			'_ywcacc_parent_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) : 'rgb(226, 226, 226)',
			'_ywcacc_parent_bg_color' => array(
				'parent_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) : 'rgb(255, 255, 255)',
				'parent_hover_color' => 'rgb(255, 255, 255)',
				),

			// Child Category Options !
			'_ywcacc_child_color' => array(
				'child_text_color' => $title_child_tipography['color'],
				'child_hover_color' => $title_child_tipography['color'],
				),
			'_ywcacc_child_font_weight' => $title_child_tipography['style'],
			'_ywcacc_child_font_size' => array(
				'font_size' =>  $title_child_tipography['size'],
				'type_font_size' => $title_child_tipography['unit'],
				),
			'_ywcacc_child_text_transform' => $title_child_tipography['transform'],
			'_ywcacc_child_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) : 'rgb(226, 226, 226)',
			'_ywcacc_child_bg_color' => array(
				'child_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) : 'rgb(255, 255, 255)',
				'child_hover_color' => 'rgb(255, 255, 255)',
				),
			),
		);
}

/**
 * Get the Style 4 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_4($style, $postid){

    // Get count style !
    $old_count_style = ! empty( get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) ) ? get_option( 'ywcca_' . strtolower( substr( $style, 0, 5 ) ) . '_' . substr( $style, - 1 ) . '_count' ) : '';
    switch ( $old_count_style ) {
        case 'rect':
            $new_count_style = 'square_style';
            break;
        case 'round':
            $new_count_style = 'circle_style';
            break;
        case 'default':
            $new_count_style = 'simple_style';
            break;
        default:
            $new_count_style = 'simple_style';
    }

    $title_tipography        = ! empty ( get_option( 'ywcca_' . $style . '_title_typography' ) ) ? get_option( 'ywcca_' . $style . '_title_typography' ) : array(
        'color'     => 'rgb(72, 72, 72)',
        'style'     => 'bold',
        'size'      => 14,
        'unit'      => 'px',
        'transform' => 'uppercase'
    );
    $title_parent_tipography = ! empty ( get_option( 'ywcca_' . $style . '_parent_typography' ) ) ? get_option( 'ywcca_' . $style . '_parent_typography' ) : array(
        'color'     => 'rgb(144, 144, 144)',
        'style'     => 'regular',
        'size'      => 13,
        'unit'      => 'px',
        'transform' => 'uppercase'
    );
    $title_child_tipography  = ! empty ( get_option( 'ywcca_' . $style . '_child_typography' ) ) ? get_option( 'ywcca_' . $style . '_child_typography' ) : array(
        'color'     => 'rgb(144, 144, 144)',
        'style'     => 'regular',
        'size'      => 12,
        'unit'      => 'px',
        'transform' => 'uppercase'
    );

    return array(

        'style_4' => array(
            '_ywcacc_count_style' => $new_count_style,
            // General setings !
            '_ywcacc_container_border' =>  ! empty( get_option( 'ywcca_' . $style . '_general_bg' ) ) ? get_option( 'ywcca_' . $style . '_general_bg' ) : 'rgb(224, 224, 224)',
            '_ywcacc_container_bg' => ! empty( get_option( 'ywcca_' . $style . '_title_bg' ) ) ? get_option( 'ywcca_' . $style . '_title_bg' ) : 'rgb(255, 255, 255)',
            '_ywcacc_border_radius' => array(
                'dimensions' => array(
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ),
                'linked' => 'yes',
                'unit' => 'px',
            ),
            '_ywcacc_count_colors' => array(
                'text_color' => ' ',
                'border_color' => ! empty( get_option( 'ywcca_' . $style . '_border_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_border_rect_count' ) : ' ',
                'background_color' => ! empty( get_option( 'ywcca_' . $style . '_back_rect_count' ) ) ? get_option( 'ywcca_' . $style . '_back_rect_count' ) : ' ',
            ),
            '_ywcacc_toggle_icon_style' => 'circle_style',
            '_ywcacc_toggle_icon' => 'plus_icon',
            '_ywcacc_toggle_colors' => array(
                'icon_color' => 'rgb(134, 134, 134)',
                'border_color' => 'rgb(234, 234, 234)',
                'background_color' => 'rgb(255, 255, 255)',
                'icon_hover_color' => 'rgb(134, 134, 134)',
                'border_hover_color' => 'rgb(234, 234, 234)',
                'background_hover_color' => 'rgb(255, 255, 255)',
            ),
            '_ywcacc_toggle_icon_position' => 'left',

            // Rest of title options !
            '_ywcacc_color_title' => $title_tipography['color'],
            '_ywcacc_font_weight' => $title_tipography['style'],
            '_ywcacc_font_size' => array(
                'font_size' => $title_tipography['size'],
                'type_font_size' => $title_tipography['unit'],
            ),
            '_ywcacc_alignment' => 'left',
            '_ywcacc_text_transform' => $title_tipography['transform'],
            '_ywcacc_border_style' => 'single_line',
            '_ywcacc_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_title_border' ) : 'rgb(224, 224, 224)',

            // Parent Category Options !
            '_ywcacc_parent_color' => array(
                'parent_text_color' => $title_parent_tipography['color'],
                'parent_hover_color' => $title_parent_tipography['color'],
            ),
            '_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
            '_ywcacc_parent_font_size' => array(
                'font_size' =>$title_parent_tipography['size'],
                'type_font_size' => $title_parent_tipography['unit'],
            ),
            '_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
            '_ywcacc_parent_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_border' ) : 'rgb(226, 226, 226)',
            '_ywcacc_parent_bg_color' => array(
                'parent_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_parent_bg' ) : 'rgb(255, 255, 255)',
                'parent_hover_color' => 'rgb(255, 255, 255)',
            ),

            // Child Category Options !
            '_ywcacc_child_color' => array(
                'child_text_color' => $title_child_tipography['color'],
                'child_hover_color' => $title_child_tipography['color'],
            ),
            '_ywcacc_child_font_weight' => $title_child_tipography['style'],
            '_ywcacc_child_font_size' => array(
                'font_size' =>  $title_child_tipography['size'],
                'type_font_size' => $title_child_tipography['unit'],
            ),
            '_ywcacc_child_text_transform' => $title_child_tipography['transform'],
            '_ywcacc_child_border_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_border' ) : 'rgb(245, 245, 245)',
            '_ywcacc_child_bg_color' => array(
                'child_default_color' => ! empty( get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) ) ? get_option( 'ywcca_' . str_replace( ' ', '', $style ) . '_child_bg' ) : 'rgb(245, 245, 245)',
                'child_hover_color' => 'rgb(245, 245, 245)',
            ),
        ),
    );
}

// News Styles !
/**
 * Get the Style 5 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_5($style, $postid){

    $title_tipography        = array(
        'color'     => 'rgb(0, 175, 167)',
        'style'     => 'bold',
        'size'      => 18,
        'unit'      => 'px',
        'transform' => 'none'
    );
    $title_parent_tipography = array(
        'color'     => 'rgb(9, 9, 9)',
        'style'     => 'bold',
        'size'      => 17,
        'unit'      => 'px',
        'transform' => 'none'
    );
    $title_child_tipography  = array(
        'color'     => 'rgb(144, 144, 144)',
        'style'     => 'bold',
        'size'      => 15,
        'unit'      => 'px',
        'transform' => 'none'
    );

    return array(

        'style_5' => array(
            '_ywcacc_count_style' => 'simple_style',
            // General setings !
            '_ywcacc_container_border' => 'rgb(196, 196, 196)',
            '_ywcacc_container_bg' => 'rgb(255, 255, 255)',
            '_ywcacc_border_radius' => array(
                'dimensions' => array(
                    'top' => 25,
                    'right' => 25,
                    'bottom' => 25,
                    'left' => 25,
                ),
                'linked' => 'yes',
                'unit' => 'px',
            ),
            '_ywcacc_count_colors' => array(
                'text_color' => 'rgb(0, 0, 0)',
                'border_color' => 'rgb(0, 0, 0)',
                'background_color' => 'rgb(255, 255, 255)',
            ),
            '_ywcacc_toggle_icon_style' => 'circle_style',
            '_ywcacc_toggle_icon' => 'plus_icon',
            '_ywcacc_toggle_colors' => array(
                'icon_color' => 'rgb(0, 175, 167)',
                'border_color' => 'rgb(0, 175, 167)',
                'background_color' => 'rgb(255, 255, 255)',
                'icon_hover_color' => 'rgb(0, 175, 167)',
                'border_hover_color' => 'rgb(0, 175, 167)',
                'background_hover_color' => 'rgb(255, 255, 255)',
            ),
            '_ywcacc_toggle_icon_position' => 'left',

            // Rest of title options !
            '_ywcacc_color_title' => $title_tipography['color'],
            '_ywcacc_font_weight' => $title_tipography['style'],
            '_ywcacc_font_size' => array(
                'font_size' => $title_tipography['size'],
                'type_font_size' => $title_tipography['unit'],
            ),
            '_ywcacc_alignment' => 'left',
            '_ywcacc_text_transform' => $title_tipography['transform'],
            '_ywcacc_border_style' => 'double_lines',
            '_ywcacc_border_color' => 'rgb(0, 175, 167)',

            // Parent Category Options !
            '_ywcacc_parent_color' => array(
                'parent_text_color' => $title_parent_tipography['color'],
                'parent_hover_color' => $title_parent_tipography['color'],
            ),
            '_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
            '_ywcacc_parent_font_size' => array(
                'font_size' =>$title_parent_tipography['size'],
                'type_font_size' => $title_parent_tipography['unit'],
            ),
            '_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
            '_ywcacc_parent_border_color' => 'rgb(245, 245, 245)',
            '_ywcacc_parent_bg_color' => array(
                'parent_default_color' => 'rgb(255, 255, 255)',
                'parent_hover_color' => 'rgb(255, 255, 255)',
            ),

            // Child Category Options !
            '_ywcacc_child_color' => array(
                'child_text_color' => $title_child_tipography['color'],
                'child_hover_color' => $title_child_tipography['color'],
            ),
            '_ywcacc_child_font_weight' => $title_child_tipography['style'],
            '_ywcacc_child_font_size' => array(
                'font_size' =>  $title_child_tipography['size'],
                'type_font_size' => $title_child_tipography['unit'],
            ),
            '_ywcacc_child_text_transform' => $title_child_tipography['transform'],
            '_ywcacc_child_border_color' => 'rgb(245, 245, 245)',
            '_ywcacc_child_bg_color' => array(
                'child_default_color' => 'rgb(245, 245, 245)',
                'child_hover_color' => 'rgb(245, 245, 245)',
            ),
        ),
    );
}

/**
 * Get the Style 6 options
 *
 * @param $style
 * @param $postid
 * @return array[]
 */
function ywcca_get_style_6($style, $postid){

    $title_tipography        = array(
        'color'     => 'rgb(0, 175, 167)',
        'style'     => 'bold',
        'size'      => 18,
        'unit'      => 'px',
        'transform' => 'capitalize'
    );
    $title_parent_tipography = array(
        'color'     => 'rgb(17, 17, 17)',
        'style'     => 'bold',
        'size'      => 18,
        'unit'      => 'px',
        'transform' => 'none'
    );
    $title_child_tipography  = array(
        'color'     => 'rgb(17, 17, 17)',
        'style'     => 'bold',
        'size'      => 13,
        'unit'      => 'px',
        'transform' => 'capitalize'
    );

   return array(
        'style_6' => array(
            '_ywcacc_count_style' => 'simple_style',
            // General setings !
            '_ywcacc_container_border' => 'rgb(245, 245, 245)',
            '_ywcacc_container_bg' => 'rgb(245, 245, 245)',
            '_ywcacc_border_radius' => array(
                'dimensions' => array(
                    'top' => 50,
                    'right' => 0,
                    'bottom' => 50,
                    'left' => 0,
                ),
                'linked' => 'yes',
                'unit' => 'px',
            ),
            '_ywcacc_count_colors' => array(
                'text_color' => 'rgb(0, 0, 0)',
                'border_color' => 'rgb(0, 0, 0)',
                'background_color' => 'rgb(245, 245, 245)',
            ),
            '_ywcacc_toggle_icon_style' => 'simple_style',
            '_ywcacc_toggle_icon' => 'plus_icon',
            '_ywcacc_toggle_colors' => array(
                'icon_color' => 'rgb(68, 138, 133)',
                'border_color' => 'rgb(245, 245, 245)',
                'background_color' => 'rgb(245, 245, 245)',
                'icon_hover_color' => 'rgb(68, 138, 133)',
                'border_hover_color' => 'rgb(245, 245, 245)',
                'background_hover_color' => 'rgb(245, 245, 245)',
            ),
            '_ywcacc_toggle_icon_position' => 'left',

            // Rest of title options !
            '_ywcacc_color_title' => $title_tipography['color'],
            '_ywcacc_font_weight' => $title_tipography['style'],
            '_ywcacc_font_size' => array(
                'font_size' => $title_tipography['size'],
                'type_font_size' => $title_tipography['unit'],
            ),
            '_ywcacc_alignment' => 'center',
            '_ywcacc_text_transform' => $title_tipography['transform'],
            '_ywcacc_border_style' => 'no_border',
            '_ywcacc_border_color' => 'rgb(245, 245, 245)',

            // Parent Category Options !
            '_ywcacc_parent_color' => array(
                'parent_text_color' => $title_parent_tipography['color'],
                'parent_hover_color' => $title_parent_tipography['color'],
            ),
            '_ywcacc_parent_font_weight' => $title_parent_tipography['style'],
            '_ywcacc_parent_font_size' => array(
                'font_size' =>$title_parent_tipography['size'],
                'type_font_size' => $title_parent_tipography['unit'],
            ),
            '_ywcacc_parent_text_transform' => $title_parent_tipography['transform'],
            '_ywcacc_parent_border_color' => 'rgb(245, 245, 245)',
            '_ywcacc_parent_bg_color' => array(
                'parent_default_color' => 'rgb(245, 245, 245)',
                'parent_hover_color' => 'rgb(245, 245, 245)',
            ),

            // Child Category Options !
            '_ywcacc_child_color' => array(
                'child_text_color' => $title_child_tipography['color'],
                'child_hover_color' => $title_child_tipography['color'],
            ),
            '_ywcacc_child_font_weight' => $title_child_tipography['style'],
            '_ywcacc_child_font_size' => array(
                'font_size' =>  $title_child_tipography['size'],
                'type_font_size' => $title_child_tipography['unit'],
            ),
            '_ywcacc_child_text_transform' => $title_child_tipography['transform'],
            '_ywcacc_child_border_color' => 'rgb(245, 245, 245)',
            '_ywcacc_child_bg_color' => array(
                'child_default_color' => 'rgb(245, 245, 245)',
                'child_hover_color' => 'rgb(245, 245, 245)',
            ),
        ),
    );
}
