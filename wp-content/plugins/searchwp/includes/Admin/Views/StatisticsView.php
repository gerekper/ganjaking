<?php

/**
 * SearchWP StatisticsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Settings;
use SearchWP\Statistics;

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

		if (
			Utils::is_swp_admin_page( 'statistics', 'default' ) ||
			Utils::is_swp_admin_page( 'stats', 'default' )
		) {
			add_action( 'searchwp\settings\page\title', [ __CLASS__, 'page_title' ] );
			add_action( 'searchwp\settings\view', [ __CLASS__, 'render' ] );
			add_action( 'searchwp\settings\after', [ __CLASS__, 'assets' ], 999 );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'get_statistics',         [ __CLASS__ , 'get_statistics' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'ignore_query',           [ __CLASS__ , 'ignore_query' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'unignore_query',         [ __CLASS__ , 'unignore_query' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'reset_statistics',       [ __CLASS__ , 'reset_statistics' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'update_trim_logs_after', [ __CLASS__ , 'update_trim_logs_after' ] );
	}

	/**
	 * AJAX callback to reset Statistics.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function update_trim_logs_after() {

		Utils::check_ajax_permissions();

		$after = isset( $_REQUEST['after'] ) ? absint( $_REQUEST['after'] ) : '';

		if ( is_numeric( $after ) ) {
			Settings::update( 'trim_stats_logs_after', $after );
		}

		wp_send_json_success();
	}

	/**
	 * AJAX callback to reset Statistics.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function reset_statistics() {

		Utils::check_ajax_permissions();

		Statistics::reset();

		wp_send_json_success( Statistics::get() );
	}

	/**
	 * AJAX callback to ignore a logged query.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function get_statistics() {

		Utils::check_ajax_permissions();

		wp_send_json_success( Statistics::get() );
	}

	/**
	 * AJAX callback to ignore a logged query.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function ignore_query() {

		Utils::check_ajax_permissions();

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

		Utils::check_ajax_permissions();

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

		wp_enqueue_style(
            $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/statistics{$debug}.css",
			[ Utils::$slug . 'modal' ],
            SEARCHWP_VERSION
        );

		Utils::localize_script( $handle, [
			'stats'           => Statistics::get(),
			'trimAfter'       => Settings::get( 'trim_stats_logs_after', 'int' ),
			'canEditSettings' => current_user_can( Settings::get_capability() ),
		] );

		add_action( 'admin_print_footer_scripts', function() {
			?>
			<style>
			.searchwp-settings-view .searchwp-settings-statistics-chart-detail table td span > span {
				word-break: break-word;
				word-wrap: anywhere;
			}
			</style>
			<?php
		} );
	}

	/**
	 * StatisticsView main page title.
	 *
	 * @since 4.2.2
	 */
	public static function page_title() {
		?>
        <div class="swp-page-header--white swp-flex--row swp-flex--align-c">
            <h1 class="swp-h1"><?php esc_html_e( 'SearchWP Statistics', 'searchwp' ); ?></h1>
        </div>
		<?php
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
        <div class="searchwp-admin-wrap wrap">
            <div class="searchwp-settings-view">
                <div class="edit-post-meta-boxes-area">
                    <div id="poststuff">
                        <div class="meta-box-sortables">
                            <div id="searchwp-statistics"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}
