<?php
/*
Plugin Name: WooCommerce integration for UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: Integrates woocommerce orders and purchases with UserPro profiles
Version: 1.7
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

if( !class_exists( 'UPWoocommerce' ) ){

class UPWoocommerce {
	
	function __construct(){
			$activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			if( in_array('woocommerce/woocommerce.php', $activated_plugins) ){
				add_action( 'init', array($this,'upw_load_language') );
				$this->define_constants();
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
				$this->includes();
			}
			else{
				add_action( 'admin_notices', array($this, 'upw_activation_notices') );
				return 0;
			}
	}

	function upw_activation_notices(){
		echo '<div class="error" role="alert"><p>Attention: UserPro Woocommerce requires WooCommerce to be installed and activated.</p></div>';
		deactivate_plugins( plugin_basename( __FILE__ ) );
		return 0;
	}
        
	function upw_load_language(){
		load_plugin_textdomain('userpro-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	function define_constants(){
		define('UPWURL',plugin_dir_url(__FILE__ ));
		define('UPWPATH',plugin_dir_path(__FILE__ ));	
	}

	function includes(){
		if (is_admin()){
			foreach (glob(UPWPATH . 'admin/*.php') as $filename) { include $filename; }

			require_once(UPWPATH.'admin/class-uw-updates-plugin.php');
                      new WPUpdatesPluginUpdater_1458( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
		}
		foreach (glob(UPWPATH . 'functions/*.php') as $filename) { require_once $filename; }
	}

	function enqueue_scripts_styles(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'upw_style_css ', UPWURL.'assets/css/upw-style.css' );
		wp_enqueue_style( 'upw_mscrollbar_style', UPWURL.'assets/css/jquery.mCustomScrollbar.css');
		wp_enqueue_script( 'upw_script_js', UPWURL.'assets/js/upw-script.js', array('jquery'), '', true );
		wp_enqueue_script( 'upw_mscrollbar_js', UPWURL.'assets/js/jquery.mCustomScrollbar.min.js', array('jquery'), '', true );
		wp_enqueue_script( 'upw_easing_js', UPWURL.'assets/js/jquery.easing.1.3.js', array('jquery'), '', true );
		wp_enqueue_script( 'upw_mousewheel_js', UPWURL.'assets/js/jquery.mousewheel.js', array('jquery'), '', true );
	}
  }
  new UPWoocommerce();
}

