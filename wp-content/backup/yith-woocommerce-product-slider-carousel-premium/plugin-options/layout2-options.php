<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$style  =   array(
    'layout2' => array(

        'layout2_settings'                       => array(
            'name' => __( 'Layout 2', 'yith-woocommerce-product-slider-carousel' ),
            'type' => 'title',
            'id'   => 'ywcps_section_layout2'
        ),

        'layout2_settings_box_bg_color'          => array(
            'name'    => __( 'Box background color', 'yith-woocommerce-product-slider-carousel' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywcps_layout2_box_bg_color',
            'default' => '#fff'
        ),

        'layout2_settings_box_border_color'      => array(
    'name'    => __( 'Box border color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_box_border_color',
    'default' => '#ededed'
),

        'layout2_settings_text_color'      => array(
    'name'    => __( 'Text color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_text_color',
    'default' => '#000'
),

        'layout2_settings_background_color_arrow' => array(
    'name'    => __( 'Background color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_background_color_arrow',
    'default' => '#ededed'
),

        'layout2_settings_border_color_arrow'            => array(
    'name'    => __( 'Border color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_border_color_arrow',
    'default' => '#ededed'
),
        'layout2_settings_color_text_arrow' =>  array(
    'name'  =>  __( 'Text color of navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'  =>  'color',
    'desc'  =>  '',
    'id'    =>  'ywcps_layout2_text_color_arrow',
    'default'   =>  '#a4a4a4'
),

        'layout2_settings_button_bg_color'       => array(
    'name'    => __( 'Button background color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_button_bg_color',
    'default' => '#c2947c'
),

        'layout2_settings_button_bg_color_hover'       => array(
    'name'    => __( 'Button background color on hover ', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_button_bg_color_hover',
    'default' => '#fff'
),

        'layout2_settings_button_color'          => array(
    'name'    => __( 'Button text color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_button_color',
    'default' => '#fff'
),
        'layout2_settings_button_color_hover'          => array(
    'name'    => __( 'Button text color on hover', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_button_color_hover',
    'default' => '#c2947c'
),
        'layout2_settings_border_button_color'          => array(
    'name'    => __( 'Button border color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_border_button_color',
    'default' => '#c2947c'
),
        'layout2_settings_border_button_color_hover'          => array(
    'name'    => __( 'Button border color on hover', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout2_border_button_color_hover',
    'default' => '#c2947c'
),

        'layout2_settings_end'                   => array(
    'type' => 'sectionend',
    'id'   => 'ywcps_section_layout2_end'
),
    ));

return apply_filters( 'ywcps_style2_settings' , $style );
