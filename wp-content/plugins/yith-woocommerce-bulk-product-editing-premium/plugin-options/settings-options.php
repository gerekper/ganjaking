<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();


$tab = array(
    'settings' => array(
        'general-options' => array(
            'title' => __( 'General Options', 'yith-woocommerce-bulk-product-editing' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'round-prices' => array(
            'id'        => 'yith-wcbep-round-prices',
            'name'      => __( 'Round Prices', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the prices will be rounded when bulk editing.', 'yith-woocommerce-bulk-product-editing' )
        ),

        'hidden-columns-per-user' => array(
            'id'        => 'yith-wcbep-hidden-columns-per-user',
            'name'      => __( 'Hidden columns per user', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the plugin will store hidden columns set by the user instead of globally.', 'yith-woocommerce-bulk-product-editing' )
        ),

        'use-regex-on-search' => array(
            'id'        => 'yith-wcbep-use-regex-on-search',
            'name'      => __( 'Use Regular Expressions', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the plugin uses Regular Expressions when searching for texts.', 'yith-woocommerce-bulk-product-editing' )
        ),

        'use-light-query' => array(
            'id'        => 'yith-wcbep-use-light-query',
            'name'      => __( 'Use Light Query', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the plugin uses a light query to retrieve products, so it improves your website performance. However, by enabling this option you CANNOT use advanced functionalities: for example, filtering variable products by price will not work. Please note: use it only if you have a huge amount of products.', 'yith-woocommerce-bulk-product-editing' )
        ),

        'general-options-end' => array(
            'type' => 'sectionend'
        )
    )
);

return apply_filters( 'yith_wcbep_panel_settings_options', $tab );