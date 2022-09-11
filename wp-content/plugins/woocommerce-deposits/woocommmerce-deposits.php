<?php
/**
 * Backwards compat.
 *
 * @since 1.4.16
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/woocommmerce-deposits.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/woocommmerce-deposits.php', '/woocommerce-deposits.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
