<?php
/**
 * Backwards compat.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/woocommmerce-shipping-stamps.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/woocommmerce-shipping-stamps.php', '/woocommerce-shipping-stamps.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
