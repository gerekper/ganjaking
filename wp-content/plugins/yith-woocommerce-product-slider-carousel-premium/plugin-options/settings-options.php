<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$settings   =   array(

    'settings'   =>  array(
        'section_product_slider_settings' =>  array(
            'name'  => __('General Settings', 'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'title',
            'id'    =>  'ywcps_section_general_start'
        ),

        'check_responsive'  =>  array(
            'name'  =>  __('Enable Responsive', 'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'checkbox',
            'id'    =>  'ywcps_check_responsive',
            'default'   =>  0,
            'std'       =>  0,
        ),

        'n_item_small_desk' =>  array(
            'name'  =>  __('Items in Standard Desktop', 'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'number',
            'desc_tip'  => __('This allows you to preset the number of slides visible with a specific browser width. For browser width between 767 and 991, this option works only if responsive slider is enabled', 'yith-woocommerce-product-slider-carousel'),
            'id'        => 'ywcps_n_item_small_desk',
            'custom_attributes' =>  array(
                'min'   =>  1,
                'max'   =>  99,
            ),
            'default'   =>  4
        ),
        'n_item_tablet' =>  array(
            'name'  =>  __('Items in Tablet' ,'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'number',
            'id'    =>  'ywcps_n_item_tablet',
            'desc_tip'  =>  __('This allows you to preset the number of slides visible with a particular browser width. For browser width between 479 and 766, this option works only if responsive slider is enabled', 'yith-woocommerce-product-slider-carousel'),
            'custom_attributes' =>  array(
                'min'   =>  1,
                'max'   =>  99,
            ),
            'default'   =>  2
        ),
        'n_item_mobile' =>  array(
            'name'  =>  __('Items in Mobile' ,'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'number',
            'id'    =>  'ywcps_n_item_mobile',
            'desc_tip'  =>  __('This allows you to preset the number of slides visible with a particular browser width. For browser width between 0 and 478, this option works only if responsive slider option is enabled', 'yith-woocommerce-product-slider-carousel'),
            'custom_attributes' =>  array(
                'min'   =>  1,
                'max'   =>  99,
            ),
	        'default' => 1
        ),

        'n_posts_per_page'  =>  array(
            'name'  =>  __('Product to show', 'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'number',
            'id'    =>  'ywcps_n_posts_per_page',
            'desc_tip'  =>  __('This option lets you choose the number of products you want to show. -1 for all', 'yith-woocommerce-product-slider-carousel'),
            'custom_attributes' =>  array(
                'min'   =>  -1,
                'max'   =>  99,
            ),
            'default'   =>  15
        ),

        'check_rtl'  =>  array(
            'name'  =>  __('Enable Rtl support', 'yith-woocommerce-product-slider-carousel'),
            'type'  =>  'checkbox',
            'id'    =>  'ywcps_check_rtl',
            'default'   =>  0,
            'std'       =>  0,
        ),

        'enable_mousewheel' => array(
        	'name' => __( 'Enable mousewhell event','yith-woocommerce-product-slider-carousel'),
	        'type' => 'checkbox',
	        'id'   => 'ywcps_enable_mousewhell',
	        'default' => 'no',
	        'desc' => __( 'If enabled, the slider will scroll by using the mouse middle button','yith-woocommerce-product-slider-carousel')
        ),

        'general_settings_end'     => array(
            'type' => 'sectionend',
            'id'   => 'ywcps_section_general_end'
        ),

    )

);

return apply_filters( 'ywsfl_general_settings' , $settings );