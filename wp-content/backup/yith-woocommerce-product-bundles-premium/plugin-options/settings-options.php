<?php

$price_sync_url = wp_nonce_url( add_query_arg( array( 'yith_wcpb_force_sync_bundle_products' => '1',
                                                      'yith_wcpb_redirect'                   => urlencode( admin_url( 'admin.php?page=yith_wcpb_panel' ) )
                                               ), admin_url() ), 'yith-wcpb-sync-pip-prices' );

$quick_view_url = "https://yithemes.com/themes/plugins/yith-woocommerce-quick-view/";

$settings = array(

    'settings' => array(

        'general-options' => array(
            'title' => __( 'General Options', 'yith-woocommerce-product-bundles' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcpb-general-options'
        ),

        'show-bundled-items-in-report' => array(
            'id'      => 'yith-wcpb-show-bundled-items-in-report',
            'name'    => __( 'Show bundled items in Reports', 'yith-woocommerce-product-bundles' ),
            'type'    => 'yith-field',
            'yith-type'    => 'onoff',
            'desc'    => __( 'Flag this option to show also the bundled items in WooCommerce Reports.', 'yith-woocommerce-product-bundles' ),
            'default' => 'no'
        ),

        'hide-bundled-items-in-cart' => array(
            'id'      => 'yith-wcpb-hide-bundled-items-in-cart',
            'name'    => __( 'Hide bundled items in Cart and Checkout', 'yith-woocommerce-product-bundles' ),
            'type'    => 'yith-field',
            'yith-type'    => 'onoff',
            'desc'    => __( 'Flag this option to hide the bundled items in WooCommerce Cart and Checkout.', 'yith-woocommerce-product-bundles' ),
            'default' => 'no'
        ),

        'bundle-out-of-stock-sync' => array(
            'id'      => 'yith-wcpb-bundle-out-of-stock-sync',
            'name'    => __( 'Out of stock Sync', 'yith-woocommerce-product-bundles' ),
            'type'    => 'yith-field',
            'yith-type'    => 'onoff',
            'desc'    => __( 'Flag this option to set the bundle as Out of Stock if it contains at least one Out of Stock item.', 'yith-woocommerce-product-bundles' ),
            'default' => 'no'
        ),

        'pip-bundle-pricing' => array(
            'id'        => 'yith-wcpb-pip-bundle-pricing',
            'name'      => __( 'Price of "per item pricing" bundles in Shop', 'yith-woocommerce-product-bundles' ),
            'type'      => 'yith-field',
            'yith-type' => 'select-images',
            'options'   => array(
                'min-max'                => array(
                    'label' => __( 'Min - Max', 'yith-woocommerce-product-bundles' ),
                    'image' => YITH_WCPB_ASSETS_URL . '/images/pip-price-min-max.jpg',
                ),
                'min'                    => array(
                    'label' => __( 'Min only', 'yith-woocommerce-product-bundles' ),
                    'image' => YITH_WCPB_ASSETS_URL . '/images/pip-price-min.jpg',
                ),
                'from-min'               => array(
                    'label' => __( 'Min only higher than', 'yith-woocommerce-product-bundles' ),
                    'image' => YITH_WCPB_ASSETS_URL . '/images/pip-price-from-min.jpg',
                ),
                'regular-and-discounted' => array(
                    'label' => __( 'Regular and discounted', 'yith-woocommerce-product-bundles' ),
                    'image' => YITH_WCPB_ASSETS_URL . '/images/pip-price-regular-and-discounted.jpg',
                )
            ),
            'desc'      => __( 'Choose how you want to view pricing for "per item pricing" bundle products', 'yith-woocommerce-product-bundles' ),
            'default'   => 'from-min'
        ),

        'pip-bundle-order-pricing' => array(
            'id'      => 'yith-wcpb-pip-bundle-order-pricing',
            'name'    => __( 'Price of "per item pricing" bundles in orders', 'yith-woocommerce-product-bundles' ),
            'type'    => 'select',
            'options' => array(
                'price-in-bundle'        => __( 'Price in bundle', 'yith-woocommerce-product-bundles' ),
                'price-in-bundled-items' => __( 'Price in bundled items', 'yith-woocommerce-product-bundles' ),
            ),
            'desc'    => __( 'Choose how you want to view order pricing for "per item pricing" bundle products', 'yith-woocommerce-product-bundles' ),
            'default' => 'price-in-bundle'
        ),

        'show-bundled-item-prices' => array(
            'id'      => 'yith-wcpb-show-bundled-item-prices',
            'name'    => __( 'Show bundled item prices in Cart and Checkout', 'yith-woocommerce-product-bundles' ),
            'type'    => 'yith-field',
            'yith-type'    => 'onoff',
            'desc'    => __( 'Flag this option to show the price of bundled items in Cart and Checkout when the option "per item pricing" is enabled.', 'yith-woocommerce-product-bundles' ),
            'default' => 'no'
        ),

        'photoswipe-for-bundled-images' => array(
            'id'        => 'yith-wcpb-photoswipe-for-bundled-images',
            'name'      => __( 'PhotoSwipe for bundled images', 'yith-woocommerce-product-bundles' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => __( 'If enabled, use PhotoSwipe to open bundled image gallery. It requires PhotoSwipe is enabled.', 'yith-woocommerce-product-bundles' ),
            'default'   => 'yes',
        ),

        'quick-view-for-bundled-items' => array(
            'id'                => 'yith-wcpb-quick-view-for-bundled-items',
            'name'              => __( 'Quick View for bundled items', 'yith-woocommerce-product-bundles' ),
            'type'              => 'checkbox',
            'desc'              => __( 'If enabled, open bundled item product link in Quick View.', 'yith-woocommerce-product-bundles' ) . ' ' .
                                   '<strong>' . sprintf( '%s required', "<a href='{$quick_view_url}'>YITH WooCommerce Quick View</a>" ) . '</strong>',
            'default'           => 'no',
            'custom_attributes' => defined( 'YITH_WCQV' ) ? array() : array( 'disabled' => 'disabled' ),
        ),

        'pip-bundle-force-price-sync' => array(
            'name'             => __( 'Bundle price sync', 'yith-woocommerce-product-bundles' ),
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<a href='$price_sync_url' class='yith-update-button'>" . __( 'Force price sync for "per item pricing" bundles', 'yith-woocommerce-product-bundles' ) . "</a>",
        ),

        'general-options-end' => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcqv-general-options'
        )

    )
);

return apply_filters( 'yith_wcpb_panel_settings_options', $settings );