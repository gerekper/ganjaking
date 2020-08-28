<?php

/**
 * SearchWP AdvancedView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Settings;
use SearchWP\Admin\NavTab;

/**
 * Class AdvancedView is responsible for providing the UI for Advanced.
 *
 * @since 4.0
 */
class AdvancedView {

	private static $slug = 'advanced';

	/**
	 * AdvancedView constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		new NavTab([
			'tab'   => self::$slug,
			'label' => __( 'Advanced', 'searchwp' ),
		]);

		add_action( 'searchwp\settings\view\\' . self::$slug,  [ __CLASS__, 'render' ] );
		add_action( 'searchwp\settings\after\\' . self::$slug, [ __CLASS__, 'assets' ], 999 );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'import_engines', [ __CLASS__, 'import_engines' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'update_setting', [ __CLASS__, 'update_setting' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'wake_indexer',   [ __CLASS__, 'wake_indexer' ] );
	}

	/**
	 * Outputs the assets needed for the AdvancedView UI.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function assets() {
		$handle = SEARCHWP_PREFIX . self::$slug;
		$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true || isset( $_GET['script_debug'] ) ? '' : '.min';

		wp_enqueue_script( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/advanced{$debug}.js",
			[ 'jquery' ], SEARCHWP_VERSION, true );

		wp_enqueue_style( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/advanced{$debug}.css",
			[], SEARCHWP_VERSION );

		Utils::localize_script( $handle, [
			'engines'  => Settings::_get_engines_settings(),
			'settings' => call_user_func_array( 'array_merge', array_map( function( $key ) {
				return [ $key => Settings::get_single( $key, 'boolean' ) ];
			}, Settings::get_keys() ) ),
		] );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function render() {
		// This node structure is as such to inherit WP-admin CSS.
		?>
		<div class="edit-post-meta-boxes-area">
			<div id="poststuff">
				<div class="meta-box-sortables">
					<div id="searchwp-advanced"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX callback to update a setting.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function update_setting() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$setting = isset( $_REQUEST['setting'] ) ? Utils::decode_string( $_REQUEST['setting'] ) : null;
		$value   = isset( $_REQUEST['value'] )   ? json_decode( stripslashes( $_REQUEST['value'] ), true ) : null;

		if ( is_null( $setting ) || is_null( $value ) ) {
			wp_send_json_error();
		}

		Settings::update( $setting, $value );

		wp_send_json_success();
	}

	/**
	 * AJAX callback to wake the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function wake_indexer() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$indexer = \SearchWP::$indexer;
		$indexer->_wake_up();

		wp_send_json_success();
	}

	/**
	 * AJAX callback to import engines.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function import_engines() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$configs = isset( $_REQUEST['configs'] ) ? json_decode( stripslashes( $_REQUEST['configs'] ), true ) : false;

		// Run these Engines through the saving process which validates and persists.
		$engines_view = new \SearchWP\Admin\Views\EnginesView();
		$engines_view->update_engines( $configs );

		wp_send_json_success();
	}
}
