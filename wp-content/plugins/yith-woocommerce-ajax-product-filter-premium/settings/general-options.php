<?php

$settings = array(

	'general' => array(

		'header'    => array(

            array( 'type' => 'open' ),

			array(
				'name' => __( 'General Settings', 'yith-woocommerce-ajax-navigation' ),
				'type' => 'title'
			),

			array( 'type' => 'close' )
		),

		'settings' => array(

			array( 'type' => 'open' ),

			array(
                'name'  => __( 'Ajax Loader', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Customize the AJAX loader icon', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_ajax_loader',
                'type'  => 'upload',
                'std'   => YITH_WCAN_URL . 'assets/images/ajax-loader.gif'
            ),

            array(
                'name'  => _x( 'Ajax WooCommerce Price Filter', 'Referer to original WooCommerce Price Filter Widget', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Use AJAX WooCommerce price filter', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_enable_ajax_price_filter',
                'type'  => 'on-off',
                'std'   => 'no'
            ),

             array(
                'name'  => _x( 'WooCommerce Price Filter slider', 'Referer to original WooCommerce Price Filter Widget', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Use WooCommerce price filter with slider', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_enable_ajax_price_filter_slider',
                'type'  => 'on-off',
                'std'   => 'yes'
            ),

            array(
                'name'  => _x( 'Instant WooCommerce Price Filter slider', 'Referer to original WooCommerce Price Filter Widget', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Use WooCommerce price filter with ajax slider without "Filter" button', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_enable_slider_in_ajax',
                'type'  => 'on-off',
                'std'   => 'no',
            ),

             array(
                'name'  => _x( 'Dropdown for WooCommerce Price Filter', 'Referer to original WooCommerce Price Filter Widget', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Add dropdown effect to original WooCommerce Price Filter widget', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_enable_dropdown_price_filter',
                'type'  => 'on-off',
                'std'   => 'no'
            ),

            array(
                'name'  => _x( 'Open/Close for WooCommerce Price Filter', 'Referer to original WooCommerce Price Filter Widget', 'yith-woocommerce-ajax-navigation' ),
                'desc'  => __( 'Select this option if you want to show the dropdown as opened or closed when the page is loaded', 'yith-woocommerce-ajax-navigation' ),
                'id'    => 'yith_wcan_dropdown_style',
                'type'  => 'select',
                'options' => array(
                    'open'  => __( 'Opened', 'yith-woocommerce-ajax-navigation' ),
                    'close' => __( 'Closed', 'yith-woocommerce-ajax-navigation' ),
                ),
                'std'   => 'open'
            ),

            array( 'type' => 'close' ),
		),
	)
);

return apply_filters( 'yith_wcan_panel_settings_options', $settings );