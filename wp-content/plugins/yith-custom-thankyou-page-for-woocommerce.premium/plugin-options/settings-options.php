<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

return array(

	'settings' => apply_filters(
		'yith_ctpw_settings_options',
		array(

			// general.
			'settings_options_start'                      => array(
				'type' => 'sectionstart',
			),

			'settings_options_title'                      => array(
				'title' => esc_html_x( 'General settings', 'Panel: page title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'settings_enable_custom_thankyou_page'        => array(
				'title'     => esc_html_x( 'Enable Custom Thank You Page', 'Admin option: Enable plugin', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to enable the plugin features', 'Admin option description: Enable plugin', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable',
				'default'   => 'yes'
			),

			'settings_select_custom_thankyou_page_or_url' => array(
				'title'     => esc_html_x( 'Redirect to a Custom Page or External URL', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'id'        => 'yith_ctpw_general_page_or_url',
				'options'   => array(
					'ctpw_page' => esc_html__( 'Custom Wordpress Page', 'yith-custom-thankyou-page-for-woocommerce' ),
					'ctpw_url'  => esc_html__( 'External URL', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'default'   => 'ctpw_page',
				'class'     => 'yith_ctpw_general_page_or_url',
				'css'       => 'min-width:300px;',
				'desc'      => esc_html__( 'Select the General Thank You Page or External URL for all products', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

			'settings_select_custom_thankyou_page'        => array(
				'title'       => esc_html_x( 'Select the General Page', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'        => 'yctpw_single_select_page',
				'id'          => 'yith_ctpw_general_page',
				'sort_column' => 'title',
				'class'       => 'wc-enhanced-select-nostd',
				'css'         => 'min-width:300px;',
				'desc'        => esc_html__( 'Select the General Thank You Page for all products', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

			'settings_select_custom_thankyou_page_url'    => array(
				'title' => esc_html_x( 'Set the Url', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'text',
				'id'    => 'yith_ctpw_general_page_url',
				'class' => 'yith_ctpw_general_page_url',
				'css'   => 'min-width:300px;',
				'desc'  => esc_html__( 'Set the URL to redirect. Write full url for ex: https://yithemes.com/', 'yith-custom-thankyou-page-for-woocommerce' ),
			),


			'setting_custom_thankyou_page_custom_style'   => array(
				'title'     => esc_html_x( 'Custom CSS', 'Admin option: Custom Style', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'textarea',
				'id'        => 'yith_ctpw_custom_style',
			),

			'settings_options_end'                        => array(
				'type' => 'sectionend',
			),
		)
	),
);

