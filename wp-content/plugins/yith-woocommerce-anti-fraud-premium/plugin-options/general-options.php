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

$default_domains = array(
	'guerrillamail.com',
	'guerrillamailblock.com',
	'sharklasers.com',
	'guerrillamail.net',
	'guerrillamail.org',
	'guerrillamail.biz',
	'spam4.me',
	'grr.la',
	'guerrillamail.de',
	'trbvm.com',
	'mailinator.com',
	'reallymymail.com',
	'mailismagic.com',
	'mailtothis.com',
	'monumentmail.com',
	'imgof.com',
	'fammix.com',
	'6paq.com',
	'grandmamail.com',
	'daintly.com',
	'evopo.com',
	'lackmail.net',
	'alivance.com',
	'bigprofessor.so',
	'walkmail.net',
	'thisisnotmyrealemail.com',
	'mailmetrash.com',
	'mytrashmail.com',
	'trashymail.com',
	'mt2009.com',
	'trash2009.com',
	'thankyou2010.com',
	'guerrillamailblock',
	'meltmail.com',
	'mintemail.com',
	'tempinbox.com',
	'fatflap.com',
	'dingbone.com',
	'fudgerub.com',
	'beefmilk.com',
	'lookugly.com',
	'smellfear.com',
	'yopmail.com',
	'jnxjn.com',
	'example.com',
	'spamgourmet.com',
	'jetable.org',
	'dunflimblag.mailexpire.com',
	'spambox.us',
	'tempomail.fr',
	'tempemail.net',
	'spamfree24.org',
	'spamfree24.de',
	'spamfree.info',
	'spamfree.com',
	'spamfree.eu',
	'spamavert.com',
	'maileater.com',
	'mailexpire.com',
	'spammotel.com',
	'spamspot.com',
	'spam.la',
	'hushmail.com',
	'hushmail.me',
	'hush.com',
	'hush.ai',
	'mac.hush.com',
	'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk.com',
	'mailnull.com',
	'sneakemail.com',
	'e4ward.com',
	'spamcero.com',
	'mytempemail.com',
	'incognitomail.org',
	'mailcatch.com',
	'deadaddress.com',
	'mailscrap.com',
	'anonymbox.com',
	'soodonims.com',
	'tempail.com',
	'20minutemail.com',
	'deagot.com',
	'demail.tk',
	'yestoa.com',
	'anontext.com',
	'shieldemail.com',
	'temporaryemail.net',
	'disposeamail.com',
	'mailmoat.com',
	'noclickemail.com',
	'trashmail.net',
	'kurzepost.de',
	'objectmail.com',
	'proxymail.eu',
	'rcpt.at',
	'trash-mail.at',
	'trashmail.at',
	'trashmail.me',
	'wegwerfmail.de',
	'wegwerfmail.net',
	'wegwerfmail.org',
	'yopmail.fr',
	'yopmail.net',
	'cool.fr.nf',
	'jetable.fr.nf',
	'nospam.ze.tc',
	'nomail.xl.cx',
	'mega.zik.dj',
	'speed.1s.fr',
	'courriel.fr.nf',
	'moncourrier.fr.nf',
	'monemail.fr.nf',
	'monmail.fr.nf',
	'emailias.com',
	'zoemail.com',
	'wh4f.org',
	'despam.it',
	'disposableinbox.com',
	'fakeinbox.com',
	'quickinbox.com',
	'emailthe.net',
	'tempalias.com',
	'explodemail.com',
	'xyzfree.net',
	'10×9.com',
	'12minutemail.com',
	'we.nispam.it',
	'no-spam.ws',
	'mytemporarymail.com',
	'yxzx.net',
	'goemailgo.com',
	'filzmail.com',
	'webemail.me',
	'temp.emeraldwebmail.com',
	'fakemail.fr',
	'my-inbox.in',
	'mail-it24.com',
	'tittbit.in',
	'mail.tittbit.in',
	'temporaryemailaddress.com',
	'temporaryemailid.com',
	'mail.cz.cc',
	'10minutemail.com',
);

$admin_email_setting_page = esc_url( add_query_arg( array( 'section' => 'ywaf_admin_notification' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ) );

return array(

	'general' => array(

		'ywaf_main_section_title' => array(
			'name' => __( 'Anti-Fraud settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_enable_plugin'      => array(
			'name'      => __( 'Enable YITH WooCommerce Anti-Fraud', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_enable_plugin',
			'default'   => 'yes',
		),
		'ywaf_main_section_end'   => array(
			'type' => 'sectionend',
		),


		'ywaf_admin_mail_title'      => array(
			'name' => __( 'Admin email settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_admin_mail_enable'     => array(
			'name'      => __( 'Enable admin email notification', 'yith-woocommerce-anti-fraud' ),
			'desc'      => __( 'Send a notification email to admin showing the outcome of anti-fraud checks', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_admin_mail_enable',
			'default'   => 'no',

		),
		'ywaf_admin_mail_settings' => array(
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'html'             => sprintf( '<div id="ywaf_admin_mail_settings"><a class="button-secondary" href="%s">%s</a></div>', $admin_email_setting_page, __( 'Edit the admin email options', 'yith-woocommerce-anti-fraud' ) ),
			'id'               => 'ywaf_admin_mail_settings',
			'yith-display-row' => true,
			'deps'             => array(
				'id'    => 'ywaf_admin_mail_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_admin_mail_end'        => array(
			'type' => 'sectionend',
		),

		'ywaf_thresholds_title'         => array(
			'name' => __( 'Settings for risk thresholds', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_medium_risk_threshold'    => array(
			'name'              => __( 'Medium Risk threshold', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'number',
			'id'                => 'ywaf_medium_risk_threshold',
			'default'           => '25',
			'class'             => 'ywaf-thresholds ywaf-medium',
			'custom_attributes' => array(
				'min'      => 1,
				'max'      => 100,
				'required' => 'required'
			)
		),
		'ywaf_high_risk_threshold'      => array(
			'name'              => __( 'High Risk threshold', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'number',
			'id'                => 'ywaf_high_risk_threshold',
			'default'           => '75',
			'class'             => 'ywaf-thresholds ywaf-high',
			'custom_attributes' => array(
				'min'      => 1,
				'max'      => 100,
				'required' => 'required'
			)
		),
		'ywaf_check_high_risk_checkout' => array(
			'name'      => __( 'Check for high risk at checkout', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_check_high_risk_checkout',
			'desc'      => __( 'Check for high risk order before validating checkout. If there\'s an high risk the order will be immediately cancelled', 'yith-woocommerce-anti-fraud' ),
			'default'   => 'no',
		),
		'ywaf_checkout_error_message'   => array(
			'name'              => __( 'Error message', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => __( 'The message that the customer will display in case of validation failure', 'yith-woocommerce-anti-fraud' ),
			'id'                => 'ywaf_checkout_error_message',
			'default'           => __( 'Sorry, your order cannot be processed. If the issue persists please contact us.', 'yith-woocommerce-anti-fraud' ),
			'custom_attributes' => implode( ' ', array(
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_check_high_risk_checkout',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_thresholds_end'           => array(
			'type' => 'sectionend',
		),

		'ywaf_rules_title' => array(
			'name' => __( 'Rule settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),

		'ywaf_rules_first_order_enable' => array(
			'name'      => __( 'Enable first order check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_first_order_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_first_order_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_first_order_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_first_order_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_international_order_enable' => array(
			'name'      => __( 'Enable international order check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_international_order_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_international_order_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_international_order_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_international_order_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_ip_country_enable' => array(
			'name'      => __( 'Enable IP geolocation check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_ip_country_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_ip_country_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_ip_country_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_ip_country_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_addresses_matching_enable' => array(
			'name'      => __( 'Enable Billing and Shipping address check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_addresses_matching_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_addresses_matching_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_addresses_matching_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_addresses_matching_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_proxy_enable' => array(
			'name'      => __( 'Enable proxy check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_proxy_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_proxy_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_proxy_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_proxy_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_suspicious_email_enable'  => array(
			'name'      => __( 'Enable suspicious email domain check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_suspicious_email_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_suspicious_email_weight'  => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_suspicious_email_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_suspicious_email_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_suspicious_email_domains' => array(
			'name'        => __( 'Suspicious domains', 'yith-woocommerce-anti-fraud' ),
			'type'        => 'yith-field',
			'yith-type'   => 'ywaf-custom-checklist',
			'id'          => 'ywaf_rules_suspicious_email_domains',
			'default'     => implode( ',', $default_domains ),
			'desc'        => __( 'Email domains considered suspicious. Enter values separated by commas, for example: suspiciousdomain1.com, suspiciousdomain2.com', 'yith-woocommerce-anti-fraud' ),
			'placeholder' => __( 'Insert a domain&hellip;', 'yith-woocommerce-anti-fraud' ),
			'deps'        => array(
				'id'    => 'ywaf_rules_suspicious_email_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_risk_country_enable' => array(
			'name'      => __( 'Enable unsafe country check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_risk_country_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_risk_country_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_risk_country_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_risk_country_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_risk_country_list'   => array(
			'name'        => __( 'Define unsafe countries', 'yith-woocommerce-anti-fraud' ),
			'id'          => 'ywaf_rules_risk_country_list',
			'type'        => 'yith-field',
			'yith-type'   => 'select-buttons',
			'placeholder' => __( 'Search for a country&hellip;', 'yith-woocommerce-anti-fraud' ),
			'desc'        => __( 'Enter here the countries that you consider unsafe', 'yith-woocommerce-anti-fraud' ),
			'multiple'    => 'true',
			'options'     => WC()->countries->get_countries(),
			'deps'        => array(
				'id'    => 'ywaf_rules_risk_country_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_high_amount_enable'     => array(
			'name'      => __( 'Enable order amount check (for orders exceeding average order amount)', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_high_amount_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_high_amount_weight'     => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_high_amount_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_high_amount_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_high_amount_multiplier' => array(
			'name'              => __( 'Average multiplier', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_high_amount_multiplier',
			'desc'              => __( 'Total order amount accepted (expressed as multiplier of average order amount)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '2',
			'custom_attributes' => implode( ' ', array(
				'min'      => 2,
				'max'      => 5,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_high_amount_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_high_amount_fixed_enable' => array(
			'name'      => __( 'Enable order amount check (for orders exceeding the below specified amount)', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_high_amount_fixed_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_high_amount_fixed_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_high_amount_fixed_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_high_amount_fixed_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_high_amount_fixed_limit'  => array(
			'name'              => sprintf( __( 'Amount Limit (%s)', 'yith-woocommerce-anti-fraud' ), get_woocommerce_currency_symbol() ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'class'             => 'wc_input_price',
			'id'                => 'ywaf_rules_high_amount_fixed_limit',
			'desc'              => __( 'Total order amount accepted. Set zero for no limit.', 'yith-woocommerce-anti-fraud' ),
			'default'           => 0,
			'custom_attributes' => implode( ' ', array(
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_high_amount_fixed_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_many_attempts_enable' => array(
			'name'      => __( 'Enable check for attempt count', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_many_attempts_enable',
			'default'   => 'yes',

		),
		'ywaf_rules_many_attempts_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_many_attempts_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_many_attempts_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_many_attempts_hours'  => array(
			'name'              => __( 'Time span to check', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_many_attempts_hours',
			'desc'              => __( 'Time span (hours) for check', 'yith-woocommerce-anti-fraud' ),
			'default'           => '1',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 48,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_many_attempts_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_many_attempts_orders' => array(
			'name'              => __( 'Maximum number of orders per time span', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_many_attempts_orders',
			'desc'              => __( 'Maximum number of orders that a user can make in the specified time span', 'yith-woocommerce-anti-fraud' ),
			'default'           => '2',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 50,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_many_attempts_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_ip_multiple_details_enable' => array(
			'name'      => __( 'Enable IP multiple details check', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_rules_ip_multiple_details_enable',
			'default'   => 'yes',
		),
		'ywaf_rules_ip_multiple_details_weight' => array(
			'name'              => __( 'Rule weight', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_ip_multiple_details_weight',
			'desc'              => __( 'Weight of the single rule in the total calculation of risk (Normal value: 10 - you can change it from 1 to 20 depending on the weight you want to designate)', 'yith-woocommerce-anti-fraud' ),
			'default'           => '10',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 20,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_ip_multiple_details_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_rules_ip_multiple_details_days'   => array(
			'name'              => __( 'Time span (days) to check', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_rules_ip_multiple_details_days',
			'desc'              => __( 'Time span (days) to check', 'yith-woocommerce-anti-fraud' ),
			'default'           => '7',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 90,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_rules_ip_multiple_details_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),

		'ywaf_rules_end' => array(
			'type' => 'sectionend',
		),

	)

);