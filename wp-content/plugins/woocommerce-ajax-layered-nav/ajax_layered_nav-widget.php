<?php
/**
 * Backwards compat.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/ajax_layered_nav-widget.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/ajax_layered_nav-widget.php', '/woocommerce-ajax-layered-nav.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
