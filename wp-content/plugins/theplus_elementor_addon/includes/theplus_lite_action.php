<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Element_Install_Plugin{
	
	private static $_instance;
	
	/**
	* @return Theplus_Element_Loader
	*/
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * constructor.
	 */
	private function __construct() {
		if( is_admin() &&  current_user_can("manage_options") ){
			add_action( 'admin_action_theplus_lite_install_plugin', array( __CLASS__,'plus_free_install') );
		}
	}
	public static function plus_free_install() {
	  // if ( isset($_POST['security']) && !empty($_POST['security']) && ! wp_verify_nonce( $_POST['security'], 'theplus-addons' ) ){	 
	    // die ( 'Invalid Nonce Security checked!');
	  // }
	  
	  $plugin_slug = 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php';	
	  $plugin_zip = 'https://downloads.wordpress.org/plugin/the-plus-addons-for-elementor-page-builder.latest-stable.zip';
	  
	  echo '<div style="position:relative;display:flex;flex-direction:column;width:50%;margin:0 auto;border:2px solid #8072fc;border-radius:5px;-webkit-box-shadow:0 0 35px 0 rgba(154,161,171,.15);box-shadow:0 0 35px 0 rgba(154,161,171,.15);height: 100%;">';
			echo '<div style="position:relative;display:flex;padding:50px;background:#8072FC;white-space:nowrap;-webkit-box-shadow:0 0 35px 0 rgba(154,161,171,.15);box-shadow:0 0 35px 0 rgba(154,161,171,.15);margin-bottom:20px;"><img style="margin:0 auto;" src="'.THEPLUS_ASSETS_URL.'images/thepluslogo.svg" /></div>';
			
			echo '<div style="position:relative;display:flex;flex-direction:column;padding:30px;">';
				 echo 'Check if The Plus Addons for Elementor Lite is Installed? - ';
				  if ( self::is_plugin_installed( $plugin_slug ) ) {
					echo 'it\'s installed! We are checking its version now.';
					self::upgrade_plugin( $plugin_slug );
					$installed = true;
				  } else {
					echo 'it\'s not installed. Installing.';
					$installed = self::install_plugin( $plugin_zip );
				  }
				  echo '<div>'; 
					  if ( !is_wp_error( $installed ) && $installed ) {
						echo 'Activating new plugin.';		
						$activate = activate_plugin( $plugin_slug );
						 echo 'It’s Activated. Go to the <a style="position:relative;padding:8px 16px 8px 16px;background:#8072fc;color:#fff;text-decoration:none;font-size:16px;line-height:28px;font-weight:500;border-radius:4px;display:inline-block;width:max-content;" href="'.admin_url( 'admin.php?page=theplus_options').'">Settings Panel</a>.<br><br>';
					 } else {
						echo 'Ops. Seems something went wrong. Please deactivate our pro version and activate it again to start the process again. If you can not resolve this issue, Reach out to our helpdesk for further help.';
					 }
				echo '</div>';			
			echo '</div>';			
	  echo '</div>';	   
	}
	
	public static function is_plugin_installed( $slug ) {
	  if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	  }
	  $all_plugins = get_plugins();
	   
	  if ( !empty( $all_plugins[$slug] ) ) {
		return true;
	  } else {
		return false;
	  }
	}
	public static function upgrade_plugin( $plugin_slug ) {
	  include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	  wp_cache_flush();
	   
	  $upgrader = new \Plugin_Upgrader();
	  $upgraded = $upgrader->upgrade( $plugin_slug );
	 
	  return $upgraded;
	}
	public static function install_plugin( $plugin_zip ) {
	  include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	  wp_cache_flush();
	   
	  $upgrader = new \Plugin_Upgrader();
	  $installed = $upgrader->install( $plugin_zip );
	 
	  return $installed;
	}
}
Theplus_Element_Install_Plugin::instance();