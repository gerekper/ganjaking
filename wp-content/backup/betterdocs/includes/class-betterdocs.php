<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
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
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class BetterDocs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      BetterDocs_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BETTER_DOCUMENTATION_VERSION' ) ) {
			$this->version = BETTER_DOCUMENTATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'betterdocs';

		$this->load_dependencies();
		$this->set_locale();
		$this->start_plugin_tracking();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		add_action( 'admin_init', array( $this, 'redirect' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - BetterDocs_Loader. Orchestrates the hooks of the plugin.
	 * - BetterDocs_i18n. Defines internationalization functionality.
	 * - BetterDocs_Admin. Defines all hooks for the admin area.
	 * - BetterDocs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * Quick Setup Wizard
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/setup-wizard/betterdocs-setup-wizard-config.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-betterdocs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-betterdocs-i18n.php';
		require_once BETTERDOCS_DIR_PATH . 'includes/class-betterdocs-usage-tracker.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'class-betterdocs-admin.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-db.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-metabox.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-settings.php';

		/**
		 * Notice Messages
		 */
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-notice.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-betterdocs-public.php';

		/**
		 * The functions responsible for betterdocs helpers
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-betterdocs-helpers.php';

		/**
		 * The class responsible for registering docs post type and it's category and tags taxonomy
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-betterdocs-docs-post-type.php';

		/**
		 * The functions responsible for betterdocs shortcodes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/betterdocs-shortcodes.php';
		
		/**
		 * The functions responsible for betterdocs breadcrumbs
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/betterdocs-breadcrumbs.php';

		/**
		 * The functions responsible for betterdocs customizer
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/customizer/customizer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/customizer/defaults.php';

		$this->loader = new BetterDocs_Loader();
	}

	/**
	 * Optional usage tracker
	 *
	 * @since v1.0.0
	*/
	public function start_plugin_tracking() {
		new BetterDocs_Plugin_Usage_Tracker(
			BETTERDOCS_FILE,
			'http://app.wpdeveloper.net',
			array(),
			true,
			true,
			1
		);
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the BetterDocs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new BetterDocs_i18n();

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

		$plugin_admin = new BetterDocs_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_menu', array( $plugin_admin, 'menu_page') );
		
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles') );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts') );
		add_filter( 'parent_file', array( &$plugin_admin, 'highlight_admin_menu'), 10, 2 );
		global $pagenow;
		if ( $pagenow == 'edit-tags.php' ) {
			add_filter( 'submenu_file', array( &$plugin_admin, 'highlight_admin_submenu'), 10, 2);
		}
		add_action( 'admin_bar_menu', array( $plugin_admin, 'toolbar_menu'), 32 );

		BetterDocs_Settings::init();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new BetterDocs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	 * @return    BetterDocs_Loader    Orchestrates the hooks of the plugin.
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

	public function is_pro_active(){
		return defined( 'BETTERDOCS_PRO_VERSION' );
	}

	public function redirect() {
		// Bail if no activation transient is set.
		if ( ! get_transient( '_betterdocs_meta_activation_notice' ) ) {
			return;
		}
		// Delete the activation transient.
		delete_transient( '_betterdocs_meta_activation_notice' );

		if ( ! is_multisite() ) {
			// Redirect to the welcome page.
			wp_safe_redirect( add_query_arg( array(
				'page'		=> 'betterdocs-setup'
			), admin_url( ! $this->is_pro_active() ? 'edit.php?post_type=docs' : 'admin.php?page=betterdocs-setup' ) ) );
		}
	}

}
