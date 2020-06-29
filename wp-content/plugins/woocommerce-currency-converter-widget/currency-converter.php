<?php
/**
 * Backwards compat.
 *
 *
 * @since 1.6.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/currency-converter.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/currency-converter.php', '/woocommerce-currency-converter.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
