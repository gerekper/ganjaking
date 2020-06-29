<?php
/**
 * Backwards compat.
 *
 *
 * @since 1.2.7
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/sale-flash-pro.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/sale-flash-pro.php', '/woocommerce-sale-flash-pro.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
