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

	'settings' => apply_filters( 'yith_wcfm_settings_options', array(

			'settings_options_start' => array(
				'type' => 'sectionstart',
			),

			'settings_options_title' => array(
				'type'  => 'title',
				'title' => __( 'General Settings', 'yith-frontend-manager-for-woocommerce' ),
			),

			'settings_options_frontend_manager_page' => array(
				'title'    => __( '"Frontend Manager" page', 'yith-frontend-manager-for-woocommerce' ),
				'id'       => 'yith_wcfm_main_page_id',
				'type'     => 'single_select_page',
				'default'  => 0,
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:350px;',
				'desc'     => _x( 'Set the page where to add the "Frontend Manager Shortcodes"', '[Admin] Option description',
                    'yith-frontend-manager-for-woocommerce' ),
				'desc_tip' => sprintf( '%s:<br/>[%s]', __( 'Page contents', 'yith-frontend-manager-for-woocommerce' ), yith_wcfm_get_main_shortcode_name() ),

			),

			'settings_options_end' => array(
				'type' => 'sectionend',
			),

            'settings_permalinks_start' => array(
                'type' => 'sectionstart',
            ),

            'settings_permalinks_title' => array(
                'type' => 'title',
                'title' => __('Permalinks Settings', 'yith-frontend-manager-for-woocommerce'),
            ),

            'settings_permalinks_frontend_manager_page' => array(
                'title' => __('Flush permalinks', 'yith-frontend-manager-for-woocommerce'),
                'id' => 'yith_wcfm_flush_rewrite_rules',
                'type' => 'yith_wcfm_button',
                'name' => _x( 'Flush Permalinks', '[Admin] button label. Please use a short string', 'yith-frontend-manager-for-woocommerce' ),
                'desc' => _x(' If after activating the plugin or after editing some options, you get a 404 error on the frontend, please, flush permalinks and update the page.', '[Admin] Option description', 'yith-frontend-manager-for-woocommerce'),
            ),

            'settings_permalinks_end' => array(
                'type' => 'sectionend',
            ),

			'settings_unauthorized_start' => array(
				'type' => 'sectionstart',
			),

			'settings_unauthorized_title' => array(
				'type'  => 'title',
				'title' => __( 'Access control', 'yith-frontend-manager-for-woocommerce' ),
			),

			'settings_options_not_authorized_title' => array(
				'title'   => __( 'Not authorized title', 'yith-frontend-manager-for-woocommerce' ),
				'id'      => 'yith_wcfm_not_authorized_title',
				'type'    => 'text',
				'css'     => 'min-width:350px;',
				'default' => _x( 'Restricted Area', '[Admin Panel]: Unauthorized access page title', 'yith-frontend-manager-for-woocommerce' ),
			),

			'settings_options_not_authorized_message' => array(
				'title'   => __( 'Not authorized message', 'yith-frontend-manager-for-woocommerce' ),
				'id'      => 'yith_wcfm_not_authorized_message',
				'type'    => 'textarea',
				'css'     => 'min-width:350px;height: 100px;resize: none;',
				'default' => _x( 'You do not have sufficient permissions to access this section', '[Admin Panel]: Unauthorized access message',
                    'yith-frontend-manager-for-woocommerce' ),
			),

			'settings_unauthorized_end' => array(
				'type' => 'sectionend',
			),
		), 
		'settings_unauthorized_end'
	)
);