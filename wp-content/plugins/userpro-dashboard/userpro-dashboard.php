<?php
/*
Plugin Name: UserPro DashBoard
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: Provides Dashboard for every user .
Version: 3.7
Author: DeluxeThemes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UPDB' ) ) :

class UPDB{

	private static $_instance;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	function __construct(){
		global $userpro;
		$this->define_constants();
		
		if( isset( $userpro ) ){
			//add_action( 'init', array($this,'updb_load_language') );
			$this->updb_load_language();
			$this->include_files();
			$this->enqueue_scripts_styles();
			add_action('admin_enqueue_scripts',array($this,'enqueue_admin_scripts_styles'));
		}
		else{
			add_action('admin_notices',array($this , 'UPDB_activation_notices'));
			return 0;
		}
	}

	function updb_load_language(){
		load_plugin_textdomain('userpro-dashboard', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	function define_constants(){

		define( 'UPDB_URL', plugin_dir_url(__FILE__ ) );
		define( 'UPDB_PATH', plugin_dir_path(__FILE__ ) );

	}

	function UPDB_activation_notices(){
		echo '<div class="error" role="alert"><p>Attention: UserPro Dashboard requires UserPro to be installed and activated.</p></div>';
		return 0;
	}

	function include_files(){
		
		if (is_admin()){
			foreach (glob(UPDB_PATH . 'admin/*.php') as $filename) { include $filename; }
		}
		foreach (glob(UPDB_PATH . 'functions/*.php') as $filename) { require_once $filename; }
		//require_once(UPDB_PATH . 'admin/updb-updates-plugin.php');
		//new WPUpdatesPluginUpdater_1457( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
		
	}

	function enqueue_scripts_styles(){
		wp_enqueue_script( 'dashboard-script', UPDB_URL.'assets/js/userpro-dashboard.js', array('jquery'), '', true );
		if( !isset( $updb_default_options ) ){
			$updb_default_options = new UPDBDefaultOptions();
		}
		$enable_dashboard = $updb_default_options->updb_get_option( 'userpro_db_enable' );
		$dashboard_slug = $updb_default_options->updb_get_option( 'slug_dashboard' );
		if( $enable_dashboard ){
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'custom-script', UPDB_URL.'assets/js/custom-js.js', array('jquery','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable'), '', true );
			wp_enqueue_style( 'updb-customizer-css', UPDB_URL.'assets/css/dashboard-customizer.css' ); 
			wp_enqueue_style( 'updb-jquery-ui', UPDB_URL.'assets/css/jquery-ui.css' ); 
		}
			
	}
	function enqueue_admin_scripts_styles(){
		
		wp_enqueue_style( 'updb-admin-css', UPDB_URL.'admin/assets/css/dashboard-admin-style.css' );
		wp_enqueue_script( 'admin-custom-script', UPDB_URL.'admin/assets/js/admin-custom.js', array(), '', true );
		
	}
}
add_action(	'wp_loaded' , 'after_plugins_loaded' );
function after_plugins_loaded(){
	$updb = UPDB::instance();
}
endif;
