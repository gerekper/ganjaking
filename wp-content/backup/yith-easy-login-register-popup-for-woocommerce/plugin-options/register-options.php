<?php
/**
 * Register settings array
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit;

$settings = [
	'register' => [
		[
			'title' => _x( 'Register options', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_register_options',
		],
		[
			'name'      => _x( 'Popup header text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the popup header section.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_header',
			'default'   => __( 'Proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Popup title', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a title for the popup.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_title',
			'default'   => __( 'You are new here. Create your account!', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'          => _x( 'Custom text before password form', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'          => 'yith-field',
			'yith-type'     => 'textarea-editor',
			'textarea_rows' => 10,
			'desc'          => _x( 'Here you can add a custom text that will be shown before the password form. Use [blogname] as placeholder for blog name.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'            => 'yith_welrp_popup_register_message',
			'default'       => __( 'It seems you don\'t have an account on [blogname] yet. But don\'t worry, you can create one and then complete your order.', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Password input label', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the password input label.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_password_label',
			'default'   => _x( 'Set a password for this account:', 'Input password label', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Repeat password option', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled, the user will be asked to enter the password twice.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_repeat_password',
			'default'   => 'no',
		],
		[
			'name'      => _x( 'Repeat password input label', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the Repeat password input label.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_repeat_password_label',
			'default'   => _x( 'Repeat password:', 'Input repeat password label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'deps'      => [
				'id'    => 'yith_welrp_popup_register_repeat_password',
				'value' => 'yes',
				'type'  => 'hide',
			],
		],
		[
			'name'      => _x( 'Password strength check', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled, an additional password strength check will be added.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_password_strength',
			'default'   => 'yes',
		],
		[
			'name'      => _x( 'Privacy policy checkbox', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled a Privacy Policy checkbox will be shown.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_policy_enabled',
			'default'   => 'no',
		],
		[
			'name'      => _x( 'Privacy policy text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the Privacy Policy checkbox. Use [terms] as placeholder for the Terms and Conditions page and [privacy_policy] as a placeholder for the Privacy Policy page.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_policy_label',
			'default'   => __( 'I have read and accepted your [terms] and [privacy_policy]', 'yith-easy-login-register-popup-for-woocommerce' ),
			'deps'      => [
				'id'    => 'yith_welrp_popup_register_policy_enabled',
				'value' => 'yes',
				'type'  => 'hide',
			],
		],
		[
			'name'      => _x( 'Check Privacy Policy by default', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled the Privacy Policy option will be checked by default.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_policy_checked',
			'default'   => 'no',
			'deps'      => [
				'id'    => 'yith_welrp_popup_register_policy_enabled',
				'value' => 'yes',
				'type'  => 'hide',
			],
		],
		[
			'name'      => _x( 'Button text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Set the label for the register popup button.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_button_label',
			'default'   => __( 'Register and proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_register_options',
		],
		[
			'title' => _x( 'Google reCaptcha', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_register_captcha_options',
		],
		[
			'name'      => _x( 'Enable Google reCaptcha', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'Add reCaptcha (v2) verification to register form.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_register_enable_recaptcha',
			'default'   => 'no',
		],
		[
			'title'     => _x( 'reCaptcha Public Key', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith_welrp_popup_register_recaptcha_public_key',
			'default'   => '',
			'deps'      => [
				'id'    => 'yith_welrp_popup_register_enable_recaptcha',
				'value' => 'yes',
				'type'  => 'hide',
			],
		],
		[
			'title'     => _x( 'reCaptcha Private Key', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith_welrp_popup_register_recaptcha_private_key',
			'default'   => '',
			'deps'      => [
				'id'    => 'yith_welrp_popup_register_enable_recaptcha',
				'value' => 'yes',
				'type'  => 'hide',
			],
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_register_captcha_options',
		],
	],
];

return apply_filters( 'yith_welrp_panel_settings_register_options', $settings );

