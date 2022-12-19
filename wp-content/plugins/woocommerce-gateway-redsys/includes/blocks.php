<?php
/**
 *
 * WooCommerce Redsys Gateway Blocks
 *
 * @package WooCommerce Redsys Gateway
 * @since version 19.0.0
 */

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;

add_action( 'woocommerce_blocks_loaded', 'my_extension_woocommerce_blocks_support' );

function my_extension_woocommerce_blocks_support() {
//	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_Redsys() );
			}
		);
//	}
}
