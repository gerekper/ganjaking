<?php

// Exit if accessed directly
!defined( 'YITH_WCCOS' ) && exit();

$import_uri = wp_nonce_url( add_query_arg( array( 'yith-wcos-import-custom-statuses' => true ), admin_url() ), 'import-custom-statuses', 'yith-wcos-import_nonce' );

$settings = array(
    'settings' => array(
        'general-options' => array(
            'title' => __( 'General Options', 'yith-woocommerce-custom-order-status' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'enable-shop-manager' => array(
            'id'        => 'yith-wccos-enable-shop-manager',
            'name'      => __( 'Enable Shop Manager to manage Custom Order Statuses', 'yith-woocommerce-custom-order-status' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'yes',
        ),

        'import-custom-statuses' => array(
            'name'             => __( 'Import Custom Statuses', 'yith-woocommerce-custom-order-status' ),
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<a href='$import_uri' class='button button-primary'>" . __( 'Import Custom Statuses', 'yith-woocommerce-custom-order-status' ) . "</a>",
        ),

        'general-options-end' => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcbk-general-options'
        ),
    )
);

return $settings;