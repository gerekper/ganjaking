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

if ( ywsl_check_wpengine() ) {
	$callback_url = site_url() . '/?hauth_done=Facebook';
} else {
	$callback_url = YITH_YWSL_URL . 'includes/hybridauth/facebook.php';
}

return array(

	'facebook' => array(

		'section_facebook_settings' => array(
			'name' => __( 'Facebook settings', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'desc' => __( '<strong>Valid OAuth Redirect URI</strong>: ' . $callback_url, 'yith-woocommerce-social-login' ),
			'id'   => 'ywsl_section_facebook'
		),

		'facebook_enable' => array(
			'name'      => __( 'Enable Facebook Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_facebook_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'facebook_id' => array(
			'name'      => __( 'Facebook App ID', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_facebook_id',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'facebook_secret' => array(
			'name'      => __( 'Facebook Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_facebook_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'facebook_icon' => array(
			'name'      => __( 'Facebook Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_facebook_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_facebook_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_facebook_end'
		),

	)
);