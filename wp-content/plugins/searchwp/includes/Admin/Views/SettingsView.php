<?php

/**
 * SearchWP SettingsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Admin\NavTab;
use SearchWP\Logic\Synonyms;
use SearchWP\Logic\Stopwords;

/**
 * Class SettingsView is responsible for providing the UI for Settings.
 *
 * @since 4.0
 */
class SettingsView {

	private static $slug = 'settings';

	/**
	 * SettingsView constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		new NavTab([
			'tab'   => self::$slug,
			'label' => __( 'Settings', 'searchwp' ),
		]);

		add_action( 'searchwp\settings\view\\' . self::$slug,  [ __CLASS__, 'render' ] );
		add_action( 'searchwp\settings\after\\' . self::$slug, [ __CLASS__, 'assets' ], 999 );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'stopwords_suggestions', [ __CLASS__ , 'get_stopwords_suggestions' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'stopwords_update',      [ __CLASS__ , 'update_stopwords' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'synonyms_update',       [ __CLASS__ , 'update_synonyms' ] );
	}

	/**
	 * AJAX callback to update saved synonyms.
	 *
	 * @since 4.0
	 */
	public static function update_synonyms() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$update = isset( $_REQUEST['synonyms'] ) ? json_decode( stripslashes( $_REQUEST['synonyms'] ), true ) : false;

		$synonyms = new Synonyms();
		$update = $synonyms->save( $update );

		wp_send_json_success( $update );
	}

	/**
	 * AJAX callback to update saved stopwords.
	 *
	 * @since 4.0
	 */
	public static function update_stopwords() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$update = isset( $_REQUEST['stopwords'] ) ? json_decode( stripslashes( $_REQUEST['stopwords'] ), true ) : false;

		$stopwords = new Stopwords();
		$update = $stopwords->save( $update );

		wp_send_json_success( $update );
	}

	/**
	 * AJAX callback to get suggested stopwords.
	 *
	 * @since 4.0
	 */
	public static function get_stopwords_suggestions() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$stopwords = new Stopwords();

		wp_send_json_success( $stopwords->get_suggestions( [
			'limit'     => absint( apply_filters( 'searchwp\stopwords\suggestions\limit', 20 ) ),
			'threshold' => floatval( apply_filters( 'searchwp\stopwords\suggestions\threshold', 0.3 ) )
		] ) );
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function assets() {
		$handle = SEARCHWP_PREFIX . self::$slug;
		$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true || isset( $_GET['script_debug'] ) ? '' : '.min';

		wp_enqueue_script( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/settings{$debug}.js",
			[ 'jquery' ], SEARCHWP_VERSION, true );

		wp_enqueue_style( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/settings{$debug}.css",
			[], SEARCHWP_VERSION );

		$stopwords = new Stopwords();
		$synonyms  = new Synonyms();

		Utils::localize_script( $handle, [
			'stopwords' => [
				'list'     => $stopwords->get(),
				'defaults' => $stopwords->get_default(),
				'suggest'  => (bool) apply_filters( 'searchwp\stopwords\suggestions', true ),
			],
			'synonyms' => $synonyms->get(),
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
					<div id="searchwp-settings"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
