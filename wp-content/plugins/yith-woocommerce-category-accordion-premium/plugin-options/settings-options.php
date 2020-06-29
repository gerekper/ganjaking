<?php
if( !defined( 'ABSPATH' ) )
    exit;

$setting    =    array(

    'settings' => array(


        'section_general_settings'     => array(
            'name' => __( 'General settings', 'yith-woocommerce-category-accordion' ),
            'type' => 'title',
            'id'   => 'ywcca_section_general'
        ),



        'hide_empty_cat' =>  array(
            'name'  => __( 'Hide empty', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Hide empty categories in accordion', 'yith-woocommerce-category-accordion'),
            'id'    =>  'ywcca_hide_empty_cat',
            'default'   =>  'no',
            'std'       =>  'no',
            'type'  =>  'checkbox'
        ),

        'event_type_start_acc' =>  array(
            'name'  =>  __( 'Open accordion on ', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Select event for open accordion menu', 'yith-woocommerce-category-accordion'),
            'id'    =>  'ywcca_event_type_start_acc',
            'type'  =>  'select',
            'options'   =>  array(
                'click' => __('On Click','yith-woocommerce-category-accordion'),
                'hover' => __('On Hover', 'yith-woocommerce-category-accordion')
            ),
            'default'   =>  'click',
            'std'       =>  'click'
        ),
        'accordion_speed' =>  array(
            'name'  => __( 'Accordion speed', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set the accordion speed in milliseconds', 'yith-woocommerce-category-accordion'),
            'id'    =>  'ywcca_accordion_speed',
            'type'  =>  'text',
            'default'   =>  '400',
            'std'       =>  '400'
        ),

        'accordion_macro_cat_close'   =>  array(
            'name'  =>  __('Closed accordion', 'yith-woocommerce-category-accordion'),
            'desc'  =>  __('Show your accordion with all categories closed', 'yith-woocommerce-category-accordion'),
            'type'  =>  'checkbox',
            'id'    =>  'ywcca_accordion_macro_cat_close',
            'default'   =>  'no',
            'std'       =>  'no'
        ),
        'open_sub_cat_parent_visit' =>  array(
            'name'  =>  __('Open subcategories', 'yith-woocommerce-category-accordion'),
            'desc'  =>  __('Open subcategories when visit the parent ones', 'yith-woocommerce-category-accordion'),
            'type'  =>  'checkbox',
            'id'    =>  'ywcca_open_sub_cat_parent_visit',
            'default'   =>  'no',
            'std'       =>  'no'
        ),

        'max_depth_acc' =>  array(
            'name'  =>  __('Max depth level', 'yith-woocommerce-category-accordion'),
            'desc'  =>  __('Set the depth of accordion, 0 for all level', 'yith-woocommerce-category-accordion'),
            'type'  =>  'number',
            'custom_attributes' =>  array(
                'min'   =>  0,
                'max'   =>  99
            ),
            'std'   =>  0,
            'default'   =>  0,
            'id'        =>  'ywcca_max_depth_acc'
        ),

        'limit_number_cat_acc' =>  array(
            'name'  =>  __('Limit', 'yith-woocommerce-category-accordion'),
            'desc'  =>  __('Choose how many categories to display. -1 for infinite', 'yith-woocommerce-category-accordion'),
            'type'  =>  'number',
            'custom_attributes' =>  array(
                'min'   =>  -1,
                'max'   =>  99
            ),
            'std'   =>  -1,
            'default'   =>  -1,
            'id'        =>  'ywcca_limit_number_cat_acc'
        ),

        'section_general_settings_end' => array(
            'type' => 'sectionend',
            'id'   => 'ywtm_section_general_end'
        )
    )
);

return apply_filters( 'yith_wc_category_accordion_options', $setting );