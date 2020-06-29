<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
$function_product = YITH_Google_Product_Feed()->product_function;

$is_panel_page = isset($_GET['page']) && isset($_GET['tab']) && 'yith_wcgpf_panel' === $_GET['page'] && 'google-product-information' === $_GET['tab'];

return array(

    'google-product-information' => apply_filters( 'yith_wcgpf_google_product_information_options', array(

            //////////////////////////////////////////////////////
            'google_tab_google_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcact_settings_tab_auction_start'
            ),

            'google_tab_google_options_title'    => array(
                'title' => esc_html_x( 'Google general feed fields', 'Panel: page title', 'yith-google-product-feed-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcgpf_tab_google_title',
            ),
            'google_tab_google_options_brand' => array(
                'title'   => esc_html_x( 'Brand', 'Admin option: Brand: YITH ', 'yith-google-product-feed-for-woocommerce' ),
                'type'    => 'text',
                'id'      => 'yith_wcgpf_tab_google_brand',
                'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
            ),
            'google_tab_google_options_condition' => array(
                'title'   => esc_html_x( 'Condition', 'Admin option: Condition: New', 'yith-google-product-feed-for-woocommerce' ),
                'type'    => 'select',
                'id'      => 'yith_wcgpf_tab_google_condition',
                'options' => $is_panel_page ? $function_product->condition() : array(),
                'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
            ),
            'google_tab_google_google_product_category' => array(
                'title'   => esc_html_x( 'Google category', 'Admin option: Google category: Animal & pet suplies ', 'yith-google-product-feed-for-woocommerce' ),
                'type'    => 'select',
                'id'      => 'yith_wcgpf_tab_google_google_product_category',
                'options' => $is_panel_page ? $function_product->google_category() : array(),
                'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
            ),
            'google_tab_google_options_adult' => array(
                'title'   => esc_html_x( 'Adult', 'Admin option: Condition: New', 'yith-google-product-feed-for-woocommerce' ),
                'type'    => 'select',
                'id'      => 'yith_wcgpf_tab_google_adult',
                'options' => $is_panel_page ? $function_product->adult() : array(),
                'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
            ),
            
            'google_tab_google_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcact_settings_tab_auction_end'
            ),

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        )
    )
);
