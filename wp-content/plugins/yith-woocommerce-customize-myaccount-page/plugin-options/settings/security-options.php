<?php
/**
 * Security options array
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 2.5.0
 */

defined( 'YITH_WCMAP' ) || exit;

$security = array(
	'settings-security' => array(
		array(
			'title' => __( 'Security Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-security-options',
		),
		array(
			'title'     => __( 'Show reCaptcha in registration form', 'yith-woocommerce-customize-myaccount-page' ),
			// translators: placeholders are used for link to reCaptcha documentation.
			'desc'      => sprintf( __( 'Enable reCaptcha in registration form. %1$sRead more here >%2$s', 'yith-woocommerce-customize-myaccount-page' ), '<a href="https://www.google.com/recaptcha/about/" target="_blank">', '</a>' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith-wcmap-enable-recaptcha',
			'default'   => 'no',
		),
		array(
			'title'     => __( 'reCaptcha public key', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith-wcmap-public-recaptcha',
			'default'   => '',
			'deps'      => array(
				'id'    => 'yith-wcmap-enable-recaptcha',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		array(
			'title'     => __( 'reCaptcha private key', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith-wcmap-private-recaptcha',
			'default'   => '',
			'deps'      => array(
				'id'    => 'yith-wcmap-enable-recaptcha',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		array(
			'title'     => __( 'Send a verification email to new users', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Enable to send an email to verify account to new users.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith-wcmap-enable-verifying-email',
			'default'   => 'no',
		),
		array(
			'title'     => __( 'Without a verified account', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Set permissions of user without a verified account.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no-login'    => _x( 'the user can\'t login in the site', '"Without account verified" option label', 'yith-woocommerce-customize-myaccount-page' ),
				'no-purchase' => _x( 'the user can login but not purchase', '"Without account verified" option label', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'id'        => 'yith-wcmap-verifying-email-effect',
			'default'   => 'no-login',
			'deps'      => array(
				'id'    => 'yith-wcmap-enable-verifying-email',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		array(
			'title'     => __( 'Block email addresses from these domains', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Add a list of email domains (comma separated) you want to block.<br>Example: <code>yopmail.com, email.org</code>', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'yith-wcmap-email-domain-blocked',
			'default'   => '',
		),
		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-security-options',
		),
	),
);

/**
 * APPLY_FILTERS: yith_wcmap_panel_security_options
 *
 * Filters the options available in the Security Options tab.
 *
 * @param array $security_options Array with options.
 *
 * @return array
 */
return apply_filters( 'yith_wcmap_panel_security_options', $security );
