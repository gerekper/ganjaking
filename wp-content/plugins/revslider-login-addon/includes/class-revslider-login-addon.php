<?php
/**
 * @link       https://www.themepunch.com
 * @package    Revslider_Login_Addon
 * @subpackage Revslider_Login_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Login_Addon {

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

		$this->plugin_name = 'revslider-login-addon';
		$this->version = REV_ADDON_LOGIN_VERSION;

		$this->load_dependencies();
		$this->set_locale();

		if(is_admin()) {
			$this->define_admin_hooks();
		}
		else {
			$enabled = get_option('revslider_login_enabled');
			if(!empty($enabled)) $this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-login-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-login-addon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revslider-login-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revslider-login-addon-public.php';

		/**
		 * The class responsible for the update process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-login-addon-update.php';

		$this->loader = new Revslider_Login_Addon_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {

		$plugin_i18n = new Revslider_Login_Addon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 **/
	private function define_admin_hooks() {

		$plugin_admin = new Revslider_Login_Addon_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnLoginUpdate(REV_ADDON_LOGIN_VERSION);

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//updates
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $update_admin ,'set_update_transient' );
		$this->loader->add_filter( 'plugins_api', $update_admin ,'set_updates_api_results',10,3 );

		//admin page
		// $this->loader->add_filter('rev_addon_dash_slideouts',$plugin_admin,'display_plugin_admin_page');		
		$this->loader->add_action( 'revslider_do_ajax', $plugin_admin, 'do_ajax',10,2);	

		//meta placeholder for login form
		$this->loader->add_action( 'rev_slider_insert_gallery_meta_row', $plugin_admin ,'add_placeholder' );

		//build js global var for activation
		$this->loader->add_action( 'revslider_activate_addon', $plugin_admin, 'get_var',10,2);	

	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new Revslider_Login_Addon_Public( $this->get_plugin_name(), $this->get_version() );

		// add shortcodes for forms display
		$this->loader->add_action( 'init', $plugin_public, 'add_shortcodes' );
		$this->loader->add_action( 'login_form_login', $plugin_public, 'redirect_to_custom_login' );

		// login error message
		$this->loader->add_action( 'wp_login_failed', $plugin_public, 'front_end_login_fail' );

		// overtake lost password
		$revslider_login_addon_values = array();
		parse_str(get_option('revslider_login_addon'), $revslider_login_addon_values);
		//var_dump($revslider_login_addon_values);
		$revslider_login_addon_values['revslider-login-lost-password-overtake'] = isset($revslider_login_addon_values['revslider-login-lost-password-overtake']) ? $revslider_login_addon_values['revslider-login-lost-password-overtake'] : '0';
	    if($revslider_login_addon_values['revslider-login-lost-password-overtake']){
			$this->loader->add_action( 'login_form_lostpassword', $plugin_public, 'redirect_to_custom_lostpassword' ) ;
	    }
		
		// replace meta with shortcode
		$this->loader->add_filter( 'revslider_modify_layer_text', $plugin_public, 'rev_addon_insert_meta',10,2 );
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
