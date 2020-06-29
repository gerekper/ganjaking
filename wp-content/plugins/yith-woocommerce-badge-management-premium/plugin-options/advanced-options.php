<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$custom_attributes = defined( 'YITH_WCBM_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );

$advanced_options = array(

	'settings' => array(

		'general-options' => array(
			'title' => __( 'General Options', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcbm-general-options',
		),

		'hide-on-sale-default-badge' => array(
			'id'      => 'yith-wcbm-hide-on-sale-default',
			'name'    => __( 'Hide "On sale" badge', 'yith-woocommerce-badges-management' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Select to hide the default Woocommerce "On sale" badge.', 'yith-woocommerce-badges-management' ),
			'default' => 'no',
		),

		'hide-in-sidebar' => array(
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

		'enable-shop-manager' => array(
			'id'        => 'yith-wcbm-enable-shop-manager',
			'name'      => __( 'Enable Shop Manager to edit these options', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'mobile-breakpoint' => array(
			'id'        => 'yith-wcbm-mobile-breakpoint',
			'name'      => __( 'Mobile Breakpoint', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => sprintf( __( 'Set the mobile breakpoint. Default: %s', 'yith-woocommerce-badges-management' ), '768px' ),
			'default'   => '768px',
		),

		'force-badge-positioning'                  => array(
			'id'        => 'yith-wcbm-force-badge-positioning',
			'name'      => __( 'Force Positioning', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => __( 'Force the badge positioning through javascript', 'yith-woocommerce-badges-management' ),
			'default'   => 'no',
			'options'   => array(
				'no'                   => __( 'No', 'yith-woocommerce-badges-management' ),
				'single-product'       => __( 'Single Product', 'yith-woocommerce-badges-management' ),
				'single-product-image' => __( 'Single Product Image', 'yith-woocommerce-badges-management' ),
				'shop'                 => __( 'Shop Page', 'yith-woocommerce-badges-management' ),
				'everywhere'           => __( 'Everywhere', 'yith-woocommerce-badges-management' ),
			),
		),
		'force-badge-positioning-timeout'          => array(
			'id'        => 'yith-wcbm-force-badge-positioning-timeout',
			'name'      => __( 'Force Positioning Timeout', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => __( 'Number of milliseconds before force positioning', 'yith-woocommerce-badges-management' ),
			'default'   => 500,
			'deps'      => array(
				'id'    => 'yith-wcbm-force-badge-positioning',
				'value' => 'single-product,single-product-image,shop,everywhere',
				'type'  => 'disable',
			),
		),
		'force-badge-positioning-on-scroll-mobile' => array(
			'id'        => 'yith-wcbm-force-badge-positioning-on-scroll-mobile',
			'name'      => __( 'Force Positioning On Scrolling in Mobile', 'yith-woocommerce-badges-management' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc_tip'  => __( 'If enabled, force positioning on scrolling in mobile devices', 'yith-woocommerce-badges-management' ),
			'default'   => 'yes',
			'deps'      => array(
				'id'    => 'yith-wcbm-force-badge-positioning',
				'value' => 'single-product,single-product-image,shop,everywhere',
				'type'  => 'disable',
			),
		),

		'general-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcqv-general-options',
		),

		'automatic-badges-options' => array(
			'title' => __( 'Automatic Badges', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'recent-products-badge' => array(
			'name'              => __( 'Recent products Badge', 'yith-woocommerce-badges-management' ),
			'type'              => 'yith-field',
			'yith-type'         => 'custom',
			'action'            => 'yith_wcbm_print_badges_select',
			'desc_tip'          => __( 'Select the badge you want to apply to all recent products.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-recent-products-badge',
			'custom_attributes' => $custom_attributes,
			'default'           => '',
		),

		'badge-newer-than' => array(
			'name'              => __( 'Recent products - newer than', 'yith-woocommerce-badges-management' ),
			'type'              => 'number',
			'desc_tip'          => __( 'Show the badge for products that are newer than X days.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-badge-newer-than',
			'custom_attributes' => $custom_attributes,
			'default'           => '0',
		),

		'on-sale-badge' => array(
			'name'              => __( 'On sale Badge', 'yith-woocommerce-badges-management' ),
			'type'              => 'yith-field',
			'yith-type'         => 'custom',
			'action'            => 'yith_wcbm_print_badges_select',
			'desc_tip'          => __( 'Select the Badge for products on sale.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-on-sale-badge',
			'custom_attributes' => $custom_attributes,
			'default'           => '',
		),

		'featured-badge' => array(
			'name'              => __( 'Featured Badge', 'yith-woocommerce-badges-management' ),
			'type'              => 'yith-field',
			'yith-type'         => 'custom',
			'action'            => 'yith_wcbm_print_badges_select',
			'desc_tip'          => __( 'Select the badge for featured products.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-featured-badge',
			'custom_attributes' => $custom_attributes,
			'default'           => '',
		),

		'out-of-stock-badge' => array(
			'name'              => __( 'Out of stock Badge', 'yith-woocommerce-badges-management' ),
			'type'              => 'yith-field',
			'yith-type'         => 'custom',
			'action'            => 'yith_wcbm_print_badges_select',
			'desc_tip'          => __( 'Select the Badge for products out of stock.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-out-of-stock-badge',
			'custom_attributes' => $custom_attributes,
			'default'           => '',
		),

		'low-stock-badge' => array(
			'name'              => __( 'Low stock Badge', 'yith-woocommerce-badges-management' ),
			'type'              => 'yith-field',
			'yith-type'         => 'custom',
			'action'            => 'yith_wcbm_print_badges_select',
			'desc_tip'          => __( 'Select the Badge for products with low stock.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-low-stock-badge',
			'custom_attributes' => $custom_attributes,
			'default'           => '',
		),

		'low-stock-qty' => array(
			'name'              => __( 'Low stock quantity', 'yith-woocommerce-badges-management' ),
			'type'              => 'number',
			'desc_tip'          => __( 'Select the low stock quantity.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-low-stock-qty',
			'custom_attributes' => $custom_attributes,
			'default'           => '3',
		),

		'automatic-badges-options-end' => array(
			'type' => 'sectionend',
		),

		'single-product-badge-options' => array(
			'title' => __( 'Single Product', 'yith-woocommerce-badges-management' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcbm-single-product-badge-options',
		),

		'hide-on-single-product' => array(
			'name'              => __( 'Hide on Single Product', 'yith-woocommerce-badges-management' ),
			'type'              => 'checkbox',
			'desc'              => __( 'Select to hide badges on Single Product Page.', 'yith-woocommerce-badges-management' ),
			'id'                => 'yith-wcbm-hide-on-single-product',
			'custom_attributes' => $custom_attributes,
			'default'           => 'no',
		),

		'show-advanced-badge-in-variable-products' => array(
			'name'              => __( 'Show advanced badges in variable products', 'yith-woocommerce-badges-management' ),
			'type'              => 'select',
			'id'                => 'yith-wcbm-show-advanced-badge-in-variable-products',
			'custom_attributes' => $custom_attributes,
			'default'           => 'same',
			'options'           => array(
				'same' => __( 'only if the discount percentage/amount is the same for all variations', 'yith-woocommerce-badges-management' ),
				'min'  => __( 'Show minimum discount percentage/amount', 'yith-woocommerce-badges-management' ),
				'max'  => __( 'Show maximum discount percentage/amount', 'yith-woocommerce-badges-management' ),
			),
		),

		'single-product-badge-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcbm-single-product-badge-options',
		),
	),
);

if ( ! current_user_can( 'manage_options' ) ) {
	unset( $advanced_options['settings']['enable-shop-manager'] );
}

return $advanced_options;