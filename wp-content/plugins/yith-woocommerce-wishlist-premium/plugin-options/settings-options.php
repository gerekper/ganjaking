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
	'settings-general'         => array(
		'title'              => _x( 'General', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'Configure the plugin general settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
	'settings-add_to_wishlist' => array(
		'title'              => _x( 'Add to wishlist', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'Configure the Add to wishlist settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
	'settings-wishlist_page'   => array(
		'title'              => _x( 'Wishlist page', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'Configure the wishlist page settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
	'settings-ask_an_estimate' => array(
		'title'              => _x( 'Ask an estimate', 'Tab title in plugin settings panel', 'yith-woocommerce-wishlist' ),
		'yith-wcwl-priority' => 20,
		'description'        => _x( 'Configure the Ask an estimate settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-wishlist' ),
	),
);

$options = array(
	'settings' => array(
		'settings-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => $sub_tabs,
		),
	),
);

return apply_filters( 'yith_wcwl_panel_settings_options', $options );
