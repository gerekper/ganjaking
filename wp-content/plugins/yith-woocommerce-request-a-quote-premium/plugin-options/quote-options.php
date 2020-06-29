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

$raq_email_quote_setting_page = esc_url( add_query_arg( array( 'section' => 'yith_ywraq_send_quote' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ) );
$raq_email_admin_setting_page = esc_url( add_query_arg( array( 'section' => 'yith_ywraq_quote_status' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ) );

$section1 = array(
	'quote_settings'        => array(
		'name' => esc_html__( 'Quote Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_quote_settings',
	),
	'enable_order_creation' => array(
		'name'      => esc_html__( 'Enable order creation', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the orders will be created. (Recommended)', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_enable_order_creation',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'email_quote_setting'   => array(
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'html'             => sprintf( '<div id="ywraq_email_quote_setting"><a class="button-secondary" href="%s">%s</a></div>', $raq_email_quote_setting_page, esc_html__( 'Edit the Quote email options', 'yith-woocommerce-request-a-quote' ) ),
		'id'               => 'ywraq_email_quote_setting',
		'yith-display-row' => true,
		'deps'             => array(
			'id'    => 'ywraq_enable_order_creation',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'show_accept_link'  => array(
		'name'      => esc_html__( 'Show users the link to accept the quote.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, "Accept" link will be shown in the quote both in the email received by the user and on My account page.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_accept_link',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'accept_link_label' => array(
		'name'      => esc_html__( 'Write in the text for your "Accept" link', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Insert the "Accept" link label', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_accept_link_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_show_accept_link',
			'value' => 'yes',
		),
		'default'   => esc_html__( 'Accept', 'yith-woocommerce-request-a-quote' ),
	),
	'page_accepted'     => array(
		'name'      => esc_html__( 'Redirect page', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Select the page where to redirect your user after the quote has been accepted.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_page_accepted',
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'options'   => ywraq_get_pages(),
		'deps'      => array(
			'id'    => 'ywraq_show_accept_link',
			'value' => 'yes',
		),
		'default'   => get_option( 'woocommerce_checkout_page_id' ),
	),
	'allow_add_to_cart' => array(
		'name'      => esc_html__( 'Allow adding products to the cart', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Allow adding products to the cart after accepting the quote', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_allow_add_to_cart',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'deps'      => array(
			'id'    => 'ywraq_show_accept_link',
			'value' => 'yes',
		),
	),

	'show_reject_link'                 => array(
		'name'      => esc_html__( 'Show "Reject" link', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, "Reject" link will be shown in the quote both in the email received by the user and on My account page.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_reject_link',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'reject_link_label'                => array(
		'name'      => esc_html__( 'Write in the text for your "Reject" link', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Insert the "Reject" link label.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_reject_link_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_show_reject_link',
			'value' => 'yes',
		),
		'default'   => esc_html__( 'Reject', 'yith-woocommerce-request-a-quote' ),
	),
	'email_admin_setting'              => array(
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'html'             => sprintf( '<div id="ywraq_email_admin_setting"><a class="button-secondary" href="%s">%s</a></div>', $raq_email_admin_setting_page, esc_html__( 'Edit the Accepted/Rejected Quote Email options', 'yith-woocommerce-request-a-quote' ) ),
		'id'               => 'ywraq_email_admin_setting',
		'yith-display-row' => true,
		'deps'             => array(
			'id'    => 'ywraq_show_accept_link',
			'value' => 'yes',
		),
	),
	'automate_send_quote'              => array(
		'name'      => esc_html__( 'Generate quotes automatically', 'yith-woocommerce-request-a-quote' ),
		'desc'      => wp_kses_post( __( 'If checked, an automatic quote will be generated and sent. The reply to quote request will be sent automatically.<br>The price of the products included in the request list will remain the same.', 'yith-woocommerce-request-a-quote' ) ),
		'id'        => 'ywraq_automate_send_quote',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'select_gateway'                   => array(
		'name'      => esc_html__( 'Payment method', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Select payment method for quote. Leave this field empty if you want to allow all gateways enabled in WooCommerce', 'yith-woocommerce-account-funds' ),
		'type'      => 'yith-field',
		'yith-type' => 'select-buttons',
		'id'        => 'ywraq_select_gateway',
		'options'   => ywraq_get_available_gateways(),
		'multiple'  => 'true',
	),
	'cron_time'                        => array(
		'name'      => esc_html__( 'Start a cron every', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Insert 0 to send the quote immediately.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_cron_time',
		'type'      => 'yith-field',
		'yith-type' => 'number',
		'default'   => '4',
		'deps'      => array(
			'id'    => 'ywraq_automate_send_quote',
			'value' => 'yes',
		),
	),
	'cron_time_type'                   => array(
		'name'      => '',
		'desc'      => esc_html__( 'Decide the time span for which the plugin will check new quote requests to be processed automatically.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_cron_time_type',
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'deps'      => array(
			'id'    => 'ywraq_automate_send_quote',
			'value' => 'yes',
		),
		'options'   => array(
			'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-request-a-quote' ),
			'hours'   => esc_html__( 'Hours', 'yith-woocommerce-request-a-quote' ),
			'days'    => esc_html__( 'Days', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'hours',
	),
	'expired_time'                     => array(
		'name'      => esc_html__( 'Set a default expiring date after', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( '(days). Quotes will automatically get an expiry date based on how many days you select above. Leave it empty if you don\'t want quotes to expire.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_expired_time',
		'type'      => 'yith-field',
		'yith-type' => 'number',
		'min'       => 0,
		'step'      => 1,
		'default'   => '0',
	),
	'calculate_default_shipping_quote' => array(
		'name'      => esc_html__( 'Add default shipping on quote', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked the default shipping will be added to the quote', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_calculate_default_shipping_quote',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'sum_multiple_shipping_costs'      => array(
		'name'      => esc_html__( 'Enable the option to add multiple shipping cost', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, from the editor\'s quote, it is possible to add more shipping costs that will be summed at checkout', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_sum_multiple_shipping_costs',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'enable_order_again'               => array(
		'name'      => esc_html__( 'Enable reorder', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Show the button to order again on My Account page', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_enable_order_again',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'order_again_button_label'         => array(
		'name'      => esc_html__( 'Text for the Request Quote Again button', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Insert here the text to show for the Request Quote Again button', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_order_again_button_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_enable_order_again',
			'value' => 'yes',
		),
		'default'   => esc_html__( 'Request Quote Again', 'yith-woocommerce-request-a-quote' ),
	),

	'quote_settings_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_quote_settings_end',
	),


);


return array( 'quote' => apply_filters( 'ywraq_quote_settings_options', $section1 ) );
