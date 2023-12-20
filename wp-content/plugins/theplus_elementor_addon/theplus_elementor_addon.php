<?php
/*
* Plugin Name: The Plus Addons for Elementor - Pro
* Plugin URI: https://theplusaddons.com/
* Description: Highly Customisable 120+ Advanced Elementor Widgets & Extensions for Performance Driven Website. Keep the free version active to access all of its features.
* Version: 5.3.1
* Author: POSIMYTH
* Author URI: https://posimyth.com/
* Text Domain: theplus
* Elementor tested up to: 3.17
* Elementor Pro tested up to: 3.17
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

update_option( 'theplus_verified', [ 'expire' => 'lifetime', 'license' => 'valid', 'verify' => 1 ] );
update_option( 'theplus_purchase_code', ['tp_api_key' => '********************'] );

defined( 'THEPLUS_VERSION' ) or define( 'THEPLUS_VERSION', '5.3.1' );
define( 'THEPLUS_FILE__', __FILE__ );

define( 'THEPLUS_PATH', plugin_dir_path( __FILE__ ) );
define( 'THEPLUS_PBNAME', plugin_basename(__FILE__) );
define( 'THEPLUS_PNAME', basename( dirname(__FILE__)) );
define( 'THEPLUS_URL', plugins_url( '/', __FILE__ ) );
define( 'THEPLUS_ASSETS_URL', THEPLUS_URL . 'assets/' );
define( 'THEPLUS_ASSET_PATH', wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . 'theplus-addons' );
define( 'THEPLUS_ASSET_URL', wp_upload_dir()['baseurl'] . '/theplus-addons' );
define( 'THEPLUS_INCLUDES_URL', THEPLUS_PATH . 'includes/' );
define( 'THEPLUS_TYPE', 'store' );
define( 'THEPLUS_TPDOC', 'https://theplusaddons.com/docs/' );

/* theplus language plugins loaded */
function theplus_pluginsLoaded() {
	
	load_plugin_textdomain( 'theplus', false, basename( dirname( __FILE__ ) ) . '/lang' ); 

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'theplus_elementor_load_notice' );
		return;
	}
	
	if(!defined("L_THEPLUS_VERSION")){
		add_action( 'admin_notices', 'theplus_lite_load_notice' );
		return;
	}
	
	// Elementor widget loader
	if(THEPLUS_TYPE=='store' && is_admin()){
		add_action( 'admin_init', 'theplus_plugin_updater', 0 );
	}
	
    require( THEPLUS_PATH . 'widgets_loader.php' );
}
add_action( 'plugins_loaded', 'theplus_pluginsLoaded' );

/* theplus update notice */
add_action('in_plugin_update_message-theplus_elementor_addon/theplus_elementor_addon.php','tp_in_plugin_update_message',10,2);
function tp_in_plugin_update_message($data,$response){
	if( isset( $data['upgrade_notice'] ) && !empty($data['upgrade_notice']) ) {
		printf(
			'<div class="update-message">%s</div>',
			wpautop( $data['upgrade_notice'] )
		);
	}
}

/* theplus elementor load notice */
function theplus_elementor_load_notice() {	
	$plugin = 'elementor/elementor.php';	
	if ( theplus_elementor_activated() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_notice = '<p>' . esc_html__( 'Something Missing : It\'s Elementor. You already installed that, Please activate Elementor, Unless The Plus Addons will not be working.', 'theplus' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Elementor Now', 'theplus' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$admin_notice = '<p>' . esc_html__( 'Something Missing : It\'s Elementor. Please install Elementor, Unless The Plus Addons will not be working.', 'theplus' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Elementor Now', 'theplus' ) ) . '</p>';
	}

	echo '<div class="notice notice-error is-dismissible">'.$admin_notice.'</div>';	
}

/* theplus lite load notice */
function theplus_lite_load_notice() {	
	$plugin = 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php';	

	if ( theplus_lite_activated() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_notice = '<p>' . esc_html__( 'You are one step away from using The Plus Addons for Elementor Pro. Please activate The Plus Addons for Elementor Lite version.', 'theplus' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate The Plus Addons for Elementor Lite', 'theplus' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=the-plus-addons-for-elementor-page-builder' ), 'install-plugin_the-plus-addons-for-elementor-page-builder' );
		$admin_notice = '<p>' . esc_html__( 'The Plus Addons for Elementor lite is missing. Would you please install that to make The Plus Addons for Elementor Pro working smoothly?', 'theplus' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install The Plus Addons for Elementor Lite', 'theplus' ) ) . '</p>';
	}

	echo '<div class="notice notice-error is-dismissible">'.$admin_notice.'</div>';	
}

/**
	* Plugin Updater	
*/
function theplus_plugin_updater() {
	
    $purchase_key = get_option( 'theplus_purchase_code' );
	$verify_api=theplus_check_api_status();
    // setup the updater
	if(!empty($purchase_key['tp_api_key']) && !empty($verify_api) && $verify_api==1){
		$edd_updater = new Theplus_SL_Plugin_Updater( TP_PLUS_SL_STORE_URL, __FILE__, array(
			'version' => THEPLUS_VERSION,
			'license' => $purchase_key['tp_api_key'],		
			'item_id'       => TP_PLUS_SL_ITEM_ID,
			'author' => 'POSIMYTH Themes',
			'url'           => home_url(),
			'beta' => false,
		));
	}
}

function theplus_activated_plugin( $plugin ) { 
	if( $plugin == plugin_basename( __FILE__ ) ) {
		$activate_plus_label=get_option( 'theplus_white_label' );			
		if ( !empty($activate_plus_label["tp_hidden_label"]) && $activate_plus_label["tp_hidden_label"] === 'on' ) {
			$activate_plus_label["tp_hidden_label"] = '';
			update_option('theplus_white_label', $activate_plus_label);
		}			
	}
}
add_action( 'activated_plugin', 'theplus_activated_plugin',10 );

/**
	* Elementor activated or not
*/
if ( ! function_exists( 'theplus_elementor_activated' ) ) {
	
	function theplus_elementor_activated() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		
		return isset( $installed_plugins[ $file_path ] );
	}
}

/**
	* The Plus Lite activated or not
*/
if ( ! function_exists( 'theplus_lite_activated' ) ) {
	
	function theplus_lite_activated() {
		$file_path = 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php';
		$installed_plugins = get_plugins();
		
		return isset( $installed_plugins[ $file_path ] );
	}
}

/**
 * Redirect lite action
 *
 * @since v1.0.0
 */
function theplus_activate() {
    add_option('theplus_activation_redirect', true);
}
register_activation_hook(__FILE__, 'theplus_activate');

function theplus_redirect_lite_version() {
	if( !defined('L_THEPLUS_VERSION') ){
		require( THEPLUS_INCLUDES_URL . 'theplus_lite_action.php' );
	}

	if ( get_option('theplus_activation_redirect', false) ) {
		delete_option('theplus_activation_redirect');

		if(!defined('L_THEPLUS_VERSION')){				
			wp_safe_redirect("admin.php?action=theplus_lite_install_plugin");
		}
	}
}
add_action('admin_init', 'theplus_redirect_lite_version');
