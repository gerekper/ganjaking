<?php
/**
 * General settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

// calculate shipping methods
$shipping_options = array();

if( version_compare( WC()->version, '2.6', '>=' ) ) {
	$available_zones = WC_Shipping_Zones::get_zones();

	if( ! empty( $available_zones ) ){
		foreach( $available_zones as $zone ){
			$zone_obj = new WC_Shipping_Zone( $zone['zone_id'] );
			$shipping_methods = $zone_obj->get_shipping_methods( true );

			if ( ! empty( $shipping_methods ) ) {
				foreach ( $shipping_methods as $key => $method ) {
					if ( 'yes' === $method->enabled ) {
						$shipping_options[ $method->id . ':' . $key ] = $zone_obj->get_zone_name() . ': ' . $method->get_title();
					}
				}
			}
		}
	}
}
else {
	$shipping_methods = WC()->shipping->load_shipping_methods();
	$shipping_options = array();

	if ( ! empty( $shipping_methods ) ) {
		foreach ( $shipping_methods as $key => $method ) {
			if ( 'yes' === $method->enabled ) {
				$shipping_options[ $key ] = $method->get_title();
			}
		}
	}
}

return apply_filters(
	'yith_wcdp_general_settings',
	array(
		'settings' => array_merge(
			array(
				'general-options' => array(
					'title' => __( 'General', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcdp_general_options'
				),

				'general-enable' => array(
					'title' => __( 'Enable plugin features', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this box to enable plugin', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_enable',
					'default' => 'no'
				),

				'general-enable-deposit' => array(
					'title' => __( 'Enable deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable deposit for all products (this behaviour can be overridden in product page)', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_enable',
					'default' => 'no'
				),

				'general-force-deposit' => array(
					'title' => __( 'Force deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to force deposit for all the products (this behaviour can be overridden in product page)', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_force',
					'default' => 'yes'
				),

				'general-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcdp_general_options_end'
				),
			),

			array(
				'general-deposit-options' => array(
					'title' => __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcdp_general_deposit_options'
				),

				'general-deposit-amount' => array(
					'title' => __( 'Deposit Amount', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'number',
					'desc' => __( 'Deposit required per product', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_amount',
					'css' => 'min-width: 100px;',
					'default' => 5,
					'custom_attributes' => array(
						'min' => 0,
						'max' => 9999999,
						'step' => 'any'
					),
					'desc_tip' => true
				),

				'general-deposit-virtual' => array(
					'title' => __( 'Deposit virtual', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option if you want your deposit products to be <b>virtual</b> (shipping, if any, will be paid on balance, for each balance order created). Otherwise deposit will use product configuration, while balance orders will contain virtual products (no shipping)', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_virtual',
					'default' => 'yes'
				),

				'general-deposit-shipping' => array(
					'title' => __( 'Shipping Handling', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'select',
					'desc' => __( 'Select how to handle shipping for products that need to be shipped, when balance order switches to completed', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_shipping',
					'options' => array(
						'use_default' => __( 'Use default shipping method', 'yith-woocommerce-deposits-and-down-payments' ),
						'let_user_choose' => __( 'Show shipping selection form to users on single product page', 'yith-woocommerce-deposits-and-down-payments' ),
						'admin_choose' => __( 'Select a shipping method to apply to all orders with products that require shipping', 'yith-woocommerce-deposits-and-down-payments' ),
					),
					'default' => 'let_user_choose',
					'desc_tip' => true
				),

				'general-deposit-shipping-admin-selection' => empty( $shipping_options ) ? array() : array(
					'title' => __( 'Shipping Method', 'yith-woocommerce-deposits-and-down-payments' ),
					'type' => 'select',
					'desc' => __( 'Select shipping method for products purchased with deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'id' => 'yith_wcdp_general_deposit_shipping_admin_selection',
					'options' => $shipping_options,
					'default' => '',
					'desc_tip' => true
				),

				'general-deposit-options-end' => array(
					'type'  => 'sectionend',
					'id'    => 'yith_wcdp_general_deposit_options_end'
				),
			)
		)
	)
);