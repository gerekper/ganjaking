<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$discount_symbol = get_option( 'yith_discount_type_discount' ) == 'fixed_cart' ? get_woocommerce_currency_symbol() : '%';
$settings        = array(
	'general-settings' => array(

		'charging_funds_settings_section_start' => array(
			'name' => __( 'Charging Account settings', 'yith-woocommerce-account-funds' ),
			'type' => 'title',
		),
		'funds_min_value'                       => array(
			'name'      => sprintf( '%s (%s)', __( 'Minimum deposit amount', 'yith-woocommerce-account-funds' ), get_woocommerce_currency_symbol() ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'       => 0,
			'step'      => 0.1,
			'default'   => 0,
			'id'        => 'yith_funds_min_value',
			'desc'      => __( 'Set a minimum required amount for deposits', 'yith-woocommerce-account-funds' ),

		),

		'funds_max_value'                     => array(
			'name'      => sprintf( '%s (%s)', __( 'Maximum deposit amount', 'yith-woocommerce-account-funds' ), get_woocommerce_currency_symbol() ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'       => 0,
			'step'      => 0.1,
			'default'   => '',
			'id'        => 'yith_funds_max_value',
			'desc'      => __( 'Set the maximum amount for each individual deposit. Leave it blank to make it unlimited.', 'yith-woocommerce-account-funds' ),
			'css'       => 'width:80px;'

		),
		'funds_step_by'                       => array(
			'name'      => __( 'Amount increments', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'Choose the increments of the deposit amount. For example, enter 5 if you want to let your users only select increments of $5', 'yith-woocommerce-account-funds' ),
			'id'        => 'yith_funds_step',
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'       => 1,
			'step'      => 1,
			'default'   => '1',
			'css'       => 'width:80px'
		),
		'funds_coupon'                        => array(
			'name'      => __( 'Use Coupon', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'If enabled, the customer can use the coupon code to purchase funds', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith_funds_enable_coupon'
		),
		'select_gateway'                      => array(
			'name'        => __( 'Payment method', 'yith-woocommerce-account-funds' ),
			'desc'        => __( 'Select payment method for deposits. Leave this field empty if you want to allow all gateways enabled in WooCommerce', 'yith-woocommerce-account-funds' ),
			'type'        => 'yith-field',
			'yith-type'   => 'select-buttons',
			'multiple' => true,
			'placeholder' => __( 'Select a Payment Method', 'yith-woocommerce-account-funds' ),
			'id'          => 'ywf_select_gateway',
			'options'     => ywf_get_gateway(),
			'default'     => '',

		),
		'charging_funds_settings_section_end' => array(
			'type' => 'sectionend',
		),
		'using_funds_settings_section_start'  => array(
			'name' => __( 'Using funds settings', 'yith-woocommerce-account-funds' ),
			'type' => 'title',
		),
		'discount_enable_discount'            => array(
			'name'      => __( 'Enable discount', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'desc'      => __( 'Apply a discount if customers use their funds to pay', 'yith-woocommerce-account-funds' ),
			'id'        => 'yith_discount_enable_discount'

		),
		'discount_type_discount'              => array(
			'name'      => __( 'Discount type', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'desc'      => __( 'Choose if the discount is fixed or in percentage', 'yith-woocommerce-account-funds' ),
			'yith-type' => 'radio',
			'options'   => array(
				'fixed_cart' => __( 'Fixed price', 'yith-woocommerce-account-funds' ),
				'percent'    => __( 'Percentage', 'yith-woocommerce-account-funds' )
			),
			'default'   => 'fixed_cart',
			'id'        => 'yith_discount_type_discount',
			'deps'      => array(
				'id'    => 'yith_discount_enable_discount',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),
		'discount_value'                      => array(
			'name'      => __( 'Discount amount', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'     => 0,
			'step'    => 0.5,
			'default' => 0,
			'id'      => 'yith_discount_value',
			'desc'    => __( 'Enter a value. Based on to the above selection, it will be calculated either as fixed amount or as percentage.', 'yith-woocommerce-account-funds' ),
			'deps'    => array(
				'id'    => 'yith_discount_enable_discount',
				'value' => 'yes',
				'type'  => 'hide'
			)

		),

		'payment_enable_partial'       => array(
			'name'      => __( 'Enable partial payments', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'Allow customers to pay for the order using their available funds and pay the rest using a different payment method.', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith_enable_partial_payment'
		),
		'using_funds_settings_section_end'    => array(
			'type' => 'sectionend',
		),

	)

);

return $settings;