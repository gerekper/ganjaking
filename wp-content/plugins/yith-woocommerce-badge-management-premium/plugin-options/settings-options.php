<?php
/**
 * Settings Tab
 *
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

$settings = array(

	'settings' => array(

		'general-options'                         => array(
			'title' => __( 'General Options', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcbm-general-options',
		),
		'hide-on-sale-default-badge'              => array(
			'id'      => 'yith-wcbm-hide-on-sale-default',
			'name'    => __( 'Hide "On sale" badge', 'yith-woocommerce-badges-management' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Select to hide the default Woocommerce "On sale" badge.', 'yith-woocommerce-badges-management' ),
			'default' => 'no',
		),
		'hide-in-sidebar'                         => array(
			'id'      => 'yith-wcbm-hide-in-sidebar',
			'name'    => __( 'Hide in sidebars', 'yith-woocommerce-badges-management' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Select to hide the badges in sidebars and widgets.', 'yith-woocommerce-badges-management' ),
			'default' => 'yes',
		),
		'product-badge-overrides-default-on-sale' => array(
			'id'      => 'yith-wcbm-product-badge-overrides-default-on-sale',
			'name'    => __( 'Product Badge overrides default on sale badge', 'yith-woocommerce-badges-management' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Select if you want to hide WooCommerce default "On Sale" badge when the product has another badge', 'yith-woocommerce-badges-management' ),
			'default' => 'yes',
		),
		'general-options-end'                     => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcbm-general-options',
		),
	),
);

return apply_filters( 'yith_wcbm_panel_settings_options', $settings );
