<?php
/**
 * STYLE ARRAY OPTIONS
 */

$style = array(

	'style' => array(

		array(
			'title' => __( 'Style Options', 'yith-woocommerce-added-to-cart-popup' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wacp-style-options',
		),

		array(
			'title'     => __( 'Overlay color', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose popup overlay color', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#000000',
			'id'        => 'yith-wacp-overlay-color',
		),

		array(
			'title'     => __( 'Overlay opacity', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose popup overlay opacity (from 0 to 1)', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'option'    => array( 'min' => 0, 'max' => 1 ),
			'step'      => 0.1,
			'default'   => 0.8,
			'id'        => 'yith-wacp-overlay-opacity',
		),

		array(
			'title'     => __( 'Popup background', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose popup background color', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'yith-wacp-popup-background',
		),

		array(
			'title'        => __( 'Closing link color', 'yith-woocommerce-added-to-cart-popup' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith-wacp-close-color',
			'colorpickers' => array(
				array(
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'normal',
					'default' => '#ffffff',
				),
				array(
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'hover',
					'default' => '#c0c0c0',
				),
			),
		),

		array(
			'name'      => __( 'Message Text Color', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose popup message text color', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wacp_get_proteo_option( 'yith-wacp-message-text-color', '#000000', true ),
			'id'        => 'yith-wacp-message-text-color',
		),

		array(
			'title'     => __( 'Message Background Color', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose popup message background color', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#e6ffc5',
			'id'        => 'yith-wacp-message-background-color',
		),

		array(
			'title'     => __( 'Message Icon', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Upload a popup message icon (suggested size 25x25 px)', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'default'   => YITH_WACP_ASSETS_URL . '/images/message-icon.png',
			'id'        => 'yith-wacp-message-icon',
		),

		array(
			'title'        => __( 'Product Name', 'yith-woocommerce-added-to-cart-popup' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith-wacp-product-name-color',
			'colorpickers' => array(
				array(
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'normal',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-product-name-color_normal', '#000000', true ),
				),
				array(
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'hover',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-product-name-color_hover', '#565656', true ),
				),
			),
		),

		array(
			'name'      => __( 'Product Price', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose color for product price', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wacp_get_proteo_option( 'yith-wacp-product-price-color', '#565656', true ),
			'id'        => 'yith-wacp-product-price-color',
		),

		array(
			'name'      => __( 'Total and Shipping label', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose color for total and shipping label', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wacp_get_proteo_option( 'yith-wacp-cart-info-label-color', '#565656', true ),
			'id'        => 'yith-wacp-cart-info-label-color',
		),

		array(
			'name'      => __( 'Total and Shipping amount', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose color for total and shipping amount', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#000000',
			'id'        => 'yith-wacp-cart-info-amount-color',
		),

		array(
			'title'        => __( 'Button Background', 'yith-woocommerce-added-to-cart-popup' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith-wacp-button-background',
			'colorpickers' => array(
				array(
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'normal',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-button-background_normal', '#ebe9eb', true ),
				),
				array(
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'hover',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-button-background_hover', '#dad8da', true ),
				),
			),
		),

		array(
			'title'        => __( 'Button Text', 'yith-woocommerce-added-to-cart-popup' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith-wacp-button-text',
			'colorpickers' => array(
				array(
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'normal',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-button-text_normal', '#515151', true ),
				),
				array(
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-woocommerce-added-to-cart-popup' ),
					'id'      => 'hover',
					'default' => yith_wacp_get_proteo_option( 'yith-wacp-button-text_hover', '#515151', true ),
				),
			),
		),

		array(
			'title'     => __( 'Related Title', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Select the color of the related product section title', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wacp_get_proteo_option( 'yith-wacp-related-title-color', '#565656', true ),
			'id'        => 'yith-wacp-related-title-color',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wacp-style-options',
		),
	),
);

return apply_filters( 'yith_wacp_panel_style_options', $style );
