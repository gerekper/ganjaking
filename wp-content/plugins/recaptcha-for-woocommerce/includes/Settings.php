<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('I13_WooCommerce_Settings_Page')) :

	class I13_WooCommerce_Settings_Page extends WC_Settings_Page {
	

		// Your class and your code / logic 

		public function __construct() {

			$this->id = 'i13_woo_recaptcha';
			$this->label = __('reCaptcha', 'recaptcha-for-woocommerce');

			add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
			add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
			add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));

			// only add this if you need to add sections for your settings tab
			add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
		}

		public function get_sections() {

			$sections = array(
			'' => __('General Settings', 'recaptcha-for-woocommerce'),
			'signup' => __('Woo Registration Captcha', 'recaptcha-for-woocommerce'),
			'login' => __('Woo Login Captcha', 'recaptcha-for-woocommerce'),
			'forgotpassword' => __('Woo Lost Password Captcha', 'recaptcha-for-woocommerce'),
			'guestcheckout' => __('Woo Checkout Captcha', 'recaptcha-for-woocommerce'),
			'add_payment_method' => __('Woo Add Payment Method Captcha', 'recaptcha-for-woocommerce'),
			'wp_login' => __('WP Login Captcha', 'recaptcha-for-woocommerce'),
			'wp_register' => __('WP Registration Captcha', 'recaptcha-for-woocommerce'),
			'wp_lostpassword' => __('WP Lost Password Captcha', 'recaptcha-for-woocommerce')
			);

			return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
		}

		public function get_settings( $current_section = '') {

			if ('signup' == $current_section) {

				/**
				 * Filter Plugin Section 2 Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				$settings = apply_filters(
					'i13woocomm_signup', array(
					'section_title_recpacha_on_signup' => array(
					'name' => __('Recaptcha On Registration Page', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '',
					'id' => 'wc_settings_tab_recapcha_signup'
					),
					'i13_recapcha_enable_on_signup' => array(
					'name' => __('Enable Recaptcha on Signup', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_signup'
					),
					'i13_recapcha_signup_title' => array(
					'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_signup_title',
					'default' => 'Captcha',
					),
										'i13_recapcha_hide_label_signup' => array(
					'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_hide_label_signup',
										 'default' => 'no',   
										 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')    
					),    
					'i13_recapcha_signup_theme' => array(
					'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_signup_theme',
					'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
					'default' => 'light',
					),
					'i13_recapcha_signup_size' => array(
					'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_signup_size',
					'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
					'default' => 'normal',
					),
										'i13_recapcha_disable_submitbtn_woo_signup' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_woo_signup'
					),      
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha_signup',
					)
					)
				);
			} else if ('login' == $current_section) {

				$settings = apply_filters(
					'i13woocomm_login_settings', array(
					'section_title_recpacha_on_login' => array(
					'name' => __('Recaptcha On Login Page', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '',
					'id' => 'wc_settings_tab_recapcha_login'
					),
					'i13_recapcha_enable_on_login' => array(
					'name' => __('Enable Recaptcha on Login', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_login'
					),
					'i13_recapcha_login_title' => array(
					'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_login_title',
					'default' => 'Captcha',
					),
										'i13_recapcha_hide_label_login' => array(
					'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_hide_label_login',
										 'default' => 'no',   
										 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')   
					),     
					'i13_recapcha_login_theme' => array(
					'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_login_theme',
					'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
					'default' => 'light',
					),
					'i13_recapcha_login_size' => array(
					'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_login_size',
					'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
					'default' => 'normal',
					),
										 'i13_recapcha_disable_submitbtn_woo_login' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_woo_login'
					),      
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha_login',
					))
				);
			} else if ('forgotpassword' == $current_section) {

				$settings = apply_filters(
					'i13woocomm_forgotpassword_settings', array(
					'section_title_recpacha_on_lost_password' => array(
					'name' => __('Recaptcha On Lost Password Page', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '',
					'id' => 'wc_settings_tab_recapcha_lostpassword'
					),
					'i13_recapcha_enable_on_lostpassword' => array(
					'name' => __('Enable Recaptcha on Lost Password', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_lostpassword'
					),
					'i13_recapcha_lostpassword_title' => array(
					'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_lostpassword_title',
					'default' => 'Captcha',
					),
										'i13_recapcha_hide_label_lostpassword' => array(
					'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_hide_label_lostpassword',
										 'default' => 'no',   
										 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')   
					),      
					'i13_recapcha_lostpassword_theme' => array(
					'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_lostpassword_theme',
					'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
					'default' => 'light',
					),
					'i13_recapcha_lostpassword_size' => array(
					'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_lostpassword_size',
					'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
					'default' => 'normal',
					),
										 'i13_recapcha_disable_submitbtn_woo_lostpassword' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_woo_lostpassword'
					),      
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha_lostpassword',
					))
				);
			} else if ('guestcheckout' == $current_section) {

				$settings = apply_filters(
					'i13woocomm_guestcheckout_settings', array(
					'section_title_recpacha_on_guestcheckout' => array(
					'name' => __('Recaptcha on Checkout', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '',
					'id' => 'wc_settings_tab_recapcha_guestcheckout'
					),
					'i13_recapcha_enable_on_guestcheckout' => array(
					'name' => __('Enable Recaptcha on Guest Checkout', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_guestcheckout'
					),
					'i13_recapcha_enable_on_logincheckout' => array(
					'name' => __('Enable Recaptcha on Login Checkout', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_logincheckout'
					),
					'i13_recapcha_guestcheckout_title' => array(
					'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_guestcheckout_title',
					'default' => 'Captcha',
					),
					'i13_recapcha_hide_label_checkout' => array(
					'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_hide_label_checkout',
										 'default' => 'no',  
										  'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')  
					),  					'i13_recapcha_checkout_timeout' => array(
					'name' => __('Recaptcha Validity', 'recaptcha-for-woocommerce'),
					'type' => 'number',
										'id' => 'i13_recapcha_checkout_timeout',
					'default' => '3',
										'desc' => __('Some payment processor needs more time to process an order. So captcha will valid for a given number of minutes once reCaptcha is validate. 0 means require validation on each request. Default value is 3 minute will used if you leave blank.', 'recaptcha-for-woocommerce'),
					),
					'i13_recapcha_guestcheckout_theme' => array(
					'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_guestcheckout_theme',
					'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
					'default' => 'light',
					),
					'i13_recapcha_guestcheckout_size' => array(
					'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_guestcheckout_size',
					'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
					'default' => 'normal',
					),
					'i13_recapcha_guestcheckout_refresh' => array(
					'name' => __('Recaptcha Refresh Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_guestcheckout_refresh',
					'default' => 'Refresh Captcha',
					),
										'i13_recapcha_disable_submitbtn_guestcheckout' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate for guest checkout.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_guestcheckout'
					),
										'i13_recapcha_disable_submitbtn_logincheckout' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate for login checkout.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_logincheckout'
					),
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha_guestcheckout',
					)
					)
				);
			} else if ('wp_login' == $current_section) {

				$settings = apply_filters(
				'i13woocomm_wplogin_settings', array(
				'section_title_recpacha_on_wplogin' => array(
				'name' => __('Recaptcha On WP Login', 'recaptcha-for-woocommerce'),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_settings_tab_recapcha_wplogin'
							),
							'i13_recapcha_enable_on_wplogin' => array(
							'name' => __('Enable Recaptcha on WP Login', 'recaptcha-for-woocommerce'),
							'type' => 'checkbox',
							'id' => 'i13_recapcha_enable_on_wplogin'
							),
							'i13_recapcha_wplogin_title' => array(
							'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
							'type' => 'text',
							'id' => 'i13_recapcha_wplogin_title',
							'default' => 'Captcha',
							),
														'i13_recapcha_hide_label_wplogin' => array(
														'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
														'type' => 'checkbox',
														'id' => 'i13_recapcha_hide_label_wplogin',
														 'default' => 'no',
														 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')   
														), 
							'i13_recapcha_wplogin_theme' => array(
							'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_wplogin_theme',
							'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
							'default' => 'light',
							),
							'i13_recapcha_wplogin_size' => array(
							'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_wplogin_size',
							'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
							'default' => 'normal',
							),
							 'i13_recapcha_disable_submitbtn_wp_login' => array(
							'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
							'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
							'type' => 'checkbox',
							'id' => 'i13_recapcha_disable_submitbtn_wp_login'
							),     
							array(
							'type' => 'sectionend',
							'id' => 'wc_settings_tab_recapcha_wplogin',
							)
				)
				);
			
			} else if ('add_payment_method' == $current_section) {

				$settings = apply_filters(
				'i13woocomm_addpaymentmethod_settings', array(
				'section_title_recpacha_on_addpaymentmethod' => array(
				'name' => __('Recaptcha On Add Payment Method Login', 'recaptcha-for-woocommerce'),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_settings_tab_recapcha_addpaymentmethod'
							),
							'i13_recapcha_enable_on_addpaymentmethod' => array(
							'name' => __('Enable Recaptcha on Add Payment Method', 'recaptcha-for-woocommerce'),
							'type' => 'checkbox',
							'id' => 'i13_recapcha_enable_on_addpaymentmethod'
							),
							'i13_recapcha_addpaymentmethod_title' => array(
							'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
							'type' => 'text',
							'id' => 'i13_recapcha_addpaymentmethod_title',
							'default' => 'Captcha',
							),
														'i13_recapcha_hide_label_addpayment' => array(
														'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
														'type' => 'checkbox',
														'id' => 'i13_recapcha_hide_label_addpayment',
														 'default' => 'no', 
														 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')   
														), 
							'i13_recapcha_addpaymentmethod_theme' => array(
							'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_addpaymentmethod_theme',
							'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
							'default' => 'light',
							),
							'i13_recapcha_addpaymentmethod_size' => array(
							'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_addpaymentmethod_size',
							'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
							'default' => 'normal',
							),
							'i13_recapcha_disable_submitbtn_paymentmethod' => array(
							'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
							'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
							'type' => 'checkbox',
							'id' => 'i13_recapcha_disable_submitbtn_paymentmethod'
							),    
							array(
							'type' => 'sectionend',
							'id' => 'wc_settings_tab_recapcha_addpaymentmethod',
							)
				)
				);
			
			} else if ('wp_register' == $current_section) {

				$settings = apply_filters(
				'i13woocomm_wpregister_settings', array(
				'section_title_recpacha_on_wplogin' => array(
				'name' => __('Recaptcha On WP Registration', 'recaptcha-for-woocommerce'),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_settings_tab_recapcha_wpregister'
							),
							'i13_recapcha_enable_on_wpregister' => array(
							'name' => __('Enable Recaptcha on WP Registration', 'recaptcha-for-woocommerce'),
							'type' => 'checkbox',
							'id' => 'i13_recapcha_enable_on_wpregister'
							),
							'i13_recapcha_wpregister_title' => array(
							'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
							'type' => 'text',
							'id' => 'i13_recapcha_wpregister_title',
							'default' => 'Captcha',
							),
														'i13_recapcha_hide_label_wpregister' => array(
														'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
														'type' => 'checkbox',
														'id' => 'i13_recapcha_hide_label_wpregister',
														 'default' => 'no',   
														 'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce')   
														), 
							'i13_recapcha_wpregister_theme' => array(
							'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_wpregister_theme',
							'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
							'default' => 'light',
							),
							'i13_recapcha_wpregister_size' => array(
							'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
							'type' => 'radio',
							'id' => 'i13_recapcha_wpregister_size',
							'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
							'default' => 'normal',
							),
							 'i13_recapcha_disable_submitbtn_wp_register' => array(
							'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
							'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
							'type' => 'checkbox',
							'id' => 'i13_recapcha_disable_submitbtn_wp_register'
							),      
							array(
							'type' => 'sectionend',
							'id' => 'wc_settings_tab_recapcha_wpregister',
							)
				)
				);
			
			} else if ('wp_lostpassword' == $current_section) {

				$settings = apply_filters(
					'i13woocomm_wplostpassword_settings', array(
					'section_title_recpacha_on_wplostpassword' => array(
					'name' => __('Recaptcha On WP Lost Password', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '',
					'id' => 'wc_settings_tab_recapcha_wplostpassword'
					),
					'i13_recapcha_enable_on_wplostpassword' => array(
					'name' => __('Enable Recaptcha on WP Lost Password', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_enable_on_wplostpassword'
					),
					'i13_recapcha_wplostpassword_title' => array(
					'name' => __('Recaptcha Field Title', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'i13_recapcha_wplostpassword_title',
					'default' => 'Captcha',
					),
									   'i13_recapcha_hide_label_wplostpassword' => array(
										'name' => __('Hide Label', 'recaptcha-for-woocommerce'),
										'type' => 'checkbox',
										'id' => 'i13_recapcha_hide_label_wplostpassword',
										 'default' => 'no',   
										  'desc' => __('Hide label on form?', 'recaptcha-for-woocommerce') 
										),      
					'i13_recapcha_wplostpassword_theme' => array(
					'name' => __('Recaptcha Theme', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_wplostpassword_theme',
					'options' => array('light' => __('Light', 'recaptcha-for-woocommerce'), 'dark' => __('Dark', 'recaptcha-for-woocommerce')),
					'default' => 'light',
					),
					'i13_recapcha_wplostpassword_size' => array(
					'name' => __('Recaptcha Size', 'recaptcha-for-woocommerce'),
					'type' => 'radio',
					'id' => 'i13_recapcha_wplostpassword_size',
					'options' => array('normal' => __('Normal', 'recaptcha-for-woocommerce'), 'compact' => __('Compact', 'recaptcha-for-woocommerce')),
					'default' => 'normal',
					),
										 'i13_recapcha_disable_submitbtn_wp_lost_password' => array(
					'name' => __('Disable submit button', 'recaptcha-for-woocommerce'),
										'desc' => __('Disable submit button until recaptcha validate.', 'recaptcha-for-woocommerce'),    
					'type' => 'checkbox',
					'id' => 'i13_recapcha_disable_submitbtn_wp_lost_password'
					),      
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha_wplostpassword',
					)
					)
				);
			} else {

				/**
				 * Filter Plugin Section 1 Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				$settings = apply_filters(
					'i13woocomm_general_settings', array(
					'section_title' => array(
					'name' => __('Recaptcha Settings', 'recaptcha-for-woocommerce'),
					'type' => 'title',
					'desc' => '<b>' . __('Please use this guideline to get ', 'recaptcha-for-woocommerce') . ' <a target="_blank" href="http://blog.i13websolution.com/how-to-get-google-recaptcha-v2-checkbox-keys/">' . __('reCaptcha keys', 'recaptcha-for-woocommerce') . '</a><b>',
					'id' => 'wc_settings_tab_recapcha'
					),
					'i13_recapcha_site_key' => array(
					'name' => __('Site Key', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'desc' => __('Get site key for recaptcha v2 checkbox from www.google.com/recaptcha', 'recaptcha-for-woocommerce'),
					'id' => 'wc_settings_tab_recapcha_site_key'
					),
					'i13_recapcha_secret_key' => array(
					'name' => __('Secret Key', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'desc' => __('Get Secret key for recaptcha v2 checkbox from www.google.com/recaptcha', 'recaptcha-for-woocommerce'),
					'id' => 'wc_settings_tab_recapcha_secret_key'
					),
					'i13_recapcha_error_msg_captcha_blank' => array(
					'name' => __('Error message recaptcha blank', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'wc_settings_tab_recapcha_error_msg_captcha_blank',
					'default' => '[recaptcha] is a required field.',
					'desc' => __('[recaptcha] will replaced with captcha field title', 'recaptcha-for-woocommerce')
						
					),
					'i13_recapcha_error_msg_captcha_no_response' => array(
					'name' => __('Error message for can not connect to server ', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'wc_settings_tab_recapcha_error_msg_captcha_no_response',
					'default' => 'Could not get response from [recaptcha] server.',
					'desc' => __('[recaptcha] will replaced with captcha field title', 'recaptcha-for-woocommerce')
					),
					'i13_recapcha_error_msg_captcha_invalid' => array(
					'name' => __('Error message for invalid captcha', 'recaptcha-for-woocommerce'),
					'type' => 'text',
					'id' => 'wc_settings_tab_recapcha_error_msg_captcha_invalid',
					'default' => 'Invalid [recaptcha].',
					'desc' => __('[recaptcha] will replaced with captcha field title', 'recaptcha-for-woocommerce')
					),
					'i13_recapcha_no_conflict' => array(
					'name' => __('No-Conflict Mode', 'recaptcha-for-woocommerce'),
					'type' => 'checkbox',
					'id' => 'i13_recapcha_no_conflict',
					'desc' => __('When checked, other reCAPTCHA occurrences on this plugin sections like checkout, registration, login etc are forcefully removed, to prevent conflicts. Only check if your site is having compatibility issues or instructed to by support.', 'recaptcha-for-woocommerce')
					),
					array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_recapcha',
					))
				);
			}

			/**
			 * Filter MyPlugin Settings
			 *
			 * @since 1.0.0
			 * @param array $settings Array of the plugin settings
			 */
			return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
		}

		/**
		 * Output the settings
		 *
		 * @since 1.0
		 */
		public function output() {

			global $current_section;

			$settings = $this->get_settings($current_section);
			WC_Admin_Settings::output_fields($settings);
		}

		public function save() {

			global $current_section;

			$settings = $this->get_settings($current_section);
			WC_Admin_Settings::save_fields($settings);
		}

	}

endif;

return new I13_WooCommerce_Settings_Page();

