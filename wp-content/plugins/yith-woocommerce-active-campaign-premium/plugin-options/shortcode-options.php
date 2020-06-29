<?php
/**
 * Shortcode settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

// retrieve lists
$list_options  = YITH_WCAC()->retrieve_lists();
$tags_options  = YITH_WCAC()->retrieve_tags();
$selected_list = get_option( 'yith_wcac_shortcode_active-campaign_list' );

return apply_filters( 'yith_wcac_shortcode_options', array(
	'shortcode' => array(
		'shortcode-general-options' => array(
			'title' => __( 'Shortcode Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => __( 'Insert the <b>[yith_wcac_subscription_form]</b> shortcode into your pages to print a subscription form; set the options for your form here', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_shortcode_options'
		),

		'shortcode-general-title' => array(
			'title'     => __( 'Form title', 'yith-woocommerce-active-campaign' ),
			'type'      => 'text',
			'id'        => 'yith_wcac_shortcode_title',
			'desc'      => __( 'Select a title for the newsletter subscription form', 'yith-woocommerce-active-campaign' ),
			'default'   => __( 'Newsletter', 'yith-woocommerce-active-campaign' ),
			'css'       => 'min-width:300px;'
		),

		'shortcode-general-submit-label' => array(
			'title'     => __( '"Submit" button label', 'yith-woocommerce-active-campaign' ),
			'type' => 'text',
			'id'        => 'yith_wcac_shortcode_submit_button_label',
			'desc'      => __( 'Select a label for the "Submit" button in the form', 'yith-woocommerce-active-campaign' ),
			'default'   => __( 'SUBMIT', 'yith-woocommerce-active-campaign' ),
			'css'       => 'min-width:300px;'
		),

		'shortcode-general-success-message' => array(
			'title'     => __( '"Successfully Registered" message', 'yith-woocommerce-active-campaign' ),
			'type'      => 'text',
			'id'        => 'yith_wcac_shortcode_success_message',
			'desc'      => __( 'Select a message to display when users complete their registration successfully',
                'yith-woocommerce-active-campaign' ),
			'default'   => __( 'Great! You\'re now subscribed to our newsletter', 'yith-woocommerce-active-campaign' ),
			'css'       => 'min-width:500px;'
		),

		'shortcode-general-show-privacy-field' => array(
			'title'   => __( 'Show privacy checkbox', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'id'      => 'yith_wcac_shortcode_show_privacy_field',
			'desc'    => __( 'Show checkbox to obtain explicit consent to collect personal data (Requested by GDPR regulations)', 'yith-woocommerce-active-campaign' ),
			'default' => 'no'
		),

		'shortcode-general-privacy-label' => array(
			'title'   => __( 'Privacy field label', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_shortcode_privacy_label',
			'desc'    => __( 'Label that describes privacy checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-active-campaign' ),
			'default' => __( 'Please, make sure to read and accept our %privacy_policy%', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'shortcode-general-hide-after-registration' => array(
			'title'     => __( 'Hide form after registration', 'yith-woocommerce-active-campaign' ),
			'type'      => 'checkbox',
			'id'        => 'yith_wcac_shortcode_hide_after_registration',
			'desc'      => __( 'If you select this option, the registration form will be hidden after a successful registration',
                'yith-woocommerce-active-campaign' ),
			'default'   => 'no'
		),

		'shortcode-general-status' => array(
			'title'     => __( 'Status', 'yith-woocommerce-active-campaign' ),
			'type'      => 'select',
			'id'        => 'yith_wcac_shortcode_status',
			'desc'      => __( 'Define the default contact status when users submit the form', 'yith-woocommerce-active-campaign' ),
			'options'   => array(
				'1' => __( 'Active', 'yith-woocommerce-active-campaign' ),
				'0' => __( 'Unsubscribe', 'yith-woocommerce-active-campaign' )
			),
			'default'   => '1'
		),

		'shortcode-general-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_shortcode_options'
		),

		'shortcode-list-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_shortcode_list_options'
		),

		'shortcode-list' => array(
			'title'             => __( 'Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Select a list for new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_shortcode_active-campaign_list',
			'options'           => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css'               => 'min-width:300px;',
			'class'             => 'list-select'
		),

		'shortcode-tags' => array(
			'title'             => __( 'Auto-subscribe tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags which will be automatically added to new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_shortcode_active-campaign_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'shortcode-show-tags' => array(
			'title'             => __( 'Show tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags among which users can choose', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_shortcode_active-campaign_show_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'shortcode-tags-label' => array(
			'title' => __( 'Tags label', 'yith-woocommerce-active-campaign' ),
			'type'  => 'text',
			'desc'  => __( 'Type here a text that will be used as title for the Tags section on the checkout page', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_shortcode_active-campaign_tags_label',
			'css'   => 'width:300px;',
		),

		'shortcode-list-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_shortcode_list_options'
		),

		'shortcode-fields-options' => array(
			'title' => __( 'Field Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_shortcode_items_options'
		),

		'shortcode-fields' => array(
			'title' => __( 'Fields', 'yith-woocommerce-active-campaign' ),
			'type'  => 'yith_wcac_custom_fields',
			'id'    => 'yith_wcac_shortcode_custom_fields',
			'value' => ''
		),

		'shortcode-fields-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_shortcode_items_options'
		),

		'shortcode-style-options' => array(
			'title' => __( 'Style Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_shortcode_style_fields_options'
		),

		'shortcode-style-enable' => array(
			'title' => __( 'Enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'  => 'checkbox',
			'desc'  => __( 'Check this option to enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_shortcode_style_enable'
		),

		'shortcode-style-button-corners' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_round_corners',
			'name'    => __( 'Round Corners for "Subscribe" Button', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Check this option  to make the button corners round', 'yith-woocommerce-active-campaign' ),
			'default' => 'yes'
		),

		'shortcode-style-button-background-color' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_background_color',
			'name'    => __( '"Subscribe" Button Background Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'shortcode-style-button-color' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_color',
			'name'    => __( '"Subscribe" Button Text Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'shortcode-style-button-border-color' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_border_color',
			'name'    => __( '"Subscribe" Button Border Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'shortcode-style-button-background-color-hover' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_background_hover_color',
			'name'    => __( '"Subscribe" Button Hover Background Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'shortcode-style-button-color-hover' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_hover_color',
			'name'    => __( '"Subscribe" Button Hover Text Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'shortcode-style-button-border-color-hover' => array(
			'id'      => 'yith_wcac_shortcode_subscribe_button_border_hover_color',
			'name'    => __( '"Subscribe" Button Hover Border Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'shortcode-style-custom' => array(
			'id'      => 'yith_wcac_shortcode_custom_css',
			'name'    => __( 'Custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'    => 'textarea',
			'desc'    => __( 'Enter here the custom CSS that will applied to the shortcode', 'yith-woocommerce-active-campaign' ),
			'default' => '',
			'css'     => 'width:100%;min-height:100px;'
		),

		'shortcode-style-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_shortcode_style_fields_options'
		),
	)
) );