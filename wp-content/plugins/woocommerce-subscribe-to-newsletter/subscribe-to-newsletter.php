<?php
/**
 * Backwards compat.
 *
 * @since 2.3.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/subscribe-to-newsletter.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/subscribe-to-newsletter.php', '/woocommerce-subscribe-to-newsletter.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
