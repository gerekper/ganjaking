<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

    'settings' => apply_filters( 'yith_wcmas_settings_options', array(

            'general_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcmas_settings_general_start'
            ),

            'general_title'    => array(
                'title' => esc_html__( 'General settings', 'yith-multiple-shipping-addresses-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcmas_settings_general_title'
            ),

            'general_enable_mas_on_frontend' => array(
	            'title'             => esc_html__( 'Enabled', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'type'              => 'checkbox',
	            'desc'              => esc_html__( 'Choose whether to enable or disable the Multiple Shipping Addresses features on the front-end.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'id'                => 'ywcmas_enable_mas_on_frontend',
	            'default'           => 'yes'
            ),

            'general_enable_guest_users' => array(
	            'title'             => esc_html__( 'Allow guest users', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'type'              => 'checkbox',
	            'desc'              => esc_html__( 'Choose whether to allow guest users to set multiple shipping addresses or not.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'id'                => 'ywcmas_enable_guest_users',
	            'default'           => 'yes'
            ),

            'general_different_addresses_limit' => array(
	            'title'             => esc_html__( 'Number of different addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'type'              => 'number',
	            'desc'              => esc_html__( 'Choose the number of different addresses a item can be shipped to in the same order.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'id'                => 'ywcmas_different_addresses_limit',
	            'css'               => 'width: 60px',
	            'default'           => '10'
            ),

            'general_search_for_variations' => array(
                'title'             => esc_html__( 'Search for variations', 'yith-multiple-shipping-addresses-for-woocommerce' ),
                'type'              => 'checkbox',
                'desc'              => esc_html__( 'Choose whether to search or not product variations in Exclude products tab', 'yith-multiple-shipping-addresses-for-woocommerce' ),
                'id'                => 'ywcmas_search_for_variations',
                'default'           => 'no'
            ),

            'general_show_weight' => array(
	            'title'             => esc_html__( 'Show package weight', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'type'              => 'checkbox',
	            'desc'              => esc_html__( 'Choose whether to show or hide the weight for every package on the Checkout page.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
	            'id'                => 'ywcmas_show_weight',
	            'default'           => 'no'
            ),

            'general_end' => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcmas_settings_general_end'
            ),

        )
    )
);