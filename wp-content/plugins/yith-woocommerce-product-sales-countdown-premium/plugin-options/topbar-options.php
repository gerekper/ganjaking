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
    'topbar' => array(
        'ywpc_topbar_title'              => array(
            'name' => __( 'Top/Bottom Countdown Bar Settings', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywpc_topbar_title',
        ),
        'ywpc_topbar_enable'             => array(
            'name'    => __( 'Enable Top/Bottom Countdown Bar', 'yith-woocommerce-product-countdown' ),
            'type'    => 'checkbox',
            'desc'    => '',
            'id'      => 'ywpc_topbar_enable',
            'default' => 'no',
        ),
        'ywpc_topbar_timer_title'        => array(
            'title'   => __( 'Title', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_timer_title',
            'default' => __( 'Click here for special offer!', 'yith-woocommerce-product-countdown' ),
            'type'    => 'text',
            'desc'    => __( 'The text displayed next to the timer', 'yith-woocommerce-product-countdown' )
        ),
        'ywpc_topbar_position'           => array(
            'name'    => __( 'Position', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_position',
            'default' => 'top',
            'type'    => 'radio',
            'options' => array(
                'top'    => __( 'Top of the page', 'yith-woocommerce-product-countdown' ),
                'bottom' => __( 'Bottom of the page', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_topbar_product'            => array(
            'title' => __( 'Selected product', 'yith-woocommerce-product-countdown' ),
            'id'    => 'ywpc_topbar_product',
            'type'  => 'custom-selector',
            'class' => 'wc-product-search',
            'css'   => 'width: 50%',
            'desc'  => __( 'The product to link in the bar', 'yith-woocommerce-product-countdown' )
        ),
        'ywpc_topbar_template'           => array(
            'name'    => __( 'Bar template', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_template',
            'default' => '1',
            'type'    => 'custom-radio-topbar',
            'class'   => 'ywpc-template-topbar',
            'options' => array(
                '1' => __( 'Style 1', 'yith-woocommerce-product-countdown' ),
                '2' => __( 'Style 2', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_topbar_appearance'         => array(
            'name'    => __( 'Color and font size', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_appearance',
            'default' => 'def',
            'class'   => 'ywpc-appearance-topbar',
            'type'    => 'radio',
            'options' => array(
                'def'  => __( 'Use template defaults', 'yith-woocommerce-product-countdown' ),
                'cust' => __( 'Customize', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_topbar_text_font_size'     => array(
            'name'              => __( 'Text font size', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for message text. Min: 10 - Max: 55', 'yith-woocommerce-product-countdown' ),
            'class'             => 'ywpc-font-size-topbar',
            'default'           => 30,
            'id'                => 'ywpc_topbar_text_font_size',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 55,
                'required' => 'required'
            )
        ),
        'ywpc_topbar_timer_font_size'    => array(
            'name'              => __( 'Timer font size', 'yith-woocommerce-product-countdown' ),
            'type'              => 'number',
            'desc'              => __( 'Set font size for timer text. Min: 10 - Max: 35', 'yith-woocommerce-product-countdown' ),
            'class'             => 'ywpc-font-size-topbar',
            'default'           => 18,
            'id'                => 'ywpc_topbar_timer_font_size',
            'custom_attributes' => array(
                'min'      => 10,
                'max'      => 35,
                'required' => 'required'
            )
        ),
        'ywpc_topbar_text_color'         => array(
            'name'    => __( 'Text color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_text_color',
            'default' => '#a12418',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for message text.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_text_label_color'   => array(
            'name'    => __( 'Label text color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_text_label_color',
            'default' => '#232323',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for label text.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_back_color'         => array(
            'name'    => __( 'Background color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_back_color',
            'default' => '#ffba00',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for box background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_timer_text_color'   => array(
            'name'    => __( 'Timer text color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_timer_text_color',
            'default' => '#363636',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for timer text.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_timer_back_color'   => array(
            'name'    => __( 'Timer background color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_timer_back_color',
            'default' => '#ffffff',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for timer background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_timer_border_color' => array(
            'name'    => __( 'Timer border color', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_topbar_timer_border_color',
            'default' => '#ff8a00',
            'type'    => 'text',
            'class'   => 'colorpick',
            'desc'    => __( 'Set color for timer background.', 'yith-woocommerce-product-countdown' ),
        ),
        'ywpc_topbar_end'                => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_topbar_end'
        ),
    )

);