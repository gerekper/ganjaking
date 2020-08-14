<?php
/**
 * Checkout settings page
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
$selected_list = get_option( 'yith_wcac_active_campaign_list' );
$options       = array(
	'checkout' => array(
		'checkout-options' => array(
			'title' => __( 'Active Campaign Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_checkout_options'
		),

		'checkout-trigger' => array(
			'title'   => __( 'Register after', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'desc'    => __( 'Select the moment in which the user will be added to the list', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_checkout_trigger',
			'options' => array(
				'never'     => __( 'Never', 'yith-woocommerce-active-campaign' ),
				'completed' => __( 'Order completed', 'yith-woocommerce-active-campaign' ),
				'created'   => __( 'Order placed', 'yith-woocommerce-active-campaign' )
			),
			'css'     => 'min-width:300px;'
		),

		'checkout-checkbox' => array(
			'title'     => __( 'Show "Newsletter subscription" checkbox', 'yith-woocommerce-active-campaign' ),
			'type'      => 'checkbox',
			'id'        => 'yith_wcac_checkout_subscription_checkbox',
			'desc'      => __( 'If you select this option, a checkbox will be added to the checkout form, inviting users to subscribe to the
			newsletter; otherwise, users will be subscribed automatically', 'yith-woocommerce-active-campaign' ),
			'default'   => ''
		),

		'checkout-checkbox-label' => array(
			'title'   => __( '"Newsletter subscription" label', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'desc'    => __( 'Enter here the label you want to use for the "Newsletter subscription" checkbox. Use <code>%privacy_policy%</code> to add a link to your store privacy policy page', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_checkout_subscription_checkbox_label',
			'default' => __( 'Subscribe to our cool newsletter', 'yith-woocommerce-active-campaign' ),
			'css'     => 'min-width:300px;'
		),

		'checkout-checkbox-position' => array(
			'title'   => __( 'Position of "Newsletter subscription"', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'desc'    => __( 'Select the position of the "Newsletter subscription" checkbox on the page', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_checkout_subscription_checkbox_position',
			'options' => apply_filters( 'yith_wcac_checkbox_position_options', array(
				'above_customer'    => __( 'Above customer details', 'yith-woocommerce-active-campaign' ),
				'below_customer'    => __( 'Below customer details', 'yith-woocommerce-active-campaign' ),
				'above_place_order' => __( 'Above "Place order" button', 'yith-woocommerce-active-campaign' ),
				'below_place_order' => __( 'Below "Place order" button', 'yith-woocommerce-active-campaign' ),
				'above_total'       => __( 'Above "Review order" total', 'yith-woocommerce-active-campaign' ),
				'above_billing'     => __( 'Above billing details', 'yith-woocommerce-active-campaign' ),
				'below_billing'     => __( 'Below billing details', 'yith-woocommerce-active-campaign' ),
				'above_shipping'    => __( 'Above shipping details', 'yith-woocommerce-active-campaign' ),
			) ),
			'default' => 'below_customer',
			'css'     => 'min-width:300px;'
		),

		'checkout-checkbox-default' => array(
			'title'     => __( 'Show "Newsletter subscription" as checked', 'yith-woocommerce-active-campaign' ),
			'type'      => 'checkbox',
			'id'        => 'yith_wcac_checkout_subscription_checkbox_default',
			'desc'      => __( 'When you check this option, "Newsletter subscription" checkbox will be displayed as already checked',
                'yith-woocommerce-active-campaign' ),
			'default'   => ''
		),

		'checkout-contact-status' => array(
			'title'   => __( 'Contact status', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'id'      => 'yith_wcac_contact_status',
			'desc'    => __( 'Contact status in Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'options' => array(
				'1' => __( 'Active', 'yith-woocommerce-active-campaign' ),
				'2' => __( 'Unsubscribed', 'yith-woocommerce-active-campaign' )
			),
			'default' => '1'
		),

		'checkout-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_checkout_options'
		),

		'checkout-list-basic-options' => array(
			'title' => __( 'List Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_list_basic_options'
		),

		'checkout-mode' => array(
			'title'   => __( 'Integration mode', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'desc'    => __( 'Select whether to use a basic set of options or add integration settings', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_active_campaign_integration_mode',
			'options' => array(
				'simple'   => __( 'Simple', 'yith-woocommerce-active-campaign' ),
				'advanced' => __( 'Advanced', 'yith-woocommerce-active-campaign' )
			)
		),

		'checkout-list' => array(
			'title'             => __( 'Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Select a list for new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_active_campaign_list',
			'options'           => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css'               => 'min-width:300px;',
			'class'             => 'list-select'
		),

		'checkout-tags' => array(
			'title'             => __( 'Auto-subscribe tags', 'yith-woocommerce-active-campaign' ),
			'type'              => 'multiselect',
			'desc'              => __( 'Select tags which will be automatically added to new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_active_campaign_tags',
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',

		),

		'checkout-advanced' => array(
			'title' => __( 'Advanced options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'yith_wcac_advanced_integration',
			'id'    => 'yith_wcac_advanced_integration',
			'value' => ''
		),

		'checkout-list-basic-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_list_basic_options'
		),
	)
);

$advanced_options = array(
	'checkout-tags' => array(
		'title'             => __( 'Tags', 'yith-woocommerce-active-campaign' ),
		'type'              => 'multiselect',
		'desc'              => __( 'Select tags for the new user', 'yith-woocommerce-active-campaign' ),
		'id'                => 'yith_wcac_active_campaign_tags',
		'options'           => $tags_options,
		'custom_attributes' => empty( $tags_options ) ? array(
			'disabled' => 'disabled'
		) : array(),
		'class'             => 'chosen_select',
		'css'               => 'width:300px;',
	)
);


return apply_filters( 'yith_wcac_checkout_options', $options );