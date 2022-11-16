<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
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
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/includes
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class Betterdocs_Pro
{
	public $multiple_kb = '';

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Betterdocs_Pro_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct()
	{
		if (defined('BETTERDOCS_PRO_VERSION')) {
			$this->version = BETTERDOCS_PRO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'betterdocs-pro';
        $this->db();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->analytics_migration();
		$this->multiple_kb = $this->get_multiple_kb();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Betterdocs_Pro_Loader. Orchestrates the hooks of the plugin.
	 * - Betterdocs_Pro_i18n. Defines internationalization functionality.
	 * - Betterdocs_Pro_Admin. Defines all hooks for the admin area.
	 * - Betterdocs_Pro_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-pro-loader.php';
		/**
		 * Extend post type Class
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-multiple-kb.php';
        /**
         * This trait is used to restrict content based on Internal KB settings
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/betterdocs-content-restrictions.php';
		/**
		 * Extend post type Class
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-elementor-extender.php';
		/**
		 * Role Management Class
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-role-management.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-pro-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-betterdocs-pro-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-betterdocs-pro-report-email.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-betterdocs-pro-admin-screen.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-pro-settings.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-analytics.php';
		/**
		 * The class responsible for defining all IA actions that occur in the settings area
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-pro-instant-answer.php';

        /**
         * The class responsible for defining all IA actions that occur in the settings area
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-betterdocs-rest-controller.php';

		/**
		 * The functions responsible for betterdocs pro shortcodes
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/betterdocs-shortcodes.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-betterdocs-pro-public.php';

		/**
		 * The functions responsible for betterdocs customizer
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/customizer/customizer.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/customizer/defaults.php';

		/**
		 * The class responsible for registering widget in elementor and extend single page functionality
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/elementor/class-betterdocs-pro-elementor.php';

		/**
		 * The class responsible for defining all actions that occur in rest api.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-pro-rest-api.php';

		$this->loader = new Betterdocs_Pro_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Betterdocs_Pro_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Betterdocs_Pro_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Betterdocs_Pro_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_filter('admin_body_class', $plugin_admin, 'body_classes');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		new BetterDocs_Pro_Admin_Screen();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Betterdocs_Pro_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'load_assets');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Betterdocs_Pro_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	public static function get_multiple_kb()
	{
		$get_multiple_kb = BetterDocs_DB::get_settings('multiple_kb');
		$multiple_kb = apply_filters('betterdocs_get_multiple_kb', $get_multiple_kb);
		return $multiple_kb;
	}

    public function db() {
        global $wpdb;
        $table_name = $wpdb->prefix.'betterdocs_analytics';
        if ( get_site_option( 'betterdocs_pro_db_version' ) != BETTERDOCS_PRO_DB_VERSION ) {
            $sql = "CREATE TABLE $table_name (
                id bigint NOT NULL AUTO_INCREMENT,
                post_id bigint DEFAULT 0 NOT NULL,
                impressions bigint DEFAULT 0 NOT NULL,
                unique_visit bigint DEFAULT 0 NOT NULL,
                happy bigint DEFAULT 0 NOT NULL,
                sad bigint DEFAULT 0 NOT NULL,
                normal bigint DEFAULT 0 NOT NULL,
                created_at date DEFAULT '0000-00-00' NOT NULL,
                PRIMARY KEY (id),
                KEY post_id (post_id),
                KEY impressions (impressions),
                KEY unique_visit (unique_visit),
                KEY happy (happy),
                KEY sad (sad),
                KEY normal (normal),
                KEY created_at (created_at)
            )";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);
            update_option( "betterdocs_pro_db_version", BETTERDOCS_PRO_DB_VERSION );
        }
    }

    public function analytics_migration() {
        global $migration_Process;
        $migration_Process = new BetterDocs_Migration_Process();
    }

    /**
     * validate html tag
     * @param $tag
     * @return mixed|string
     */
    public static function validate_html_tag( $tag ){
        $allowed_tags = [
            'article',
            'aside',
            'div',
            'footer',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'header',
            'main',
            'nav',
            'p',
            'section',
            'span',
        ];
        return in_array( strtolower( $tag ), $allowed_tags ) ? $tag : 'div';
    }
}
