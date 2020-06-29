<?php
/**
 * Cart Message Options
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


return array(
	'layout' => array(


		/* LAYOUT SETTINGS 2*/
		'layout2_settings'                       => array(
			'name' => __( 'Layout 2', 'yith-woocommerce-cart-messages' ),
			'desc' => __( 'In this section you can customize each message layout', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_layout2',
		),

		'layout2_settings_box_bg_color'          => array(
			'name'      => __( 'Box background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_box_bg_color',
			'default'   => '#e4f2fc',
		),

		'layout2_settings_box_border_color'      => array(
			'name'      => __( 'Box border color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_box_border_color',
			'default'   => '#cedde9',
		),

		'layout2_settings_box_text_color'        => array(
			'name'      => __( 'Box text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_box_text_color',
			'default'   => '#353535',
		),

		'layout2_settings_icon_background_color' => array(
			'name'      => __( 'Icon background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_icon_background_color',
			'default'   => '#0066b4',
		),

		'layout2_settings_icon_image'            => array(
			'name'      => __( 'Icon image', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'desc'      => '',
			'id'        => 'ywcm_layout2_settings_icon_image',
			'default'   => YITH_YWCM_ASSETS_URL . '/images/cart-notice-2.png',
		),

		'layout2_settings_button_bg_color'       => array(
			'name'      => __( 'Button background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_button_bg_color',
			'default'   => '#0066b4',
		),

		'layout2_settings_button_bg_color_hover' => array(
			'name'      => __( 'Button background color on hover ', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_button_bg_color_hover',
			'default'   => '#044a80',
		),

		'layout2_settings_button_color'          => array(
			'name'      => __( 'Button text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout2_button_color',
			'default'   => '#fff',
		),


		'layout2_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_layout2_end',
		),

		/* LAYOUT SETTINGS 3*/
		'layout3_settings'                       => array(
			'name' => __( 'Layout 3', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_layout3',
		),

		'layout3_settings_box_bg_color'          => array(
			'name'      => __( 'Box background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout3_box_bg_color',
			'default'   => '#fff',
		),

		'layout3_settings_box_border_color'      => array(
			'name'      => __( 'Box border color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout3_box_border_color',
			'default'   => '#e3e3e3',
		),

		'layout3_settings_box_text_color'        => array(
			'name'      => __( 'Box text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout3_box_text_color',
			'default'   => '#353535',
		),

		'layout3_settings_icon_image'            => array(
			'name'      => __( 'Icon image', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'ywcm_layout3_settings_icon_image',
			'default'   => YITH_YWCM_ASSETS_URL . '/images/cart-notice-3.png',
		),

		'layout3_settings_button_bg_color'       => array(
			'name'      => __( 'Button background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout3_button_bg_color',
			'default'   => '#00b7de',
		),

		'layout3_settings_button_bg_color_hover' => array(
			'name'      => __( 'Button background color on hover ', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout3_button_bg_color_hover',
			'default'   => '#0594b2',
		),

		'layout3_settings_button_color'          => array(
			'name'      => __( 'Button text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout3_button_color',
			'default'   => '#fff',
		),

		'layout3_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_layout3_end',
		),


		/* LAYOUT SETTINGS 4*/

		'layout4_settings'                       => array(
			'name' => __( 'Layout 4', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_layout4',
		),

		'layout4_settings_box_bg_color'          => array(
			'name'      => __( 'Box background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout4_box_bg_color',
			'default'   => '#ffffe8',
		),

		'layout4_settings_box_border_color'      => array(
			'name'      => __( 'Box border color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout4_box_border_color',
			'default'   => '#ffdd81',
		),

		'layout4_settings_box_text_color'        => array(
			'name'      => __( 'Box text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout4_box_text_color',
			'default'   => '#353535',
		),

		'layout4_settings_icon_image'            => array(
			'name'      => __( 'Icon image', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'ywcm_layout4_settings_icon_image',
			'default'   => YITH_YWCM_ASSETS_URL . '/images/cart-notice-4.png',
		),

		'layout4_settings_button_bg_color'       => array(
			'name'      => __( 'Button background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout4_button_bg_color',
			'default'   => '#ff7e00',
		),


		'layout4_settings_button_bg_color_hover' => array(
			'name'      => __( 'Button background color on hover ', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout4_button_bg_color_hover',
			'default'   => '#bb5c00',
		),

		'layout4_settings_button_color'          => array(
			'name'      => __( 'Button text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout4_button_color',
			'default'   => '#fff',
		),

		'layout4_settings_button_border_color'   => array(
			'name'      => __( 'Button border color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout4_button_border_color',
			'default'   => '#bb5c00',
		),
		'layout4_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_layout4_end',
		),


		/* LAYOUT SETTINGS 5*/

		'layout5_settings'                       => array(
			'name' => __( 'Layout 5', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_layout5',
		),

		'layout5_settings_box_bg_color'          => array(
			'name'      => __( 'Box background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout5_box_bg_color',
			'default'   => '#5f5f5f',
		),


		'layout5_settings_box_text_color'        => array(
			'name'      => __( 'Box text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout5_box_text_color',
			'default'   => '#fff',
		),


		'layout5_settings_icon_image'            => array(
			'name'    => __( 'Icon image', 'yith-woocommerce-cart-messages' ),
			'type'    => 'yith_ywcm_upload',
			'id'      => 'ywcm_layout5_settings_icon_image',
			'default' => YITH_YWCM_ASSETS_URL . '/images/cart-notice-5.png',
		),

		'layout5_settings_button_bg_color'       => array(
			'name'      => __( 'Button background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout5_button_bg_color',
			'default'   => '#f1c40f',
		),


		'layout5_settings_button_bg_color_hover' => array(
			'name'      => __( 'Button background color on hover ', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout5_button_bg_color_hover',
			'default'   => '#e2b70b',
		),

		'layout5_settings_button_color'          => array(
			'name'      => __( 'Button text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout5_button_color',
			'default'   => '#353535',
		),


		'layout5_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_layout5_end',
		),

		/* LAYOUT SETTINGS 6*/

		'layout6_settings'                       => array(
			'name' => __( 'Layout 6', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_layout6',
		),

		'layout6_settings_box_bg_color'          => array(
			'name'      => __( 'Box background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout6_box_bg_color',
			'default'   => '#ff7e00',
		),


		'layout6_settings_box_text_color'        => array(
			'name'      => __( 'Box text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcm_layout6_box_text_color',
			'default'   => '#fff',
		),


		'layout6_settings_icon_image'            => array(
			'name'      => __( 'Icon image', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'ywcm_layout6_settings_icon_image',
			'default'   => YITH_YWCM_ASSETS_URL . '/images/cart-notice-6.png',
		),

		'layout6_settings_button_bg_color'       => array(
			'name'      => __( 'Button background color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout6_button_bg_color',
			'default'   => '#ffea34',
		),

		'layout6_settings_button_bg_color_hover' => array(
			'name'      => __( 'Button background color on hover ', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout6_button_bg_color_hover',
			'default'   => '#ead730',
		),

		'layout6_settings_button_color'          => array(
			'name'      => __( 'Button text color', 'yith-woocommerce-cart-messages' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'ywcm_layout6_button_color',
			'default'   => '#353535',
		),


		'layout6_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_layout6_end',
		),

	),
);
