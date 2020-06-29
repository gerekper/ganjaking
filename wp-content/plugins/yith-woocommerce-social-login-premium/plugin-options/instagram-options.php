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

	'instagram' => array(

		'section_instagram_settings' => array(
			'name' => __( 'Instagram settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=Instagram', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_instagram'
		),

		'instagram_enable' => array(
			'name'      => __( 'Enable Instagram Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_instagram_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'instagram_key' => array(
			'name'      => __( 'Instagram Client ID', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_instagram_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'instagram_secret' => array(
			'name'      => __( 'Instagram Client Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_instagram_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'instagram_icon' => array(
			'name'      => __( 'Instagram Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_instagram_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_instagram_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_instagram_end'
		),

	)
);