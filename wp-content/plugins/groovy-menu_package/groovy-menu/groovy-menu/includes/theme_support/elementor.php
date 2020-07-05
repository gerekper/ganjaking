<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

global $gm_supported_module;

if ( ! function_exists( 'groovy_menu_support_elementor_init' ) ) {
	function groovy_menu_support_elementor_init() {
		if ( defined( 'ELEMENTOR_VERSION' ) && did_action( 'elementor/loaded' ) ) {
			require_once 'elementor-gm-widget.php';

			add_action( 'gm_enqueue_script_actions', 'groovy_menu_support_elementor_enquare_styles', 50 );
			add_action( 'elementor/editor/after_enqueue_styles', function () {
				wp_enqueue_style( 'groovy-css-admin-menu', GROOVY_MENU_URL . 'assets/style/admin-common.css', [], GROOVY_MENU_VERSION );
			} );

		}
	}
}

add_action( 'init', 'groovy_menu_support_elementor_init', 99 );


if ( ! function_exists( 'groovy_menu_support_elementor_post_types' ) ) {

	/**
	 * Add Elementor post types for Groovy Menu.
	 *
	 * @param $post_types array Post types list.
	 *
	 * @return array
	 */
	function groovy_menu_support_elementor_post_types( $post_types ) {

		if ( defined( 'ELEMENTOR_VERSION' ) && is_array( $post_types ) && ! in_array( 'elementor_library', $post_types, true ) ) {
			$post_types[] = 'elementor_library';
		}

		return $post_types;
	}
}

add_filter( 'groovy_menu_single_post_add_meta_box_post_types', 'groovy_menu_support_elementor_post_types', 10, 1 );


if ( ! function_exists( 'groovy_menu_prevent_output_for_elementor_post_types' ) ) {

	/**
	 * Prevent output Groovy Menu for Elementor post types.
	 *
	 * @param $prevent bool if return true - groovy menu will disapear.
	 *
	 * @return bool
	 */
	function groovy_menu_prevent_output_for_elementor_post_types( $prevent ) {

		if ( defined( 'ELEMENTOR_VERSION' ) && 'elementor_library' === get_post_type() ) {
			$prevent = true;
		}

		return $prevent;
	}
}

add_filter( 'groovy_menu_prevent_output_html', 'groovy_menu_prevent_output_for_elementor_post_types', 10, 1 );


if ( ! function_exists( 'groovy_menu_support_elementor_enquare_styles' ) ) {

	/**
	 * Add Elementor styles for editor
	 */
	function groovy_menu_support_elementor_enquare_styles() {
		if ( ! empty( $_GET['elementor-preview'] ) ) {
			wp_enqueue_style( 'groovy-css-admin-menu', GROOVY_MENU_URL . 'assets/style/admin-common.css', [], GROOVY_MENU_VERSION );
		}
	}
}


/**
 * Main Class for Elementor Groovy Menu plugin
 *
 * The main class that initiates and runs the plugin.
 *
 * @since  2.1.1
 */
final class Elementor_Groovy_Menu_Plugin_Extension {

	/**
	 * Plugin Version
	 *
	 * @since  2.1.1
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since  2.1.1
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Instance
	 *
	 * @since  2.1.1
	 *
	 * @access private
	 * @static
	 *
	 * @var Elementor_Groovy_Menu_Plugin_Extension The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since  2.1.1
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Groovy_Menu_Plugin_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since  2.1.1
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since  2.1.1
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );

			return;
		}

		// Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since  2.1.1
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'groovy-menu' ),
			'<strong>' . esc_html__( 'Groovy Menu Elementor module', 'groovy-menu' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'groovy-menu' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}


	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since  2.1.1
	 *
	 * @access public
	 */
	public function init_widgets() {

		// Include Widget files
		require_once( __DIR__ . '/elementor-gm-widget.php' );

		// Register widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_Groovy_Menu_Plugin() );

	}

}

Elementor_Groovy_Menu_Plugin_Extension::instance();
