<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $porto_settings;

// Lazy load
require PORTO_LIB . '/lib/lazy-load/lazy-load.php';

// Infinite Scroll
require PORTO_LIB . '/lib/infinite-scroll/infinite-scroll.php';

// Image Swatch
if ( class_exists( 'Woocommerce' ) ) {
	require PORTO_LIB . '/lib/woocommerce-swatches/woocommerce-swatches.php';
}

// Live Search
if ( isset( $porto_settings['search-live'] ) && $porto_settings['search-live'] ) {
	require PORTO_LIB . '/lib/live-search/live-search.php';
}

// Porto Studio
if ( ( ( class_exists( 'Vc_Manager' ) && ( ( is_admin() && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] || porto_is_ajax() ) ) || ( isset( $_REQUEST['vc_editable'] ) && $_REQUEST['vc_editable'] ) ) ) ||
	( defined( 'ELEMENTOR_VERSION' ) && ( porto_is_elementor_preview() || wp_doing_ajax() ) ) ) &&
	( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) ) {
	require_once PORTO_LIB . '/lib/porto-studio/porto-studio.php';
}

// Pre Order
if ( class_exists( 'Woocommerce' ) && isset( $porto_settings['woo-pre-order'] ) && $porto_settings['woo-pre-order'] ) {
	require PORTO_LIB . '/lib/woocommerce-pre-order/init.php';
}
