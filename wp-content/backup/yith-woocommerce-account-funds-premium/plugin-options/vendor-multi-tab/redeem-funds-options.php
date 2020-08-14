<?php

$disabled_gateway = array();


if ( ! defined( 'YITH_PAYOUTS_PREMIUM' ) || ( defined('YITH_PAYOUTS_VERSION') && version_compare( YITH_PAYOUTS_VERSION, '1.0.12', '<' ) )) {
	$disabled_gateway[] = 'yith_payout';
}

if ( ! defined( 'YITH_WCSC_PREMIUM' ) || ( defined( 'YITH_WCSC_VERSION' )  && version_compare( YITH_WCSC_VERSION, '2.0.4', '<' ) ) ) {
	$disabled_gateway[] = 'yith_stripe_connect';
}

$disable_field_class = defined( 'YITH_WPV_PREMIUM' ) && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '3.5.3', '>=' ) ? '' : 'yith-disabled';

$string              = _x( 'Require %s to use this feature ', '[PART OF] Require YITH PayPal Payouts For WooCommerce Premium v1.0.12 or YITH Stripe Connect For WooCommerce Premium v2.0.4', 'yith-woocommerce-account-funds' );
$redeem_method_desc  = sprintf( '<br/><strong style="font-style: italic;">' . $string . '</strong>', "<a href='https://yithemes.com/themes/plugins/yith-paypal-payouts-for-woocommerce/' target='_blank'>YITH PayPal Payouts For WooCommerce Premium 1.0.12</a>" );
$settings            = array(
	'vendor-multi-tab-redeem-funds' => array(

		'redeeming_section_start'           => array(
			'type' => 'title',
			'name' => __( 'Funds Redeem Settings', 'yith-woocommerce-account-funds' )
		),
		'redeeming_description'             => empty( $disable_field_class ) ? array() :array(
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             => sprintf( '<p class="info-box">%s</p>', __( 'These features are available only with YITH WooCommerce MultiVendor Premium 3.5.3 or greater', 'yith-woocommerce-account-funds' ) )
		),
		'vendor_can_redeem'                 => array(
			'id'        => 'ywf_vendor_can_redeem',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'class'     => $disable_field_class,
			'name'      => __( 'Vendor can redeem', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'If enabled the vendor can redeem funds', 'yith-woocommerce-account-funds' ),
			'default'   => 'no'
		),
		'redeeming_min_fund_needs'          => array(
			'name'      => __( 'Minimum funds to redeem', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'class'     => $disable_field_class,
			'yith-type' => 'number',
			'id'        => 'ywf_min_fund_needs',
			'min'       => 0,
			'step'      => 0.1,
			'default'   => 50,
			'desc'      => __( 'Set the minimum balance necessary for a vendor to redeem funds', 'yith-woocommerce-account-funds' )
		),
		'redeeming_max_fund_needs'          => array(
			'name'      => __( 'Maximum funds to redeem', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'class'     => $disable_field_class,
			'yith-type' => 'number',
			'id'        => 'ywf_max_fund_redeem',
			'min'       => 0,
			'step'      => 0.1,
			'default'   => 100,
			'desc'      => __( 'Set the maximum funds that can be redeemed in a transaction, leave empty to disable this restriction.', 'yith-woocommerce-account-funds' )
		),
		'redeeming_gateway'                 => array(
			'id'        => 'ywf_redeeming_gateway',
			'class'     => $disable_field_class,
			'name'      => __( 'Gateway Method', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'Set the gateway to use for transferring funds from the site to the vendor (You will pay a fee to transfer money from the site to PayPal )', 'yith-woocommerce-account-funds' ) . $redeem_method_desc,
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
			/*	'yith_stripe_connect' => __( 'Stripe Connect', 'yith-woocommerce-account-funds' ),*/
				'yith_payout'         => __( 'PayPal Payouts', 'yith-woocommerce-account-funds' )
			),
			'data'      => array(
				'disabled' => $disabled_gateway
			),
			'default'   => 'yith_payout'
		),
		'redeeming_payment_type'            => array(
			'id'        => 'ywf_redeeming_payment_type',
			'name'      => __( 'Automatic Redeem Type', 'yith-woocommerce-account-funds' ),
			'class'     => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'none'                => __( 'None', 'yith-woocommerce-account-funds' ),
				'automatic'           => __( 'Automatically when the minimum threshold is reached', 'yith-woocommerce-account-funds' ),
				'automatic_with_date' => __( 'Automatically, on a specific day and when the minimum threshold is reached.', 'yith-woocommerce-account-funds' ),
			),
			'default'   => 'automatic'
		),
		'redeeming_day'                     => array(
			'id'        => 'ywf_redeeming_day',
			'class'     => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'name'      => __( 'Redeem Day', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'Set a day of the month for redeeming payment funds', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'       => 1,
			'max'       => 31,
			'default'   => 15,
			'deps'      => array(
				'id'    => 'ywf_redeeming_payment_type',
				'value' => 'automatic_with_date',
				'type'  => 'show'
			),
		),
		'redeeming_fund_manual'             => array(
			'id'        => 'ywf_enable_manual_redeeming',
			'class'     => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'name'      => __( 'Manual Redeem', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'desc'      => __( 'If enabled, the vendor can redeem funds from \'My Account\' Page', 'yith-woocommerce-account-funds' )
		),
		'redeeming_button_label'            => array(
			'class'     => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'id'        => 'ywf_redeeming_button_label',
			'name'      => __( 'Redeem Button', 'yith-woocommerce-account-funds' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Redeem funds!', 'yith-woocommerce-account-funds' ),
			'desc'      => __( 'Set the text for the redeem button visible on \'My Account\' page', 'yith-woocommerce-account-funds' ),
			'deps'      => array(
				'id'    => 'ywf_enable_manual_redeeming',
				'value' => 'yes',
				'type'  => 'show'
			),
		),
		'redeeming_button_text_color'       => array(
			'class'        => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'id'           => 'ywf_redeeming_button_text_color',
			'name'         => __( 'Redeem Button Text Color', 'yith-woocommerce-account-funds' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name'    => __( 'Default', 'yith-woocommerce-account-funds' ),
					'id'      => 'color',
					'default' => '#333333'
				),
				array(
					'name'    => __( 'Hover', 'yith-woocommerce-account-funds' ),
					'id'      => 'hover_color',
					'default' => '#333333'
				)
			),
			'desc'         => __( 'Set the color for the button text', 'yith-woocommerce-account-funds' ),
			'deps'         => array(
				'id'    => 'ywf_enable_manual_redeeming',
				'value' => 'yes',
				'type'  => 'show'
			),
		),
		'redeeming_button_background_color' => array(
			'id'           => 'ywf_redeeming_button_background_color',
			'class'        => count( $disabled_gateway ) == 2 ? 'yith-disabled': $disable_field_class,
			'name'         => __( 'Redeem Button Color', 'yith-woocommerce-account-funds' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name'    => __( 'Default', 'yith-woocommerce-account-funds' ),
					'id'      => 'color',
					'default' => '#eeeeee'
				),
				array(
					'name'    => __( 'Hover', 'yith-woocommerce-account-funds' ),
					'id'      => 'hover_color',
					'default' => '#d5d5d5'
				)
			),
			'desc'         => __( 'Set the color for the button background', 'yith-woocommerce-account-funds' ),
			'deps'         => array(
				'id'    => 'ywf_enable_manual_redeeming',
				'value' => 'yes',
				'type'  => 'show'
			),
		),
		'redeeming_section_end'             => array(
			'type' => 'sectionend',

		)
	)
);

return $settings;