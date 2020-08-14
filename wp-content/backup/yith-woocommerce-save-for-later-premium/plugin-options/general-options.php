<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


$is_wishlist_enabled    =   defined( 'YITH_WCWL' );

$settings   =   array(

    'general'   =>  array(
        'section_save_for_later_settings' =>  array(
            'name'  => __('General Settings', 'yith-woocommerce-save-for-later'),
            'type'  =>  'title',
            'id'    =>  'ywsfl_section_general_start'
        ),

        'text_add_from_list' =>  array(
            'name'      =>  __('"Save for Later" text', 'yith-woocommerce-save-for-later'),
            'type'      =>  'text',
            'default'   =>  __('Save for Later', 'yith-woocommerce-save-for-later'),
            'std'       =>  __('Save for Later', 'yith-woocommerce-save-for-later'),
            'id'        =>  'ywsfl_text_add_button',
            'desc'      =>  __('You can set the text for your "Save for Later" link', 'yith-woocommerce-save-for-later')
        ),

        'save_for_later_page'   =>  array(
            'name'  =>  __('Save for Later page', 'yith-woocommerce-save-for-later'),
            'type'  =>  'text',
            'default'   =>  __('Save for Later'),
            'std'       =>  'Save for Later',
            'desc'      =>  __('This page contains the [yith_wsfl_saveforlater] shortcode.<br> You can use this shortcode in other pages!.', 'yith-woocommerce-save-for-later'),
            'id'        => 'ywsfl_page_name',
            'custom_attributes' => array( 'readonly'=>'readonly' )
        ),

        'add_wishlist_link'  =>  array(
            'name'              =>  __('Show "Add to Wishlist"', 'yith-woocommerce-save-for-later'),
            'type'              =>  'checkbox',
            'default'           =>  0,
            'std'               =>  0,
            'desc_tip'          =>  __('If YITH WooCommerce Wishlist is installed, you can display the "Add to Wishlist" link in your "Save for Later" list', 'yith-woocommerce-save-for-later'),
            'custom_attributes' =>  $is_wishlist_enabled ?    array() : array('disabled'  => 'disabled'),
            'id'                =>'ywsfl_show_wishlist_link'

        ),

        'general_settings_end'     => array(
            'type' => 'sectionend',
            'id'   => 'ywsfl_section_general_end'
        )

    )

);

return apply_filters( 'ywsfl_general_settings' , $settings );