<?php
/**
 * Premium plugin options
 *
 * @package YITH\BadgeManagementPremium\PluginOptions
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$custom_attributes = defined( 'YITH_WCBM_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );

$advanced_options = array(

	'settings' => array(

		'general-options'                          => array(
			'title' => __( 'General Options', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcbm-general-options',
		),
		'hide-on-sale-default-badge'               => array(
			'name'      => __( 'Hide WooCommerce "On sale" badge', 'yith-woocommerce-badges-management' ),
			'id'        => 'yith-wcbm-hide-on-sale-default',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Select to hide the default Woocommerce "On sale" badge.', 'yith-woocommerce-badges-management' ),
			'default'   => 'no',
		),
		'when-hide-on-sale'                        => array(
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
		'hide-in-sidebar'                          => array(
			'id'        => 'yith-wcbm-hide-in-sidebar',
			'name'      => __( 'Hide badges in sidebars', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Select to hide the badges in sidebars and widgets.', 'yith-woocommerce-badges-management' ),
			'default'   => 'yes',
		),
		'hide-badges-in-single-product-pages'      => array(
			'id'        => 'yith-wcbm-hide-on-single-product',
			'name'      => __( 'Hide badges in single product pages', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to hide the badges in the product detail pages.', 'yith-woocommerce-badges-management' ),
			'default'   => 'yes',
		),
		'mobile-breakpoint'                        => array(
			'id'        => 'yith-wcbm-mobile-breakpoint',
			'name'      => __( 'Mobile Breakpoint (px)', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => __( 'Set the mobile breakpoint. Default: 768px', 'yith-woocommerce-badges-management' ),
			'default'   => 768,
			'min'       => 0,
		),
		'hide-badges-in-mobile'                    => array(
			'id'        => 'yith-wcbm-hide-on-mobile',
			'name'      => __( 'Hide badges on mobile', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to hide all badges on mobile devices, based on the mobile breakpoint setting.', 'yith-woocommerce-badges-management' ),
			'default'   => 'no',
		),
		'general-options-end'                      => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcbm-general-options',
		),
		'extra-options'                            => array(
			'title' => __( 'Extra', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'enable-shop-manager'                      => array(
			'id'        => 'yith-wcbm-enable-shop-manager',
			'name'      => __( 'Enable Shop Manager to edit these options', 'yith-woocommerce-badges-management' ),
			'desc'      => __( 'Enable to allow your shop managers to manage the options of this plugin.', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),
		'enable-force-badge-positioning'           => array(
			'id'        => 'yith-wcbm-enable-force-badge-positioning',
			'name'      => __( 'Force Badge Positioning', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to force the badge positioning through JS to fix theme issues.', 'yith-woocommerce-badges-management' ),
			'default'   => 'no',
		),
		'force-badge-positioning'                  => array(
			'id'        => 'yith-wcbm-force-badge-positioning',
			'name'      => __( 'Force Positioning on', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => __( 'Choose where do you want to force the positioning.', 'yith-woocommerce-badges-management' ),
			'default'   => 'single-product',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'single-product'       => __( 'Single Product', 'yith-woocommerce-badges-management' ),
				'single-product-image' => __( 'Single Product Image', 'yith-woocommerce-badges-management' ),
				'shop'                 => __( 'Shop Page', 'yith-woocommerce-badges-management' ),
				'everywhere'           => __( 'Everywhere', 'yith-woocommerce-badges-management' ),
			),
			'deps'      => array(
				'id'    => 'yith-wcbm-enable-force-badge-positioning',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'force-badge-positioning-timeout'          => array(
			'id'        => 'yith-wcbm-force-badge-positioning-timeout',
			'name'      => __( 'Force Positioning Timeout', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => __( 'Number of milliseconds before force positioning.', 'yith-woocommerce-badges-management' ),
			'default'   => 500,
			'deps'      => array(
				'id'    => 'yith-wcbm-enable-force-badge-positioning',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'force-badge-positioning-on-scroll-mobile' => array(
			'id'        => 'yith-wcbm-force-badge-positioning-on-scroll-mobile',
			'name'      => __( 'Force Positioning On Scrolling on Mobile', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'If enabled, force positioning on scrolling on mobile devices.', 'yith-woocommerce-badges-management' ),
			'default'   => 'yes',
			'deps'      => array(
				'id'    => 'yith-wcbm-enable-force-badge-positioning',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'show-advanced-badge-in-variable-products' => array(
			'class'             => 'wc-enhanced-select',
			'name'              => __( 'Show advanced badges in variable products', 'yith-woocommerce-badges-management' ),
			'type'              => 'select',
			'id'                => 'yith-wcbm-show-advanced-badge-in-variable-products',
			'custom_attributes' => $custom_attributes,
			'default'           => 'same',
			'options'           => array(
				'same' => __( 'Only if the discount % and amount is the same for all variations', 'yith-woocommerce-badges-management' ),
				'min'  => __( 'Show the lowest discount % and amount', 'yith-woocommerce-badges-management' ),
				'max'  => __( 'Show the highest discount % and amount', 'yith-woocommerce-badges-management' ),
			),

		),
		'extra-options-end'                        => array(
			'type' => 'sectionend',
		),
	),
);

if ( ! current_user_can( 'manage_options' ) ) {
	unset( $advanced_options['settings']['enable-shop-manager'] );
}

return $advanced_options;
