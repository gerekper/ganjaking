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

$callback_url = YITH_YWSL_URL . 'includes/hybridauth/vkontakte.php';

return array(

	'vkontakte' => array(

		'section_vkontakte_settings' => array(
			'name' => __( 'Vkontakte settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . $callback_url, 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_vkontakte'
		),

		'vkontakte_enable' => array(
			'name'      => __( 'Enable Vkontakte Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_vkontakte_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'vkontakte_id' => array(
			'name'      => __( 'Vkontakte App ID', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_vkontakte_id',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'vkontakte_secret' => array(
			'name'      => __( 'Vkontakte Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_vkontakte_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'vkontakte_icon' => array(
			'name'      => __( 'Vkontakte Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_vkontakte_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_vkontakte_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_vkontakte_end'
		),

	)
);