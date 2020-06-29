<?php
/**
 * Backward compat.
 *
 * @since 2.2.9
 * @version 2.2.9
 *
 * @see https://github.com/woocommerce/woocommerce-shipping-per-product/issues/32
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/shipping-per-product.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/shipping-per-product.php', '/woocommerce-shipping-per-product.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
