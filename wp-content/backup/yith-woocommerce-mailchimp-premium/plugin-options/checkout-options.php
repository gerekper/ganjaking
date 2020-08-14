<?php
/**
 * Checkout settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

// retrieve lists
$list_options = YITH_WCMC()->retrieve_lists();
$selected_list = get_option( 'yith_wcmc_mailchimp_list' );

return apply_filters( 'yith_wcmc_checkout_options', array(
	'checkout' => array(
		'checkout-options' => array(
			'title' => __( 'Mailchimp Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_checkout_options'
		),

		'checkout-trigger' => array(
			'title' => __( 'Register after', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select when the user should be added to the list', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_checkout_trigger',
			'options' => array(
				'never' => __( 'Never', 'yith-woocommerce-mailchimp' ),
				'completed' => __( 'Order completed', 'yith-woocommerce-mailchimp' ),
				'created' => __( 'Order placed', 'yith-woocommerce-mailchimp' )
			),
			'css' => 'min-width:300px;'
		),

		'checkout-checkbox' => array(
			'title' => __( 'Show "Newsletter subscription" checkbox', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_subscription_checkbox',
			'desc' => __( 'When you select this option, a checkbox will be added to the checkout form, inviting users to subscribe to the newsletter; otherwise, users will be subscribed automatically', 'yith-woocommerce-mailchimp' ),
			'default' => 'yes'
		),

		'checkout-checkbox-label' => array(
			'title' => __( '"Newsletter subscription" label', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Enter here the label you want to use for the "Newsletter subscription" checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_subscription_checkbox_label',
			'default' => __( 'Subscribe to our cool newsletter', 'yith-woocommerce-mailchimp' ),
			'css' => 'min-width:300px;'
		),

		'checkout-checkbox-position' => array(
			'title' => __( 'Position for "Newsletter subscription"', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select position for "Newsletter subscription" checkbox in the page', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_subscription_checkbox_position',
			'options' => apply_filters( 'yith_wcmc_checkbox_position_options', array(
				'above_customer' => __( 'Above customer details', 'yith-woocommerce-mailchimp' ),
				'below_customer' => __( 'Below customer details', 'yith-woocommerce-mailchimp' ),
				'above_place_order' => __( 'Above "Place order" button', 'yith-woocommerce-mailchimp' ),
				'below_place_order' => __( 'Below "Place order" button', 'yith-woocommerce-mailchimp' ),
				'above_total' => __( 'Above "Review order" total', 'yith-woocommerce-mailchimp' ),
				'above_billing' => __( 'Above billing details', 'yith-woocommerce-mailchimp' ),
				'below_billing' => __( 'Below billing details', 'yith-woocommerce-mailchimp' ),
				'above_shipping' => __( 'Above shipping details', 'yith-woocommerce-mailchimp' ),
			) ),
			'default' => 'below_customer',
			'css' => 'min-width:300px;'
		),

		'checkout-checkbox-default' => array(
			'title' => __( 'Show "Newsletter subscription" checked', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_subscription_checkbox_default',
			'desc' => __( 'When you check this option, "Newsletter subscription" checkbox will be printed as already checked', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'checkout-email-type' => array(
			'title' => __( 'Email type', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'id' => 'yith_wcmc_email_type',
			'desc' => __( 'User\'s preferential email type (HTML or plain text)', 'yith-woocommerce-mailchimp' ),
			'options' => array(
				'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
				'text' => __( 'Text', 'yith-woocommerce-mailchimp' )
			),
			'default' => 'html'
		),

		'checkout-double-optin' => array(
			'title' => __( 'Double Opt-in', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_double_optin',
			'desc' => __( 'When you check this option, MailChimp will send a confirmation email before adding the user to the list', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'checkout-update-existing' => array(
			'title' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_update_existing',
			'desc' => __( 'When you check this option, existing users will be updated and MailChimp servers will not show errors', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'checkout-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_checkout_options'
		),

		'checkout-list-basic-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_list_basic_options'
		),

		'checkout-list' => array(
			'title' => __( 'MailChimp list', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a list for the new user', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_mailchimp_list',
			'options' => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css' => 'min-width:300px;',
			'class' => 'list-select'
		),

		'checkout-list-basic-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_list_basic_options'
		),
	)
) );