<?php
/**
 * GENERAL ARRAY OPTIONS
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

$general = array(

	'general' => array(

		'general-options' => array(
			'title' => __( 'General Options', 'yith-woocommerce-waiting-list' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcwtl-general-options',
		),

		'enable-waiting-list' => array(
			'id'        => 'yith-wcwtl-enable',
			'title'     => __( 'Enable Waiting List', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'waiting-list-success-msg' => array(
			'id'        => 'yith-wcwtl-button-success-msg',
			'title'     => __( 'Subscription message', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Message for successful subscription in the waiting list.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'You have been added to the waiting list of this product.', 'yith-woocommerce-waiting-list' ),
		),

		'waiting-list-success-msg-double-optin' => array(
			'id'        => 'yith-wcwtl-button-success-msg-double-optin',
			'title'     => __( 'Subscription message with Double Opt-in enabled', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Message for successful subscription in the waiting list when the option "Double Opt-in" is enabled', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Please confirm your subscription to the waiting list, through the email that we have just sent you', 'yith-woocommerce-waiting-list' ),
		),

		'waiting-list-leave-msg' => array(
			'id'        => 'yith-wcwtl-button-leave-msg',
			'title'     => __( 'Removal message', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Message for successful removal from the waiting list.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'You have been removed from the waiting list of this product.', 'yith-woocommerce-waiting-list' ),
		),

		'waiting-list-error-msg' => array(
			'id'        => 'yith-wcwtl-button-error-msg',
			'title'     => __( 'Error message', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Error message showed when a user try to subscribe to a waiting list.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'An error has occurred or you\'re already register in this waiting list. Please try again.', 'yith-woocommerce-waiting-list' ),
		),

		'waiting-list-error-msg-user-already-subscribed' => array(
			'id'        => 'yith-wcwtl-button-error-msg-for-user-already-subscribed',
			'title'     => __( 'Error message for users already subscribed', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Error message showed when a user tries to subscribe a waiting list in which that email address already appears.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'This email address is already recorded for this waiting list', 'yith-woocommerce-waiting-list' ),
		),

		'waiting-list-auto-mailout' => array(
			'id'        => 'yith-wcwtl-auto-mailout',
			'title'     => __( 'Automatic email', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'When a status product is set back as \'In-stock\', this option sends an email to all the users in the waiting list.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'waiting-list-keep-after-email' => array(
			'id'        => 'yith-wcwtl-keep-after-email',
			'title'     => __( 'Keep the list after email', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Keep the waiting list after sending the email.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'waiting-list-inverted-exclusion' => array(
			'id'        => 'yith-wcwtl-exclusion-inverted',
			'title'     => __( 'Invert Exclusion', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Use the elements in the exclusion list as the active ones', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'waiting-ajax-submit' => array(
			'id'        => 'yith-wcwtl-ajax_submit',
			'title'     => __( 'Enable AJAX form', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Enable AJAX form submit on frontend.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'waiting-double-optin-subscription' => array(
			'id'            => 'yith-wcwtl-enable-double-optin',
			'title'         => __( 'Enable Double Opt-in', 'yith-woocommerce-waiting-list' ),
			'desc'          => __( 'Enable Double Opt-in subscription method for waiting list.', 'yith-woocommerce-waiting-list' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		),

		'waiting-double-optin-subscription-logged' => array(
			'id'            => 'yith-wcwtl-enable-double-optin-logged',
			'desc'          => __( 'Enable Double Opt-in also for logged in customers.', 'yith-woocommerce-waiting-list' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end',
		),

		'waiting-privacy-checkbox' => array(
			'id'        => 'yith-wcwtl-enable-privacy-checkbox',
			'title'     => __( 'Enable Privacy checkbox', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'Add a checkbox for the Privacy Policy to the Waiting List form. When shown, this checkbox is required to subscribe to the list.', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'waiting-privacy-checkbox-text' => array(
			'id'        => 'yith-wcwtl-privacy-checkbox-text',
			'title'     => __( 'Privacy checkbox label', 'yith-woocommerce-waiting-list' ),
			'desc'      => __( 'The text for the privacy policy checkbox in waiting list form. You can use the shortcode [terms] and [privacy_policy] (from WooCommerce 3.4.0).', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'default'   => __( 'Your email will be used to notify you about product availability. You can read more in our [privacy_policy].', 'yith-woocommerce-waiting-list' ),
			'deps'      => array(
				'id'    => 'yith-wcwtl-enable-privacy-checkbox',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		'general-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcwtl-general-options',
		),

		'mandrill-options' => array(
			'title' => __( 'Mandrill Options', 'yith-woocommerce-waiting-list' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcwtl-mandrill-options',
		),

		'enable-mandrill-list' => array(
			'id'        => 'yith-wcwtl-use-mandrill',
			'title'     => __( 'Use Mandrill', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'mandrill-api-key' => array(
			'id'        => 'yith-wcwtl-mandrill-api-key',
			'title'     => __( 'Mandrill API Key', 'yith-woocommerce-waiting-list' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
			'css'       => 'max-width:350px;',
		),

		'mandrill-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcwtl-mandrill-options',
		),
	),
);

return apply_filters( 'yith_wcwt_panel_general_options', $general );