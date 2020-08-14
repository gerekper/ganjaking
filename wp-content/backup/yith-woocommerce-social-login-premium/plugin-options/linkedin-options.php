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

	'linkedin' => array(

		'section_linkedin_settings' => array(
			'name' => __( 'LinkedIn settings', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=LinkedIn', 'yith-woocommerce-social-login' ),
			'id'   => 'ywsl_section_linkedin'
		),

		'linkedin_enable' => array(
			'name'      => __( 'Enable LinkedIn Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_linkedin_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'linkedin_api_key' => array(
			'name'      => __( 'LinkedIn Api Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_linkedin_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'linkedin_secret' => array(
			'name'      => __( 'LinkedIn secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_linkedin_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'linkedin_icon' => array(
			'name'      => __( 'LinkedIn Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_linkedin_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_linkedin_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_linkedin_end'
		)
	)
);