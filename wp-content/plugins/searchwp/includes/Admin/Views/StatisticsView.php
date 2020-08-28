<?php

/**
 * SearchWP StatisticsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Statistics;
use SearchWP\Admin\NavTab;

/**
 * Class StatisticsView is responsible for displaying Statistics.
 *
 * @since 4.0
 */
class StatisticsView {

	private static $slug = 'statistics';

	/**
	 * StatisticsView constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		new NavTab([
			'tab'   => self::$slug,
			'label' => __( 'Statistics', 'searchwp' ),
		]);

		add_action( 'searchwp\settings\view\\' . self::$slug, [ __CLASS__, 'render' ] );
		add_action( 'searchwp\settings\after\\' . self::$slug, [ __CLASS__, 'assets' ], 999 );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'get_statistics',   [ __CLASS__ , 'get_statistics' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'ignore_query',     [ __CLASS__ , 'ignore_query' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'unignore_query',   [ __CLASS__ , 'unignore_query' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'reset_statistics', [ __CLASS__ , 'reset_statistics' ] );
	}

	/**
	 * AJAX callback to reset Statistics.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function reset_statistics() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		Statistics::reset();

		// sleep(1);

		wp_send_json_success( Statistics::get() );
	}

	/**
	 * AJAX callback to ignore a logged query.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function get_statistics() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		wp_send_json_success( Statistics::get() );
	}

	/**
	 * AJAX callback to ignore a logged query.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function ignore_query() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$query  = isset( $_REQUEST['query'] ) ? json_decode( stripslashes( $_REQUEST['query'] ) ) : '';
		$result = Statistics::ignore_query( $query );

		if ( $result ) {
			wp_send_json_success( Statistics::get() );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * AJAX callback to unignore an ignored query.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function unignore_query() {
		check_ajax_referer( SEARCHWP_PREFIX . 'settings' );

		$query = isset( $_REQUEST['query'] ) ? json_decode( stripslashes( $_REQUEST['query'] ) ) : '';
		$result = Statistics::unignore_query( $query );

		if ( $result ) {
			wp_send_json_success( Statistics::get() );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Outputs the assets needed for the StatisticsView UI.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function assets() {
		$handle = SEARCHWP_PREFIX . self::$slug;
		$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true || isset( $_GET['script_debug'] ) ? '' : '.min';

		wp_enqueue_script( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/statistics{$debug}.js",
			[ 'jquery' ], SEARCHWP_VERSION, true );

		wp_enqueue_style( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/statistics{$debug}.css",
			[], SEARCHWP_VERSION );

		Utils::localize_script( $handle, [
			'stats' => Statistics::get(),
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
					<div id="searchwp-statistics"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
