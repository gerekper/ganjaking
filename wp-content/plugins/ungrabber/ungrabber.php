<?php
/**
 * Plugin Name: UnGrabber
 * Plugin URI: https://1.envato.market/ungrabber
 * Description: Most effective way to protect your online content from being copy.
 * Author: Merkulove
 * Version: 2.0.1
 * Author URI: https://1.envato.market/cc-merkulove
 * Requires PHP: 5.6
 * Requires at least: 3.0
 * Tested up to: 5.3.2
 **/

/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Merkulove\UnGrabber\AssignmentsTab;
use Merkulove\UnGrabber\PluginUpdater;
use Merkulove\UnGrabber\Helper;
use Merkulove\UnGrabber\PluginHelper;
use Merkulove\UnGrabber\Settings;
use Merkulove\UnGrabber\Shortcodes;
use Merkulove\UnGrabber\EnvatoItem;
    
/**
 * SINGLETON: Core class used to implement a UnGrabber plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class UnGrabber {

	/**
	 * Plugin version.
	 *
	 * @string version
	 * @since 1.0.0
	 **/
	public static $version = '';

	/**
	 * Use minified libraries if SCRIPT_DEBUG is turned off.
	 *
	 * @since 1.0.0
	 **/
	public static $suffix = '';

	/**
	 * URL (with trailing slash) to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $url = '';

	/**
	 * PATH to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $path = '';

	/**
	 * Plugin base name.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $basename = '';

	/**
	 * The one true UnGrabber.
	 *
	 * @var UnGrabber
	 * @since 1.0.0
	 **/
	private static $instance;

    /**
     * Sets up a new plugin instance.
     *
     * @since 1.0.0
     * @access public
     **/
    private function __construct() {

	    /** Initialize main variables. */
	    $this->initialization();

	    /** Define admin hooks. */
	    $this->admin_hooks();

	    /** Define public hooks. */
	    $this->public_hooks();

	    /** Define hooks that runs on both the front-end as well as the dashboard. */
	    $this->both_hooks();

    }

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function both_hooks() {

		/** Load translation. */
		add_action( 'plugins_loaded', [$this, 'load_textdomain'] );

		/** Adds all the necessary shortcodes. */
		Shortcodes::get_instance();

	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function public_hooks() {

		/** Load JavaScript for Frontend Area. */
		add_action( 'wp_enqueue_scripts', [$this, 'scripts'] ); // JS.

		/** JavaScript Required. */
        add_action( 'wp_footer', [$this, 'javascript_required'] );

	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function admin_hooks() {

		/** Add plugin settings page. */
		Settings::get_instance()->add_settings_page();

		/** Load JS and CSS for Backend Area. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ], 100 ); // CSS.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 100 ); // JS.

		/** Remove "Thank you for creating with WordPress" and WP version only from plugin settings page. */
		add_action( 'admin_enqueue_scripts', [$this, 'remove_wp_copyrights'] );

		/** Remove all "third-party" notices from plugin settings page. */
		add_action( 'in_admin_header', [$this, 'remove_all_notices'], 1000 );

	}

	/**
	 * Initialize main variables.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function initialization() {

		/** Plugin version. */
		if ( ! function_exists('get_plugin_data') ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( __FILE__ );
		self::$version = $plugin_data['Version'];

		/** Gets the plugin URL (with trailing slash). */
		self::$url = plugin_dir_url( __FILE__ );

		/** Gets the plugin PATH. */
		self::$path = plugin_dir_path( __FILE__ );

		/** Use minified libraries if SCRIPT_DEBUG is turned off. */
		self::$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/** Set plugin basename. */
		self::$basename = plugin_basename( __FILE__ );

		/** Initialize plugin settings. */
		Settings::get_instance();

		/** Initialize PluginHelper. */
		PluginHelper::get_instance();

		/** Plugin update mechanism enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			PluginUpdater::get_instance();
		}

	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_version() {
		return self::$version;
	}

	/**
	 * Protect Content if JavaScript is Disabled.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function javascript_required() {

		/** Arbitrary JavaScript is not allowed in AMP. */
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

		/** Get plugin settings */
		$options = Settings::get_instance()->options;

		if ( 'on' !== $options['javascript'] ) { return; }

		ob_start();
		?>
		<noscript>
			<div id='mdp-ungrabber-js-disabled'>
				<div><?php echo wp_kses_post( $options['javascript_msg'] ); ?></div>
			</div>
			<style>
				#mdp-ungrabber-js-disabled {
					position: fixed;
					top: 0;
					left: 0;
					height: 100%;
					width: 100%;
					z-index: 999999;
					text-align: center;
					background-color: #FFFFFF;
					color: #000000;
					font-size: 40px;
					display: flex;
					align-items: center;
					justify-content: center;
				}
			</style>
		</noscript>
		<?php
		$result = ob_get_clean();

		echo $result;
	}

	/**
	 * Add JavaScript for the public-facing side of the site.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function scripts() {

		/** Arbitrary JavaScript is not allowed in AMP. */
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

		/** Checks if plugin should work on this page. */
		if ( ! AssignmentsTab::get_instance()->display() ) { return; }

		/** Get Plugin Settings. */
		$options = Settings::get_instance()->options;

		wp_enqueue_script( 'mdp-ungrabber-hotkeys', self::$url . 'js/hotkeys' . self::$suffix . '.js', [], self::$version, true );
		wp_enqueue_script( 'mdp-ungrabber', self::$url . 'js/ungrabber' . self::$suffix . '.js', ['mdp-ungrabber-hotkeys'], self::$version, true );

		wp_localize_script( 'mdp-ungrabber', 'mdpUnGrabber',
			[
				'selectAll'     => $options['select_all'], // Disable Select All.
				'copy'          => $options['copy'], // Disable Copy.
				'cut'           => $options['cut'], // Disable Cut.
				'paste'         => $options['paste'], // Disable Paste.
				'save'          => $options['save'], // Disable Save.
				'viewSource'    => $options['view_source'], // Disable View Source.
				'printPage'     => $options['print_page'], // Disable Print Page.
				'developerTool' => $options['developer_tool'], // Disable Developer Tool.
				'readerMode'    => $options['reader_mode'], // Disable Safari Reader Mode.
				'rightClick'    => $options['right_click'], // Disable Right Click.
				'textSelection' => $options['text_selection'], // Disable Text Selection.
				'imageDragging' => $options['image_dragging'], // Disable Image Dragging.
			]
		);

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function admin_styles() {

		/** Get current screen to add styles on specific pages. */
		$screen = get_current_screen();

		/** Plugin Settings Page. */
		if ( 'toplevel_page_mdp_ungrabber_settings' === $screen->base ) {
			wp_enqueue_style( 'merkulov-ui', self::$url . 'css/merkulov-ui' . self::$suffix . '.css', [], self::$version );
			wp_enqueue_style( 'mdp-ungrabber-admin', self::$url . 'css/admin' . self::$suffix . '.css', [], self::$version );

        /** Plugin popup on update. */
		} elseif ( 'plugin-install' === $screen->base ) {

			/** Styles only for our plugin. */
			if ( isset( $_GET['plugin'] ) AND $_GET['plugin'] === 'ungrabber' ) {
				wp_enqueue_style( 'mdp-ungrabber-plugin-install', self::$url . 'css/plugin-install' . self::$suffix . '.css', [], self::$version );
			}
		}

	}

	/**
	 * Add JS for admin area.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function admin_scripts() {

		/** Get current screen to add scripts on specific pages. */
		$screen = get_current_screen();

		/** Plugin Settings Page. */
		if ( $screen->base == 'toplevel_page_mdp_ungrabber_settings' ) {
			wp_enqueue_script( 'merkulov-ui', self::$url . 'js/merkulov-ui' . self::$suffix . '.js', [], self::$version, true );
		}

	}

	/**
	 * Remove "Thank you for creating with WordPress" and WP version only from plugin settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	public function remove_wp_copyrights() {

		/** Remove "Thank you for creating with WordPress" and WP version from plugin settings page. */
		$screen = get_current_screen(); // Get current screen.

		/** Plugin Settings Page. */
		if ( $screen->base == 'toplevel_page_mdp_ungrabber_settings' ) {
			add_filter( 'admin_footer_text', '__return_empty_string', 11 );
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}

	}

	/**
	 * Remove all other notices.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_all_notices() {

		/** Work only on plugin settings page. */
		$screen = get_current_screen();
		if ( $screen->base !== 'toplevel_page_mdp_ungrabber_settings' ) { return; }

		/** Remove other notices. */
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

	}

	/**
	 * Loads plugin translated strings.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function load_textdomain() {

		load_plugin_textdomain( 'ungrabber', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Run when the plugin is activated.
	 *
	 * @static
	 * @since 1.0.0
	 **/
	public static function on_activation() {

		/** Security checks. */
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		/** Send install Action to our host. */
		Helper::get_instance()->send_action( 'install', 'ungrabber', self::$version );

	}

	/**
	 * Main UnGrabber Instance.
	 *
	 * Insures that only one instance of UnGrabber exists in memory at any one time.
	 *
	 * @static
	 * @return UnGrabber
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof UnGrabber ) ) {
			self::$instance = new UnGrabber;
		}

		return self::$instance;
	}

} // End Class UnGrabber.

/** Run when the plugin is activated. */
register_activation_hook( __FILE__, ['Merkulove\UnGrabber', 'on_activation'] );

/** Run UnGrabber class. */
UnGrabber::get_instance();