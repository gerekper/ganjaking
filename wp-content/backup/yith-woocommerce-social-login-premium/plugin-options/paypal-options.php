<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


return array(

	'paypal' => array(

		'section_paypal_settings' => array(
			'name' => __( 'Paypal settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=Paypal', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_paypal'
		),

		'paypal_enable' => array(
			'name'      => __( 'Enable Paypal Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_paypal_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'paypal_key' => array(
			'name'      => __( 'Paypal Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_paypal_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'paypal_secret' => array(
			'name'      => __( 'Paypal Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_paypal_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'paypal_icon' => array(
			'name'      => __( 'Paypal Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_paypal_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'paypal_environment' => array(
			'name'      => __( 'Environment', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_paypal_environment',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'sandbox' => __( 'Sandbox', 'yith-woocommerce-social-login' ),
				'live'    => __( 'Live', 'yith-woocommerce-social-login' ),
			),
			'default'   => 'before',
			'css'       => 'min-width:300px'
		),


		'section_paypal_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_paypal_end'
		),

	)
);