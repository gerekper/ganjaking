<?php
/**
 * Login settings array
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit;

$settings = [
	'login' => [
		[
			'title' => _x( 'Login options', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_login_options',
		],
		[
			'name'      => _x( 'Popup header text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the popup header section.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_header',
			'default'   => __( 'Proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Popup title', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a title for the login popup. Use [username] as a placeholder for the current username.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_title',
			'default'   => _x( 'Welcome back, [username]!', '[username] is a placeholder for username.', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'          => _x( 'Custom text before the password form', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'          => 'yith-field',
			'yith-type'     => 'textarea-editor',
			'textarea_rows' => 10,
			'desc'          => _x( 'Here you can add a custom text that will be shown before the password form.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'            => 'yith_welrp_popup_login_message',
			'default'       => __( 'Great to see you again! Enter your password to continue.', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Password input label', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter text for password input label.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_input_label',
			'default'   => _x( 'Password:', 'Input password label', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Text for Stay signed in option', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the Stay signed in option under the password field.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_remember_label',
			'default'   => __( 'Stay signed in', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Check Stay signed in by default', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled, the Stay signed in option will be checked by default.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_remember_checked',
			'default'   => 'no',
		],
		[
			'name'      => _x( 'Button label', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter the text for the login button.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_login_button_label',
			'default'   => __( 'Sign in', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_login_options',
		],
	],
];

return apply_filters( 'yith_welrp_panel_settings_login_options', $settings );

