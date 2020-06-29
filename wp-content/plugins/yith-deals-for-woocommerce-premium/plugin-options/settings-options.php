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

    'settings' => apply_filters( 'yith_wcdls_settings_options', array(

            //////////////////////////////////////////////////////

            'yith_wcdls_settings_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcdls_settings_tab_start'
            ),

            'yith_wcdls_settings_options_title'    => array(
                'title' => esc_html_x( 'General settings', 'Panel: page title', 'yith-deals-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcdls_settings_tab_general_settings'
            ),
            'yith_wcdls_settings_box_size' => array(
                'title'   => esc_html_x( 'Popup Size', 'Admin option: Popup Size', 'yith-deals-for-woocommerce' ),
                'type'    => 'yith_wcdls_box_size',
                'default'   => array(
                    'width'     => '700',
                    'height'    => '700'
                ),
                'desc'    => esc_html_x( 'Set popup size.', 'Admin option description: Set popup size.', 'yith-deals-for-woocommerce' ),
                'id'      => 'yith-wcdls-box-size-pixel',
            ),

            'settings_tab_deals_show_config_shop_managers' => array(
	            'title'     => esc_html_x( 'Show Deals option for shop manager', 'Admin option: Show Payment Restriction option for shop managers', 'yith-deals-for-woocommerce' ),
	            'type'      => 'yith-field',
	            'yith-type' => 'onoff',
	            'desc'      => esc_html_x( 'Check this option to manage deals option for shop manager role', 'Admin option description: Check this option to manage payment restriction option for shop manager role', 'yith-deals-for-woocommerce' ),
	            'id'        => 'yith_wcdls_settings_tab_allow_shop_manager',
	            'default'   => 'no'
            ),

            'yith_wcdls_settings_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcdls_settings_tab_end'
            ),

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        )
    )
);
