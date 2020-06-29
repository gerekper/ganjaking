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
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Weather_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Revslider_Weather_Addon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'revslider-weather-addon';
		$this->version = REV_ADDON_WEATHER_VERSION;
		
		$this->load_dependencies();
		$this->set_locale();
		
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Revslider_Weather_Addon_Loader. Orchestrates the hooks of the plugin.
	 * - Revslider_Weather_Addon_i18n. Defines internationalization functionality.
	 * - Revslider_Weather_Addon_Admin. Defines all hooks for the admin area.
	 * - Revslider_Weather_Addon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-weather-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-weather-addon-i18n.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revslider-weather-addon-admin.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-weather-addon-update.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revslider-weather-addon-public.php';

		/**
		 * The class responsible for the Yahoo Weather.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-weather-addon-owm.php';

		$this->loader = new Revslider_Weather_Addon_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Revslider_Weather_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Revslider_Weather_Addon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Revslider_Weather_Addon_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnWeatherUpdate(REV_ADDON_WEATHER_VERSION);
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// saving the api key in the AddOn modal
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,2);	

		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

		//Slider Options
		$this->loader->add_filter('revslider_slider_addons', $plugin_admin, 'add_addon_settings_slider', 10, 2);
		
		//Weather Icons
		//$this->loader->add_filter('revslider_object_library_icon_paths', $plugin_admin, 'add_addon_settings_slider', 10, 1);
		
		
		$this->loader->add_filter('revslider_object_library_icon_paths', $plugin_admin, 'extend_font_icons_path', 10, 1);
		$this->loader->add_filter('revslider_get_font_tags', $plugin_admin, 'extend_font_tags', 10, 1);
		$this->loader->add_filter('revslider_get_font_icons', $plugin_admin, 'extend_font_tags_on_weather', 10, 1);

		//build js global var for activation
		$this->loader->add_action( 'revslider_activate_addon', $plugin_admin, 'get_var',10,2);	

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Revslider_Weather_Addon_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'revslider_fe_javascript_output', $plugin_public, 'write_init_script', 10, 2);
		$this->loader->add_action( 'revslider_add_layer_attributes', $plugin_public, 'write_layer_attributes', 10, 3);
		$this->loader->add_action( 'revslider_add_layer_html', $plugin_public, 'revslider_add_layer_html', 1,2 );
		$this->loader->add_filter( 'revslider_modify_layer_text', $plugin_public, 'rev_addon_insert_meta',10,2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Revslider_Weather_Addon_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
