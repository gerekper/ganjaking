<?php

$settings = array(

	'general' => array(

		'section_general_settings'                        => array(
			'name' => __( 'General settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_general',
		),

		'enabled'                                         => array(
			'name'      => __( 'Enable Subscription', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_enabled',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),

		'add_to_cart_label'                               => array(
			'name'      => __( '"Add to cart" label in subscription products', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_add_to_cart_label',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Subscribe', 'yith-woocommerce-subscription' ),
		),

		'allow_customer_renew_subscription'               => array(
			'name'      => __( 'Allow customer to renew subscriptions when is cancelled or expired', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_allow_customer_renew_subscription',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),

		/*
			'allow_customer_manual_renewals' => array(
			  'name'    => __( 'Accept Manual Renewals', 'yith-woocommerce-subscription' ),
			  'desc'    => '',
			  'id'      => 'ywsbs_allow_customer_manual_renewals',
			  'type'    => 'checkbox',
			  'default' => 'no'
		  ),
	*/
		  // since 1.2.6
		  'disable_the_reduction_of_order_stock_in_renew' => array(
			  'name'      => __( 'Disable the reduction of stock in the renew order', 'yith-woocommerce-subscription' ),
			  'desc'      => '',
			  'id'        => 'ywsbs_disable_the_reduction_of_order_stock_in_renew',
			  'type'      => 'yith-field',
			  'yith-type' => 'checkbox',
			  'default'   => 'no',
		  ),

		'delete_subscription_order_cancelled'             => array(
			'name'      => __( 'Delete subscription if the main order is cancelled', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_delete_subscription_order_cancelled',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),

		'enable_shop_manager'                             => array(
			'name'      => __( 'Enable Shop Manager to edit these options', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_enable_shop_manager',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),

		'enable_log'                                      => array(
			'name'      => __( 'Enable Log', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_enable_log',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),

		'section_end_form'                                => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_general_end_form',
		),


		'section_overdue_settings'                        => array(
			'name' => __( 'Overdue settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_overdue',
		),



		'enable_overdue_period'                           => array(
			'name'      => __( 'Enable Overdue time', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_enable_overdue_period',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),

		'overdue_period'                                  => array(
			'name'      => __( 'Overdue subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => __( 'days', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_overdue_period',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
		),

		'overdue_start_period'                            => array(
			'name'      => __( 'Overdue pending payment subscriptions after ........ hour(s)', 'yith-woocommerce-subscription' ),
			'desc'      => __( 'hours', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_overdue_start_period',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
		),


		'section_end_overdue_form'                        => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_overdue_end_form',
		),



		'section_suspend_settings'                        => array(
			'name' => __( 'Suspension settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_suspension',
		),


		'suspend_for_failed_recurring_payment'            => array(
			'name'      => __( 'Suspend a subscription if a recurring payment fail', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_suspend_for_failed_recurring_payment',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),


		'enable_suspension_period'                        => array(
			'name'      => __( 'Enable Suspended subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_enable_suspension_period',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),


		'suspension_period'                               => array(
			'name'      => __( 'Suspended subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => __( 'days', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_suspension_period',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '20',
		),

		'suspension_start_period'                         => array(
			'name'      => __( 'Suspend pending payment subscriptions after ........ hour(s)', 'yith-woocommerce-subscription' ),
			'desc'      => __( 'hours', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_suspension_start_period',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
		),

		'section_end_suspension_form'                     => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_suspend_end_form',
		),


		'section_cancel_settings'                         => array(
			'name' => __( 'Cancel settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_cancel',
		),

		'allow_customer_cancel_subscription'              => array(
			'name'      => __( 'Allow customer to cancel subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_allow_customer_cancel_subscription',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),


		'cancel_start_period'                             => array(
			'name'      => __( 'Cancel pending payment subscriptions after ........ hour(s)', 'yith-woocommerce-subscription' ),
			'desc'      => __( 'hours', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_cancel_start_period',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '24',
		),


		'section_end_cancel_form'                         => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_cancel_end_form',
		),


		'section_price_settings'                          => array(
			'name' => __( 'Price settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_price',
		),

		'show_trial_period'                               => array(
			'name'      => __( 'Show trial period', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_show_trial_period',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),

		'show_fee'                                        => array(
			'name'      => __( 'Show fee info', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_show_fee',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'yes',
		),

		'show_length_period'                              => array(
			'name'      => __( 'Show total subscription length', 'yith-woocommerce-subscription' ),
			'desc'      => '',
			'id'        => 'ywsbs_show_length_period',
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'default'   => 'no',
		),

		'subscription_total_amount'                       => array(
			'name'        => __( 'Show subscription total', 'yith-woocommerce-subscription' ),
			'desc-inline' => __( 'Show subscription total on the checkout page if setting max length option.', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_subscription_total_amount',
			'type'        => 'yith-field',
			'yith-type'   => 'checkbox',
			'default'     => 'no',
		),

		'section_end_price_form'                          => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_end_price_form',
		),

		'section_renew_settings'                          => array(
			'name' => __( 'Renew order settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_renew_section',
		),

		'renew_now_on_my_account'                         => array(
			'name'        => __( 'Show the Renew Now button on My Account > Orders ', 'yith-woocommerce-subscription' ),
			'desc-inline' => __( 'Permits to your customer to force the payment of a renew order if this has at least a failed attempt', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_renew_now_on_my_account',
			'type'        => 'yith-field',
			'yith-type'   => 'checkbox',
			'default'     => 'no',
		),

		'section_end_renew_section'                       => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_end_renew_section',
		),


	),

);

return apply_filters( 'yith_ywsbs_panel_settings_options', $settings );
