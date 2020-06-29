<?php
/**
 * Define default location of uploadfolder for E-mail attachments relative to serverbase.
 * You can change this path later in the adminpage. You should not change it here unless
 * you have a good reason to do so. If the direcory does not exost, it will be created
 * automatically on activation.
 * 
 */
$woocommerce_email_attachments_plugin_path = str_replace(basename( __FILE__), "", __FILE__ );
$woocommerce_email_attachments_plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__ ), '', plugin_basename( __FILE__ ) );

if( ! defined( 'WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH' ) )
{
	$wc_ip_dir = wp_upload_dir();
	$wc_ip_dir_path = $wc_ip_dir['basedir']  .'/' . trailingslashit( $wc_email_att_upload_basedir );
	$wc_ip_dir_path = str_replace( '\\', '/', $wc_ip_dir_path );
	define ( 'WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH', $wc_ip_dir_path );
}

/**
 * Localisation
 **/
function woocommerce_email_attachments_init()
{
	$language_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	load_plugin_textdomain( 'woocommerce_email_attachments', false, $language_path );
}
add_action( 'init', 'woocommerce_email_attachments_init' );

if( is_admin() && ! class_exists('inoplugs_plupload') )
{
			//	load only on our option page
	if( ( false !== strpos( $_SERVER['REQUEST_URI'], 'tab=inoplugs_email' ) ) ||
	    ( false !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax.php' ) ) )
	{
		require_once $woocommerce_email_attachments_plugin_path.'v210/inoplugs_plupload/inoplugs_plupload.php';
	}
}

/**
 * load woocommerce_email_attachments class
 **/
if ( ! class_exists( 'woocommerce_email_attachments_activation' ) )
{
	require_once $woocommerce_email_attachments_plugin_path . 'v210/woocommerce_email_attachments_activation.php';
}

if ( ! class_exists( 'woocommerce_email_attachments' ) )
{
	require_once $woocommerce_email_attachments_plugin_path.'v210/woocommerce_email_attachments.php';
	
	global $woocommerce_email_attachments;
	$woocommerce_email_attachments = new woocommerce_email_attachments();
	
	add_action( 'admin_notices', array('woocommerce_email_attachments', 'show_admin_messages') );

	global $wc_email_att_skip_files, $wc_email_att_upload_basedir;
	
	woocommerce_email_attachments::$skip_files = $wc_email_att_skip_files;
	woocommerce_email_attachments::$plugin_url = $woocommerce_email_attachments_plugin_url;
}


