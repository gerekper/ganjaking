<?php
/**
 * Settings options
 *
 * @package YITH\GiftCards\PluginOptions
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

$sub_tabs = array(
	'dashboard-lists'   => array(
		'title'              => _x( 'Wishlists', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'See all the wishlists created in your site.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
	'dashboard-popular' => array(
		'title'              => _x( 'Popular', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'Check the most popular products in your site.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
);

$options = array(
	'dashboard' => array(
		'dashboard-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => $sub_tabs,
		),
	),
);

return apply_filters( 'yith_wcwl_panel_dashboard_options', $options );
