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
$selected_list = get_option( 'yith_wcac_register_active-campaign_list' );


return apply_filters( 'yith_wcac_register_options', array(
	'register' => array(
		'register-general-options' => array(
			'title' => __( 'Register Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
            'desc'  => __( 'Set the options for <b>YITH Active Campaign Subscription Form</b> that appears below the registration form.', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_register_options'
		),
		'register-checkbox-enable' => array(
			'title'   => __( 'Enable "Newsletter subscription" on register page', 'yith-woocommerce-active-campaign' ),//@since 1.0.2
			'type'    => 'checkbox',
			'id'      => 'yith_wcac_register_subscription_checkbox_enable',
			'default' => ''
		),
		'register-checkbox' => array(
			'title'   => __( 'Show "Newsletter subscription" checkbox', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'id'      => 'yith_wcac_register_subscription_checkbox',
			'desc'    => __( 'When you select this option, a checkbox will be added to the register form, inviting users to subscribe to the
			newsletter; otherwise, users will automatically subscribe', 'yith-woocommerce-active-campaign' ),
			'default' => ''
		),

		'register-checkbox-label' => array(
			'title'   => __( '"Newsletter subscription" label', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'desc'    => __( 'Enter here the label you want to use for the "Newsletter subscription" checkbox', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_register_subscription_checkbox_label',
			'default' => __( 'Subscribe to our cool newsletter', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'register-checkbox-default' => array(
			'title'   => __( 'Show "Newsletter subscription" as checked', 'yith-woocommerce-active-campaign' ),
			'type'    => 'checkbox',
			'id'      => 'yith_wcac_register_subscription_checkbox_default',
			'desc'    => __( 'When you check this option, the "Newsletter subscription" checkbox will be printed as already checked',
                'yith-woocommerce-active-campaign' ),
			'default' => ''
		),

		'register-general-status' => array(
			'title'     => __( 'Status', 'yith-woocommerce-active-campaign' ),
			'type'      => 'select',
			'id'        => 'yith_wcac_register_status',
			'desc'      => __( 'Define the default contact status when users submit the form', 'yith-woocommerce-active-campaign' ),
			'options'   => array(
				'1' => __( 'Active', 'yith-woocommerce-active-campaign' ),
				'0' => __( 'Unsubscribe', 'yith-woocommerce-active-campaign' )
			),
			'default'   => '1'
		),

		'register-general-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_register_options'
		),

		'register-list-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_register_list_options'
		),

		'register-list' => array(
			'title'             => __( 'Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Select a list for new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_register_active-campaign_list',
			'options'           => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css'               => 'min-width:300px;',
			'class'             => 'list-select'
		),

		'register-tags' => array(
			'title'             => __( 'Auto-subscribe tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags which will be automatically added to new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_register_active-campaign_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'register-show-tags' => array(
			'title'             => __( 'Show tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags among which users can choose', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_register_active-campaign_show_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		),

		'register-tags-label' => array(
			'title' => __( 'Tags label', 'yith-woocommerce-active-campaign' ),
			'type'  => 'text',
			'desc'  => __( 'Type here a text that will be used as title for the Tags section on the checkout page', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_register_active-campaign_tags_label',
			'css'   => 'width:300px;',
		),

		'register-list-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_register_list_options'
		),

		'register-fields-options' => array(
			'title' => __( 'Field Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_register_fields_options'
		),

		'register-fields' => array(
			'title' => __( 'Fields', 'yith-wctc' ),
			'type'  => 'yith_wcac_custom_fields',
			'id'    => 'yith_wcac_register_custom_fields',
			'value' => ''
		),

		'register-fields-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_register_fields_options'
		),

		'register-style-options' => array(
			'title' => __( 'Style Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_register_style_fields_options'
		),

		'register-style-enable' => array(
			'title' => __( 'Enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'  => 'checkbox',
			'desc'  => __( 'Check this option to enable custom CSS', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_register_style_enable'
		),

		'register-style-custom' => array(
			'id'      => 'yith_wcac_register_custom_css',
			'name'    => __( 'Custom CSS', 'yith-woocommerce-active-campaign' ),
			'type'    => 'textarea',
			'desc'    => __( 'Enter your custom CSS for the register area here', 'yith-woocommerce-active-campaign' ),
			'default' => '',
			'css'     => 'width:100%;min-height:100px;'
		),

		'register-style-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_register_style_fields_options'
		),
	)
) );