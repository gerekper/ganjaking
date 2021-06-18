<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */

if(!defined('ABSPATH')) exit();

class Revslider_Maintenance_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;
	
	/**
	 * Stores if the JavaScript was already added to the page
	 */
	public $bricket_found = false;
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {

		$this->plugin_name = 'revslider-maintenance-addon';
		$this->version = REV_ADDON_MAINTENANCE_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		
		$enabled = get_option('revslider_maintenance_enabled');
		if(!empty($enabled)){
			add_filter('revslider_layer_content', array($this, 'check_if_slider_has_options'), 10, 5);
		}
		if(is_admin()){
			$this->define_admin_hooks();
		}else{
			if(!empty($enabled)){
				$this->define_public_hooks();
			}
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Revslider_Maintenance_Addon_Loader. Orchestrates the hooks of the plugin.
	 * - Revslider_Maintenance_Addon_i18n. Defines internationalization functionality.
	 * - Revslider_Maintenance_Addon_Admin. Defines all hooks for the admin area.
	 * - Revslider_Maintenance_Addon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-maintenance-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-maintenance-addon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revslider-maintenance-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revslider-maintenance-addon-public.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-maintenance-addon-update.php';

		$this->loader = new Revslider_Maintenance_Addon_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Revslider_Maintenance_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale(){
		$plugin_i18n = new Revslider_Maintenance_Addon_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks(){

		$plugin_admin = new Revslider_Maintenance_Addon_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnMaintenanceUpdate(REV_ADDON_MAINTENANCE_VERSION);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

		//ajax calls
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,2);	

		//build js global var for activation
		$this->loader->add_action( 'revslider_activate_addon', $plugin_admin, 'get_var',10,2);	
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new Revslider_Maintenance_Addon_Public( $this->get_plugin_name(), $this->get_version() );
		
		//redirect if needed
		$this->loader->add_action( 'wp_loaded' , $plugin_public , 'maintenance_mode' );
	}
	
	/**
	 * check if any slider has maintenance brickets. If yes, include the JavaScript file for that Slider
	 **/
	public function check_if_slider_has_options($text, $_text, $slider_id, $slide, $layer){
		if(!$this->bricket_found){
			if(
				strpos($text, '{{t_days}}') !== false ||
				strpos($text, '{{t_hours}}') !== false ||
				strpos($text, '{{t_minutes}}') !== false ||
				strpos($text, '{{t_seconds}}') !== false
			){
				//set that the javascript is loaded
				$this->bricket_found = true;
				add_action('revslider_add_slider_base_post', array($this, 'add_dynamic_js'), 10, 1);
			}
		}
		
		return $text;
	}
	
	/**
	 * adds the javascript to the page
	 **/
	public function add_dynamic_js($_output){
		$mta = Revslider_Maintenance_Addon_Public::return_mta_data();
		//if($mta['revslider-maintenance-addon-countdown-active']){
			Revslider_Maintenance_Addon_Public::add_js($mta);
		//}
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
