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

	'settings' => array(

		'section_general_settings' => array(
			'name' => __( 'General settings', 'yith-woocommerce-social-login' ),
			'type' => 'title',
			'id'   => 'ywsl_section_general'
		),

		'redirect_url' => array(
			'name'      => __( 'After login, redirect user to:', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_redirect_url',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'auto'      => __( 'Auto - Take user back to the page he/she was in', 'yith-woocommerce-social-login' ),
				'cart'      => __( 'Cart Page', 'yith-woocommerce-social-login' ),
				'checkout'  => __( 'Checkout', 'yith-woocommerce-social-login' ),
				'myaccount' => __( 'My Account', 'yith-woocommerce-social-login' ),
				'shop'      => __( 'Shop', 'yith-woocommerce-social-login' ),
				'custom'    => __( 'Custom URL (add below)', 'yith-woocommerce-social-login' ),
			),
			'css'       => 'min-width:300px',
			'desc_tip'  => false,
		),

		'redirect_url_custom' => array(
			'name'      => __( 'Custom URL redirect', 'yith-woocommerce-social-login' ),
			'desc'      => __( 'Paste the URL of the page for redirect', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_redirect_custom_url',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'social_label' => array(
			'name'      => __( 'Label', 'yith-woocommerce-social-login' ),
			'desc'      => __( 'Edit content of the label displayed above social login buttons', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_social_label',
			'default'   => __( 'Login with:', 'yith-woocommerce-social-login' ),
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'social_label_checkout' => array(
			'name'      => __( 'Description in checkout page', 'yith-woocommerce-social-login' ),
			'desc'      => __( 'Edit content of the description in checkout page', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_social_label_checkout',
			'default'   => __( 'Social Sign In', 'yith-woocommerce-social-login' ),
			'type'      => 'yith-field',
			'yith-type' => 'text'
		),

		'social_networks' => array(
			'name' => __( 'List of Social Networks', 'yith-woocommerce-social-login' ),
			'desc' => __( 'Drag and Drop content for sort social login buttons', 'yith-woocommerce-social-login' ),
			'id'   => 'ywsl_social_networks',
			'type' => 'ywsl_social_networks'
		),

		'callback_url' => array(
			'name'      => __( 'Callback Url', 'yith-woocommerce-social-login' ),
			'desc'      => __( 'Choose if the url of callback is the root of domain or the url of the library, if you change all social callback urls must be changed', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_callback_url',
			'options'   => apply_filters( 'ywsl_callback_url_list', array(
				'hybrid' => YITH_YWSL_URL . 'includes/hybridauth/',
				'root'   => site_url()
			) ),
			'default'   => 'root',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
		),

		'enable_log' => array(
			'name'      => __( 'Enable Log', 'yith-woocommerce-social-login' ),
			'desc'      => sprintf( __( 'If enabled, you can view log details in %s', 'yith-woocommerce-social-login' ), '<a target="_blank" href="' . YITH_YWSL_URL . 'logs/log.txt">' . YITH_YWSL_URL . 'logs/log.txt</a>' ),
			'id'        => 'ywsl_enable_log',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_general_end'
		),


		'page_options' => array(
			'title' => __( 'Show login buttons in:', 'yith-woocommerce-social-login' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'ywsl_page_options'
		),

		'show_in_checkout' => array(
			'title'     => __( 'Checkout', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_checkout',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'show_in_myaccount_login' => array(
			'title'     => __( 'My Account Login Form', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_my_account_login_form',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'show_in_register_form' => array(
			'title'     => __( 'My Account Registration Form', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_my_account_register_form',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'show_in_comments' => array(
			'title'     => __( 'Show before post comments', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_comments',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'show_in_comments_after_form' => array(
			'title'     => __( 'Show after comment form', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_comments_after_form',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'show_in_wp_login' => array(
			'title'     => __( 'WordPress Login', 'yith-woocommerce-social-login' ),
			'id'        => 'ywsl_show_in_wp_login',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'section_page_options_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_section_page_options_end'
		),

		'myaccount_options' => array(
			'title' => __( 'My Account Options:', 'yith-woocommerce-social-login' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'ywsl_myaccount_options'
		),

		'ywsl_show_list' => array(
			'name'      => __( 'Show Social Connection list in My Account:', 'yith-woocommerce-social-login' ),
			'desc'      => '',
			'id'        => 'ywsl_myaccount_show_list',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'none'   => __( 'None', 'yith-woocommerce-social-login' ),
				'before' => __( 'Before Recent Orders', 'yith-woocommerce-social-login' ),
				'after'  => __( 'At the end of page', 'yith-woocommerce-social-login' ),

			),
			'default'   => 'before',
			'css'       => 'min-width:300px'
		),

		'myaccount_options_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywsl_myaccount_options_end'
		),

	)
);