<?php
/**
 * Widget settings page
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
$selected_list = get_option( 'yith_wcac_widget_active-campaign_list' );


return apply_filters( 'yith_wcac_widget_options', array(
	'widget' => array(
		'widget-general-options' => array(
			'title' => __( 'Widget Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => sprintf( __( 'Set here the options for <b>YITH Active Campaign Subscription Form</b> widget; use the widget in you sidebars, by selecting it from <a href="%s">Appearance > Widgets</a>', 'yith-woocommerce-active-campaign' ), admin_url( 'widgets.php' ) ),
			'id'    => 'yith_wcac_widget_options'
		),

		'widget-general-title' => array(
			'title'   => __( 'Form title', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_widget_title',
			'desc'    => __( 'Select a title for the newsletter subscription form', 'yith-woocommerce-active-campaign' ),
			'default' => __( 'Newsletter', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'widget-general-submit-label' => array(
			'title'   => __( '"Submit" button label', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_widget_submit_button_label',
			'desc'    => __( 'Select a label for the "Submit" button in the form', 'yith-woocommerce-active-campaign' ),
			'default' => __( 'SUBMIT', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'widget-general-success-message' => array(
			'title'     => __( '"Successfully Registered" message', 'yith-woocommerce-active-campaign' ),
			'type'      => 'text',
			'id'        => 'yith_wcac_widget_success_message',
			'desc'      => __( 'Select a message to display when users complete their registration successfully',
                'yith-woocommerce-active-campaign' ),
			'default'   => __( 'Great! You\'re now subscribed to our newsletter', 'yith-woocommerce-active-campaign' ),
			'css'       => 'min-width:500px;'
		),

		'widget-general-show-privacy-field' => array(
			'title'   => __( 'Show privacy checkbox', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'id'      => 'yith_wcac_widget_show_privacy_field',
			'desc'    => __( 'Show checkbox to obtain explicit consent to collect personal data (Requested by GDPR regulations)', 'yith-woocommerce-active-campaign' ),
			'default' => 'no'
		),

		'widget-general-privacy-label' => array(
			'title'   => __( 'Privacy field label', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_widget_privacy_label',
			'desc'    => __( 'Label that describes privacy checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-active-campaign' ),
			'default' => __( 'Please, make sure to read and accept our %privacy_policy%', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'widget-general-hide-after-registration' => array(
			'title'     => __( 'Hide form after registration', 'yith-woocommerce-active-campaign' ),
			'type'      => 'checkbox',
			'id'        => 'yith_wcac_widget_hide_after_registration',
			'desc'      => __( 'When you select this option, the registration form will be hidden after a successful registration', 'yith-woocommerce-active-campaign' ),
			'default'   => 'no'
		),

		'widget-general-status' => array(
			'title'     => __( 'Status', 'yith-woocommerce-active-campaign' ),
			'type'      => 'select',
			'id'        => 'yith_wcac_widget_status',
			'desc'      => __( 'Define the default contact status when users submit the form', 'yith-woocommerce-active-campaign' ),
			'options'   => array(
				'1' => __( 'Active', 'yith-woocommerce-active-campaign' ),
				'0' => __( 'Unsubscribe', 'yith-woocommerce-active-campaign' )
			),
			'default'   => '1'
		),

		'widget-general-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_widget_options'
		),

		'widget-list-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_widget_list_options'
		),

		'widget-list' => array(
			'title'             => __( 'Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Select a list for new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_widget_active-campaign_list',
			'options'           => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css'               => 'min-width:300px;',
			'class'             => 'list-select'
		),

		'widget-tags' => array(
			'title'             => __( 'Auto-subscribe tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags which will be automatically added to new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_widget_active-campaign_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'widget-show-tags' => array(
			'title'             => __( 'Show tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags among which users can choose', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_widget_active-campaign_show_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'widget-tags-label' => array(
			'title' => __( 'Tags label', 'yith-woocommerce-active-campaign' ),
			'type'  => 'text',
			'desc'  => __( 'Type here a text that will be used as title for the Tags section on the checkout page', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_widget_active-campaign_tags_label',
			'css'   => 'width:300px;',
		),

		'widget-list-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_widget_list_options'
		),

		'widget-fields-options' => array(
			'title' => __( 'Field Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_widget_fields_options'
		),

		'widget-fields' => array(
			'title' => __( 'Fields', 'yith-wctc' ),
			'type'  => 'yith_wcac_custom_fields',
			'id'    => 'yith_wcac_widget_custom_fields',
			'value' => ''
		),

		'widget-fields-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_widget_fields_options'
		),

		'widget-style-options' => array(
			'title' => __( 'Style Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_widget_style_fields_options'
		),

		'widget-style-enable' => array(
			'title' => __( 'Enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'  => 'checkbox',
			'desc'  => __( 'Check this option to enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_widget_style_enable'
		),

		'widget-style-button-corners' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_round_corners',
			'name'    => __( 'Round Corners for "Subscribe" Button', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Check this option to make the button corners round', 'yith-woocommerce-active-campaign' ),
			'default' => 'yes'
		),

		'widget-style-button-background-color' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_background_color',
			'name'    => __( '"Subscribe" Button Background Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'widget-style-button-color' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_color',
			'name'    => __( '"Subscribe" Button Text Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'widget-style-button-border-color' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_border_color',
			'name'    => __( '"Subscribe" Button Border Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#ebe9eb'
		),

		'widget-style-button-background-color-hover' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_background_hover_color',
			'name'    => __( '"Subscribe" Button Hover Background Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'widget-style-button-color-hover' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_hover_color',
			'name'    => __( '"Subscribe" Button Hover Text Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#515151'
		),

		'widget-style-button-border-color-hover' => array(
			'id'      => 'yith_wcac_widget_subscribe_button_border_hover_color',
			'name'    => __( '"Subscribe" Button Hover Border Color', 'yith-woocommerce-active-campaign' ),
			'type'    => 'color',
			'desc'    => '',
			'default' => '#dad8da'
		),

		'widget-style-custom' => array(
			'id'      => 'yith_wcac_widget_custom_css',
			'name'    => __( 'Custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'    => 'textarea',
			'desc'    => __( 'Enter your custom CSS for the widget here', 'yith-woocommerce-active-campaign' ),
			'default' => '',
			'css'     => 'width:100%;min-height:100px;'
		),

		'widget-style-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_widget_style_fields_options'
		),
	)
) );