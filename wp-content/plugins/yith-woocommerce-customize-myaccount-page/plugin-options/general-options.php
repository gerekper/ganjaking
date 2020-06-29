<?php
/**
 * GENERAL ARRAY OPTIONS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$general = array(

	'general' => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-general-options',
		),

		array(
			'title'     => __( 'Custom Avatar', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Let users upload a custom avatar as their profile picture.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith-wcmap-custom-avatar',
			'default'   => 'yes',
		),

		array(
			'title'     => __( 'Menu style', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-menu-style',
			'default'   => 'sidebar',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => __( 'Choose the style for the "My Account" menu', 'yith-woocommerce-customize-myaccount-page' ),
			'options'   => array(
				'sidebar' => __( 'Sidebar', 'yith-woocommerce-customize-myaccount-page' ),
				'tab'     => __( 'Tab', 'yith-woocommerce-customize-myaccount-page' ),
			),
		),

		array(
			'title'     => __( 'Sidebar position', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Choose the position of the menu in "My Account" page (only for sidebar style)', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'left'  => __( 'Left', 'yith-woocommerce-customize-myaccount-page' ),
				'right' => __( 'Right', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'id'        => 'yith-wcmap-menu-position',
			'default'   => 'left',
		),

		array(
			'title'     => __( 'Default endpoint', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Choose the default endpoint for "My account" page', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => yith_wcmap_endpoints_list(),
			'id'        => 'yith-wcmap-default-endpoint',
			'default'   => 'dashboard',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-general-options',
		),

		array(
			'title' => __( 'Style Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-style-options',
		),

		array(
			'title'     => __( 'Menu item color', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-menu-item-color',
			'default'   => '#777777',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose a color for menu items.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'title'     => __( 'Menu item color on hover', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-menu-item-color-hover',
			'default'   => '#000000',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose colour of menu items on mouse hover.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'title'     => __( 'Logout color', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-logout-color',
			'default'   => '#ffffff',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose the color of the Logout text.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'title'     => __( 'Logout color on hover', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-logout-color-hover',
			'default'   => '#ffffff',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose the color of the Logout text on mouse hover.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'title'     => __( 'Logout background color', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-logout-background',
			'default'   => '#c0c0c0',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose the color of the Logout background.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'title'     => __( 'Logout background color on hover', 'yith-woocommerce-customize-myaccount-page' ),
			'id'        => 'yith-wcmap-logout-background-hover',
			'default'   => '#333333',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => __( 'Choose the color of the Logout background on mouse hover.', 'yith-woocommerce-customize-myaccount-page' ),
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-style-options',
		),
	),
);

return apply_filters( 'yith_wcmap_panel_general_options', $general );