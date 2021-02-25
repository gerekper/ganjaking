<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_404_Addon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-404-addon-admin.js', array( 'jquery','revbuilder-admin' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_404_addon', $this->get_var() );
		}

	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-404-addon') {
		if($slug == 'revslider-404-addon'){
			return array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_404_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-404-addon'),
					'settings' =>  __('Settings','revslider-404-addon'),
					'activefofmode' =>  __('Activate 404 Mode','revslider-404-addon'),
					'configuration' =>  __('Configuration','revslider-404-addon'),
					'fofcontent' =>  __('404 Content','revslider-404-addon'),
					'slider' => __('Slider','revslider-404-addon'),
					'page' => __('Page','revslider-404-addon'),
					'pagetitle' => __('Page Title','revslider-404-addon'),
					'save' => __('Save Configration','revslider-404-addon'),
					'entersometitle' => __('Enter Some Title','revslider-404-addon'),
					'loadvalues' => __('Loading 404 Add-On Configration','revslider-404-addon'),
					'savevalues' => __('Saving 404 Add-On Configration','revslider-404-addon')
				),
				'title_placholder' => __('Enter Some Title','revslider-404-addon') 
			);
		}
		else{
			return $var;
		}
	}

	

	/**
	 * Saves Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function save_404() {
		if(isset($_REQUEST['data']['revslider_404_form'])){
			update_option( "revslider_404_addon", $_REQUEST['data']['revslider_404_form'] );
			return 1;
		}
		else{
			return 0;
		}
		
	}

	/**
	 * Load Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function values_404() {
		$revslider_404_addon_values = array();
		parse_str(get_option('revslider_404_addon'), $revslider_404_addon_values);
		$return = json_encode($revslider_404_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 *
	 * @since    1.0.0
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_404_enabled", $enabled );	
	}

	/**
	 * Handle Ajax Calls from RevSlider core
	 *
	 * @since    1.0.0
	 */
	public function do_ajax($return, $action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-404-addon':
				$this->change_addon_status( 1 );
				return  __('404 AddOn enabled', 'revslider-404-addon');
				break;
			
			case 'wp_ajax_disable_revslider-404-addon':
				$this->change_addon_status( 0 );
				return  __('404 AddOn disabled', 'revslider-404-addon');
				break;

			case 'wp_ajax_get_values_revslider-404-addon':
				$return = $this->values_404();
				if(empty($return)) $return = true;
				return $return;
				break;
			
			case 'wp_ajax_save_values_revslider-404-addon':
				$return = $this->save_404();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-404-addon');
				} 
				else {
					return  __('404 Configuration saved', 'revslider-404-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}

}