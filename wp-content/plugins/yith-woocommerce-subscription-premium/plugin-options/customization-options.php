<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
}

$style_colors  = ywsbs_get_status_colors();
$style_options = array();
foreach ( $style_colors as $current_status => $colors ) {

	$style_options[ $current_status . '_subscription_label' ] = array(
		// translators: Placeholder status label.
		'name'         => sprintf( esc_html_x( '%s subscription label colors', 'Placeholder status label', 'yith-woocommerce-subscription' ), ucfirst( ywsbs_get_status_label( $current_status ) ) ),

		'id'           => 'ywsbs_' . $current_status . '_subscription_status_style',
		'type'         => 'yith-field',
		'yith-type'    => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'name'    => __( 'Text color:', 'yith-woocommerce-subscription' ),
				'id'      => 'color',
				'default' => $colors['color'],
			),
			array(
				'name'    => __( 'Background color:', 'yith-woocommerce-subscription' ),
				'id'      => 'background-color',
				'default' => $colors['background-color'],
			),
		),
	);

}

$section1 = array(

	'section_customization_settings'        => array(
		'name' => esc_html__( 'Product Page Customization', 'yith-woocommerce-subscription' ),
		'type' => 'title',
		'id'   => 'ywsbs_section_customization',
	),

	'add_to_cart_label'                     => array(
		'name'      => esc_html__( '"Add to cart" label in subscription products', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Choose a label to replace the add to cart button label in subscription products.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_add_to_cart_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Subscribe', 'yith-woocommerce-subscription' ),
	),

	'show_trial_period'                     => array(
		'name'      => esc_html__( 'Show trial period', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable to show the trial period in the subscription product page.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_trial_period',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'show_trial_period_text'                => array(
		'name'      => esc_html__( 'Enter a text for the free trial period', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Use {{trialtime}} to show the trial period.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_trial_period_text',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => wp_kses_post( _x( 'Get a {{trialtime}} free trial!', 'Do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) ),
		'deps'      => array(
			'id'    => 'ywsbs_show_trial_period',
			'value' => 'yes',
		),
	),

	'show_trial_period_color'               => array(
		'name'      => esc_html__( 'Free trial text color', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Set the text color for the free trial text.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_trial_period_color',
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'default'   => '#467484',
		'deps'      => array(
			'id'    => 'ywsbs_show_trial_period',
			'value' => 'yes',
		),
	),

	'show_fee'                              => array(
		'name'      => __( 'Show fee info', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable to show the fee amount in the subscription product page.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_fee',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'show_fee_text'                         => array(
		'name'      => esc_html__( 'Enter a text for the fee info', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Use {{feeprice}} to show the fee amount.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_fee_text',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => wp_kses_post( _x( '+ a signup fee of {{feeprice}}', 'Do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) ),
		'deps'      => array(
			'id'    => 'ywsbs_show_fee',
			'value' => 'yes',
		),
	),

	'show_fee_color'                        => array(
		'name'      => esc_html__( 'Fee info text color', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Set the text color for the fee info text.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_fee_color_color',
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'default'   => '#467484',
		'deps'      => array(
			'id'    => 'ywsbs_show_fee',
			'value' => 'yes',
		),
	),

	'section_end_form'                      => array(
		'type' => 'sectionend',
		'id'   => 'ywsbs_section_customization_end_form',
	),

	'section_cart_settings'                 => array(
		'name' => esc_html__( 'Cart and Checkout Customization', 'yith-woocommerce-subscription' ),
		'type' => 'title',
		'id'   => 'ywsbs_section_cart_customization',
	),

	'place_order_label'                     => array(
		'name'      => esc_html__( '"Place Order" label in checkout page', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'This text replaces "Place order" button label, if there is at least one subscription product in the cart.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_place_order_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => wp_kses_post( __( 'Signup now', 'yith-woocommerce-subscription' ) ),
	),

	'show_trial_period_text_on_cart'        => array(
		'name'      => esc_html__( 'Enter a text for the free trial period to show on cart', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'This text will be used in the cart and checkout. Use {{trialtime}} to show the trial period.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_show_trial_period_text_on_cart',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => wp_kses_post( _x( '{{trialtime}} free trial', 'Do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) ),
	),

	'subscription_total_amount'             => array(
		'name'      => esc_html__( 'Show total subscription length', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable to show the total subscription length in cart and checkout.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_subscription_total_amount',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'total_subscription_length_text'        => array(
		'name'      => esc_html__( 'Enter a text for the subscription total', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Use {{sub-time}} and {{sub-total}} as placeholder', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_total_subscription_length_text',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => wp_kses_post( _x( 'Subscription total for {{sub-time}}: {{sub-total}}', 'Do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) ),
		'deps'      => array(
			'id'    => 'ywsbs_subscription_total_amount',
			'value' => 'yes',
		),
	),

	'show_next_billing_date'                => array(
		'name'      => esc_html__( 'Show Next Billing Date', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_show_next_billing_date',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'show_next_billing_date_text'           => array(
		'name'      => esc_html__( 'Enter a label for the next billing info', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_show_next_billing_date_text',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Next billing on:', 'yith-woocommerce-subscription' ),
		'deps'      => array(
			'id'    => 'ywsbs_show_next_billing_date',
			'value' => 'yes',
		),
	),


	'show_next_billing_date_text_for_trial' => array(
		'name'      => esc_html__( 'Enter a label for the next billing info in case of trial', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_show_next_billing_date_text_for_trial',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'First billing on:', 'yith-woocommerce-subscription' ),
		'deps'      => array(
			'id'    => 'ywsbs_show_next_billing_date',
			'value' => 'yes',
		),
	),

	'thank_you_page_layout'                 => array(
		'name'      => esc_html__( 'Thank you page layout', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Choose where to display the related subscription.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_thank_you_page_layout',
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'default'   => 'standard',
		'options'   => array(
			'standard' => esc_html__( 'Table mode underneath the order details.', 'yith-woocommerce-subscription' ),
			'box'      => esc_html__( 'In a separate box.', 'yith-woocommerce-subscription' ),
		),
	),

	'section_cart_customization_end_form'   => array(
		'type' => 'sectionend',
		'id'   => 'ywsbs_section_cart_customization_end_form',
	),

	'section_subscription_status_settings'  => array(
		'name' => esc_html__( 'Subscription status', 'yith-woocommerce-subscription' ),
		'type' => 'title',
		'id'   => 'ywsbs_subscription_status_settings',
	),
);

$section2 = array(

	'section_subscription_status_end_form'  => array(
		'type' => 'sectionend',
		'id'   => 'ywsbs_section_subscription_status_end_form',
	),

	'section_my_account_settings'           => array(
		'name' => esc_html__( 'Subscription section in My Account', 'yith-woocommerce-subscription' ),
		'type' => 'title',
		'id'   => 'ywsbs_my_account_settings',
	),

	'allow_customer_cancel_subscription'    => array(
		'name'      => esc_html__( 'Show the Cancel button on My Account > Subscriptions ', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable if you want to allow the customer to cancel a subscription. This option can be overridden by each subscription product.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_allow_customer_cancel_subscription',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'resubscribe_on_my_account'             => array(
		'name'      => esc_html__( 'Show the Resubscribe button on My Account > Subscriptions ', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable if you want to allow the customer to resubscribe an ended or deleted subscription.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_resubscribe_on_my_account',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),


	'resubscribe_condition'                 => array(
		'name'      => esc_html__( 'Maintain the same price of the previous subscription.', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable if you want to allow the customer to resubscribe with the same price of the expired subscription.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_resubscribe_condition',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'deps'      => array(
			'id'    => 'ywsbs_resubscribe_on_my_account',
			'value' => 'yes',
		),
	),

	'renew_now_on_my_account'               => array(
		'name'      => esc_html__( 'Show the Renew Now button on My Account > Orders ', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enable if you allow the customer to force payment if a renewal subscription has at least one failed attempt.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_renew_now_on_my_account',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),


	'subscription_action_style'             => array(
		'name'      => esc_html__( 'Pause/Cancel subscription style', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Choose the style of the actions "pause subscription" and "cancel subscription" in my account.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_subscription_action_style',
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'options'   => array(
			'buttons'  => esc_html__( 'Buttons', 'yith-woocommerce-subscription' ),
			'dropdown' => esc_html__( 'Dropdown', 'yith-woocommerce-subscription' ),
		),
		'default'   => 'buttons',
	),

	'text_pause_subscription_dropdown'      => array(
		'name'          => esc_html__( 'Text for pause subscription dropdown', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text to pause a subscription dropdown. Use {{max_pause_period}} to show the max length of pause, {{max_pause_number}} to show the max limit of pause for this subscription', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_pause_subscription_dropdown',
		'type'          => 'yith-field',
		'yith-type'     => 'textarea-editor',
		'textarea_rows' => 5,
		'default'       => __( '<strong>Pause subscription</strong><p>If you want to suspend the subscription without cancelling it. You can suspend it for max {{max_pause_period}}</p>', 'yith-woocommerce-subscription' ),
		'deps'          => array(
			'id'    => 'ywsbs_subscription_action_style',
			'value' => 'dropdown',
		),
	),

	'text_resume_subscription_dropdown'     => array(
		'name'          => esc_html__( 'Text for resume subscription dropdown', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text for resume subscription dropdown.', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_resume_subscription_dropdown',
		'type'          => 'yith-field',
		'yith-type'     => 'textarea-editor',
		'textarea_rows' => 5,
		'default'       => __( '<strong>Resume subscription</strong><p>If you want to resume the subscription and end the pause.</p>', 'yith-woocommerce-subscription' ),
		'deps'          => array(
			'id'    => 'ywsbs_subscription_action_style',
			'value' => 'dropdown',
		),
	),

	'text_cancel_subscription_dropdown'     => array(
		'name'          => esc_html__( 'Text for cancel subscription dropdown', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text for cancel subscription dropdown.', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_cancel_subscription_dropdown',
		'type'          => 'yith-field',
		'yith-type'     => 'textarea-editor',
		'classes'       => 'show-if-cancel',
		'textarea_rows' => 5,
		'default'       => __(
			'<strong>Cancel subscription</strong><p>If you want to cancel the subscription, you will lose access to the subscription you purchased.</p>',
			'yith-woocommerce-subscription'
		),
		'deps'          => array(
			'id'    => 'ywsbs_subscription_action_style',
			'value' => 'dropdown',
		),
	),

	'text_pause_subscription_modal'         => array(
		'name'          => esc_html__( 'Text for pause subscription modal', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text for pause subscription modal. Use {{max_pause_period}} to show the max length of pause, {{max_pause_number}} to show the max limit of the pause for this subscription.', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_pause_subscription_modal',
		'type'          => 'yith-field',
		'yith-type'     => 'textarea-editor',
		'textarea_rows' => 5,
		'default'       => __(
			'<strong>Are you sure you want to pause this subscription?</strong><p>You can pause it for {{max_pause_period}}. During this time
you will not pay anything but your access to the subscription products will be
blocked. You can pause this subscription only for {{max_pause_number}} times.</p>',
			'yith-woocommerce-subscription'
		),
	),


	'text_pause_subscription_button'        => array(
		'name'      => esc_html__( 'Text for pause subscription button', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_text_pause_subscription_button',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Yes, I want to pause', 'yith-woocommerce-subscription' ),
	),


	'text_resume_subscription_modal'        => array(
		'name'          => esc_html__( 'Text for resume subscription modal', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text for resume subscription modal. ', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_resume_subscription_modal',
		'type'          => 'yith-field',
		'yith-type'     => 'textarea-editor',
		'textarea_rows' => 5,
		// translator: placeholders html tags.
		'default'       => sprintf( esc_html_x( '%1$sAre you sure you want to resume this subscription?%2$s', 'placeholders html tags', 'yith-woocommerce-subscription' ), '<strong>', '</strong>' ), //phpcs:ignore
	),

	'text_resume_subscription_button'       => array(
		'name'      => esc_html__( 'Text for resume subscription button', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_text_resume_subscription_button',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Yes, I want to resume', 'yith-woocommerce-subscription' ),
	),


	'text_resume_subscription_close_button' => array(
		'name'      => esc_html__( 'Text to close modal windows for resume', 'yith-woocommerce-subscription' ),
		'desc'      => '',
		'id'        => 'ywsbs_text_resume_subscription_close_button',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'No, I want to keep it paused', 'yith-woocommerce-subscription' ),
	),

	'text_cancel_subscription_modal'        => array(
		'name'          => esc_html__( 'Text for cancel subscription modal', 'yith-woocommerce-subscription' ),
		'desc'          => esc_html__( 'Enter the text for cancel subscription modal.', 'yith-woocommerce-subscription' ),
		'id'            => 'ywsbs_text_cancel_subscription_modal',
		'type'          => 'yith-field',

		'yith-type'     => 'textarea-editor',
		'textarea_rows' => 5,
		// translators: placeholders html tags.
		'default'       => sprintf( esc_html_x( '%1$sAre you sure you want to cancel this subscription?%2$s', 'placeholders html tags', 'yith-woocommerce-subscription' ), '<strong>', '</strong>' ),
		'deps'          => array(
			'id'    => 'ywsbs_allow_customer_cancel_subscription',
			'value' => 'yes',
		),
	),


	'text_cancel_subscription_button'       => array(
		'name'      => esc_html__( 'Text for cancel subscription button', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enter the text for cancel subscription button.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_text_cancel_subscription_button',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Yes, I want to cancel', 'yith-woocommerce-subscription' ),
		'deps'      => array(
			'id'    => 'ywsbs_allow_customer_cancel_subscription',
			'value' => 'yes',
		),
	),

	'text_close_modal'                      => array(
		'name'      => esc_html__( 'Text to close modal window', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enter the text to close the modal window.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_text_close_modal',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'No, keep me subscribed', 'yith-woocommerce-subscription' ),
	),

	'text_switch_plan'                      => array(
		'name'      => esc_html__( 'Text to switch plan', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enter the text to switch subscription plan.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_text_switch_plan',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Change plan >', 'yith-woocommerce-subscription' ),
	),

	'text_buy_new_plan'                     => array(
		'name'      => esc_html__( 'Text to add the new plan to cart', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enter the text to add the new plan to cart.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_text_buy_new_plan',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Go to checkout >', 'yith-woocommerce-subscription' ),
	),

	'text_new_plan_on_cart'                 => array(
		'name'      => esc_html__( 'Text to show before the product name during the switch', 'yith-woocommerce-subscription' ),
		'desc'      => esc_html__( 'Enter the text to show before the product name during the switch.', 'yith-woocommerce-subscription' ),
		'id'        => 'ywsbs_text_new_plan_on_cart',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => esc_html__( 'Change plan to:', 'yith-woocommerce-subscription' ),
	),

	'section_my_account__end_form'          => array(
		'type' => 'sectionend',
		'id'   => 'ywsbs_my_account_end_form',
	),

);

$customization = array_merge( $section1, $style_options, $section2 );

$settings = array(
	'customization' => $customization,
);

return apply_filters( 'yith_ywsbs_panel_customization_options', $settings );
