<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$no_form_plugin         = '';
$inquiry_form           = array();
$yit_contact_form       = array();
$default_link_form      = esc_url( add_query_arg( array( 'tab' => 'raqform' ) ) );
$raq_email_setting_page = esc_url( add_query_arg( array( 'section' => 'yith_ywraq_send_email_request_quote' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ) );
$active_plugins         = apply_filters(
	'ywraq_form_type_list',
	array(
		'default' => esc_html__( 'Default', 'yith-woocommerce-request-a-quote' ),
	)
);

$section1 = array(
	'section_form_settings' => array(
		'name' => esc_html__( 'Form settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_section_form',
	),
	'inquiry_form'          => array(
		'name'      => esc_html__( 'Request form', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Choose one. You can also add forms from YIT Contact Form, Contact Form 7 or Gravity Form that must be installed and activated.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'default'   => 'default',
		'options'   => apply_filters(
			'ywraq_form_type_list',
			array(
				'default' => esc_html__( 'Default', 'yith-woocommerce-request-a-quote' ),
			)
		),
		'id'        => 'ywraq_inquiry_form_type',
	),
	'default_form'          => array(
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'html'             => sprintf( '<div id="ywraq_inquiry_form_link"><a class="button-secondary" href="%s">%s</a></div>', $default_link_form, esc_html__( 'Edit the default form', 'yith-woocommerce-request-a-quote' ) ),
		'id'               => 'ywraq_inquiry_form_link',
		'yith-display-row' => true,
		'deps'             => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
	),
	'email_request_setting' => array(
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'html'             => sprintf( '<div id="ywraq_email_request_setting"><a class="button-secondary" href="%s">%s</a></div>', $raq_email_setting_page, esc_html__( 'Edit the Request a quote email options', 'yith-woocommerce-request-a-quote' ) ),
		'id'               => 'ywraq_email_request_setting',
		'yith-display-row' => true,
		'deps'             => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
	),

);

$section1 = apply_filters( 'ywraq_additional_form_options', $section1 );

$section2 = array(

	// @since 1.1.6
	'add_user_registration_check'     => array(
		'name'      => esc_html__( 'Enable registration on the "Request a Quote" page', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'The plugin adds a checkbox below the form', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_add_user_registration_check',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => 'no',
	),

	'force_user_to_register'          => array(
		'name'      => '',
		'desc'      => esc_html__( 'Force Registration on the "Request a Quote" page', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_force_user_to_register',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => 'yes',
	),

	// @since 1.9.0
	'reCAPTCHA'                       => array(
		'name'      => esc_html__( 'Add a reCAPTCHA to the default form', 'yith-woocommerce-request-a-quote' ),
		'desc'      => sprintf( '%s <a href="https://www.google.com/recaptcha/admin">%s</a>', esc_html__( 'To start using reCAPTCHA V2, you need to sign up for an', 'yith-woocommerce-request-a-quote' ), esc_html__( ' API key pair for your site', 'yith-woocommerce-request-a-quote' ) ),
		'id'        => 'ywraq_reCAPTCHA',
		'class'     => 'field_with_deps',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => 'no',
	),
	// @since 1.9.0
	'reCAPTCHA_sitekey'               => array(
		'name'      => esc_html__( 'Add reCAPTCHA site key', 'yith-woocommerce-request-a-quote' ),
		'desc'      => '',
		'id'        => 'ywraq_reCAPTCHA_sitekey',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => '',
	),
	// @since 1.9.0
	'reCAPTCHA_secretkey'             => array(
		'name'      => esc_html__( 'Add reCAPTCHA secret key', 'yith-woocommerce-request-a-quote' ),
		'desc'      => '',
		'id'        => 'ywraq_reCAPTCHA_secretkey',
		'class'     => 'regular-input',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => '',
	),
	'autocomplete_default_form'       => array(
		'name'      => esc_html__( 'Autocomplete Form', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Check this option if you want that the fields connected to WooCommerce fields will be filled automatically', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_autocomplete_default_form',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'deps'      => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => 'no',
	),
	'data-format-datepicker'          => array(
		'name'              => esc_html__( 'Date Picker Format', 'yith-woocommerce-request-a-quote' ),
		'desc'              => '',
		'id'                => 'ywraq-date-format-datepicker',
		'type'              => 'yith-field',
		'custom_attributes' => 'style="width:80px"',
		'yith-type'         => 'text',
		'deps'              => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'           => 'mm/dd/yy',
	),
	'time-format-datepicker'          => array(
		'name'              => esc_html__( 'Time Picker Format Time', 'yith-woocommerce-request-a-quote' ),
		'desc'              => '',
		'id'                => 'ywraq-time-format-datepicker',
		'type'              => 'yith-field',
		'custom_attributes' => 'style="width:80px"',
		'yith-type'         => 'select',
		'class'             => 'wc-enhanced-select',
		'options'           => array(
			'12' => '12',
			'24' => '24',
		),
		'deps'              => array(
			'id'    => 'ywraq_inquiry_form_type',
			'value' => 'default',
			'type'  => 'hide',
		),
		'default'   => 'no',
	),

	'section_end_form'                => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_premium_end_form',
	),

	//@since 1.4.4
	'section_after_submit_action'     => array(
		'name' => esc_html__( 'Actions', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_after_submit_action',
	),

	'how_show_after_sent_the_request' => array(
		'name'      => esc_html__( 'Set what to show after sending the quote ', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Select which details you want to show after sending the quote', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_how_show_after_sent_the_request',
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'options'   => array(
			'simple_message'  => esc_html__( 'Show a simple message', 'yith-woocommerce-request-a-quote' ),
			'thank_you_quote' => esc_html__( 'Show quote details', 'yith-woocommerce-request-a-quote' ),
			'thank_you_page'  => esc_html__( 'Thank you page', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'simple_message',
	),
	'message_after_sent_the_request'  => array(
		'name'      => esc_html__( 'Show this message after a quote request is sent', 'yith-woocommerce-request-a-quote' ),
		'desc'      => '',
		'id'        => 'ywraq_message_after_sent_the_request',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Your request has been sent successfully.', 'yith-woocommerce-request-a-quote' ),
		'deps'      => array(
			'id'    => 'ywraq_how_show_after_sent_the_request',
			'value' => 'simple_message',
		),
	),

	'enable_link_details'             => array(
		'name'      => esc_html__( 'Show request details after it has been submitted', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the link of the quote details will be showed', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_enable_link_details',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'deps'      => array(
			'id'    => 'ywraq_how_show_after_sent_the_request',
			'value' => 'simple_message',
		),
	),

	'message_to_view_details'         => array(
		'name'      => esc_html__( 'Show this text to lead users to Details page', 'yith-woocommerce-request-a-quote' ),
		'desc'      => '',
		'id'        => 'ywraq_message_to_view_details',
		'class'     => 'regular-input',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_enable_link_details',
			'value' => 'yes',
			'type'  => 'hide',
		),
		'default'   => esc_html__( 'You can see details at:', 'yith-woocommerce-request-a-quote' ),
	),

	'thank_you_page'                  => array(
		'name'    => esc_html__( 'Select your Thank-you page', 'yith-woocommerce-request-a-quote' ),
		'desc'    => '',
		'id'      => 'ywraq_thank_you_page',
		'type'    => 'single_select_page',
		'default' => '',
		'class'   => 'wc-enhanced-select',
		'css'     => 'min-width:300px',
		'deps'    => array(
			'id'    => 'ywraq_activate_thank_you_page',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	// @since 1.4.4
	'section_after_submit_action_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_after_submit_action_end',
	),

);


$options = array(
	'form' => array_merge( $section1, $section2 ),
);

return $options;
