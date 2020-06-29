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

	'yahoo' => array(

		'section_yahoo_settings' => array(
			'name' => __( 'Yahoo settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . site_url(), 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_yahoo'
		),

		'yahoo_enable' => array(
			'name'      => __( 'Enable Yahoo Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_yahoo_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'yahoo_key' => array(
			'name'      => __( 'Yahoo Consumer Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_yahoo_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'yahoo_secret' => array(
			'name'      => __( 'Yahoo Consumer Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_yahoo_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'yahoo_icon' => array(
			'name'      => __( 'Yahoo Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_yahoo_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_yahoo_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_yahoo_end'
		),

	)
);