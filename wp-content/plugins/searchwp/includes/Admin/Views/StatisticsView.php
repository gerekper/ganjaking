<?php

/**
 * SearchWP StatisticsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\License;
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

		// This style is for the non-Vue part of the page.
		wp_enqueue_style(
			$handle . '_static',
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/statistics.css',
			[],
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
            <?php self::get_metrics_upsell(); ?>
        </div>
		<?php
	}

	/**
     * Outputs the upsell for the Metrics extension.
     *
     * @since 4.3.10
	 */
    private static function get_metrics_upsell() {

		$license_type = License::get_type();

		$link_text  = '';
		$link_url   = '';
		$bonus_text = '';

        switch ( $license_type ) {
            case '':
			case 'standard':
                $link_text  = __( 'Upgrade to PRO Today to Unlock Metrics', 'searchwp' );
                $link_url   = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Statistics+Upsell+Button&utm_campaign=SearchWP&utm_content=Get+SearchWP+Pro+Today+To+Unlock+The+Metrics+Extension';
                $bonus_text = __( '<strong>Bonus:</strong> SearchWP Standard users get up to <span class="green">$200 off their upgrade price</span>, automatically applied at checkout.', 'searchwp' );
				break;

            case 'pro':
			case 'agency':
                $link_text = __( 'Get SearchWP Metrics Today', 'searchwp' );
                $link_url  = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Statistics+Upsell+Button&utm_campaign=SearchWP&utm_content=Get+SearchWP+Metrics+Today+To+Unlock+Advanced+Tracking';
                break;

            default:
                break;
		}

        ?>
        <div class="searchwp-settings-statistics-upsell">
            <h5><?php esc_html_e( 'Get Metrics Extension Today and Unlock Advanced Search Tracking', 'searchwp' ); ?></h5>
            <p>
				<?php esc_html_e( 'Take the next step in tracking your search statistics with the Metrics extension!', 'searchwp' ); ?><br>
            	<?php esc_html_e( 'Get a unique insight into your visitors\' search behavior with advanced tools like click tracking and custom reporting.', 'searchwp' ); ?>
			</p>
			<img class="swp-img" src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/statistics/metrics-upsell.jpg' ); ?>" alt="Metrics screenshot">
			<div class="list">
                <ul>
                    <li><?php esc_html_e( 'Zoom into your data: structure your report by date range, search queries, engines, or everything at once.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Analyze popular searches and discover which results users clicked for every query.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Automatically move your most clicked content to the top of search results.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Check queries with no results for new content ideas preventing failed searches.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Use unique, actionable advice based on your website data to make your best content shine.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Blocklist the unwanted search queries to keep your search reports clean and focused.', 'searchwp' ); ?></li>
                    <li><?php esc_html_e( 'Get in-depth metrics for every engine: total searches, searches per user, average click rank, etc.', 'searchwp' ); ?></li>
                </ul>
            </div>
            <a href="<?php echo esc_url( $link_url ); ?>" class="swp-button swp-button--green" target="_blank" rel="noopener noreferrer" title="<?php esc_html_e( 'Get SearchWP Metrics Today', 'searchwp' ); ?>"><?php echo esc_html( $link_text ); ?></a>
			<?php if ( ! empty( $bonus_text ) ) : ?>
				<p>
					<?php
						echo wp_kses(
							$bonus_text,
							[
								'strong' => [],
								'span'   => [
									'class' => [],
								],
							]
						);
					?>
				</p>
			<?php endif; ?>
        </div>
        <?php
    }
}
