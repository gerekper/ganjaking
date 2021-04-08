<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider Class
 *
 * Base class for WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - register_widgets()
 * - load_localisation()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - ensure_post_thumbnails_support()
 */
class WooSlider {
	public $admin;
	public $frontend;
	public $post_types;
	public $token = 'wooslider';
	public $plugin_url;
	public $plugin_path;
	public $slider_count = 1;
	public $version;
	private $file;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->plugin_path = trailingslashit( dirname( $file ) );

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );

		// Load the Utils class.
		require_once( 'class-wooslider-utils.php' );

		// Setup post types.
		require_once( 'class-wooslider-posttypes.php' );
		$this->post_types = new WooSlider_PostTypes();
		$this->post_types->token = $this->token;
		$this->post_types->file = $this->file;

		// Setup settings screen.
		require_once( 'class-wooslider-settings-api.php' );
		require_once( 'class-wooslider-settings.php' );
		$this->settings = new WooSlider_Settings();
		$this->settings->token = 'wooslider-settings';
		if ( is_admin() ) {
			$this->settings->has_tabs 	= true;
			$this->settings->name 		= __( 'Slideshow Settings', 'wooslider' );
			$this->settings->menu_label	= __( 'Settings', 'wooslider' );
			$this->settings->page_slug	= 'wooslider-settings';
		}

		$this->settings->setup_settings();

		// Differentiate between administration and frontend logic.
		if ( is_admin() ) {
			require_once( 'class-wooslider-admin.php' );
			$this->admin = new WooSlider_Admin();
			$this->admin->token = $this->token;
		} else {
			require_once( 'class-wooslider-frontend.php' );
			$this->frontend = new WooSlider_Frontend();
			$this->frontend->token = $this->token;
			$this->frontend->init();
		}

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
	} // End __construct()

	/**
	 * Register the widgets.
	 * @return [type] [description]
	 */
	public function register_widgets () {
		require_once( $this->plugin_path . 'widgets/widget-wooslider-base.php' );
		require_once( $this->plugin_path . 'widgets/widget-wooslider-attachments.php' );
		require_once( $this->plugin_path . 'widgets/widget-wooslider-posts.php' );
		require_once( $this->plugin_path . 'widgets/widget-wooslider-slides.php' );

		register_widget( 'WooSlider_Widget_Attachments' );
		register_widget( 'WooSlider_Widget_Posts' );
		register_widget( 'WooSlider_Widget_Slides' );
	} // End register_widgets()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wooslider', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wooslider';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'wooslider' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @since  1.0.1
	 * @return  void
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()
} // End Class
?>