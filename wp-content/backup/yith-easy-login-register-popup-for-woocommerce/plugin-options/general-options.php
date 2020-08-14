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
	'general' => [
		[
			'title' => _x( 'Popup general settings', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_general_settings',
		],
		[
			'title'     => _x( 'Popup size', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => _x( 'Set the size of the popup window in pixel. In our demo we use a 590px width.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_width',
			'default'   => '590',
		],
		[
			'title'     => _x( 'Close popup by clicking on the background', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled, when the user clicks on the overlay background the popup will close.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_close_overlay',
			'default'   => 'yes',
		],
		[
			'title'     => _x( 'Blur background', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => _x( 'If enabled, the page content will be blurred when the popup is opened.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_blur_overlay',
			'default'   => 'yes',
		],
		[
			'title'     => _x( 'Popup animations', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'multi-select',
			'class'     => 'wc-enhanced-select',
			'desc'      => _x( 'Choose the entrance and exit animation effects for the popup opening and closing. Default values are fade in and fade out.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_animation',
			'selects'   => [
				[
					'title'   => _x( 'Entrance animation', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'entrance',
					'options' => [
						[
							'label'   => _x( 'Fading entrances', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'fadeIn'         => _x( 'Fade in', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInUp'       => _x( 'Fade in up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInDown'     => _x( 'Fade in down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInRight'    => _x( 'Fade in right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInLeft'     => _x( 'Fade in left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInUpBig'    => _x( 'Fade in up big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInDownBig'  => _x( 'Fade in down big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInRightBig' => _x( 'Fade in right big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeInLeftBig'  => _x( 'Fade in left big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Bouncing entrances', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'bounceIn'      => _x( 'Bounce in', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceInUp'    => _x( 'Bounce in up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceInDown'  => _x( 'Bounce in down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceInRight' => _x( 'Bounce in right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceInLeft'  => _x( 'Bounce in left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Flippers', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'flipInX' => _x( 'Flip in X', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'flipInY' => _x( 'Flip in Y', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Sliding entrances', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'slideInUp'    => _x( 'Slide in up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideInDown'  => _x( 'Slide in down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideInRight' => _x( 'Slide in right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideInLeft'  => _x( 'Slide in left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Zoom entrances', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'zoomIn'      => _x( 'Zoom in', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomInUp'    => _x( 'Zoom in up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomInDown'  => _x( 'Zoom in down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomInRight' => _x( 'Zoom in right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomInLeft'  => _x( 'Zoom in left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
					],
					'default' => 'fadeIn',
				],
				[
					'title'   => _x( 'Exit animation', 'Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'exit',
					'options' => [
						[
							'label'   => _x( 'Fading exits', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'fadeOut'         => _x( 'Fade out', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutUp'       => _x( 'Fade out up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutDown'     => _x( 'Fade out down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutRight'    => _x( 'Fade out right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutLeft'     => _x( 'Fade out left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutUpBig'    => _x( 'Fade out up big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutDownBig'  => _x( 'Fade out down big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutRightBig' => _x( 'Fade out right big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'fadeOutLeftBig'  => _x( 'Fade out left big', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Bouncing exits', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'bounceOut'      => _x( 'Bounce out', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceOutUp'    => _x( 'Bounce out up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceOutDown'  => _x( 'Bounce out down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceOutRight' => _x( 'Bounce out right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'bounceOutLeft'  => _x( 'Bounce out left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Flippers', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'flipOutX' => _x( 'Flip out X', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'flipOutY' => _x( 'Flip out Y', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Sliding exits', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'slideOutUp'    => _x( 'Slide out up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideOutDown'  => _x( 'Slide out down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideOutRight' => _x( 'Slide out right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'slideOutLeft'  => _x( 'Slide out left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
						[
							'label'   => _x( 'Zoom exits', '[admin]Plugin animation option group', 'yith-easy-login-register-popup-for-woocommerce' ),
							'options' => [
								'zoomOut'      => _x( 'Zoom out', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomOutUp'    => _x( 'Zoom out up', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomOutDown'  => _x( 'Zoom out down', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomOutRight' => _x( 'Zoom out right', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
								'zoomOutLeft'  => _x( 'Zoom out left', '[admin]Plugin animation option label', 'yith-easy-login-register-popup-for-woocommerce' ),
							],
						],
					],
					'default' => 'fadeOut',
				],
			],
		],
		[
			'title'     => _x( 'Close popup icon', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'desc'      => _x( 'Upload a custom icon for the Close button in the popup (X icon by default).', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_popup_close_icon',
			'default'   => '',
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_general_settings',
		],
		[
			'title' => _x( 'Colors', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_welrp_color_customization',
		],
		[
			'title'     => _x( 'Popup background color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith_welrp_popup_bg',
			'default'   => '#ffffff',
		],
		[
			'title'     => _x( 'Popup header background color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith_welrp_popup_header_bg',
			'default'   => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_popup_header_bg', '#ffffff' ),
		],
		[
			'title'     => _x( 'Popup text color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith_welrp_popup_text_color',
			'default'   => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_popup_text_color', '#1a1a1a' ),
		],
		[
			'title'        => _x( 'Popup link colors', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith_welrp_popup_link_color',
			'colorpickers' => [
				[
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'normal',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_popup_link_color[normal]', '#007acc' ),
				],
				[
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'hover',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_popup_link_color[hover]', '#686868' ),
				],
			],
		],
		[
			'title'        => _x( 'Button colors', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => _x( 'Set the button colors (default and hover)', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'           => 'yith_welrp_button_bg_color',
			'colorpickers' => [
				[
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'normal',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_bg_color[normal]', '#a46497' ),
				],
				[
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'hover',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_bg_color[hover]', '#96588a' ),
				],
			],
		],
		[
			'title'        => _x( 'Button border colors', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => _x( 'Set the button border color (default and hover)', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'           => 'yith_welrp_button_br_color',
			'colorpickers' => [
				[
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'normal',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_br_color[normal]', '#a46497' ),
				],
				[
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'hover',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_br_color[hover]', '#96588a' ),
				],
			],
		],
		[
			'title'        => _x( 'Button text colors', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith_welrp_button_lb_color',
			'colorpickers' => [
				[
					'name'    => _x( 'Default color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'normal',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_lb_color[normal]', '#ffffff' ),
				],
				[
					'name'    => _x( 'Hover color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
					'id'      => 'hover',
					'default' => YITH_Easy_Login_Register::get_proteo_default( 'yith_welrp_button_lb_color[hover]', '#ffffff' ),
				],
			],
		],
		[
			'title'     => _x( 'Overlay background color', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith_welrp_overlay_color',
			'default'   => 'rgba(0,0,0,0.5)',
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_color_customization',
		],
	],
];

return apply_filters( 'yith_welrp_panel_general_settings_options', $settings );

