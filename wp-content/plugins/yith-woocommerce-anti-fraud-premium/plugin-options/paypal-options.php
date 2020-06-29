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

$paypal_email_setting_page = esc_url( add_query_arg( array( 'section' => 'ywaf_paypal_verify' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ) );


return array(

	'paypal' => array(

		'ywaf_paypal_title'          => array(
			'name' => __( 'PayPal settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_paypal_enable'         => array(
			'name'      => __( 'Enable PayPal verification', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_paypal_enable',
			'default'   => 'yes',
		),
		'ywaf_protect_downloads'     => array(
			'name'      => __( 'Prevent downloads if verification failed or still processing', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_protect_downloads',
			'default'   => 'yes',
			'deps'      => array(
				'id'    => 'ywaf_paypal_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_paypal_email_settings' => array(
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'html'             => sprintf( '<div id="ywaf_paypal_email_settings"><a class="button-secondary" href="%s">%s</a></div>', $paypal_email_setting_page, __( 'Edit the PayPal email options', 'yith-woocommerce-anti-fraud' ) ),
			'id'               => 'ywaf_paypal_email_settings',
			'yith-display-row' => true,
			'deps'             => array(
				'id'    => 'ywaf_paypal_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_paypal_resend_days'    => array(
			'name'              => __( 'Time span before further attempts', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_paypal_resend_days',
			'desc'              => __( 'Number of days that have to pass before sending another email if the order is still waiting for verification', 'yith-woocommerce-anti-fraud' ),
			'default'           => '2',
			'class'             => 'ywaf-thresholds ywaf-medium',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 30,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_paypal_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_paypal_cancel_days'    => array(
			'name'              => __( 'Time span before the orders are cancelled', 'yith-woocommerce-anti-fraud' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => 'ywaf_paypal_cancel_days',
			'desc'              => __( 'Number of days that have to pass before deleting the order if it is not verified', 'yith-woocommerce-anti-fraud' ),
			'default'           => '5',
			'class'             => 'ywaf-thresholds ywaf-high',
			'custom_attributes' => implode( ' ', array(
				'min'      => 1,
				'max'      => 30,
				'required' => 'required'
			) ),
			'deps'              => array(
				'id'    => 'ywaf_paypal_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_paypal_verified'       => array(
			'name'        => __( 'PayPal verified addresses', 'yith-woocommerce-anti-fraud' ),
			'type'        => 'yith-field',
			'yith-type'   => 'ywaf-custom-checklist',
			'id'          => 'ywaf_paypal_verified',
			'default'     => '',
			'desc'        => __( 'Verified email addresses', 'yith-woocommerce-anti-fraud' ),
			'placeholder' => __( 'Enter an email address&hellip;', 'yith-woocommerce-anti-fraud' ),
			'deps'        => array(
				'id'    => 'ywaf_paypal_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_paypal_end'            => array(
			'type' => 'sectionend',
		),

	)

);