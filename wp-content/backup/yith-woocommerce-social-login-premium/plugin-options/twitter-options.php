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

$callback_url = YITH_YWSL_URL . 'includes/hybridauth/twitter.php';

return array(

	'twitter' => array(

		'section_twitter_settings' => array(
			'name' => __( 'Twitter settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . $callback_url, 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_twitter'
		),

		'twitter_enable' => array(
			'name'      => __( 'Enable Twitter Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_twitter_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'twitter_key' => array(
			'name'      => __( 'Twitter Key', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_twitter_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'twitter_secret' => array(
			'name'      => __( 'Twitter Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_twitter_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'twitter_icon' => array(
			'name'      => __( 'Twitter Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_twitter_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_twitter_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_twitter_end'
		),

	)
);