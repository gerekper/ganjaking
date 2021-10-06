<?php
/**
 * Backwards compat.
 *
 *
 * @since 1.9.11
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/woocommmerce-bookings.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/woocommmerce-bookings.php', '/woocommerce-bookings.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
