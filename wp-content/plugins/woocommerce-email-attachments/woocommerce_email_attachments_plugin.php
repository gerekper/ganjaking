<?php
/**
 * Backwards compat.
 *
 * @since 3.0.9
 */
if ( ! defined( 'ABSPATH' ) )   {  exit;  }

$active_plugins = get_option( 'active_plugins', array() );

foreach ( $active_plugins as $key => $active_plugin ) 
{
	if ( strstr( $active_plugin, '/woocommerce_email_attachments_plugin.php' ) ) 
	{
		$active_plugins[ $key ] = str_replace( '/woocommerce_email_attachments_plugin.php', '/woocommerce-email-attachments.php', $active_plugin );
	}
}

update_option( 'active_plugins', $active_plugins );

