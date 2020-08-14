<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$available_gateway = YITH_Vendors_Gateways::get_available_gateways( 'object' );
$gateway_works_on_checkout = $payments_available_gateways_on_checkout = array();
$default_gateway = 'yith-wcmv-manual-payments';

if( ! empty( $available_gateway ) ){
	$gateway_works_on_checkout = array( $default_gateway => _x( 'Disabled', '[Admin] Option disabled', 'yith-woocommerce-product-vendors' ) );

	foreach ( $available_gateway as $gateway_slug => $gateway ) {
		/** @var YITH_Vendors_Gateway $gateway */
		if( $gateway->get_is_available_on_checkout() && $gateway->is_enabled() ){
			$gateway_works_on_checkout[ $gateway_slug ] = $gateway->get_method_title();
		}
	}

	$payments_available_gateways_on_checkout = array(
		'id' => 'yith_wcmv_checkout_gateway',
		'name' => _x( 'Pay commissions to vendors during checkout', '[Admin] Option name', 'yith-woocommerce-product-vendors' ),
		'desc' => _x( 'Select the gateway/service to use on checkout. All enabled gateway can be used in admin area', '[Admin] Option description', 'yith-woocommerce-product-vendors' ),
		'type' => 'select',
		'options' => $gateway_works_on_checkout,
		'default' => $default_gateway
	);
}

$gateways = apply_filters( 'yith_wcmv_panel_gateways_options', array(

		'default' => array(

			'payments_options_start' => array( 'type' => 'sectionstart' ),

			'payments_options_title' => array(
				'title' => __( 'Gateway options', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
			),

			'payments_options_table' => array( 'type' => 'yith_wcmv_gateways_list' ),

			'payments_available_gateways_on_checkout' => $payments_available_gateways_on_checkout,

			'payments_options_end' => array( 'type' => 'sectionend' ),
		)
	)
);

$to_return = $gateways['default'];

if ( ! empty( $_GET['section'] ) && ! empty( $gateways[ $_GET['section'] ] ) ) {
	$to_return = $gateways[ $_GET['section'] ];
}

return array( 'gateways' => $to_return );