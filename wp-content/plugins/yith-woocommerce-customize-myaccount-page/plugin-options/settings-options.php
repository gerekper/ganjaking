<?php
/**
 * Settings options
 *
 * @package YITH WooCommerce Customize My Account Page
 */

defined( 'ABSPATH' ) || exit();

$sub_tabs = array(
	'settings-general'  => array(
		'title'       => _x( 'General', 'Tab title in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
		'description' => _x( 'Configure the plugin general settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
	),
	'settings-style'    => array(
		'title'       => _x( 'Style', 'Tab title in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
		'description' => _x( 'Configure the plugin style settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
	),
	'settings-security' => array(
		'title'       => _x( 'Security', 'Tab title in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
		'description' => _x( 'Configure the plugin security settings.', 'Tab description in plugin settings panel', 'yith-woocommerce-customize-myaccount-page' ),
	),
);

$sub_tabs = apply_filters( 'yith_wcmap_panel_settings_sub_tabs', $sub_tabs );

$options = array(
	'settings' => array(
		'settings-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => $sub_tabs,
		),
	),
);

return apply_filters( 'yith_wcmap_panel_settings_options', $options );