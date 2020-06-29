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
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/includes
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
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_rel_posts {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rev_addon_rel_posts_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'rev_addon_rel_posts';
		$this->version = REV_ADDON_REL_POSTS_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		
		if(is_admin()) {
			$this->define_admin_hooks();
		}
		else {
			$enabled = get_option('revslider_rel_posts_enabled');
			if(!empty($enabled)) $this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rev_addon_rel_posts_Loader. Orchestrates the hooks of the plugin.
	 * - Rev_addon_rel_posts_i18n. Defines internationalization functionality.
	 * - Rev_addon_rel_posts_Admin. Defines all hooks for the admin area.
	 * - Rev_addon_rel_posts_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_rel_posts-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_rel_posts-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rev_addon_rel_posts-admin.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rev_addon_rel_posts-update.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rev_addon_rel_posts-public.php';

		$this->loader = new Rev_addon_rel_posts_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rev_addon_rel_posts_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rev_addon_rel_posts_i18n();

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

		$plugin_admin = new Rev_addon_rel_posts_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnRelPostsUpdate(REV_ADDON_REL_POSTS_VERSION);

		//option page
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		//AJAX Functions
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,3);	

		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

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

		$plugin_public = new Rev_addon_rel_posts_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'the_content' , $plugin_public, 'filter_print_related_posts' );	
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
	 * @return    Rev_addon_rel_posts_Loader    Orchestrates the hooks of the plugin.
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
