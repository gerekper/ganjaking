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
	require_once PORTO_LIB . '/lib/live-search/live-search.php';
}

// Porto Studio
if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
	if ( ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 0 === strpos( $_REQUEST['action'], 'porto_studio' ) ) ||
		( defined( 'ELEMENTOR_VERSION' ) && porto_is_elementor_preview() ) ||
		( defined( 'VCV_VERSION' ) && porto_is_vc_preview() ) ) {
		require_once PORTO_LIB . '/lib/porto-studio/porto-studio.php';
	} elseif ( ( defined( 'WPB_VC_VERSION' ) && ( is_admin() && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] || porto_is_ajax() ) ) ) ||
		( is_admin() && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) ) ) {
		add_action(
			'admin_enqueue_scripts',
			function() {
				require_once PORTO_LIB . '/lib/porto-studio/porto-studio.php';
			},
			5
		);
	} elseif ( defined( 'WPB_VC_VERSION' ) && ! empty( $_REQUEST['vc_editable'] ) ) { // WPB Frontend editor
		require_once PORTO_LIB . '/lib/porto-studio/porto-studio.php';
	}
}

// Pre Order
if ( class_exists( 'Woocommerce' ) && isset( $porto_settings['woo-pre-order'] ) && $porto_settings['woo-pre-order'] ) {
	require PORTO_LIB . '/lib/woocommerce-pre-order/init.php';
}

// Sales Poopup
if ( class_exists( 'Woocommerce' ) && isset( $porto_settings['woo-sales-popup'] ) && $porto_settings['woo-sales-popup'] &&
	( ! wp_is_mobile() || ( isset( $porto_settings['woo-sales-popup-mobile'] ) && $porto_settings['woo-sales-popup-mobile'] ) ) ) {
	require PORTO_LIB . '/lib/woocommerce-sales-popup/init.php';
}

// add-ons only in front-end
if ( class_exists( 'Woocommerce' ) ) {
	add_action(
		'template_redirect',
		function() {
			if ( is_product() ) {
				// Video thumbnail
				require_once PORTO_LIB . '/lib/video-thumbnail/init.php';
			}
		}
	);
}
