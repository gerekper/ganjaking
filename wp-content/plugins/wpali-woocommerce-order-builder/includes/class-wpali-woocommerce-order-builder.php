<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpali.com
 * @since      1.0.7
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/includes
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
 * @since      1.0.7
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/includes
 * @author     ALI KHALLAD <ali@wpali.com>
 */
class Wpali_Woocommerce_Order_Builder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpali_Woocommerce_Order_Builder_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'WWOB_VERSION' ) ) {
			$this->version = WWOB_VERSION;
		} else {
			$this->version = '1.0.7';
		}
		$this->plugin_name = 'wpali-woocommerce-order-builder';

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
	 * - Wpali_Woocommerce_Order_Builder_Loader. Orchestrates the hooks of the plugin.
	 * - Wpali_Woocommerce_Order_Builder_i18n. Defines internationalization functionality.
	 * - Wpali_Woocommerce_Order_Builder_Admin. Defines all hooks for the admin area.
	 * - Wpali_Woocommerce_Order_Builder_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpali-woocommerce-order-builder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpali-woocommerce-order-builder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpali-woocommerce-order-builder-admin.php';

		if ( class_exists( 'WooCommerce' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wpali-woocommerce-order-builder-admin-metaboxes.php';
		}
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpali-woocommerce-order-builder-public.php';

		$this->loader = new Wpali_Woocommerce_Order_Builder_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpali_Woocommerce_Order_Builder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wpali_Woocommerce_Order_Builder_i18n();

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

		$plugin_admin = new Wpali_Woocommerce_Order_Builder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		if ( !class_exists( 'WooCommerce' ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'wwob_admin_notice__error' );
		}

	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.7
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wpali_Woocommerce_Order_Builder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		if ( class_exists( 'WooCommerce' ) ) {
			$this->loader->add_action( 'wc_get_template', $plugin_public, 'theme_customisations_wc_get_template', 11, 5 );
			$this->loader->add_action( 'init', $plugin_public, 'wwob_display_product_display_functions' );
			$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_public, 'wwob_woocommerce_extended_product', 10 );
			$this->loader->add_action( 'woocommerce_after_add_to_cart_quantity', $plugin_public, 'WWOB_add_total_after_quantity', 10 );
			$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_public, 'WWOB_add_html_tags_after_addtocart', 10 );
			
			$this->loader->add_filter( 'woocommerce_add_cart_item', $plugin_public, 'set_wwob_prices' );
			$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'wwob_add_cart_item_data', 10, 2 );
			$this->loader->add_filter( 'woocommerce_get_cart_item_from_session', $plugin_public, 'set_get_cart_item_from_session', 20 , 3 );

			$this->loader->add_filter( 'woocommerce_email_order_meta_fields', $plugin_public, 'wwob_email_order_meta_fields' );
			$this->loader->add_filter( 'woocommerce_order_item_product', $plugin_public, 'wwob_order_item_product', 10, 2 );
			$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'wwob_get_item_data', 10 , 2 );
			$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_public, 'wwob_add_order_item_meta', 10 , 2 );
			$this->loader->add_action( 'wp_footer', $plugin_public, 'wwob_extra_items_styles');
			$this->loader->add_action( 'woocommerce_add_to_cart_validation', $plugin_public, 'wwob_prevent_items_add_to_cart',  10, 2);
			$this->loader->add_filter( 'body_class', $plugin_public, 'add_wwob_slug_body_class');
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
	 * @return    Wpali_Woocommerce_Order_Builder_Loader    Orchestrates the hooks of the plugin.
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
