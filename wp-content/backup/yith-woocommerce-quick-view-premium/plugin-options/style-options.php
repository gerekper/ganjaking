<?php

$style_settings = array(

	'style' => array(

		10 => array(
			'title' => __( 'General Style', 'yith-woocommerce-quick-view' ),
			'desc'  => '',
			'type'  => 'title',
			'id'    => 'yith-wcqv-style-general',
		),

		20 => array(
			'name'      => __( 'Modal Window Background Color', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith-wcqv-background-modal',
			'default'   => '#ffffff',
		),

		30 => array(
			'id'        => 'yith-wcqv-button-quick-view-color',
			'name'      => __( '\'Quick View\' Button Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-color', '#222222', true ),
		),

		40 => array(
			'id'        => 'yith-wcqv-button-quick-view-text-color',
			'name'      => __( '\'Quick View\' Button Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-text-color', '#ffffff', true ),
		),

		50 => array(
			'id'        => 'yith-wcqv-button-quick-view-color-hover',
			'name'      => __( '\'Quick View\' Button Hover Color ', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-color-hover', '#ababab', true ),
		),

		60 => array(
			'id'        => 'yith-wcqv-button-quick-view-text-color-hover',
			'name'      => __( '\'Quick View\' Button Hover Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-text-color-hover', '#ffffff', true ),
		),

		70 => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcqv-style-general-end',
		),

		80 => array(
			'title' => __( 'Content Style', 'yith-woocommerce-quick-view' ),
			'desc'  => '',
			'type'  => 'title',
			'id'    => 'yith-wcqv-style-content',
		),

		90 => array(
			'id'        => 'yith-wcqv-main-text-color',
			'name'      => __( 'Main Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-main-text-color', '#222222', true ),
		),

		100 => array(
			'id'        => 'yith-wcqv-star-color',
			'name'      => __( 'Star Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#f7c104',
		),

		110 => array(
			'id'        => 'yith-wcqv-button-cart-color',
			'name'      => __( '\'Add to Cart\' Button Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-color', '#a46497', true ),
		),

		120 => array(
			'id'        => 'yith-wcqv-button-cart-text-color',
			'name'      => __( '\'Add to Cart\' Button Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-text-color', '#ffffff', true ),
		),

		130 => array(
			'id'        => 'yith-wcqv-button-cart-color-hover',
			'name'      => __( '\'Add to Cart\' Button Hover Color ', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-color-hover', '#935386', true ),
		),

		140 => array(
			'id'        => 'yith-wcqv-button-cart-text-color-hover',
			'name'      => __( '\'Add to Cart\' Button Hover Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-text-color-hover', '#ffffff', true ),
		),

		150 => array(
			'id'        => 'yith-wcqv-button-details-color',
			'name'      => __( '\'View Details\' Button Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-color', '#ebe9eb', true ),
		),

		160 => array(
			'id'        => 'yith-wcqv-button-details-text-color',
			'name'      => __( '\'View Details\' Button Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-text-color', '#515151', true ),
		),

		170 => array(
			'id'        => 'yith-wcqv-button-details-color-hover',
			'name'      => __( '\'View Details\' Button Hover Color ', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-color-hover', '#dad8da', true ),
		),

		180 => array(
			'id'        => 'yith-wcqv-button-details-text-color-hover',
			'name'      => __( '\'View Details\' Button Hover Text Color', 'yith-woocommerce-quick-view' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-text-color-hover', '#515151', true ),
		),

		210 => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcqv-style-content-end',
		),
	),
);

return apply_filters( 'yith_wcqv_panel_style_settings', $style_settings );