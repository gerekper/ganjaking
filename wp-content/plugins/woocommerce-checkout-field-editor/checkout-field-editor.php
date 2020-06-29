<?php
/**
 * Backwards compat.
 *
 * @see https://github.com/woocommerce/woocommerce-checkout-field-editor/issues/36
 *
 * @since 1.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/checkout-field-editor.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/checkout-field-editor.php', '/woocommerce-checkout-field-editor.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
