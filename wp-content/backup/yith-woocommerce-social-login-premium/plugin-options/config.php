<?php
/**
 * Config file for HybridAuth Class
 *
 * @author  YITH
 * @package YITH WooCommerce Social Login
 * @version 1.0.0
 */

$slash = defined( 'YWSL_FINAL_SLASH' ) && YWSL_FINAL_SLASH ? '/' : '';

return array(
	'base_url'   => ( get_option( 'ywsl_callback_url' ) == 'root' ) ? site_url() . $slash : YITH_YWSL_URL . 'includes/hybridauth/',
	'providers'  => array(
		// openid providers
		"OpenID" => array(
			"enabled" => true
		),

		'Google' => array(
			'enabled' => ( get_option( 'ywsl_google_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_google_id' ),
				'secret' => get_option( 'ywsl_google_secret' )
			)
		),

		'Facebook' => array(
			'enabled'        => ( get_option( 'ywsl_facebook_enable' ) == 'yes' ) ? true : false,
			'keys'           => array(
				'id'     => get_option( 'ywsl_facebook_id' ),
				'secret' => get_option( 'ywsl_facebook_secret' )
			),
			'trustForwarded' => false
		),

		'Twitter' => array(
			'enabled'      => ( get_option( 'ywsl_twitter_enable' ) == 'yes' ) ? true : false,
			'keys'         => array(
				'key'    => get_option( 'ywsl_twitter_key' ),
				'secret' => get_option( 'ywsl_twitter_secret' )
			),
			'includeEmail' => true,
		),

		'LinkedIn' => array(
			'enabled' => ( get_option( 'ywsl_linkedin_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_linkedin_key' ),
				'secret' => get_option( 'ywsl_linkedin_secret' )
			)
		),

		'Yahoo' => array(
			'enabled' => ( get_option( 'ywsl_yahoo_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_yahoo_key' ),
				'secret' => get_option( 'ywsl_yahoo_secret' )
			)
		),

		'Foursquare' => array(
			'enabled' => ( get_option( 'ywsl_foursquare_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_foursquare_key' ),
				'secret' => get_option( 'ywsl_foursquare_secret' )
			)
		),

		'Live' => array(
			'enabled' => ( get_option( 'ywsl_live_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_live_key' ),
				'secret' => get_option( 'ywsl_live_secret' )
			)
		),

		'Instagram' => array(
			'enabled' => ( get_option( 'ywsl_instagram_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_instagram_key' ),
				'secret' => get_option( 'ywsl_instagram_secret' )
			)
		),

		'Paypal' => array(
			'enabled' => ( get_option( 'ywsl_paypal_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'      => get_option( 'ywsl_paypal_key' ),
				'secret'  => get_option( 'ywsl_paypal_secret' ),
				'sandbox' => ( get_option( 'ywsl_paypal_environment' ) == 'sandbox' ) ? true : false,
			)
		),


		'Tumblr' => array(
			'enabled' => ( get_option( 'ywsl_tumblr_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'key'    => get_option( 'ywsl_tumblr_key' ),
				'secret' => get_option( 'ywsl_tumblr_secret' )
			)
		),

		'Vkontakte' => array(
			'enabled' => ( get_option( 'ywsl_vkontakte_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_vkontakte_id' ),
				'secret' => get_option( 'ywsl_vkontakte_secret' )
			)
		),

		'GitHub' => array(
			'enabled' => ( get_option( 'ywsl_github_enable' ) == 'yes' ) ? true : false,
			'keys'    => array(
				'id'     => get_option( 'ywsl_github_id' ),
				'secret' => get_option( 'ywsl_github_secret' )
			)
		),

		//        'AOL' => array(
		//            'enabled' => ( get_option( 'ywsl_aol_enable' ) == 'yes' ) ? true : false,
		//        )
	),
	'debug_mode' => ( get_option( 'ywsl_enable_log' ) == 'yes' ) ? true : false,
	'debug_file' => YITH_YWSL_DIR . 'logs/log.txt',
);