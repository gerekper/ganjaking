<?php
if (!defined('ABSPATH') || !is_main_site()) return;

/*
Plugin Name: Advanced WordPress Reset
Plugin URI: http://sigmaplugin.com/downloads/advanced-wordpress-reset
Description: Reset your WordPress database back to its first original status, just like if you make a fresh installation.
Version: 1.1.0
Author: Younes JFR.
Author URI: http://www.sigmaplugin.com
Contributors: symptote
Text Domain: advanced-wp-reset
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/********************************************************************
* Define common constants
********************************************************************/
if (!defined("DBR_PLUGIN_VERSION")) 				define("DBR_PLUGIN_VERSION", "1.1.0");
if (!defined("DBR_PLUGIN_DIR_PATH")) 				define("DBR_PLUGIN_DIR_PATH", plugins_url('' , __FILE__));
if (!defined("DBR_PLUGIN_BASENAME")) 				define("DBR_PLUGIN_BASENAME", plugin_basename(__FILE__));

/********************************************************************
* Load language
********************************************************************/
add_action('plugins_loaded', 'DBR_load_textdomain');
function DBR_load_textdomain() {
	load_plugin_textdomain('advanced-wp-reset', false, plugin_basename(dirname(__FILE__)) . '/languages');
}

/********************************************************************
* Add sub menu under tools
********************************************************************/
add_action('admin_menu', 'DBR_add_admin_menu');
function DBR_add_admin_menu() {
	global $DBR_tool_submenu;
	$DBR_tool_submenu = add_submenu_page('tools.php', 'Advanced WP Reset', 'Advanced WP Reset', 'manage_options', 'advanced_wp_reset', 'DBR_main_page_callback');
}

/********************************************************************
* Load CSS and JS
********************************************************************/
add_action('admin_enqueue_scripts', 'DBR_load_styles_and_scripts');
function DBR_load_styles_and_scripts($hook) {
	// Enqueue our js and css in the plugin pages only
	global $DBR_tool_submenu;
	if($hook != $DBR_tool_submenu){
		return;
	}
	wp_enqueue_style('DBR_css', DBR_PLUGIN_DIR_PATH . '/css/admin.css');
	//wp_enqueue_script('DBR_js', DBR_PLUGIN_DIR_PATH . '/js/admin.js');
    //wp_enqueue_script('jquery');
    //wp_enqueue_script('jquery-ui-dialog');
	//wp_enqueue_style('wp-jquery-ui-dialog');
}

/********************************************************************
* Activation of the plugin
********************************************************************/
register_activation_hook(__FILE__, 'DBR_activate_plugin');
function DBR_activate_plugin(){
	// Anything to do on activation? Maybe later...
}

/********************************************************************
* Add rating box to the top of wordpress admin panel
********************************************************************/
$aDBc_upload_dir = wp_upload_dir();
$aDBc_file_path = str_replace('\\' ,'/', $aDBc_upload_dir['basedir']) . "/DBR.txt";
if(isset($_GET['DBR_rate']) && $_GET['DBR_rate'] == "0"){
	$handle = fopen($aDBc_file_path, "w");
	if($handle){
		fwrite($handle, "0");
	}
}else{
	if(file_exists($aDBc_file_path)){
		$content = file_get_contents($aDBc_file_path);
		if($content == "1"){
			add_action('admin_notices', 'DBR_show_rate_box');
		}
	}
}
function DBR_show_rate_box(){
	$aDBc_new_URI = $_SERVER['REQUEST_URI'];
	$aDBc_new_URI = add_query_arg('DBR_rate', "0", $aDBc_new_URI);
	$style_botton = "background: #f0f5fa;padding: 4px !important;text-decoration: none;margin-right:10px;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;	box-shadow: 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30,140,190,.8);	";
?>
	<div style="padding:15px !important;" class="updated DBR-top-main-msg">
		<span style="font-size:16px;color:green;font-weight:bold;"><?php _e('Awesome!', 'advanced-wp-reset'); ?></span>
		<p style="font-size:14px;line-height:30px">
			<?php _e('The plugin "Advanced DB Reset" just helped you reset your database to a fresh installation with success!', 'advanced-wp-reset'); ?>
			<br/>
			<?php _e('Could you please kindly help the plugin in your turn by giving it 5 stars rating? (Thank you in advance)', 'advanced-wp-reset'); ?>
			<div style="font-size:14px;margin-top:10px">
			<a  style="<?php echo $style_botton ?>" target="_blank" href="https://wordpress.org/support/plugin/advanced-wp-reset/reviews/?filter=5">
			<?php _e('Ok, you deserved it', 'advanced-wp-reset'); ?></a>
			<form method="post" action="" style="display:inline">
			<input type="hidden" name="dont_show_rate" value=""/>
			<a style="<?php echo $style_botton ?>" href="<?php echo $aDBc_new_URI; ?>"><?php _e('I already did', 'advanced-wp-reset'); ?></a>
			<a style="<?php echo $style_botton ?>" href="<?php echo $aDBc_new_URI; ?>"><?php _e('Please don\'t show this again', 'advanced-wp-reset'); ?></a>
			</form>
			</div>
		</p>
	</div>	
<?php
}



/********************************************************************
* Deactivation of the plugin
********************************************************************/
register_deactivation_hook(__FILE__, 'DBR_deactivate_plugin');
function DBR_deactivate_plugin(){
	// Anything to do on deactivation? Maybe later...
}

/********************************************************************
* UNINSTALL
********************************************************************/
register_uninstall_hook(__FILE__, 'DBR_uninstall');
function DBR_uninstall(){
	// Anything to do on uninstall? Maybe later...
}

/********************************************************************
* The admin page of the plugin
********************************************************************/
function DBR_main_page_callback(){
	if(!current_user_can("manage_options")){
		_e('You do not have sufficient permissions to access this page.','advanced-wp-reset');
		die();
	}
	//if(array_key_exists('reset-db', $_GET)){
		//echo '<div id="DBR_message" class="updated notice is-dismissible"><p>';
		//_e('Your database has been reset successfully!','advanced-wp-reset');
		//echo '</p></div>';
	//}
	?>
	<div class="wrap">
		<h2>Advanced WordPress Reset</h2>
		<div class="DBR-margin-r-300">
			<div class="DBR-tab-box">
				<div class="DBR-tab-box-div">
					<?php include_once 'includes/reset.php'; ?>
				</div>
			</div>
			<div class="DBR-sidebar"><?php include_once 'includes/sidebar.php'; ?></div>
		</div>
	</div>
<?php 
}

/***************************************************************
* Get functions
***************************************************************/
include_once 'includes/functions.php';

?>