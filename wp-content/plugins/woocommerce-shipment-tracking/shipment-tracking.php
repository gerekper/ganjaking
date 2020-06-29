<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backwards compat.
 *
 * @since 1.6.0
 */

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/shipment-tracking.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/shipment-tracking.php', '/woocommerce-shipment-tracking.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
