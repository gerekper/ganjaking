<?php
/**
 * SECURITY ARRAY OPTIONS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$security = array(

	'security' => array(

		array(
			'title' => __( 'Security Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-security-options',
		),

		array(
			'title'     => __( 'Enable reCaptcha', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Add reCaptcha (v2) verification to register form.', 'yith-woocommerce-customize-myaccount-page' ),
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
		),

		array(
			'title'     => __( 'reCaptcha private key', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith-wcmap-private-recaptcha',
			'default'   => '',
		),

		array(
			'title'     => __( 'Enable account email verification', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'After registration process send an email with a verification link to the customer.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith-wcmap-enable-verifying-email',
			'default'   => 'no',
		),

		array(
			'title'     => __( 'Customer without verified account', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Choose the restriction for customer without email verified.', 'yith-woocommerce-customize-myaccount-page' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no-login'    => __( 'Block login', 'yith-woocommerce-customize-myaccount-page' ),
				'no-purchase' => __( 'Block purchase', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'id'        => 'yith-wcmap-verifying-email-effect',
			'default'   => 'no-login',
		),

		array(
			'title'     => __( 'Block email domains', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'      => __( 'Add a list of email domains (comma separated) you want to block. Customer with one of those email domain cannot be register into your site.', 'yith-woocommerce-customize-myaccount-page' ),
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

return apply_filters( 'yith_wcmap_panel_security_options', $security );