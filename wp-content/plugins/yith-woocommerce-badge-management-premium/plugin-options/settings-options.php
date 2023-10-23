<?php
/**
 * Settings Tab
 *
 * @package YITH\BadgeManagement\PluginOptions
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

$settings = array(
	'settings' => array(
		'general-options'            => array(
			'title' => __( 'General Options', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcbm-general-options',
		),
		'hide-on-sale-default-badge' => array(
			'name'      => __( 'Hide WooCommerce "On sale" badge', 'yith-woocommerce-badges-management' ),
			'id'        => 'yith-wcbm-hide-on-sale-default',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Select to hide the default Woocommerce "On sale" badge.', 'yith-woocommerce-badges-management' ),
			'default'   => 'no',
		),
		'when-hide-on-sale'          => array(
			'id'        => 'yith-wcbm-when-hide-on-sale',
			'name'      => __( 'Hide "On sale" on:', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'all-products'        => __( 'All products', 'yith-woocommerce-badges-management' ),
				'products-with-badge' => __( 'Products where a custom badge is applied only', 'yith-woocommerce-badges-management' ),
			),
			'desc'      => __( 'Choose whether to always hide the default WooCommerce "On sale" badge or only in products where a custom badge is applied.', 'yith-woocommerce-badges-management' ),
			'default'   => 'all-products',
			'deps'      => array(
				'id'    => 'yith-wcbm-hide-on-sale-default',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'hide-in-sidebar'            => array(
			'id'        => 'yith-wcbm-hide-in-sidebar',
			'name'      => __( 'Hide badges in sidebars', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Select to hide the badges in sidebars and widgets.', 'yith-woocommerce-badges-management' ),
			'default'   => 'yes',
		),
		'general-options-end'        => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcbm-general-options',
		),
		'extra-options'              => array(
			'title' => __( 'Extra', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'enable-shop-manager'        => array(
			'id'        => 'yith-wcbm-enable-shop-manager',
			'name'      => __( 'Enable Shop Manager to edit these options', 'yith-woocommerce-badges-management' ),
			'desc'      => __( 'Enable to allow your shop managers to manage the options of this plugin.', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),
		'extra-options-end'          => array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'yith_wcbm_panel_settings_options', $settings );
