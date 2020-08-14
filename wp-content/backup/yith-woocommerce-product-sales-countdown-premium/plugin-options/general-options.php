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
    'general' => array(
        'ywpc_general_title'                    => array(
            'name' => __( 'General Settings', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywpc_general_title',
        ),
        'ywpc_general_enable_plugin'            => array(
            'name'    => __( 'Enable YITH WooCommerce Product Countdown', 'yith-woocommerce-product-countdown' ),
            'type'    => 'checkbox',
            'desc'    => '',
            'id'      => 'ywpc_enable_plugin',
            'default' => 'yes',
        ),
        'ywpc_general_what_show'                => array(
            'name'    => __( 'Select type', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_what_show',
            'default' => 'both',
            'type'    => 'radio',
            'options' => array(
                'both'  => __( 'Both timer and sale bar', 'yith-woocommerce-product-countdown' ),
                'timer' => __( 'Timer only ', 'yith-woocommerce-product-countdown' ),
                'bar'   => __( 'Sale bar only', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_general_where_show'               => array(
            'name'    => __( 'Select position', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_where_show',
            'default' => 'both',
            'type'    => 'radio',
            'options' => array(
                'both' => __( 'Categories and product detail page', 'yith-woocommerce-product-countdown' ),
                'loop' => __( 'Categories only', 'yith-woocommerce-product-countdown' ),
                'page' => __( 'Product detail page only', 'yith-woocommerce-product-countdown' ),
                'code' => __( 'Widget/Shortcode only', 'yith-woocommerce-product-countdown' ),
            ),
        ),
        'ywpc_general_before_sale_start'        => array(
            'name'    => __( 'Show timer before sale starts', 'yith-woocommerce-product-countdown' ),
            'type'    => 'checkbox',
            'desc'    => '',
            'id'      => 'ywpc_before_sale_start',
            'default' => 'no',
        ),
        'ywpc_general_before_sale_start_status' => array(
            'name'    => __( 'Disable products before sale starts', 'yith-woocommerce-product-countdown' ),
            'type'    => 'checkbox',
            'desc'    => '',
            'id'      => 'ywpc_before_sale_start_status',
            'default' => 'no',
        ),
        'ywpc_general_end_sale'                 => array(
            'name'    => __( 'Behaviour on expiration or sold out', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_end_sale',
            'default' => 'hide',
            'type'    => 'radio',
            'options' => array(
                'hide'    => __( 'Hide countdown and/or sale bar', 'yith-woocommerce-product-countdown' ),
                'remove'  => __( 'Remove product from sale', 'yith-woocommerce-product-countdown' ),
                'disable' => __( 'Leave the product unavailable', 'yith-woocommerce-product-countdown' )
            ),
        ),
        'ywpc_general_end_sale_summary'         => array(
            'name'    => __( 'Show sale summary', 'yith-woocommerce-product-countdown' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Only if product is unavailable', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_end_sale_summary',
            'default' => 'no',
        ),
        'ywpc_general_end'                      => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_general_end'
        ),

        'ywpc_position_start'    => array(
            'name' => __( 'Timer and sale bar position', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywpc_position_start'
        ),
        'ywpc_position_product'  => array(
            'name'    => __( 'Product Page', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_position_product',
            'default' => '0',
            'type'    => 'select',
            'desc'    => __( 'The position where timer and sale bar are showed in product detail pages.', 'yith-woocommerce-product-countdown' ),
            'options' => array(
                '0' => __( 'Before title', 'yith-woocommerce-product-countdown' ),
                '1' => __( 'After price', 'yith-woocommerce-product-countdown' ),
                '2' => __( 'Before "Add to cart"', 'yith-woocommerce-product-countdown' ),
                '3' => __( 'Before tabs', 'yith-woocommerce-product-countdown' ),
                '4' => __( 'Between tabs and related products', 'yith-woocommerce-product-countdown' ),
                '5' => __( 'After related products', 'yith-woocommerce-product-countdown' )
            ),
        ),
        'ywpc_position_category' => array(
            'name'    => __( 'Category', 'yith-woocommerce-product-countdown' ),
            'id'      => 'ywpc_position_category',
            'default' => '0',
            'type'    => 'select',
            'desc'    => __( 'The position where timer and sale bar are showed in category pages.', 'yith-woocommerce-product-countdown' ),
            'options' => array(
                '0' => __( 'Before title', 'yith-woocommerce-product-countdown' ),
                '1' => __( 'Before price', 'yith-woocommerce-product-countdown' ),
                '2' => __( 'Between price and "Add to cart"', 'yith-woocommerce-product-countdown' ),
                '3' => __( 'After "Add to cart"', 'yith-woocommerce-product-countdown' )
            ),
        ),
        'ywpc_position_end'      => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_position_end'
        ),

        'ywpc_shortcode_start'     => array(
            'name' => __( 'Shortcode settings', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'id'   => 'ywpc_shortcode_start'
        ),
        'ywpc_shortcode_title'     => array(
            'name'          => __( 'Product elements to show', 'yith-woocommerce-product-countdown' ),
            'type'          => 'checkbox',
            'desc'          => __( 'Title', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_shortcode_title',
            'default'       => 'yes',
            'checkboxgroup' => 'start'
        ),
        'ywpc_shortcode_rating'    => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Rating', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_shortcode_rating',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_shortcode_price'     => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Price', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_shortcode_price',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_shortcode_image'     => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Image', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_shortcode_image',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_shortcode_addtocart' => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( '"Add to cart"', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_shortcode_addtocart',
            'default'       => 'yes',
            'checkboxgroup' => 'end'
        ),
        'ywpc_shortcode_end'       => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_shortcode_end'
        ),

        'ywpc_widget_start'     => array(
            'name' => __( 'Widget settings', 'yith-woocommerce-product-countdown' ),
            'type' => 'title',
            'desc' => '',
            'id'   => 'ywpc_widget_start'
        ),
        'ywpc_widget_title'     => array(
            'name'          => __( 'Product elements to show', 'yith-woocommerce-product-countdown' ),
            'type'          => 'checkbox',
            'desc'          => __( 'Title', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_widget_title',
            'default'       => 'yes',
            'checkboxgroup' => 'start'
        ),
        'ywpc_widget_rating'    => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Rating', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_widget_rating',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_widget_price'     => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Price', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_widget_price',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_widget_image'     => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( 'Image', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_widget_image',
            'default'       => 'yes',
            'checkboxgroup' => '',
        ),
        'ywpc_widget_addtocart' => array(
            'name'          => '',
            'type'          => 'checkbox',
            'desc'          => __( '"Add to cart"', 'yith-woocommerce-product-countdown' ),
            'id'            => 'ywpc_widget_addtocart',
            'default'       => 'yes',
            'checkboxgroup' => 'end'
        ),
        'ywpc_widget_end'       => array(
            'type' => 'sectionend',
            'id'   => 'ywpc_shortcode_end'
        ),

    )

);