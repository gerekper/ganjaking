<?php

$custom_style = array(

    'seo' => array(

        'header'   => array(

            array( 'type' => 'open' ),

            array(
                'name' => __( 'SEO', 'yith-woocommerce-ajax-navigation' ),
                'type' => 'title'
            ),

            array( 'type' => 'close' )
        ),

        'settings' => array(

            array( 'type' => 'open' ),

            array(
                'name' => __( 'Enable SEO option', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Add "robots" meta tag in head tag of HTML page if filters have been activated.', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_seo',
                'type' => 'on-off',
                'std'  => 'no',
            ),

            array(
                'name' => __( 'Meta tag options', 'yith-woocommerce-ajax-navigation' ),
                'desc' => '',
                'id'   => 'yith_wcan_seo_value',
                'type' => 'select',
                'std'  => 'noindex-follow',
                'options' => array(
	                'disabled'         => __( 'Disabled', 'yith-woocommerce-ajax-navigation' ),
                    'noindex-nofollow'  => 'noindex, nofollow',
                    'noindex-follow'    => 'noindex, follow',
                    'index-nofollow'    => 'index, nofollow',
                    'index-follow'      => 'index, follow',
                ),
                'custom_attributes' => array(
                    'style' => 'width: 150px;'
                ),
                'deps' => array(
                    'ids'    => 'yith_wcan_enable_seo',
                    'values' => 'yes'
                )
            ),

	        array(
		        'name' => __( 'Add rel="nofollow" to filter url', 'yith-woocommerce-ajax-navigation' ),
		        'desc' => '',
		        'id'   => 'yith_wcan_seo_rel_nofollow',
		        'type' => 'on-off',
		        'std'  => 'no',
		        'deps' => array(
			        'ids'    => 'yith_wcan_enable_seo',
			        'values' => 'yes'
		        )
	        ),

            array(
                'name' => __( 'Change browser URL', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Enable this option if you want to update the URL after applying a filter.', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_change_browser_url',
                'type' => 'on-off',
                'std'  => 'yes',
            ),

            array( 'type' => 'close' ),
        ),
    )
);

return apply_filters( 'yith_wcan_panel_seo_options', $custom_style );