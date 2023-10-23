<?php
/**
 * General array options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

$general = array(
	'settings-general' => array(
		array(
			'title' => __( 'General Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-general-options',
		),
		array(
			'title'     => __( 'Enable AJAX navigation', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Enable AJAX navigation between the endpoints.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_wcmap_enable_ajax_navigation',
			'default'   => 'no',
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
			'title' => __( 'User Avatar', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-general-options',
		),
		array(
			'title'     => __( 'Default user avatar', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Choose whether to use a default avatar icon or upload a custom one for your users.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'default' => __( 'Default user avatar', 'yith-woocommerce-customize-myaccount-page' ),
				'custom'  => __( 'Upload a custom user avatar', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'id'        => 'yith_wcmap_avatar[default]',
			'default'   => 'default',
		),
		array(
			'title'     => __( 'Upload default avatar', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Upload a custom avatar for you users. A square image with a width of 200px works best.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'media',
			'id'        => 'yith_wcmap_avatar[custom_default]',
			'default'   => '',
			'deps'      => array(
				'id'        => 'yith_wcmap_avatar\\[default\\]',
				'target-id' => 'yith_wcmap_avatar\\[custom_default\\]',
				'value'     => 'custom',
				'type'      => 'hide',
			),
		),
		array(
			'title'     => __( 'Avatar size', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Set the avatar size in px.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => 120,
			'step'      => 1,
			'min'       => 1,
			'id'        => 'yith_wcmap_avatar[avatar_size]',
		),
		array(
			'title'     => __( 'Avatar border radius', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Set the avatar border radius. A border radius of 0 means a square avatar, a border radius of 10 is a circle avatar.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'option'    => array(
				'min' => 0,
				'max' => 10,
			),
			'default'   => 0,
			'step'      => 1,
			'id'        => 'yith_wcmap_avatar[border_radius]',
			'class'     => 'yith_wcmap_avatar_border_radius',
		),
		array(
			'title'     => __( 'Allow users to upload avatar', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'If enabled users can upload a custom image to replace the default avatar.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_wcmap_avatar[custom]',
			'default'   => 'yes',
		),
		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-general-options',
		),
	),
);

/**
 * APPLY_FILTERS: yith_wcmap_panel_general_options
 *
 * Filters the options available in the General Settings tab.
 *
 * @param array $general_options Array with options.
 *
 * @return array
 */
return apply_filters( 'yith_wcmap_panel_general_options', $general );
