<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/includes
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
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_gal {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rev_addon_gal_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'rev_addon_gal';
		$this->version = REV_ADDON_GAL_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		
		$enabled = get_option('revslider_gallery_enabled');
		
		if(is_admin()) {
			$this->define_admin_hooks($enabled);
		}
		else {
			if(!empty($enabled)) $this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rev_addon_gal_Loader. Orchestrates the hooks of the plugin.
	 * - Rev_addon_gal_i18n. Defines internationalization functionality.
	 * - Rev_addon_gal_Admin. Defines all hooks for the admin area.
	 * - Rev_addon_gal_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_gal-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_gal-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rev_addon_gal-admin.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_gal-update.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rev_addon_gal-public.php';

		$this->loader = new Rev_addon_gal_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rev_addon_gal_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rev_addon_gal_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks($enabled) {

		$plugin_admin = new Rev_addon_gal_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnGalUpdate(REV_ADDON_GAL_VERSION);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,2);	

		$this->loader->add_action( 'wp_ajax_save_gal', $plugin_admin, 'save_gal');
		$this->loader->add_action( 'wp_ajax_nopriv_save_gal', $plugin_admin, 'save_gal' );

		//add media form fields
		$this->loader->add_action( 'print_media_templates', $plugin_admin ,'rev_addon_media_form' );
		
		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

		//build js global var for activation
		$this->loader->add_action( 'revslider_activate_addon', $plugin_admin, 'get_var',10,2);	
		
		// gutenberg extension
		// don't enqueue if the development plugin is activated
		if($enabled && !function_exists('revslider_gallery_addon_gutenberg_extension')) {
			$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'gutenberg_enqueue_scripts' );
		}
		
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rev_addon_gal_Public( $this->get_plugin_name(), $this->get_version() );

		//insert layer meta data
		$this->loader->add_action( 'rev_slider_insert_meta', $plugin_public ,'rev_addon_insert_meta', 10, 2);

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// gutenberg extension
		// don't enqueue if the development plugin is activated
		if(!function_exists('revslider_gallery_addon_gutenberg_extension')) {
			$this->loader->add_filter( 'render_block', $plugin_public, 'gutenberg_block_content_fitler', 10, 2);
		}

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
	 * @return    Rev_addon_gal_Loader    Orchestrates the hooks of the plugin.
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
