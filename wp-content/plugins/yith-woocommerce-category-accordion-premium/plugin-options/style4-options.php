<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$style   =   array(
    'style4' => array(


        /* LAYOUT SETTINGS 4*/

        'style4_general_settings'                       => array(
            'name' => __( 'General Settings', 'yith-woocommerce-category-accordion' ),
            'type' => 'title',
            'id'   => 'ywcca_section_general_4'
        ),

        'style4_general_title_typ' =>  array(
            'name'  =>  __( 'Title Container Background', 'yith-woocommerce-category-accordion' ),
            'type'  =>  'typography',
            'id'    =>  'ywcca_style4_title_typography',
            'default' => array(
                'size'   => 14,
                'unit'   => 'px',
                'style'  => 'bold',
                'transform' =>  'uppercase',
                'color'  => '#484848',
            ),
            'style'   =>  array(
                'selectors'   =>  '.ywcca_widget_container_style_4 .ywcca_widget_title',
                'properties'  =>  'font-size,
                                 font-style,
                                 text-transform,
                                 color'
            )
        ),

        'style4_general_title_background' =>  array(
            'name'  =>  __( 'Title Border Color', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set background color for your title container', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style4_title_bg',
            'default'   => '#ffffff',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_widget_title',
                'properties'    =>  'background'
            )
        ),

        'style4_general_title_border' =>  array(
            'name'  =>  __( 'Border Color', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set border-bottom-color for your title container', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style4_title_border',
            'default'   => '#e0e0e0',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_widget_title',
                'properties'    =>  'border-bottom-color'
            )
        ),

        'style4_settings_style_count'   =>  array(
            'name'  =>  __( 'Count Style', 'yith-woocommerce-category-accordion' ),
            'type'  =>  'select',
            'desc'  =>  __( 'Choose count category style', 'yith-woocommerce-category-accordion' ),
            'id'    =>  'ywcca_style_4_count',
            'options'   =>  array(
                'default'   =>  __('Default', 'yith-woocommerce-category-accordion'),
                'rect'      =>  __( 'Rectangle', 'yith-woocommerce-category-accordion'),
                'round'     =>  __( 'Round', 'yith-woocommerce-category-accordion' )
            ),
            'default' => 'round'
        ),

        'style4_settings_background_rect_count'   =>  array(
            'name'   =>  __('Background color', 'yith-woocommerce-category-accordion'),
            'desc'   => __( 'Set the background for count container', 'yith-woocommerce-category-accordion' ),
            'type'   =>  'color',
            'id'     =>  'ywcca_style4_back_rect_count',
            'default'    =>  '#f0f0f0',
            'style'  =>  array(
                'selectors'  =>  '.ywcca_widget_container_style_4 ul.ywcca_category_accordion_widget li span.rectangle_count',
                'properties' =>  'background'
            )

        ),

        'style4_settings_border_rect_count'  =>  array(
            'name'   =>  __('Border color', 'yith-woocommerce-category-accordion'),
            'desc'   => __( 'Set the border color for count container', 'yith-woocommerce-category-accordion' ),
            'type'   =>  'color',
            'id'     =>  'ywcca_style4_border_rect_count',
            'default'    =>  '#e0e0e0',
            'style'  =>  array(
                'selectors'  =>  '.ywcca_widget_container_style_4 ul.ywcca_category_accordion_widget li span.rectangle_count',
                'properties' =>  'border-color'
            )

        ),

        'style4_settings_background_round_count'   =>  array(
            'name'   =>  __('Background color', 'yith-woocommerce-category-accordion'),
            'desc'   => __( 'Set the background for count container', 'yith-woocommerce-category-accordion' ),
            'type'   =>  'color',
            'id'     =>  'ywcca_style4_back_round_count',
            'default'    =>  '#f5f5f5',
            'style'  =>  array(
                'selectors'  =>  '.ywcca_widget_container_style_4 ul.ywcca_category_accordion_widget li span.round_count',
                'properties' =>  'background'
            )
        ),

        'style4_settings_border_round_count'  =>  array(
            'name'   =>  __('Border color', 'yith-woocommerce-category-accordion'),
            'desc'   => __( 'Set the border color for count container', 'yith-woocommerce-category-accordion' ),
            'type'   =>  'color',
            'id'     =>  'ywcca_style4_border_round_count',
            'default'    =>  '#cccccc',
            'style'     =>  array(
                'selectors' =>  '.ywcca_widget_container_style_4 ul.ywcca_category_accordion_widget li span.round_count',
                'properties'    =>  'border-color'
            )
        ),


        'style4_general_settings_end'                   => array(
            'type' => 'sectionend',
            'id'   => 'ywcps_section_general_4_end'
        ),

        'style4_parent_settings'                       => array(
            'name' => __( 'Style for Parent Categories', 'ywcps' ),
            'type' => 'title',
            'id'   => 'ywcps_section_parent_4'
        ),

        'style4_parent_typ' =>  array(
            'name'  =>  __( 'Typography for Parent Categories', 'yith-woocommerce-category-accordion' ),
            'type'  =>  'typography',
            'id'    =>  'ywcca_style4_parent_typography',
            'default' => array(
                'size'   => 13,
                'unit'   => 'px',
                'style'  => 'regular',
                'transform' =>  'uppercase',
                'color'  => '#909090',
            ),
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget li.cat-item,
                                    .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-menu li.menu-item,
                                    .ywcca_widget_container_style_4 .ywcca_category_accordion_widget li.cat-item a,
                                    .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-menu li.menu-item a',
                'properties'    => 'font-size,
                                    font-style,
                                    text-transform,
                                    color'
            )
        ),

        'style4_parent_background' =>  array(
            'name'  =>  __( 'Parent Categories Background', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set background color for your parent categories', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style4_parent_bg',
            'default'   => '#fff',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget li.cat-item,
                                     .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-menu li.menu-item',
                'properties'    =>  'background'
            )
        ),

        'style4_parent_border' =>  array(
            'name'  =>  __( 'Border Color', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set border top color for your parent categories container', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style2_parent_border',
            'default'   => '#e2e2e2',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget li.cat-item,
                                     .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-menu li.menu-item',
                'properties'    =>  'border-bottom-color'
            )
        ),



        'style4_parent_settings_end'                   => array(
            'type' => 'sectionend',
            'id'   => 'ywcps_section_parent_4_end'
        ),

        'style4_child_settings'                       => array(
            'name' => __( 'Style for Child Categories', 'ywcps' ),
            'type' => 'title',
            'id'   => 'ywcps_section_child_4'
        ),

        'style4_child_typ' =>  array(
            'name'  =>  __( 'Typography for Child Categories', 'yith-woocommerce-category-accordion' ),
            'type'  =>  'typography',
            'id'    =>  'ywcca_style4_child_typography',
            'default' => array(
                'size'   => 12,
                'unit'   => 'px',
                'style'  => 'regular',
                'transform' =>  'uppercase',
                'color'  => '#909090',
            ),
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.yith-children li,
                                     .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-sub-menu li.menu-item,
                                    .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.yith-children li a,
                                    .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-sub-menu li.menu-item a',
                'properties'    => 'font-size,
                                    font-style,
                                    text-transform,
                                    color'
            )
        ),

        'style4_child_background' =>  array(
            'name'  =>  __( 'Child Categories Background', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set background color for your child categories', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style4_child_bg',
            'default'   => '#f5f5f5',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.yith-children li,
                                     .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-sub-menu li.menu-item',
                'properties'    => 'background'
            )
        ),

        'style4_child_border' =>  array(
            'name'  =>  __( 'Border Color', 'yith-woocommerce-category-accordion' ),
            'desc'  =>  __('Set border top color for your child categories container', 'yith-woocommerce-category-accordion'),
            'type'  =>  'color',
            'id'    =>  'ywcca_style4_child_border',
            'default'   => '#f5f5f5',
            'style' =>  array(
                'selectors'     =>  '.ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.yith-children li,
                                     .ywcca_widget_container_style_4 .ywcca_category_accordion_widget ul.ywcca-sub-menu li.menu-item',
                'properties'    => 'border-bottom-color'
            )
        ),



        'style4_child_settings_end'                   => array(
            'type' => 'sectionend',
            'id'   => 'ywcps_section_child_4_end'
        ),
    )
);
return apply_filters( 'ywcca_style4_settings' , $style );