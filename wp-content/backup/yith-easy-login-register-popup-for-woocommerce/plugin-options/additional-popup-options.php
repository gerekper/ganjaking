<?php
/**
 * Additional Popup settings array
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit;

$settings = [
	'additional-popup' => [
		[
			'title' => _x( 'Additional Login/Register Popup', '[admin]Settings section title', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => _x( 'You can also take our login/register popup out of the checkout process and place it anywhere on your site using CSS selectors. As a selector, you can either use the ID or the class of the element that will trigger the popup. Make sure that the ID is preceded by a hashtag # (e.g. #elementID) and the class by a dot . (e.g. .elementClass). You can also specify more elements and separate them with a comma (e.g. #element1, #element2).', '[admin]Settings section description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'    => 'yith_welrp_additional_popup_options',
		],
		[
			'name'      => _x( 'Enter the ID or CLASS of the element that will open the popup', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => '',
			'id'        => 'yith_welrp_additional_popup_selectors',
			'default'   => '',
		],
		[
			'type'      => 'yith-field',
			'yith-type' => 'simple-text',
			'desc'      => _x( 'This popup will use the same content and settings of the checkout popup. You can change the following texts to make it simpler and avoid any reference to the checkout process.', '[admin]Settings section description', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Popup title', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => _x( 'Enter a title for the popup.', '[admin]Plugin option description', 'yith-easy-login-register-popup-for-woocommerce' ),
			'id'        => 'yith_welrp_additional_popup_title',
			'default'   => __( 'Login or Register', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Register button text', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => '',
			'id'        => 'yith_welrp_additional_popup_register_button',
			'default'   => __( 'Register', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'name'      => _x( 'Save password button in Recover password step', '[admin]Plugin option label', 'yith-easy-login-register-popup-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => '',
			'id'        => 'yith_welrp_additional_popup_set_password_button',
			'default'   => __( 'Save password and access', 'yith-easy-login-register-popup-for-woocommerce' ),
		],
		[
			'type' => 'sectionend',
			'id'   => 'yith_welrp_additional_popup_options',
		],
	],
];

return apply_filters( 'yith_welrp_panel_settings_additional_popup_options', $settings );

