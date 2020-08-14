<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

return array(
    'style' => array(
        'ywpc_style_title'                => array(
            'name' => __( 'Customization Settings', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywpc_style_title',
        ),
        'ywpc_style_timer_title'          => array(
            'title'   => __( 'Timer title', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_timer_title',
            'default' => __( 'Sale ends in', 'yith-woocommerce-product-countdown' ),
            'type'    => 'text',
            'desc'    => ''
        ),
        'ywpc_style_timer_title_before'   => array(
            'title'   => __( 'Timer title before sale starts', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_timer_title_before',
            'default' => __( 'Countdown to upcoming sale', 'yith-woocommerce-product-countdown' ),
            'type'    => 'text',
            'desc'    => ''
        ),
        'ywpc_style_sale_bar title'       => array(
            'title'   => __( 'Sale bar title', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_sale_bar_title',
            'default' => __( 'On sale', 'yith-woocommerce-product-countdown' ),
            'type'    => 'text',
            'desc'    => ''
        ),
        'ywpc_style_template'             => array(
            'name'    => __( 'Countdown and sale bar template', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_template',
            'default' => '1',
            'type'    => 'custom-radio',
            'class'   => 'ywpc-template',
            'options' => array(
                '1' => __( 'Style 1', 'yith-woocommerce-product-countdown' ),
                '2' => __( 'Style 2', 'yith-woocommerce-product-countdown' ),
                '3' => __( 'Style 3', 'yith-woocommerce-product-countdown' ),
            ),
        ),

        'ywpc_style_appearance'           => array(
            'name'    => __( 'Color and font size', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_appearance',
            'default' => 'def',
            'class'   => 'ywpc-appearance',
            'type'    => 'radio',
            'options' => array(
                'def'  => __( 'Use template default settings', 'yith-woocommerce-product-countdown' ),
                'cust' => __( 'Customize', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_style_text_font_size'       => array(
            'name'              => __( 'Text font size', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for message text. Min: 10 - Max: 55', 'yith-woocommerce-product-countdown' ),
            'class'             => 'ywpc-font-size',
            'default'           => 25,
            'id'                => 'ywpc_text_font_size',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 55,
                'required' => 'required'
            )
        ),
        'ywpc_style_timer_font_size'      => array(
            'name'              => __( 'Timer font size', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for timer text. Min: 10 - Max: 55', 'yith-woocommerce-product-countdown' ),
            'class'             => 'ywpc-font-size',
            'default'           => 28,
            'id'                => 'ywpc_timer_font_size',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 55,
                'required' => 'required'
            )
        ),
        'ywpc_style_text_font_size_loop'  => array(
            'name'              => __( 'Text font size (category page)', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for message text. Min: 10 - Max: 20', 'yith-woocommerce-product-countdown' ),
            'default'           => 15,
            'id'                => 'ywpc_text_font_size_loop',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 20,
                'required' => 'required'
            )
        ),
        'ywpc_style_timer_font_size_loop' => array(
            'name'              => __( 'Timer font size (category page)', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for timer text. Min: 10 - Max: 35', 'yith-woocommerce-product-countdown' ),
            'default'           => 15,
            'id'                => 'ywpc_timer_font_size_loop',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 35,
                'required' => 'required'
            )
        ),
        'ywpc_style_text_color'           => array(
            'name'    => __( 'Text color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_text_color',
            'default' => '#a12418',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for message text.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_border_color'         => array(
            'name'    => __( 'Border color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_border_color',
            'default' => '#dbd8d8',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for box border.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_back_color'           => array(
            'name'    => __( 'Background color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_back_color',
            'default' => '#fafafa',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for box background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_timer_fore_color'     => array(
            'name'    => __( 'Timer text color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_timer_fore_color',
            'default' => '#3c3c3c',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for timer text.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_timer_back_color'     => array(
            'name'    => __( 'Timer background color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_timer_back_color',
            'default' => '#ffffff',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for timer background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_bar_fore_color'       => array(
            'name'    => __( 'Sale bar main color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_bar_fore_color',
            'default' => '#a12418',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for sale bar foreground.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_bar_back_color'       => array(
            'name'    => __( 'Sale bar background color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_bar_back_color',
            'default' => '#e6e6e6',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for sale bar background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_style_end'                  => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_style_end'
        ),
    )
);