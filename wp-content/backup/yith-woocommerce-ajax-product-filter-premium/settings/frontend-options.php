<?php

$frontend = array(

    'frontend' => array(

        'header'   => array(

            array( 'type' => 'open' ),

            array(
                'name' => __( 'Frontend Settings', 'yith-woocommerce-ajax-navigation' ),
                'type' => 'title'
            ),

            array( 'type' => 'close' )
        ),

        'settings' => array(

            array( 'type' => 'open' ),

            array(
                'id'   => 'yith_wcan_frontend_description',
                'name' => _x( 'How To:', 'Admin panel: option description', 'yith-woocommerce-ajax-navigation' ),
                'type' => 'wcan_description',
                'desc' => _x( "If your theme is using WooCommerce standard templates, you don't need to change the following values.
                                Otherwise, add the classes used in the templates of your theme.
                                If you don't know how to do, please contact the developer of your theme to be correctly instructed.", 'Admin: Panel section description', 'yith-woocommerce-ajax-navigation' ),
            ),

            array(
                'name' => __( 'Product Container', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the CSS class or id for the product container', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>.products</strong>)',
                'id'   => 'yith_wcan_ajax_shop_container',
                'type' => 'text',
                'std'  => '.products'
            ),

            array(
                'name' => __( 'Shop Pagination Container', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the CSS class or id for the shop pagination container', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>nav.woocommerce-pagination</strong>)',
                'id'   => 'yith_wcan_ajax_shop_pagination',
                'type' => 'text',
                'std'  => 'nav.woocommerce-pagination'
            ),

            array(
                'name' => __( 'Result Count Container', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the CSS class or id for the results count container', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>.woocommerce-result-count</strong>)',
                'id'   => 'yith_wcan_ajax_shop_result_container',
                'type' => 'text',
                'std'  => '.woocommerce-result-count'
            ),

            array(
                'name' => __( 'Scroll up to top anchor', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enter here the HTML tag for the scroll up to to top feature', 'yith-woocommerce-ajax-navigation' ) . ' (Default: <strong>.yit-wcan-container</strong>)',
                'id'   => 'yith_wcan_ajax_scroll_top_class',
                'type' => 'text',
                'std'  => '.yit-wcan-container'
            ),

            array(
                'name'    => __( 'Order by', 'yith-woocommerce-ajax-navigation' ),
                'desc'    => __( 'Sort by number of products contained or alphabetically', 'yith-woocommerce-ajax-navigation' ),
                'id'      => 'yith_wcan_ajax_shop_terms_order',
                'type'    => 'select',
                'options' => array(
                    'alphabetical'  => __( 'Alphabetically', 'yith-woocommerce-ajax-navigation' ),
                    'menu_order'    => __( 'WooCommerce Default', 'yith-woocommerce-ajax-navigation' )
                ),
                'std'     => 'alphabetical'
            ),

            array( 'type' => 'close' ),
        ),
    )
);

return apply_filters( 'yith_wcan_panel_frontend_options', $frontend );