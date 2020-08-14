<?php
/**
 * General plugin settings array
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit;

$settings = [
	'first-step' => [
		[
			'title' => _x( 'First Step Options', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_first_step_settings',
		],
		[
			'name'      => _x( 'Popup header text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the popup header section.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_header',
			'default'   => __( 'Proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Popup title', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Set a title for the popup.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_title',
			'default'   => __( 'But first... login or register!', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Allow username', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled the user can also enter the username to log in. If disabled, only the email address can be used to log in.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_allow_username',
			'default'   => 'yes',
		],
		[
			'name'          => _x( 'Custom text before the form', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'          => 'yith-field',
			'yith-type'     => 'textarea-editor',
			'textarea_rows' => 10,
			'desc'          => _x( 'Here you can add a custom text that will be shown before the form.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'            => 'yith_welrp_popup_message',
			'default'       => '',
		],
		[
			'title'     => _x( 'User input label', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a text for the email/username input field.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_input_label',
			'default'   => _x( 'Email address or username:', 'User login input label', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'title'     => _x( 'Button text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter the text for the button that redirects users to the next step.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_button_label',
			'default'   => __( 'Continue', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_first_step_settings',
		],
		[
			'title' => _x( 'Social Login Options', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_social_login_options',
		],
		[
			'title'            => _x( 'Facebook login', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element-fixed',
			'yith-display-row' => false,
			'id'               => 'yith_welrp_social_login_facebook',
			'value'            => '',
			'default'          => '',
			'elements'         => [
				[
					'title' => _x( 'Facebook App ID', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'  => 'text',
					'desc'  => sprintf( _x( 'Add your Facebook App ID. <a href="%s" target="_blank">Find it ></a>', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ), 'https://developers.facebook.com/docs/apps/?locale=en_US' ),
					'id'    => 'app_id',
				],
				[
					'title' => _x( 'Facebook App Secret', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'  => 'text',
					'desc'  => _x( 'Add your Facebook App Secret', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'    => 'app_secret',
				],
				[
					'title'   => _x( 'Facebook button text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'    => 'text',
					'id'      => 'button_label',
					'default' => __( 'Login with Facebook', 'yith-easy-login-register-popup-for-woocommerce' ),
				],
				[
					'title'        => _x( 'Button background color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'background_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_background_color', '#3c66c4' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_background_color_hover', '#3853a6' ),
						],
					],
				],
				[
					'title'        => _x( 'Button border color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'border_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_border_color', '#3c66c4' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_border_color_hover', '#3853a6' ),
						],
					],
				],
				[
					'title'        => _x( 'Button text color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'text_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_text_color', '#ffffff' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'facebook_text_color_hover', '#ffffff' ),
						],
					],
				],
				[
					'title' => _x( 'Facebook icon', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'  => 'upload',
					'desc'  => _x( 'Upload a custom icon for Facebook button. Recommended size 20x20px (leave empty to use the default one).', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'    => 'icon',
				],
			],
		],
		[
			'title'            => _x( 'Google login', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element-fixed',
			'yith-display-row' => false,
			'id'               => 'yith_welrp_social_login_google',
			'value'            => '',
			'default'          => '',
			'elements'         => [
				[
					'title' => _x( 'Google Client ID', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'  => 'text',
					'desc'  => sprintf( _x( 'Add your Google Client ID. <a href="%s" target="_blank">Find it ></a>', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ), 'https://support.google.com/googleapi/answer/6158849' ),
					'id'    => 'app_id',
				],
				[
					'title'   => _x( 'Google button text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'    => 'text',
					'id'      => 'button_label',
					'default' => __( 'Login with Google', 'yith-easy-login-register-popup-for-woocommerce' ),
				],
				[
					'title'        => _x( 'Button background color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'background_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_background_color', '#cf4332' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_background_color_hover', '#a83a2b' ),
						],
					],
				],
				[
					'title'        => _x( 'Button border color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'border_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_border_color', '#cf4332' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_border_color_hover', '#a83a2b' ),
						],
					],
				],
				[
					'title'        => _x( 'Button text color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'         => 'multi-colorpicker',
					'id'           => 'text_color',
					'colorpickers' => [
						[
							'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'normal',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_text_color', '#ffffff' ),
						],
						[
							'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							'id'      => 'hover',
							'default' => YITH_Easy_Login_Register::get_proteo_default( 'google_text_color_hover', '#ffffff' ),
						],
					],
				],
				[
					'title' => _x( 'Google icon', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'type'  => 'upload',
					'desc'  => _x( 'Upload a custom icon for Google button. Recommended size 20x20px (leave empty to use the default one).', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'    => 'icon',
				],
			],
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_social_login_options',
		],
	],
];

return apply_filters( 'yith_welrp_panel_settings_popup_options', $settings );

