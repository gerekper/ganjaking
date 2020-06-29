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

	'tumblr' => array(

		'section_tumblr_settings' => array(
			'name' => __( 'Tumblr settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=Tumblr', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_tumblr'
		),

		'tumblr_enable' => array(
			'name'      => __( 'Enable Tumblr Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_tumblr_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'tumblr_key' => array(
			'name'      => __( 'Tumblr Consumer Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_tumblr_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'tumblr_secret' => array(
			'name'      => __( 'Tumblr Secret Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_tumblr_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'tumblr_icon' => array(
			'name'      => __( 'Tumblr Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_tumblr_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_tumblr_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_tumblr_end'
		),

	)
);