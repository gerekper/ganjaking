<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'wp_enqueue_scripts', 'porto_action_head', 8 );
if ( ! function_exists( 'porto_action_head' ) ) :
	function porto_action_head() {
		global $porto_layout, $porto_sidebar;
		$porto_layout_arr = porto_meta_layout();
		$porto_layout     = $porto_layout_arr[0];
		$porto_sidebar    = $porto_layout_arr[1];
		if ( in_array( $porto_layout, porto_options_both_sidebars() ) ) {
			$GLOBALS['porto_sidebar2'] = $porto_layout_arr[2];
		}
	}
endif;

add_filter( 'body_class', 'porto_action_body_class' );
if ( ! function_exists( 'porto_action_body_class' ) ) :
	function porto_action_body_class( $classes ) {
		global $porto_settings;
		$body_class  = '';
		$wrapper     = porto_get_wrapper_type();
		$body_class  = $wrapper;
		$body_class .= ' blog-' . get_current_blog_id();
		if ( $porto_settings['css-type'] ) {
			$body_class .= ' ' . $porto_settings['css-type'];
		}

		$header_is_side = porto_header_type_is_side();
		if ( $header_is_side ) {
			$body_class .= ' body-side';
		}

		$loading_overlay = porto_get_meta_value( 'loading_overlay' );
		if ( 'no' !== $loading_overlay && ( 'yes' === $loading_overlay || ( 'yes' !== $loading_overlay && $porto_settings['show-loading-overlay'] ) ) ) {
			$body_class .= ' loading-overlay-showing';
		}
		$classes[] = esc_attr( $body_class );
		return $classes;
	}
endif;

add_action( 'porto_before_wrapper', 'porto_action_add_loading_overlay', 8 );
if ( ! function_exists( 'porto_action_add_loading_overlay' ) ) :
	function porto_action_add_loading_overlay() {
		global $porto_settings;
		$loading_overlay = porto_get_meta_value( 'loading_overlay' );
		if ( 'no' !== $loading_overlay && ( 'yes' === $loading_overlay || ( 'yes' !== $loading_overlay && $porto_settings['show-loading-overlay'] ) ) ) {
			echo '<div class="loading-overlay"><div class="bounce-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
		}
	}
endif;

// wp_body_open function introduced in WP 5.2.
if ( function_exists( 'wp_body_open' ) ) :
	add_action( 'porto_before_wrapper', 'wp_body_open', 5 );
endif;
