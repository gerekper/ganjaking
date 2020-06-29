<?php


$style   =   array(
    'layout3' => array(
/* LAYOUT SETTINGS 3*/
'layout3_settings'                       => array(
    'name' => __( 'Layout 3', 'yith-woocommerce-product-slider-carousel' ),
    'type' => 'title',
    'id'   => 'ywcps_section_layout3'
),

        'layout3_settings_box_bg_color'          => array(
    'name'    => __( 'Box background color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_box_bg_color',
    'default' => '#fff'
),

        'layout3_settings_box_border_color'      => array(
    'name'    => __( 'Box border color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_box_border_color',
    'default' => '#ededed'
),

        'layout3_settings_text_color'      => array(
    'name'    => __( 'Text color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_text_color',
    'default' => '#000'
),

        'layout3_settings_background_color_arrow' => array(
    'name'    => __( 'Background color navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_background_color_arrow',
    'default' => '#fff'
),

        'layout3_settings_border_color_arrow'            => array(
    'name'    => __( 'Border color navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_border_color_arrow',
    'default' => '#ededed'
),
        'layout3_settings_color_text_arrow' =>  array(
    'name'  =>  __( 'Text color navigation arrow', 'yith-woocommerce-product-slider-carousel' ),
    'type'  =>  'color',
    'desc'  =>  '',
    'id'    =>  'ywcps_layout3_text_color_arrow',
    'default'   =>  '#8b8b8b'
),

        'layout3_settings_button_bg_color'       => array(
    'name'    => __( 'Button background color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_button_bg_color',
    'default' => '#828282'
),

        'layout3_settings_button_bg_color_hover'       => array(
    'name'    => __( 'Button background color on hover ', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_button_bg_color_hover',
    'default' => '#434343'
),

        'layout3_settings_button_color'          => array(
    'name'    => __( 'Button text color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_button_color',
    'default' => '#fff'
),
        'layout3_settings_button_color_hover'          => array(
    'name'    => __( 'Button text color on hover', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_button_color_hover',
    'default' => '#fff'
),
        'layout3_settings_border_button_color'          => array(
    'name'    => __( 'Button border color', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_border_button_color',
    'default' => '#828282'
),
        'layout3_settings_border_button_color_hover'          => array(
    'name'    => __( 'Button border color on hover', 'yith-woocommerce-product-slider-carousel' ),
    'type'    => 'color',
    'desc'    => '',
    'id'      => 'ywcps_layout3_border_button_color_hover',
    'default' => '#434343'
),

        'layout3_settings_end'                   => array(
    'type' => 'sectionend',
    'id'   => 'ywcps_section_layout3_end'
)
    )
);

return apply_filters( 'ywcps_style3_settings' , $style );