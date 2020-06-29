<?php
/**
 * Widget settings page
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
$selected_list = get_option( 'yith_wcmc_widget_mailchimp_list' );

// retrieve groups
$groups_options = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_groups( $selected_list ) : array();

return apply_filters( 'yith_wcmc_widget_options', array(
	'widget' => array(
		'widget-general-options' => array(
			'title' => __( 'Widget Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => sprintf( __( 'Set here the options for <b>YITH MailChimp Subscription Form</b> widget; use the widget in you sidebars, by selecting it from <a href="%s">Appearance > Widgets</a>', 'yith-woocommerce-mailchimp' ), admin_url( 'widgets.php' ) ),
			'id' => 'yith_wcmc_widget_options'
		),

		'widget-general-title' => array(
			'title' => __( 'Form title', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'id' => 'yith_wcmc_widget_title',
			'desc' => __( 'Select a title for the newsletter subscription form', 'yith-woocommerce-mailchimp' ),
			'default' => __( 'Newsletter', 'yith-woocommerce-mailchimp' ),
			'css' => 'min-width:300px;'
		),

		'widget-general-submit-label' => array(
			'title' => __( '"Submit" button label', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'id' => 'yith_wcmc_widget_submit_button_label',
			'desc' => __( 'Select a label for the "Submit" button found in the form', 'yith-woocommerce-mailchimp' ),
			'default' => __( 'SUBMIT', 'yith-woocommerce-mailchimp' ),
			'css' => 'min-width:300px;'
		),

		'widget-general-success-message' => array(
			'title' => __( '"Successfully Registered" message', 'yith-woocommerce-mailchimp' ),
			'type' => 'textarea',
			'id' => 'yith_wcmc_widget_success_message',
			'desc' => __( 'Select a message to display to users when registration has been completed successfully', 'yith-woocommerce-mailchimp' ),
			'default' => __( 'Great! You\'re now subscribed to our newsletter', 'yith-woocommerce-mailchimp' ),
			'css' => 'min-width:500px; min-height:100px;'
		),

		'widget-general-show-privacy-field' => array(
			'title' => __( 'Show privacy checkbox', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_widget_show_privacy_field',
			'desc' => __( 'Show checkbox to obtain explicit consent to collect personal data (Requested by GDPR regulations)', 'yith-woocommerce-mailchimp' ),
			'default' => 'no'
		),

		'widget-general-privacy-label' => array(
			'title' => __( 'Privacy field label', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'id' => 'yith_wcmc_widget_privacy_label',
			'desc' => __( 'Label that describes privacy checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-mailchimp' ),
			'default' => __( 'Please, make sure to read and accept our %privacy_policy%', 'yith-woocommerce-mailchmp' ),
			'css' => 'min-width:300px;'
		),

		'widget-general-hide-after-registration' => array(
			'title' => __( 'Hide form after registration', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_widget_hide_after_registration',
			'desc' => __( 'When you select this option, the registration form will be hidden after a successful registration', 'yith-woocommerce-mailchimp' ),
			'default' => 'no'
		),

		'widget-general-email-type' => array(
			'title' => __( 'Email type', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'id' => 'yith_wcmc_widget_email_type',
			'desc' => __( 'User preferential email type (HTML or plain text)', 'yith-woocommerce-mailchimp' ),
			'options' => array(
				'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
				'text' => __( 'Text', 'yith-woocommerce-mailchimp' )
			),
			'default' => 'html'
		),

		'widget-general-double-optin' => array(
			'title' => __( 'Double Opt-in', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_widget_double_optin',
			'desc' => __( 'When you check this option, MailChimp will send a confirmation email before adding the user to the list', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'widget-general-update-existing' => array(
			'title' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_widget_update_existing',
			'desc' => __( 'When you check this option, existing users will be updated and MailChimp servers will not show errors', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'widget-general-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_widget_options'
		),

		'widget-list-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_widget_list_options'
		),

		'widget-list' => array(
			'title' => __( 'MailChimp list', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a list for the new user', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_widget_mailchimp_list',
			'options' => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css' => 'min-width:300px;',
			'class' => 'list-select'
		),

		'widget-group' => array(
			'title' => __( 'Auto-subscribe interest groups', 'yith-woocommerce-mailchimp' ),
			'type' => 'multiselect',
			'desc' => __( 'Select an interest group to which new users are automatically added', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_widget_mailchimp_groups',
			'options' => $groups_options,
			'custom_attributes' => empty( $groups_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class' => 'chosen_select group-select',
			'css' => 'width:300px;'
		),

		'widget-group-selectable' => array(
			'title' => __( 'Show the following interest groups', 'yith-woocommerce-mailchimp' ),
			'type' => 'multiselect',
			'desc' => __( 'Select interests groups that user can choose among', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_widget_mailchimp_groups_selectable',
			'options' => $groups_options,
			'custom_attributes' => empty( $groups_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class' => 'chosen_select group-select',
			'css' => 'width:300px;'
		),

		'widget-list-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_widget_list_options'
		),

		'widget-fields-options' => array(
			'title' => __( 'Field Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_widget_fields_options'
		),

		'widget-fields' => array(
			'title' => __( 'Fields', 'yith-wctc' ),
			'type' => 'yith_wcmc_custom_fields',
			'id' => 'yith_wcmc_widget_custom_fields',
			'value' => ''
		),

		'widget-fields-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_widget_fields_options'
		),

		'widget-style-options' => array(
			'title' => __( 'Style Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_widget_style_fields_options'
		),

		'widget-style-enable' => array(
			'title' => __( 'Enable custom CSS', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'desc' => __( 'Check this option to enable custom CSS handling', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_widget_style_enable'
		),

		'widget-style-button-corners' => array(
			'id'        => 'yith_wcmc_widget_subscribe_button_round_corners',
			'name'      => __( 'Round Corners for "Subscribe" Button', 'yith-woocommerce-mailchimp' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Check this option  to make button corners round', 'yith-woocommerce-mailchimp' ),
			'default'   => 'yes'
		),

		'widget-style-button-background-color' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_background_color',
			'name'    => __( '"Subscribe" Button Background Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'widget-style-button-color' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_color',
			'name'    => __( '"Subscribe" Button Text Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'widget-style-button-border-color' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_border_color',
			'name'    => __( '"Subscribe" Button Border Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'widget-style-button-background-color-hover' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_background_hover_color',
			'name'    => __( '"Subscribe" Button Hover Background Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'widget-style-button-color-hover' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_hover_color',
			'name'    => __( '"Subscribe" Button Hover Text Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'widget-style-button-border-color-hover' => array(
			'id'      => 'yith_wcmc_widget_subscribe_button_border_hover_color',
			'name'    => __( '"Subscribe" Button Hover Border Color', 'yith-woocommerce-mailchimp' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'widget-style-custom' => array(
			'id'      => 'yith_wcmc_widget_custom_css',
			'name'    => __( 'Custom css', 'yith-woocommerce-mailchimp' ),
			'type'    => 'textarea',
			'desc'    => __( 'Insert here your custom CSS for the widget', 'yith-woocommerce-mailchimp' ),
			'default' => '',
			'css' => 'width:100%;min-height:100px;'
		),

		'widget-style-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_widget_style_fields_options'
		),
	)
) );