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

	'github' => array(

		'section_github_settings' => array(
			'name' => __( 'GitHub settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=GitHub', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_github'
		),

		'github_enable' => array(
			'name'      => __( 'Enable GitHub Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_github_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'github_key' => array(
			'name'      => __( 'GitHub Client ID', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_github_id',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'github_secret' => array(
			'name'      => __( 'GitHub Client Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_github_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'github_icon' => array(
			'name'      => __( 'GitHub Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_github_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_github_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_github_end'
		),

	)
);