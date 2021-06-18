<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @package    Revslider_Login_Addon
 * @subpackage Revslider_Login_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Login_Addon_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	/*
	public function enqueue_styles() {
		if(isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-login-addon-admin.css', array(), $this->version, 'all' );
		}
	}
	*/

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-login-addon-admin.js', array( 'jquery','revbuilder-admin', 'jquery-ui-core', 'jquery-ui-datepicker' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_login_addon', $this->get_var() );
		}
	}

	/**
	 * Returns the global JS variable
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-login-addon') {
		if($slug == 'revslider-login-addon'){
			return array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_login_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-login-addon'),
					'settings' =>  __('Settings','revslider-login-addon'),					
					'configuration' =>  __('Configuration','revslider-login-addon'),
					'logincontent' =>  __('Content from','revslider-login-addon'),
					'slider' => __('Slider','revslider-login-addon'),
					'page' => __('Page','revslider-login-addon'),
					'pagetitle' => __('Page Title','revslider-login-addon'),
					'save' => __('Save Configration','revslider-login-addon'),
					'entersometitle' => __('Enter Some Title','revslider-login-addon'),
					'loadvalues' => __('Loading Login Add-On Configration','revslider-login-addon'),
					'savevalues' => __('Saving Login Add-On Configration','revslider-login-addon'),															
					'defredlink' => __('Redirect Link','revslider-login-addon'),
					'displink' => __('Use Lost Password Link','revslider-login-addon'),
					'ovlopassword' => __('Overtake Lost Password','revslider-login-addon'),
					'remember' => __('Show Remember Me','revslider-login-addon'),
					'remaining_days' => __('Remaining days','revslider-login-addon'),
					'remaining_hours' => __('Remaining hours','revslider-login-addon'),
					'remaining_minutes' => __('Remaining minutes','revslider-login-addon'),
					'remaining_seconds' => __('Remaining seconds','revslider-login-addon'),
					'login_form' => __('Login Form','revslider-login-addon'),
					'login' => __('Login','revslider-login-addon')

				),
				'title_placholder' => __('Enter Some Title','revslider-login-addon') 
			);
		}
		else{
			return $var;
		}
	}


	/**
	 * Saves Values for this Add-On
	 */
	public function save_login() {
		// Verify that the incoming request is coming with the security nonce
		if(isset($_REQUEST['data']['revslider_login_form'])){
			update_option( "revslider_login_addon", $_REQUEST['data']['revslider_login_form'] );
			return 1;
		}
		else{
			return 0;
		}
		
	}


	/**
	 * Load Values for this Add-On
	 */
	public function values_login() {
		$revslider_login_addon_values = array();
		parse_str(get_option('revslider_login_addon'), $revslider_login_addon_values);
		$return = json_encode($revslider_login_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_login_enabled", $enabled );	
	}

	/**
	 * Enable this Add-On
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-login-addon':
				$this->change_addon_status( 1 );
				return  __('login AddOn enabled', 'revslider-login-addon');
				break;
			
			case 'wp_ajax_disable_revslider-login-addon':
				$this->change_addon_status( 0 );
				return  __('login AddOn disabled', 'revslider-login-addon');
				break;

			case 'wp_ajax_get_values_revslider-login-addon':
				$return = $this->values_login();
				if(empty($return)) $return = true;
				return $return;
				break;
			case 'wp_ajax_save_values_revslider-login-addon':
				$return = $this->save_login();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-login-addon');
				} 
				else {
					return  __('login Configuration saved', 'revslider-login-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}

}
