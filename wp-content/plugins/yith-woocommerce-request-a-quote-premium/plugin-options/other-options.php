<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$section = array(
	'other_settings'         => array(
		'name' => esc_html__( 'Exclusion List Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_quote_settings',
	),
	'show_btn_exclusion'     => array(
		'name'      => esc_html__( 'Enable Exclusion List', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'By enabling the exclusion list, you can exclude the products you prefer from the plugin behavior. You can add the products you wish to be excluded from Request a Quote features in the new "Exclusion List" tab soon after enabling this option.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_btn_exclusion',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'exclusion_list_setting' => array(
		'name'      => esc_html__( 'Exclusion List Settings', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'From here, you can change the behavior of the "exclusion list".', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_exclusion_list_setting',
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'deps'      => array(
			'id'    => 'ywraq_show_btn_exclusion',
			'value' => 'yes',
		),
		'options'   => array(
			'hide' => esc_html__( 'Hide the button "add to quote" on all products of the exclusion list.', 'yith-woocommerce-request-a-quote' ),
			'show' => esc_html__( 'Show the "add to quote" button on all products of the exclusion list.', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'hide',
	),

	'other_settings_end'     => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_other_settings_end',
	),
	'pdf_tab_settings'       => array(
		'name' => esc_html__( 'PDF Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_pdf_tab_settings',
	),
	'enable_pdf'             => array(
		'name'      => esc_html__( 'Allow creating PDF Documents', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'By checking this option, a new tab named PDF Settings will be enabled. Here, you can customizethe PDF.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_enable_pdf',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'pdf_tab_settings_end'   => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_pdf_tab_settings_end',
	),
);


if ( ! catalog_mode_plugin_enabled() ) {
	$general_settings = array(
		'general_settings'     => array(
			'name' => esc_html__( 'General Settings', 'yith-woocommerce-request-a-quote' ),
			'type' => 'title',
			'id'   => 'ywraq_pdf_tab_settings',
		),
		'hide_add_to_cart'     => array(
			'name'      => esc_html__( 'Hide "Add to cart" button', 'yith-woocommerce-request-a-quote' ),
			'desc'      => esc_html__( 'Hide "Add to cart" button to all products', 'yith-woocommerce-request-a-quote' ),
			'id'        => 'ywraq_hide_add_to_cart',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'hide_price'           => array(
			'name'      => esc_html__( 'Hide price', 'yith-woocommerce-request-a-quote' ),
			'desc'      => esc_html__( 'Hide price to all products', 'yith-woocommerce-request-a-quote' ),
			'id'        => 'ywraq_hide_price',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),
		'general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywraq_general_settings_end',
		),
	);

	$section = array_merge( $section, $general_settings );
}

return array( 'other' => apply_filters( 'ywraq_quote_settings_options', $section ) );
