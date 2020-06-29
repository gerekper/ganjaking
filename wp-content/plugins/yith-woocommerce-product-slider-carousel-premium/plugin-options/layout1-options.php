<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$style   =   array(
    'layout1' => array(


        /* LAYOUT SETTINGS 1*/
        'layout1_settings'                       => array(
            'name' => __( 'Layout 1', 'yith-woocommerce-product-slider-carousel' ),
            'type' => 'title',
            'id'   => 'ywcps_section_layout1'
        ),

        'layout1_settings_box_bg_color'          => array(
            'name'    => __( 'Box background color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_box_bg_color',
            'default' => '#f7f7f7'
        ),

        'layout1_settings_box_border_color'      => array(
            'name'    => __( 'Box border color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_box_border_color',
            'default' => '#cccccc'
        ),

        'layout1_settings_text_color'      => array(
            'name'    => __( 'Text color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_text_color',
            'default' => '#000000'
        ),

        'layout1_settings_background_color_arrow' => array(
            'name'    => __( 'Background color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_background_color_arrow',
            'default' => '#f7f7f7'
        ),

        'layout1_settings_border_color_arrow'            => array(
            'name'    => __( 'Border color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_border_color_arrow',
            'default' => '#f7f7f7'
        ),
        'layout1_settings_color_text_arrow' =>  array(
            'name'  =>  __( 'Text color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
            'type'  =>  'color',
            'desc'  =>  '',
            'id'    =>  'ywcps_layout1_text_color_arrow',
            'default'   =>  '#a9a9a9'
        ),

        'layout1_settings_button_bg_color'       => array(
            'name'    => __( 'Button background color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_button_bg_color',
            'default' => '#f7f7f7'
        ),

        'layout1_settings_button_bg_color_hover'       => array(
            'name'    => __( 'Button background color on hover ', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_button_bg_color_hover',
            'default' => '#7f7f7f'
        ),

        'layout1_settings_button_color'          => array(
            'name'    => __( 'Button text color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_button_color',
            'default' => '#000'
        ),
        'layout1_settings_button_color_hover'          => array(
            'name'    => __( 'Button text color on hover', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_button_color_hover',
            'default' => '#fff'
        ),

        'layout1_settings_border_button_color'          => array(
            'name'    => __( 'Button border color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_border_button_color',
            'default' => '#f7f7f7'
        ),
        'layout1_settings_border_button_color_hover'          => array(
            'name'    => __( 'Button border color on hover', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout1_border_button_color_hover',
            'default' => '#7f7f7f'
        ),


        'layout1_settings_end'                   => array(
            'type' => 'sectionend',
            'id'   => 'ywcps_section_layout1_end'
        ),

    )
);
return apply_filters( 'ywcps_style_settings' , $style );