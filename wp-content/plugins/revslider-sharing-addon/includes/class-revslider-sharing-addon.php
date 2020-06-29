<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Revslider_Sharing_Addon
 * @subpackage Revslider_Sharing_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Sharing_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
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
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {

		$this->plugin_name = 'revslider-sharing-addon';
		$this->version = REV_ADDON_SHARING_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-sharing-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-sharing-addon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revslider-sharing-addon-admin.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-sharing-addon-update.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revslider-sharing-addon-public.php';

		$this->loader = new Revslider_Sharing_Addon_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {

		$plugin_i18n = new Revslider_Sharing_Addon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Revslider_Sharing_Addon_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnSharingUpdate(REV_ADDON_SHARING_VERSION);
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

		//add actions
		$this->loader->add_action( 'rs_action_add_layer_action', $plugin_admin , 'rs_action_add_layer_actions', 10 );
		$this->loader->add_action( 'rs_action_add_layer_action_details', $plugin_admin , 'rs_action_add_layer_actions_details', 10 );

		//ajax call
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,2);

		//build js global var for activation
		$this->loader->add_action( 'revslider_activate_addon', $plugin_admin, 'get_var',10,2);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Revslider_Sharing_Addon_Public( $this->get_plugin_name(), $this->get_version() );

		// Add actions to output
		$this->loader->add_filter( 'rs_action_output_layer_simple_link', $plugin_public, 'rs_action_output_layer_simple_link', 10, 8 );
		
		// Force Layer tag to be an "<a>" (both hooks needed)
		$this->loader->add_filter( 'rs_action_type', $plugin_public, 'rs_action_type', 10, 1 );
		$this->loader->add_filter( 'rs_action_link_type', $plugin_public, 'rs_action_link_type', 10, 1 );
		
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
