<?php
/**
 * Plugin Name: WooCommerce E-Mail Attachments (by Inoplugs)
 * Plugin URI: http://www.woothemes.com/products/email-attachments/
 * Description: <a href="http://www.woothemes.com/products/email-attachments/" target="_blank">WooCommerce E-Mail Attachments</a> provides a possibility for adding attachments to WooCommerce E-Mails. You can upload files for exclusiv use as attachment to E-Mails or use files from the media gallery. You can also add an informational note to the E-Mail, that an attachment has been added and add CC and BCC reciepients.<br /> Email to <a href="mailto:support@inoplugs.com">support@inoplugs.com</a> with any questions.
 * Author: InoPlugs
 * Author URI: http://inoplugs.com
 * Version: 3.2
 * Text Domain: woocommerce_email_attachments
 * WC requires at least: 2.1.0
 * WC tested up to: 7.7.0
 * WP tested up to: 6.2.2
 *
 * Woo: 18661:6d6299c28142e976e1155cdb853f8014
 *
 * Copyright Guenter Schoenmann (email : support@inoplugs.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		WooCommerce E-Mail Attachments
 * @author		InoPlugs
 * @since		1.0
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  } // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '6d6299c28142e976e1155cdb853f8014', '18661' );

/********************************************************************************************************************************
 * Parameters that can be changed by user  -  START
 ********************************************************************************************************************************/

/**
 * Set the name of the default upload folder, placed below WP standard upload folder
 */
if( ! defined('WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH') )
{
	define ( 'WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH', '/wc_email_attachment_files' );
}

global $wc_email_att_skip_files, $wc_email_att_upload_basedir, $wc_email_att_htaccess;
/**
 * Set filenames that are created by default in upload folder. These files are ignored to check for an empty folder before removing it.
 * (e.g. index.php). Normally you do not have to alter this value.
 *
 * Example: array ('indes.php', '.htaccess');
*/
$wc_email_att_skip_files =  array ( 'index.php', '.htaccess' );

/**
 * Change the .htaccess text - Default only images can be viewed from outside. Put each line in an own '' seperated by ,
 */
$wc_email_att_htaccess = array (
		'deny from all',
		'',
		'<FilesMatch "\.(png|jpe?g|gif)$">',
		'Satisfy Any',
		'Allow from all',
		'</FilesMatch>'
	);

/**
 * Set the name of the default upload folder, placed below WP standard upload folder
 *
 * No longer valid for WooCommerce >= 2.1.0
 */
$wc_email_att_upload_basedir = 'woocommerce_email_attachments';

/********************************************************************************************************************************
 * Parameters that can be changed by user  -  END
 ********************************************************************************************************************************/


/**
 * Check for activation, .... to speed up loading
 */
global $wc_email_att_are_activation_hooks, $wc_email_att_plugin_base_name, $wc_email_att_plugin_file, $wc_email_att_plugin_path;

$wc_email_att_plugin_file = __FILE__;
$wc_email_att_plugin_base_name = plugin_basename( __FILE__ );
$wc_email_att_are_activation_hooks = false;
$wc_email_att_plugin_path = str_replace( basename( __FILE__ ), '', __FILE__ );

if( is_admin() )
{
	$wc_email_att_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

	switch ( $wc_email_att_action )
	{
		case 'activate':
		case 'deactivate':
		case 'delete-plugin':
			if( isset( $_REQUEST['plugin'] ) && ( $_REQUEST['plugin'] == $wc_email_att_plugin_base_name))
			{
				$wc_email_att_are_activation_hooks = true;
			}
			break;
		case 'activate-selected':
		case 'deactivate-selected':
		case 'delete-selected':
			if( isset( $_REQUEST['checked'] ) && is_array ( $_REQUEST['checked'] ) && in_array( $wc_email_att_plugin_base_name, $_REQUEST['checked'] ) )
			{
				$wc_email_att_are_activation_hooks = true;
			}
			break;
		default:
			$wc_email_att_are_activation_hooks = false;
			break;
	}
}

if( ! function_exists( 'wc_email_att_load_plugin_version' ) )
{
	function wc_email_att_load_plugin_version()
	{
		global $wc_email_att_are_activation_hooks, $wc_email_att_activation, $wc_email_att_plugin_path;

		if ( ! ( function_exists( 'WC' ) || $wc_email_att_are_activation_hooks ) )		//	up to 2.0.20 only WC does not exist
		{
			require_once $wc_email_att_plugin_path.'woocommerce_email_attachments_load_v210.php';
		}
		else
		{
					//	if WC not active, by default use latest version for activation hooks
			$version = ( function_exists( 'WC' ) ) ? WC()->version : '2.1.1';
			if( version_compare( $version, '2.1.0', '<' ) )
			{
				require_once $wc_email_att_plugin_path . 'woocommerce_email_attachments_load_v210.php';
			}
			else
			{
				require_once $wc_email_att_plugin_path . 'woocommerce_email_attachments_load.php';
			}
		}

		if( is_admin() && $wc_email_att_are_activation_hooks )
		{
				//	backwards compatibility
			if( class_exists( 'woocommerce_email_attachments_activation' ) )
			{
				$wc_email_att_activation = new woocommerce_email_attachments_activation();
			}
			else
			{
				$wc_email_att_activation = new WC_Email_Att_Activation();
			}
		}
	}
}

if( ! function_exists( 'wc_email_att_check_woocomm_is_loaded' ) )
{
	/**
	 * if WooCommerce was not loaded = disabled, we have to load our plugin for activationhooks
	 */
	function wc_email_att_check_woocomm_is_loaded()
	{
		if( class_exists( 'WooCommerce' ) )
		{
			return;
		}
		wc_email_att_load_plugin_version();
	}
}

/**
 * To ensure backwards compatibility with WC we have to decide, which version of our plugin to activate
 * As WP does not call 'plugins_loaded' hook on activation, we have to implement it this way
 */
if( $wc_email_att_are_activation_hooks )
{
	require_once $wc_email_att_plugin_path . 'woocommerce_email_attachments_activation.php';
}
else
{
	/**
	* We need this class to decide, if we have to fallback to older versions for backup compatibility
	*/
	if ( class_exists( 'WooCommerce' ) )
	{
		wc_email_att_load_plugin_version();
	}
	else
	{
		add_action( 'before_woocommerce_init', 'wc_email_att_load_plugin_version' );
	}
}

/**
 * Declare compatibility - we do not have any order access
 *
 * @link https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book
 * @since 3.2
 */
add_action( 'before_woocommerce_init', function()
	{
		if( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) )
		{
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

