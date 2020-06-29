<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}
$settings   =   array(

    'single-product'   =>  array(
        'section_save_for_later_sp_settings' =>  array(
            'name'  => __('Single Product Settings', 'yith-woocommerce-save-for-later'),
            'type'  =>  'title',
        ),

        'show_button_single_product' =>  array(
            'name'      =>  __('Show Save For Later button on single product page', 'yith-woocommerce-save-for-later'),
            'type'      =>  'checkbox',
            'default'   => 'no',
            'id'        =>  'ywsfl_show_button_single_product',
            'desc_tip'  =>  __('If checked, the button shows on single product page', 'yith-woocommerce-save-for-later')
        ),

        'button_text_single_product'   =>  array(
            'name'  =>  __('Add button text', 'yith-woocommerce-save-for-later'),
            'type'  =>  'text',
            'default'   =>  __('Save for Later', 'yith-woocommerce-save-for-later'),

            'desc'      =>  __('Set the text for "save for later" button', 'yith-woocommerce-save-for-later'),
            'id'        => 'ywsfl_button_text_single_product',

        ),
        'button_text_remove_from_save_list' => array(
            'name'              =>  __('Remove button text', 'yith-woocommerce-save-for-later'),
            'type'              =>  'text',
            'default'           =>  __( 'Remove from list','yith-woocommerce-save-for-later' ),
            'desc'          =>  __( 'Button text to remove product from the list', 'yith-woocommerce-save-for-later'),
            'id'                =>'ywsfl_button_text_remove_in_list'
        ),

        'section_save_for_later_sp_end'     => array(
            'type' => 'sectionend',

        )

    )

);

return apply_filters( 'ywsfl_single_product_settings' , $settings );