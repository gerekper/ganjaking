<?php
/**
 * Backwards compat.
 *
 * @since 2.3.18
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/min-max-quantities.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/min-max-quantities.php', '/woocommerce-min-max-quantities.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
