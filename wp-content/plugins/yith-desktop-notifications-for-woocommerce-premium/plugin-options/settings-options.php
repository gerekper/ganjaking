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

    'settings' => apply_filters( 'yith_wcdn_settings_options', array(

            'settings_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcdn_settings_options_start'
            ),

            'settings_options_title'    => array(
                'title' => esc_html_x( 'General settings', 'Panel: page title', 'yith-desktop-notifications-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcdn_settings_options_title'
            ),

            'settings_option_desktop_notification_check_new_notification' => array(
                'title'   => esc_html_x( 'Check new notifications', 'Admin option: Check new notifications', 'yith-desktop-notifications-for-woocommerce' ),
                'type'    => 'number',
                'desc'    => esc_html_x( 'Number of seconds to pass before checking for new notifications', 'Admin option description: number of days', 'yith-desktop-notifications-for-woocommerce' ),
                'id'      => 'yith_wcdn_settings_check_new_notification',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '1'
                ),
                'default'           => '10'
            ),
            'settings_option_desktop_notification_looping_sound' => array(
                'title'   => esc_html_x( 'Looping sound', 'Admin option: Looping sound', 'yith-desktop-notifications-for-woocommerce' ),
                'type'    => 'checkbox',
                'desc'    => esc_html_x( 'Check this option to set a looping sound that will stop when the notification is closed', 'Admin option description: Check this option to set a looping sound that will stop when the notification is closed', 'yith-desktop-notifications-for-woocommerce' ),
                'id'      => 'yith_wcdn_settings_looping_sound',
                'default'           => 'no'
            ),
            
            'settings_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yith_wcdn_settings_options_end'
            ),


        )
    )
);
