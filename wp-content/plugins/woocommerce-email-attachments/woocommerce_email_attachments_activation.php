<?php
/**
 * Holds the functions needed for backward compatibility for activation/deactivation/uninstall 
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  } // Exit if accessed directly

global $wc_email_att_are_activation_hooks, $wc_email_att_plugin_file;

if( ! function_exists( 'handler_wc_email_attachments_activate' ) )
{
	function handler_wc_email_attachments_activate()
	{
		global $wc_email_att_activation;
		
		wc_email_att_load_plugin_version();
		$wc_email_att_activation->on_activate();
	}
}

if( ! function_exists( 'handler_wc_email_attachments_deactivate' ) )
{
	function handler_wc_email_attachments_deactivate()
	{
		global $wc_email_att_activation;
		
		wc_email_att_load_plugin_version();
		$wc_email_att_activation->on_deactivate();
	}
}

if( ! function_exists( 'handler_wc_email_attachments_uninstall' ) )
{
	function handler_wc_email_attachments_uninstall()
	{
		global $wc_email_att_activation;
		
		wc_email_att_load_plugin_version();
		$wc_email_att_activation->on_uninstall();
	}
}

/**
 * To ensure backwards compatibility with WC we have to decide, which version of our plugin to activate
 * As WP does not call 'plugins_loaded' hook on activation, we have to implement it this way 
 */
if( $wc_email_att_are_activation_hooks )
{
	/**
		* Register activation, deactivation, uninstall hooks
		* ==================================================
		*
		* See Documentation for WP 3.3.1
		*/
		
		register_activation_hook( $wc_email_att_plugin_file, 'handler_wc_email_attachments_activate' );
		register_deactivation_hook( $wc_email_att_plugin_file, 'handler_wc_email_attachments_deactivate' );
		register_uninstall_hook( $wc_email_att_plugin_file, 'handler_wc_email_attachments_uninstall' );
}

