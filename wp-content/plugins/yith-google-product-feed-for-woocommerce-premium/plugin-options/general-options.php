<?php

return array(

    'general' => apply_filters( 'yith_wcgpf_general_options', array(

            //////////////////////////////////////////////////////

            'general_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcgpf_general_options_start'
            ),

            'general_options_title'    => array(
                'title' => esc_html_x( 'General settings', 'Panel: page title', 'yith-google-product-feed-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcgpf_general_options_title'
            ),
            'general_options_display_tax' => array(
                'title'   => esc_html_x( 'Show price including taxes based on store settings', 'Admin option: Show price including taxes based on store settings', 'yith-google-product-feed-for-woocommerce' ),
                'type'    => 'checkbox',
                'desc'    => esc_html_x( 'Check this option if you want to show price with tax included depending on store settings', 'Admin option description: Check this option if you want to show price with tax included depending on store settings', 'yith-google-product-feed-for-woocommerce' ),
                'id'      => 'yith_wcgpf_general_options_display_tax',
                'default' => 'no'
            ),

            'general_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcgpf_general_options_end'
            ),

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        )
    )
);