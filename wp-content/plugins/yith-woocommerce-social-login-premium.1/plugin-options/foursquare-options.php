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

	'foursquare' => array(

		'section_foursquare_settings' => array(
			'name' => __( 'Foursquare settings', 'yith-woocommerce-social-login' ),
			'desc' => __( '<strong>Callback URL</strong>: ' . YITH_WC_Social_Login()->get_base_url() . '?hauth.done=Foursquare', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_foursquare'
		),

		'foursquare_enable' => array(
			'name'      => __( 'Enable Foursquare Login', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_foursquare_enable',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'foursquare_key' => array(
			'name'      => __( 'Foursquare Client ID', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_foursquare_key',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'foursquare_secret' => array(
			'name'      => __( 'Foursquare Client Secret', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_foursquare_secret',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'foursquare_icon' => array(
			'name'      => __( 'Foursquare Icon', 'yit' ),
			'desc'      => '',
			'id'        => 'ywsl_foursquare_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'section_foursquare_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_foursquare_end'
		),

	)
);