<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WC_MS_Post_Types {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 10 );
    }

    public static function register_post_types() {
        if ( post_type_exists('order_shipment') ) {
            return;
        }

        if ( !function_exists( 'wc_register_order_type' ) ) {
            return;
        }

        wc_register_order_type(
            'order_shipment',
            apply_filters( 'wc_ms_register_post_type_order_shipment',
                array(
                    'label'               => __('Order Shipments', 'wc_shipping_multiple_address'),
                    'description'         => __( 'This is where store order shipments are stored.', 'wc_shipping_multiple_address' ),
                    'public'              => false,
                    'show_ui'             => true,
                    'capability_type'     => 'shop_order',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'show_in_nav_menus'   => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title', 'comments', 'custom-fields' ),
                    'has_archive'         => false,
                    'exclude_from_orders_screen'       => true,
                    'add_order_meta_boxes'             => false,
                    'exclude_from_order_count'         => true,
                    'exclude_from_order_views'         => true,
                    'exclude_from_order_reports'       => true,
                    'exclude_from_order_sales_reports' => true,
                    'class_name'                       => 'WC_Order'
                )
            )
        );
    }

}

WC_MS_Post_Types::init();
