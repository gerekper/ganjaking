<?php
/**
 * Mail options tab
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


$settings_start         = array(
	'review_reminder_mail_section_title' => array(
		'name' => esc_html__( 'Email Settings', 'yith-woocommerce-review-reminder' ),
		'type' => 'title',
		'desc' => '',
	),
);
$settings_mail          = ywrr_mail_options();
$wcet_args              = array( 'page' => 'yith_wcet_panel' );
$wcet_url               = esc_url( add_query_arg( $wcet_args, admin_url( 'admin.php' ) ) );
$email_templates_enable = ( ywrr_check_ywcet_active() ) ? array(
	/* translators: %s plugin name */
	'name'      => sprintf( esc_html__( 'Use %s', 'yith-woocommerce-review-reminder' ), 'YITH WooCommerce Email Templates' ),
	'type'      => 'yith-field',
	'yith-type' => 'onoff',
	/* translators: %s plugin name */
	'desc'      => sprintf( esc_html__( 'By enabling this option, you will have to assign a template from %s', 'yith-woocommerce-review-reminder' ), '<a href="' . $wcet_url . '" target="_blank">YITH WooCommerce Email Templates</a>' ),
	'id'        => 'ywrr_mail_template_enable',
	'default'   => 'no',
) : '';
$email_templates_deps   = ( ywrr_check_ywcet_active() ) ? array(
	'id'    => 'ywrr_mail_template_enable',
	'value' => 'no',
) : '';
$other_settings         = array(
	'review_reminder_mail_template_enable'  => $email_templates_enable,
	'review_reminder_mail_template'         => array(
		'name'      => esc_html__( 'Email template', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'desc'      => '',
		'class'     => 'wc-enhanced-select',
		'options'   => array(
			'base'      => esc_html__( 'Woocommerce Template', 'yith-woocommerce-review-reminder' ),
			'premium-1' => esc_html__( 'Template 1', 'yith-woocommerce-review-reminder' ),
			'premium-2' => esc_html__( 'Template 2', 'yith-woocommerce-review-reminder' ),
			'premium-3' => esc_html__( 'Template 3', 'yith-woocommerce-review-reminder' ),
		),
		'default'   => 'base',
		'id'        => 'ywrr_mail_template',
		'deps'      => $email_templates_deps,
	),
	'review_reminder_mail_item_link'        => array(
		'name'      => esc_html__( 'Set links destination', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'desc'      => esc_html__( 'Set the destination you want to show in the email', 'yith-woocommerce-review-reminder' ),
		'options'   => array(
			'product' => esc_html__( 'Product page', 'yith-woocommerce-review-reminder' ),
			'review'  => esc_html__( 'Default WooCommerce Reviews Tab', 'yith-woocommerce-review-reminder' ),
			'custom'  => esc_html__( 'Custom Anchor', 'yith-woocommerce-review-reminder' ),
		),
		'default'   => 'product',
		'id'        => 'ywrr_mail_item_link',
	),
	'review_reminder_mail_item_link_hash'   => array(
		'name'      => esc_html__( 'Set Custom Anchor', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'desc'      => esc_html__( 'HTML ID of the comments tab if different from the standard one', 'yith-woocommerce-review-reminder' ),
		'id'        => 'ywrr_mail_item_link_hash',
		'deps'      => array(
			'id'    => 'ywrr_mail_item_link',
			'value' => 'custom',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_comment_form_id'       => array(
		'name'      => esc_html__( 'Comment Form ID', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => '#commentform',
		'desc'      => esc_html__( 'HTML ID of the comments form. Leave blank if you don\'t want the page to scroll to the form when the customer visits the email links', 'yith-woocommerce-review-reminder' ),
		'id'        => 'ywrr_comment_form_id',
		'deps'      => array(
			'id'    => 'ywrr_mail_item_link',
			'value' => 'review,custom',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_comment_form_offset'   => array(
		'name'      => esc_html__( 'Comment Form Offset', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'number',
		'default'   => 0,
		'desc'      => esc_html__( 'Set a positive or negative value to adjust the scrolling offset when the customer visits the email links', 'yith-woocommerce-review-reminder' ),
		'id'        => 'ywrr_comment_form_offset',
		'deps'      => array(
			'id'    => 'ywrr_mail_item_link',
			'value' => 'review,custom',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_login_from_link'       => array(
		'name'      => esc_html__( 'Login from email link', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => '',
		'id'        => 'ywrr_login_from_link',
		'default'   => 'no',
	),
	'review_reminder_mail_enable_analytics' => array(
		'name'      => esc_html__( 'Add Google Analytics to email links', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => '',
		'id'        => 'ywrr_enable_analytics',
		'default'   => 'no',
	),
	'review_reminder_mail_campaign_source'  => array(
		'name'              => esc_html__( 'Campaign Source', 'yith-woocommerce-review-reminder' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => esc_html__( 'Referrer: google, citysearch, newsletter4', 'yith-woocommerce-review-reminder' ),
		'id'                => 'ywrr_campaign_source',
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywrr_enable_analytics',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_mail_campaign_medium'  => array(
		'name'              => esc_html__( 'Campaign Medium', 'yith-woocommerce-review-reminder' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => esc_html__( 'Marketing medium: cpc, banner, email', 'yith-woocommerce-review-reminder' ),
		'id'                => 'ywrr_campaign_medium',
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywrr_enable_analytics',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_mail_campaign_term'    => array(
		'name'        => esc_html__( 'Campaign Term', 'yith-woocommerce-review-reminder' ),
		'type'        => 'yith-field',
		'yith-type'   => 'ywrr-custom-checklist',
		'desc'        => esc_html__( 'Identify the paid keywords. Enter values separated by commas (for example, term1, term2)', 'yith-woocommerce-review-reminder' ),
		'id'          => 'ywrr_campaign_term',
		'placeholder' => esc_html__( 'Insert a term&hellip;', 'yith-woocommerce-review-reminder' ),
		'deps'        => array(
			'id'    => 'ywrr_enable_analytics',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_mail_campaign_content' => array(
		'name'      => esc_html__( 'Campaign Content', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'desc'      => esc_html__( 'Use to differentiate ads', 'yith-woocommerce-review-reminder' ),
		'id'        => 'ywrr_campaign_content',
		'deps'      => array(
			'id'    => 'ywrr_enable_analytics',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_mail_campaign_name'    => array(
		'name'              => esc_html__( 'Campaign Name', 'yith-woocommerce-review-reminder' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => esc_html__( 'Product, promo code, or slogan', 'yith-woocommerce-review-reminder' ),
		'id'                => 'ywrr_campaign_name',
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywrr_enable_analytics',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
	'review_reminder_mandrill_enable'       => array(
		'name'      => esc_html__( 'Enable Mandrill', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => esc_html__( 'Use Mandrill to send emails', 'yith-woocommerce-review-reminder' ),
		'id'        => 'ywrr_mandrill_enable',
		'default'   => 'no',
	),
	'review_reminder_mandrill_apikey'       => array(
		'name'              => esc_html__( 'Mandrill API Key', 'yith-woocommerce-review-reminder' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => '',
		'id'                => 'ywrr_mandrill_apikey',
		'default'           => '',
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywrr_mandrill_enable',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	),
);
$settings_end           = array(
	'review_reminder_mail_test'        => array(
		'name'      => esc_html__( 'Test email', 'yith-woocommerce-review-reminder' ),
		'desc'      => esc_html__( 'Type an email address to send a test email', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'text-button',
		'buttons'   => array(
			array(
				'name'  => esc_html__( 'Send Test Email', 'yith-woocommerce-review-reminder' ),
				'class' => 'ywrr-send-test-email',
			),
		),
		'default'   => get_option( 'admin_email' ),
		'id'        => 'ywrr_email_test',
	),
	'review_reminder_mail_section_end' => array(
		'type' => 'sectionend',
	),
);

return array(
	'mail' => array_merge( $settings_start, $settings_mail, $other_settings, $settings_end ),
);
