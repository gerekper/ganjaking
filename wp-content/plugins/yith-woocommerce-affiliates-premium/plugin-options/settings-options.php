<?php
/**
 * General settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcaf_general_settings',
	array(
		'settings' => array_merge(
			array(

				'general-options' => array(
					'title' => __( 'General', 'yith-woocommerce-affiliates' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcaf_general_options'
				),

				'general-referral-var' => array(
					'title' => __( 'Referral var name', 'yith-woocommerce-affiliates' ),
					'type' => 'text',
					'desc' => __( 'Select name of referral var used to store referral token in query var', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_var_name',
					'css' => 'min-width: 300px;',
					'default' => 'ref',
					'desc_tip' => true
				),

				'general-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcaf_cookie_options'
				),
			),

			array(

				'cookie-options' => array(
					'title' => __( 'Cookie', 'yith-woocommerce-affiliates' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcaf_general_options'
				),

				'cookie-referral-name' => array(
					'title' => __( 'Referral cookie name', 'yith-woocommerce-affiliates' ),
					'type' => 'text',
					'desc' => __( 'Select name for cookie that will store referral token. This name should be as unique as possible, so to avoid collision with other plugins: If you change this setting, all cookies created before will no longer be effective', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_cookie_name',
					'css' => 'min-width: 300px;',
					'default' => 'yith_wcaf_referral_token',
					'desc_tip' => true
				),

				'cookie-referral-expire-needed' => array(
					'title' => __( 'Make referral cookie expire', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option if you want to make referral cookie expire', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_make_cookie_expire',
					'default' => 'yes'
				),

				'cookie-referral-expiration' => array(
					'title' => __( 'Referral cookie exp.', 'yith-woocommerce-affiliates' ),
					'type' => 'number',
					'desc' => __( 'Number of seconds before referral cookie expires', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_cookie_expire',
					'css' => 'min-width: 100px;',
					'default' => WEEK_IN_SECONDS,
					'custom_attributes' => array(
						'min' => 1,
						'max' => 9999999999999,
						'step' => 1
					),
					'desc_tip' => true
				),

				// @since 1.7.0

				'cookie-set-via-ajax' => array(
					'title' => __( 'Set cookie via AJAX', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option if you want to execute an ajax call to set up affiliate cookies, whenever system finds referral query string in the url', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_cookie_ajax_set',
					'default' => 'no'
				),

				'cookie-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcaf_cookie_options'
				),
			),

			array(

				'pages-options' => array(
					'title' => __( 'Affiliate pages', 'yith-woocommerce-affiliates' ),
					'type' => 'title',
					'desc' => '',
				),

				'page-dashboard-options' => array(
					'title' => __( 'Affiliate dashboard page', 'yith-woocommerce-affiliates' ),
					'desc'     => __( 'Page contents:', 'yith-woocommerce-affiliates' ) . ' [' . apply_filters( 'yith_wcaf_affiliate_dashboard_shortcode_tag', 'yith_wcaf_affiliate_dashboard' ) . ']',
					'type' => 'single_select_page',
					'id' => 'yith_wcaf_dashboard_page_id',
					'default'  => '',
					'class'    => 'wc-enhanced-select',
					'css'      => 'max-width:300px;',
					'desc_tip' => true,
				),

				'pages-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcaf_cookie_options'
				),

			),

            array(

                'affiliates_socials_section_start' => array(
                    'name' => __( 'Social Networks & Share', 'yith-woocommerce-affiliates' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'yith_wcaf_socials_share'
                ),

                'affiliates_share_on_facebook' => array(
                    'name'    => __( 'Share on Facebook', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'Show "Share on Facebook" button', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_share_fb',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
                'affiliates_share_on_twitter' => array(
                    'name'    => __( 'Tweet on Twitter', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'Show "Tweet on Twitter" button', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_share_twitter',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
                'affiliates_share_on_pinterest' => array(
                    'name'    => __( 'Pin on Pinterest', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'Show "Pin on Pinterest" button', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_share_pinterest',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
                'affiliates_share_by_email' => array(
                    'name'    => __( 'Share by Email', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'Show "Share by Email" button', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_share_email',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
                'affiliates_share_by_whatsapp' => array(
                    'name'    => __( 'Share by WhatsApp', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'Show "Share by WhatsApp" button', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_share_whatsapp',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
                'affiliates_socials_title' => array(
                    'name'    => __( 'Social title', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_socials_title',
                    'default' => sprintf( __( 'My Referral URL on %s', 'yith-woocommerce-affiliates' ), get_bloginfo( 'name' ) ),
                    'type'    => 'text',
                    'css'     => 'min-width:300px;',
                ),
                'affiliates_socials_text' =>  array(
                    'name'    => __( 'Social text', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'It will be used by Twitter, WhatsApp and Pinterest. Use <strong>%referral_url%</strong> where you want to show the URL of your Affiliate.', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_socials_text',
                    'css'     => 'width:100%; height: 75px;',
                    'default' => '%referral_url%',
                    'type'    => 'textarea'
                ),
                'affiliates_socials_image' => array(
                    'name'    => __( 'Social image URL', 'yith-woocommerce-affiliates' ),
                    'desc'    => __( 'It will be used by Pinterest.', 'yith-woocommerce-affiliates' ),
                    'id'      => 'yith_wcaf_socials_image_url',
                    'default' => '',
                    'type'    => 'text',
                    'css'     => 'min-width:300px;',
                ),

                'affiliates_socials_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'yith_wcaf_styles'
                )
            ),

			array(

				'referral-registration-options' => array(
					'title' => __( 'Referral registration', 'yith-woocommerce-affiliates' ),
					'type'  => 'title',
					'id'    => 'yith_wcaf_referral_registration_options'
				),

				'referral-registration-form' => array(
					'title' => __( 'Registration form', 'yith-woocommerce-affiliates' ),
					'type' => 'select',
					'desc' => sprintf(
						'<span data-value="any">%s</span><span data-value="plugin">%s <b>[yith_wcaf_registration_form]</b></span>',
						__( 'You\'ve selected the default registration form. The affiliate registration fields selected below will be added to the default form that you are using to let your users register. If you\'re using the default WooCommerce registration form, you\'ll find it on My Account page. You\'ll have just one registration form for both your users and affiliates, in one place.', 'yith-woocommerce-affiliates' ),
						__( 'You\'ve selected the affiliate registration form. Your users will only be able to register as affiliates using this form. It is displayed by default in Affiliate Dashboard page, but you can print it anywhere you prefer on your website using the dedicated shortcode', 'yith-woocommerce-affiliates' )
					),
					'options' => array(
						'any' => __( 'Default registration form', 'yith-woocommerce-affiliates' ),
						'plugin' => __( 'Affiliate dedicated registration form', 'yith-woocommerce-affiliates' )
					),
					'id' => 'yith_wcaf_referral_registration_form_options',
					'css' => 'min-width: 300px;',
					'class' => 'variable-description'
				),

				'referral-registration-show-login-form' => array(
					'title' => __( 'Show Login form', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Show Login Form on registration form side', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_registration_show_login_form',
					'default' => 'no'
				),

				'referral-registration-show-name-field' => array(
					'title' => __( 'Show Name field', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Show "First Name" field on registration form', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_registration_show_name_field',
					'default' => 'yes'
				),

				'referral-registration-show-surname-field' => array(
					'title' => __( 'Show Surname field', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Show "Last Name" field on registration form', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_registration_show_surname_field',
					'default' => 'yes'
				),

				'referral-registration-show-fields-on-settings' => array(
					'title' => __( 'Show fields in Settings', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Show additional fields in Affiliate Dashboard\'s settings page', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_show_fields_on_settings',
					'default' => 'no'
				),

				'referral-registration-show-fields-on-become-an-affiliate' => array(
					'title' => __( 'Show fields in Become an Affiliate', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Show additional fields in Become an Affiliate section', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_show_fields_on_become_an_affiliate',
					'default' => 'no'
				),

				/**
				 * @since 1.2.4
				 */
				'referral-registration-process-dangling-commissions' => array(
					'title' => __( 'Associate old commissions', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Whenever adding a new affiliate, check if some commission already exists for his default token, and assign it to him eventually', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_referral_registration_process_dangling_commissions',
					'default' => 'no'
				),

				'referral-registration-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcaf_referral_registration_options'
				),

			),

			array(

				'commission-options' => array(
					'title' => __( 'Commissions', 'yith-woocommerce-affiliates' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcaf_commission_options'
				),

				'commission-general-rate' => array(
					'title' => __( 'General rate', 'yith-woocommerce-affiliates' ),
					'type' => 'number',
					'desc' => __( 'General rate to apply to affiliates', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_general_rate',
					'css' => 'max-width: 50px;',
					'default' => 0,
					'custom_attributes' => array(
						'min' => 0,
						'max' => 100,
						'step' => 'any'
					)
				),

				'commission-avoid-auto-referral' => array(
					'title' => __( 'Avoid auto commission', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Prevent affiliate from getting commissions from his/her own sales', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_commission_avoid_auto_referral',
					'default' => 'yes'
				),

				'commission-exclude-tax' => array(
					'title' => __( 'Exclude tax from commissions', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Exclude tax from referral commission calculation', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_commission_exclude_tax',
					'default' => 'yes'
				),

				'commission-exclude-discount' => array(
					'title' => __( 'Exclude discount from commissions', 'yith-woocommerce-affiliates' ),
					'type' => 'checkbox',
					'desc' => __( 'Exclude discounts from referral commission calculation', 'yith-woocommerce-affiliates' ),
					'id' => 'yith_wcaf_commission_exclude_discount',
					'default' => 'yes'
				),

				'commission-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcaf_commission_options'
				),

			)
		)
	)
);