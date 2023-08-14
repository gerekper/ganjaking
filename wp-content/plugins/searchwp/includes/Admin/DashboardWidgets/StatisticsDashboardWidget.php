<?php

/**
 * SearchWP StatisticsDashboardWidget.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\DashboardWidgets;

use SearchWP\Settings;
use SearchWP\Statistics;

/**
 * Class StatisticsDashboardWidget is responsible for displaying a Statistics Dashboard Widget.
 *
 * @since 4.0
 */
class StatisticsDashboardWidget {

	private static $days = 30;

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function __construct() {
		if ( current_user_can( Statistics::get_capability() )
				&& apply_filters( 'searchwp\admin\dashboard_widgets\statistics', true )
		) {
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
			add_action( 'wp_dashboard_setup',    [ __CLASS__, 'add' ] );
			self::$days = absint( apply_filters( 'searchwp\admin\dashboard_widgets\statistics\days', 30 ) );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'admin_dashboard_widget_statistics', [ __CLASS__, 'get' ] );
	}

	/**
	 * Callback to add the Dashboard Widget.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function add() {
		wp_add_dashboard_widget(
			SEARCHWP_PREFIX . 'statistics',
			sprintf( __( 'SearchWP Statistics %d Day Overview', 'searchwp' ), self::$days ), // Translators: placeholder is a number of days.
			[ __CLASS__, 'view' ]
		);
	}

	/**
	 * Render the view for the Dashboard Widget.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function view() {
		$nonce = wp_create_nonce( SEARCHWP_PREFIX . 'admin_dashboard_widget_statistics' );

		$engines = Settings::get_engines();
		?>
		<div class="searchwp-admin-dashboard-widget-statistics" id="searchwp-admin-dashboard-widget-statistics">
			<?php if ( count( $engines ) > 1 ) : ?>
				<ul>
					<?php foreach ( $engines as $engine ) : ?>
						<li>
							<a href="#searchwp-admin-dashboard-widget-statistics-<?php echo esc_attr( $engine->get_name() ); ?>">
								<span><?php echo esc_html( $engine->get_label() ); ?></span>
								<span class="dashicons dashicons-arrow-right"></span>
							</a>
						</li<>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php foreach ( $engines as $engine ) : ?>
				<div class="searchwp-admin-dashboard-widget-statistics-engine"
						id="searchwp-admin-dashboard-widget-statistics-<?php echo esc_attr( $engine->get_name() ); ?>"
						data-engine="<?php echo esc_attr( $engine->get_name() ); ?>">
					<p class="description"><?php esc_html_e( 'Loading...', 'searchwp' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<script>
			jQuery(document).ready(function($){
				var $el = $('#searchwp-admin-dashboard-widget-statistics');

				$(document).on('click', '#searchwp-admin-dashboard-widget-statistics > ul > li a', function(e) {
					e.preventDefault();
					$el.find('> ul .searchwp-is-active').removeClass('searchwp-is-active');
					$(this).parent().addClass('searchwp-is-active');

					$('.searchwp-admin-dashboard-widget-statistics-engine').hide();
					$el.find($(this).attr('href')).show();
				});

				$('.searchwp-admin-dashboard-widget-statistics-engine').each(function(index, engine) {
					var engineName = $(engine).data('engine');

					$(this).hide();

					$.post(ajaxurl, {
						_ajax_nonce: '<?php echo esc_js( $nonce ); ?>',
						action: '<?php echo esc_js( SEARCHWP_PREFIX . 'admin_dashboard_widget_statistics' ); ?>',
						engine: engineName
					}, function(response) {
						if (response.success) {
							$(engine).html(response.data);
						} else {
							alert('There was an error retrieving statistics for ' + engineName);
						}

						// Show the stats.
						if($('#searchwp-admin-dashboard-widget-statistics > ul').length) {
							$('#searchwp-admin-dashboard-widget-statistics > ul > li:first a').trigger('click');
						} else {
							$('#searchwp-admin-dashboard-widget-statistics > div.searchwp-admin-dashboard-widget-statistics-engine').show();
						}
					});
				});
			});
		</script>
		<style>
			#searchwp_statistics .inside {
				/* We're creating a faux column so we need to adjust these core properties. */
				overflow: hidden;
				margin-top: 0;
				margin-bottom: 0;
				padding-top: 11px;
				padding-bottom: 11px;
			}

			.searchwp-admin-dashboard-widget-statistics {
				display: flex;
			}

			.searchwp-admin-dashboard-widget-statistics > ul {
				list-style: none;
				margin: 0;
				padding: 0 2em 0 0;
				position: relative;
			}

			.searchwp-admin-dashboard-widget-statistics > ul:after {
				display: block;
				position: absolute;
				top: -4em;
				right: 1.5em; /* Partial offset from .ui-tabs-nav padding-right. */
				bottom: -4em;
				left: -4em;
				content: '';
				z-index: 1;
				background: #f7f7f7;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > * {
				position: relative;
				z-index: 2;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > li:not(.searchwp-is-active) a {
				color: inherit !important;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > li a {
				display: flex;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > li a span {
				display: block;
				line-height: 1.5;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > li a span.dashicons {
				line-height: 1;
				visibility: hidden;
			}

			.searchwp-admin-dashboard-widget-statistics > ul > li.searchwp-is-active a span.dashicons {
				visibility: visible;
			}

			.searchwp-admin-dashboard-widget-statistics-engine {
				flex: 1;
				padding-top: 0;
				padding-right: 1em;
			}

			.searchwp-admin-dashboard-widget-statistics-engine table {
				width: 100%;
			}

			.searchwp-admin-dashboard-widget-statistics-engine th {
				text-align: left;
				padding-bottom: 0.35em;
			}

			.searchwp-admin-dashboard-widget-statistics-engine td {
				padding: 0.3em 0;
				vertical-align: top;
			}

			.searchwp-admin-dashboard-widget-statistics-engine td div {
				padding-right: 2em;
				word-break: break-word;
				word-wrap: anywhere;
			}
		</style>
		<?php
	}

	/**
	 * AJAX callback to retrieve Statistics to display in the Widget.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function get() {
		check_ajax_referer( SEARCHWP_PREFIX . 'admin_dashboard_widget_statistics' );

		$engine = isset( $_REQUEST['engine'] ) ? stripslashes( $_REQUEST['engine'] ) : '';
		$engine_settings = Settings::get_engine_settings( $engine );

		if ( empty( $engine_settings ) ) {
			wp_send_json_error( __( 'Invalid Engine name', 'searchwp' ) );
		}

		$statistics = Statistics::get_popular_searches( [
			'days'    => self::$days,
			'engine'  => $engine,
			'exclude' => Settings::get( 'ignored_queries', 'array' ),
		] );

		wp_send_json_success( Statistics::display( $statistics, false ) );
	}

	/**
	 * Enqueue our assets
	 *
	 * @since 4.0
	 * @param string $hook The hook for this screen.
	 * @return void
	 */
	public static function assets( $hook ) {
		if ( 'index.php' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
	}
}
