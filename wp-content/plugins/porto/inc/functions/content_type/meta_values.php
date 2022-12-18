<?php
/**
 * Functions for ReduxFramework used for fallback
 */

if ( ! function_exists( 'porto_ct_banner_pos' ) ) :
	function porto_ct_banner_pos() {

		return array(
			''              => __( 'Default', 'porto' ),
			'before_header' => __( 'Before Header', 'porto' ),
			'below_header'  => __( 'Behind Header', 'porto' ),
			'fixed'         => __( 'Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_banner_type' ) ) :
	function porto_ct_banner_type() {

		return array(
			'rev_slider'    => __( 'Revolution Slider', 'porto' ),
			'master_slider' => __( 'Master Slider', 'porto' ),
			'banner_block'  => __( 'Banner Block', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_header_view' ) ) :
	function porto_ct_header_view() {

		return array(
			'default' => __( 'Default', 'porto' ),
			'fixed'   => __( 'Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_footer_view' ) ) :
	function porto_ct_footer_view() {
		return array(
			''       => __( 'Default', 'porto' ),
			'simple' => __( 'Simple', 'porto' ),
			'fixed'  => __( 'Simple and Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_master_sliders' ) ) :
	global $porto_master_sliders, $porto_check_master_sliders;
	$porto_master_sliders = null;

	$porto_check_master_sliders = false;

	function porto_ct_master_sliders() {

		global $wpdb, $porto_master_sliders, $porto_check_master_sliders;
		if ( $porto_master_sliders ) {
			return $porto_master_sliders;
		}
		if ( ! class_exists( 'Master_Slider' ) ) {
			return array();
		}
		if ( ! $porto_check_master_sliders ) {

			$table_name = $wpdb->prefix . 'masterslider_sliders';
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name ) {
				$sliders        = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . esc_sql( $table_name ) . ' WHERE status=%s ORDER BY ID DESC', 'published' ) );
				$master_sliders = array();

				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $slider ) {
						$master_sliders[ $slider->ID ] = '#' . $slider->ID . ': ' . $slider->title;
					}
				}
				$porto_master_sliders = $master_sliders;
			}
			$porto_check_master_sliders = true;
		}
		return $porto_master_sliders;
	}
endif;

if ( ! function_exists( 'porto_ct_rev_sliders' ) ) :
	global $porto_rev_sliders, $porto_check_rev_sliders;
	$porto_rev_sliders = null;

	$porto_check_rev_sliders = false;
	function porto_ct_rev_sliders() {

		global $wpdb, $porto_rev_sliders, $porto_check_rev_sliders;
		if ( $porto_rev_sliders ) {
			return $porto_rev_sliders;
		}
		if ( ! class_exists( 'RevSliderFront' ) ) {
			return array();
		}
		if ( ! $porto_check_rev_sliders ) {
			$table_name = $wpdb->prefix . 'revslider_sliders';

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name ) {
				$sliders     = $wpdb->get_results( 'SELECT * FROM ' . esc_sql( $table_name ) );
				$rev_sliders = array();
				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $slider ) {
						$rev_sliders[ $slider->alias ] = '#' . $slider->id . ': ' . $slider->title;
					}
				}
				$porto_rev_sliders = $rev_sliders;
			}
			$porto_check_rev_sliders = true;
		}
		return $porto_rev_sliders;
	}
endif;

if ( ! function_exists( 'porto_ct_related_product_columns' ) ) :
	function porto_ct_related_product_columns() {

		return array(
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		);
	}
endif;

if ( ! function_exists( 'porto_ct_category_view_mode' ) ) :
	function porto_ct_category_view_mode() {

		return array(

			''     => __( 'Default', 'porto' ),
			'grid' => __( 'Grid', 'porto' ),
			'list' => __( 'List', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_orderby' ) ) :
	function porto_ct_categories_orderby() {

		return array(

			'id'    => __( 'ID', 'porto' ),
			'name'  => __( 'Name', 'porto' ),
			'slug'  => __( 'Slug', 'porto' ),
			'count' => __( 'Count', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_order' ) ) :
	function porto_ct_categories_order() {

		return array(
			'asc'  => __( 'Asc', 'porto' ),
			'desc' => __( 'Desc', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_sort_pos' ) ) :
	function porto_ct_categories_sort_pos() {

		return array(
			'content'     => __( 'In Content', 'porto' ),
			'breadcrumbs' => __( 'In Breadcrumbs', 'porto' ),
			'sidebar'     => __( 'In Sidebar', 'porto' ),
			'hide'        => __( 'Hide', 'porto' ),
		);
	}
endif;
