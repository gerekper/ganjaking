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
$selected_list = get_option( 'yith_wcmc_register_mailchimp_list' );
$groups_options = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_groups( $selected_list ) : array();

return apply_filters( 'yith_wcmc_register_options', array(
	'register' => array(
		'register-options' => array(
			'title' => __( 'Mailchimp Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_register_options'
		),

		'register-subscription' => array(
			'title' => __( 'Subscribe customers on registration', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_register_subscription',
			'desc' => __( 'When you select this option, customers will be subscribed to your newsletter during registration process', 'yith-woocommerce-mailchimp' ),
			'default' => 'no'
		),

		'register-checkbox' => array(
			'title' => __( 'Show "Newsletter subscription" checkbox', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_register_subscription_checkbox',
			'desc' => __( 'When you select this option, a checkbox will be added to the registration form, inviting users to subscribe to the newsletter; otherwise, users will be subscribed automatically', 'yith-woocommerce-mailchimp' ),
			'default' => 'yes'
		),

		'register-checkbox-label' => array(
			'title' => __( '"Newsletter subscription" label', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Enter here the label you want to use for the "Newsletter subscription" checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_register_subscription_checkbox_label',
			'default' => __( 'Subscribe to our cool newsletter', 'yith-woocommerce-mailchimp' ),
			'css' => 'min-width:300px;'
		),

		'register-checkbox-default' => array(
			'title' => __( 'Show "Newsletter subscription" checked', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_register_subscription_checkbox_default',
			'desc' => __( 'When you check this option, "Newsletter subscription" checkbox will be printed as already checked', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'register-email-type' => array(
			'title' => __( 'Email type', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'id' => 'yith_wcmc_register_email_type',
			'desc' => __( 'User\'s preferential email type (HTML or plain text)', 'yith-woocommerce-mailchimp' ),
			'options' => array(
				'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
				'text' => __( 'Text', 'yith-woocommerce-mailchimp' )
			),
			'default' => 'html'
		),

		'register-double-optin' => array(
			'title' => __( 'Double Opt-in', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_register_double_optin',
			'desc' => __( 'When you check this option, MailChimp will send a confirmation email before adding the user to the list', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'register-update-existing' => array(
			'title' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_register_update_existing',
			'desc' => __( 'When you check this option, existing users will be updated and MailChimp servers will not show errors', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'register-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_register_options'
		),

		'register-list-basic-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_register_list_basic_options'
		),

		'register-list' => array(
			'title' => __( 'MailChimp list', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a list for the new user', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_register_mailchimp_list',
			'options' => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css' => 'min-width:300px;',
			'class' => 'list-select'
		),

		'register-groups' => array(
			'title' => __( 'Interest groups', 'yith-woocommerce-mailchimp' ),
			'type' => 'multiselect',
			'desc' => __( 'Select an interest group for the new user', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_register_mailchimp_groups',
			'options' => $groups_options,
			'custom_attributes' => empty( $groups_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class' => 'chosen_select',
			'css' => 'width:300px;'
		),

		'register-list-basic-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_register_list_basic_options'
		),
	)
) );