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

$firebase_url = '<a target="_blank" href="https://console.firebase.google.com/">https://console.firebase.google.com/</a>';
$app_secret   = '';

if ( get_option( 'ylc_authentication_method' ) == '' && ! ylc_is_setup_complete() ) {
	$app_secret = array(
		'name'              => esc_html__( 'Firebase App Secret', 'yith-live-chat' ),
		'desc'              => '',
		'id'                => 'firebase-appsecret',
		'type'              => 'text',
		'std'               => ylc_get_default( 'firebase-appsecret' ),
		'custom_attributes' => array(
			'disabled' => 'disabled',
			'style'    => 'width: 100%'
		),
	);
}

return array(
	'general' => array(
		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'General Settings', 'yith-live-chat' ),
				'type' => 'title'
			),
			array(
				'type' => 'close'
			)
		),
		/* =================== END SKIN =================== */

		/* =================== GENERAL =================== */
		'settings' => array(
			array(
				'name' => esc_html__( 'Enable Live Chat', 'yith-live-chat' ),
				'desc' => esc_html__( 'Activate/Deactivate the live chat features. ', 'yith-live-chat' ),
				'id'   => 'plugin-enable',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'plugin-enable' )
			),
			array(
				'name'              => esc_html__( 'Firebase Project ID', 'yith-live-chat' ),
				'desc'              => esc_html__( 'ID of your Firebase project. If you don\'t have one, get a free Firebase application here: ', 'yith-live-chat' ) . $firebase_url,
				'id'                => 'firebase-appurl',
				'type'              => 'text',
				'class'             => 'ylc-custom-text',
				'std'               => ylc_get_default( 'firebase-appurl' ),
				'custom_attributes' => array(
					'required' => 'required',
					'style'    => 'width: 200px'
				),
			),
			$app_secret,
			array(
				'name'              => esc_html__( 'Firebase API Key', 'yith-live-chat' ),
				'desc'              => esc_html__( 'Paste here the API Key of your Firebase application', 'yith-live-chat' ),
				'id'                => 'firebase-apikey',
				'type'              => 'text',
				'std'               => ylc_get_default( 'firebase-apikey' ),
				'custom_attributes' => array(
					'required' => 'required',
					'style'    => 'width: 100%'
				),
			),
			array(
				'name'              => esc_html__( 'Firebase Private Key', 'yith-live-chat' ),
				'desc'              => esc_html__( 'Paste here the Private Key of your Firebase application', 'yith-live-chat' ),
				'id'                => 'firebase-private-key',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'firebase-private-key' ),
				'custom_attributes' => array(
					'required' => 'required',
					'style'    => 'width: 100%'
				),
			),
			array(
				'name' => esc_html__( 'Firebase Database Rules', 'yith-live-chat' ),
				'desc' => esc_html__( 'Copy and paste in the Rules of your Firebase application', 'yith-live-chat' ),
				'id'   => 'firebase-rules',
				'type' => 'firebase-rules',
				'std'  => file_get_contents( YLC_DIR . 'assets/ylc-rules.json' ),
			),
		)
	)
);