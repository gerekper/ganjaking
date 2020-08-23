<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

$options_installed = array(
	'wc_settings_tab_recapcha_site_key',
	'wc_settings_tab_recapcha_secret_key',
	'wc_settings_tab_recapcha_error_msg_captcha_blank',
	'wc_settings_tab_recapcha_error_msg_captcha_no_response',
	'wc_settings_tab_recapcha_error_msg_captcha_invalid',
	'i13_recapcha_enable_on_signup',
	'i13_recapcha_signup_title',
	'i13_recapcha_signup_theme',
	'i13_recapcha_signup_size',
		'i13_recapcha_disable_submitbtn_woo_signup',
	'i13_recapcha_enable_on_login',
		'i13_recapcha_disable_submitbtn_woo_login',
	'i13_recapcha_login_title',
	'i13_recapcha_login_theme',
	'i13_recapcha_login_size',
	'i13_recapcha_enable_on_lostpassword',
	'i13_recapcha_lostpassword_title',
	'i13_recapcha_lostpassword_theme',
	'i13_recapcha_lostpassword_size',
		'i13_recapcha_disable_submitbtn_woo_lostpassword',
		'i13_recapcha_disable_submitbtn_logincheckout',
		'i13_recapcha_disable_submitbtn_guestcheckout',
		'i13_recapcha_disable_submitbtn_wp_login',
	'i13_recapcha_enable_on_guestcheckout',
	'i13_recapcha_enable_on_logincheckout',
	'i13_recapcha_checkout_timeout',
	'i13_recapcha_guestcheckout_title',
	'i13_recapcha_guestcheckout_refresh',
	'i13_recapcha_guestcheckout_theme',
	'i13_recapcha_guestcheckout_size',
	'i13_recapcha_enable_on_wplogin',
	'i13_recapcha_wplogin_title',
	'i13_recapcha_wplogin_theme',
	'i13_recapcha_wplogin_size',
	'i13_recapcha_enable_on_wpregister',
	'i13_recapcha_wpregister_title',
	'i13_recapcha_wpregister_theme',
	'i13_recapcha_wpregister_size',
	'i13_recapcha_enable_on_wplostpassword',
	'i13_recapcha_wplostpassword_title',
	'i13_recapcha_wplostpassword_theme',
	'i13_recapcha_wplostpassword_size',
	'i13_recapcha_no_conflict',
		'i13_recapcha_enable_on_addpaymentmethod',
		'i13_recapcha_addpaymentmethod_title',
		'i13_recapcha_addpaymentmethod_theme',
		'i13_recapcha_addpaymentmethod_size',
		'i13_recapcha_disable_submitbtn_paymentmethod',
		'i13_recapcha_disable_submitbtn_wp_register',
		'i13_recapcha_disable_submitbtn_wp_lost_password',
				'i13_recapcha_hide_label_signup',
				'i13_recapcha_hide_label_login',
				'i13_recapcha_hide_label_lostpassword',
				'i13_recapcha_hide_label_checkout',
				'i13_recapcha_hide_label_wplogin',
				'i13_recapcha_hide_label_addpayment',
				'i13_recapcha_hide_label_wpregister',
				'i13_recapcha_hide_label_wplostpassword'


);

foreach ($options_installed as $opt) {
	
	delete_option($opt);
}
