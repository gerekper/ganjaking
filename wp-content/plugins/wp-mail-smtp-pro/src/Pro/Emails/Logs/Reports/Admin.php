<?php

namespace WPMailSMTP\Pro\Emails\Logs\Reports;

use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\WP;
use WPMailSMTP\Admin\Area;
use WPMailSMTP\Admin\ParentPageAbstract;
use WPMailSMTP\Pro\Emails\Logs\Migration;
use WPMailSMTP\Admin\Pages\EmailReportsTab;

/**
 * Email reports admin page.
 *
 * @since 3.0.0
 */
class Admin extends EmailReportsTab {

	/**
	 * Email reports list table object.
	 *
	 * @since 3.0.0
	 *
	 * @var Table
	 */
	private $table;

	/**
	 * Emails stats report object.
	 *
	 * @since 3.0.0
	 *
	 * @var Report
	 */
	private $report;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param ParentPageAbstract $parent_page Parent page.
	 */
	public function __construct( $parent_page = null ) {

		parent::__construct( $parent_page );

		$this->report = new Report( $this->parse_report_params() );
	}

	/**
	 * Parse report params from request.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private function parse_report_params() {

		$params = [];

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['order'] ) ) {
			$params['order'] = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) );
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$params['orderby'] = sanitize_key( $_REQUEST['orderby'] );
		}

		if ( ! empty( $_REQUEST['s'] ) ) {
			$params['search'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
		}

		$timespan = ! empty( $_REQUEST['timespan'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['timespan'] ) ) : 7;

		if ( $timespan === 'custom' && isset( $_REQUEST['date'] ) ) {
			$date           = array_filter( explode( ' - ', sanitize_text_field( wp_unslash( $_REQUEST['date'] ) ) ) );
			$params['date'] = ! empty( $date ) ? $date : [ current_time( 'Y-m-d' ) ];
		} elseif ( is_numeric( $timespan ) ) {
			$params['date'] = [
				( new \DateTime( 'now', WP::wp_timezone() ) )->modify( "- $timespan days" )->format( 'Y-m-d' ),
				( new \DateTime( 'now', WP::wp_timezone() ) )->modify( '- 1 day' )->format( 'Y-m-d' ),
			];
		}
		// phpcs:enable

		return $params;
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.0.0
	 */
	public function hooks() {

		if (
			wp_mail_smtp()->get_pro()->get_logs()->is_enabled() &&
			wp_mail_smtp()->get_pro()->get_logs()->is_valid_db()
		) {
			add_action( 'admin_init', [ $this, 'init_table' ] );
			add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );

			// Initialize screen options.
			add_action( 'load-wp-mail-smtp_page_wp-mail-smtp-' . $this->get_parent_slug(), [ $this, 'screen_options' ] );
			add_filter( 'set-screen-option', [ $this, 'set_screen_options' ], 10, 3 );
			add_filter( 'set_screen_option_wp_mail_smtp_report_items_per_page', [ $this, 'set_screen_options' ], 10, 3 );
		}

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_main_styles' ] );
	}

	/**
	 * Register ajax hooks.
	 *
	 * @since 3.0.0
	 */
	public function ajax() {

		add_action( 'wp_ajax_wp_mail_smtp_email_reports_get_single_stats', [ $this, 'get_single_stats_ajax' ] );
	}

	/**
	 * Initialize stats table.
	 *
	 * @since 3.0.0
	 */
	public function init_table() {

		$this->table = new Table( $this->report );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script(
			'wp-mail-smtp-moment',
			wp_mail_smtp()->assets_url . '/js/vendor/moment.min.js',
			[],
			'2.22.2',
			true
		);

		wp_enqueue_script(
			'wp-mail-smtp-chart',
			wp_mail_smtp()->assets_url . '/js/vendor/chart.min.js',
			[ 'wp-mail-smtp-moment' ],
			'2.9.4',
			true
		);

		wp_enqueue_style(
			'wp-mail-smtp-admin-flatpickr',
			wp_mail_smtp()->assets_url . '/css/vendor/flatpickr.min.css',
			[],
			'4.6.9'
		);
		wp_enqueue_script(
			'wp-mail-smtp-admin-flatpickr',
			wp_mail_smtp()->assets_url . '/js/vendor/flatpickr.min.js',
			[],
			'4.6.9',
			false
		);

		wp_enqueue_script(
			'wp-mail-smtp-email-reports',
			wp_mail_smtp()->pro->assets_url . "/js/smtp-pro-email-reports{$min}.js",
			[ 'jquery', 'wp-mail-smtp-chart' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-email-reports',
			'wp_mail_smtp_email_reports',
			[
				'nonce'                    => wp_create_nonce( 'wp-mail-smtp-email-reports-nonce' ),
				'stats_by_date_chart_data' => $this->report->get_stats_by_date_chart_data(),
				'stats_totals'             => $this->report->get_stats_totals(),
				'date_range'               => $this->report->get_date_range(),
				'is_search'                => ! empty( $this->report->get_params( 'search' ) ),
				'texts'                    => [
					'all_emails'         => esc_html__( 'All Emails', 'wp-mail-smtp-pro' ),
					'search_results'     => esc_html__( 'Search Results', 'wp-mail-smtp-pro' ),
					'confirmed_emails'   => esc_html__( 'Confirmed sent emails', 'wp-mail-smtp-pro' ),
					'sent_emails'        => esc_html__( 'Sent emails', 'wp-mail-smtp-pro' ),
					'unconfirmed_emails' => esc_html__( 'Unconfirmed sent emails', 'wp-mail-smtp-pro' ),
					'failed_emails'      => esc_html__( 'Failed emails', 'wp-mail-smtp-pro' ),
					'opened_emails'      => esc_html__( 'Opened emails', 'wp-mail-smtp-pro' ),
					'clicked_links'      => esc_html__( 'Clicked links', 'wp-mail-smtp-pro' ),
				],
				'no_send_confirmations'    => Helpers::mailer_without_send_confirmation(),
				'open_email_tracking'      => wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking(),
				'click_link_tracking'      => wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking(),
			]
		);
	}

	/**
	 * Enqueue main CSS.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_main_styles() {

		wp_enqueue_style(
			'wp-mail-smtp-email-reports',
			wp_mail_smtp()->pro->assets_url . '/css/smtp-pro-email-reports.min.css',
			[],
			WPMS_PLUGIN_VER
		);
	}

	/**
	 * Register the screen options.
	 *
	 * @since 3.0.0
	 */
	public function screen_options() {

		add_screen_option(
			'per_page',
			[
				'label'   => esc_html__( 'Number of items per page:', 'wp-mail-smtp-pro' ),
				'option'  => 'wp_mail_smtp_report_items_per_page',
				'default' => 20,
			]
		);
	}

	/**
	 * Set the screen options.
	 *
	 * @since 3.0.0
	 *
	 * @param bool   $keep   Whether to save or skip saving the screen option value.
	 * @param string $option The option name.
	 * @param int    $value  The number of items to use.
	 *
	 * @return bool|int
	 */
	public function set_screen_options( $keep, $option, $value ) {

		if ( $option === 'wp_mail_smtp_report_items_per_page' ) {
			return (int) $value;
		}

		return $keep;
	}

	/**
	 * Output HTML of the reports page.
	 *
	 * @since 3.0.0
	 */
	public function display() {

		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_enabled() ) {
			$this->display_reports_disabled();
		} elseif ( ! wp_mail_smtp()->get_pro()->get_logs()->is_valid_db() ) {
			$this->display_reports_not_installed();
		} else {
			$this->display_reports();
		}
	}

	/**
	 * Output HTML of the email reports.
	 *
	 * @since 3.0.0
	 */
	private function display_reports() {

		?>
		<form action="<?php echo esc_url( $this->get_link() ); ?>" method="get">
			<input type="hidden" name="page" value="<?php echo esc_attr( Area::SLUG . '-' . $this->get_parent_page()->get_slug() ); ?>"/>

			<div class="wp-mail-smtp-email-reports">
				<div class="wp-mail-smtp-email-reports__header">
					<h2 class="wp-mail-smtp-email-reports__title">
						<?php if ( ! empty( $this->report->get_params( 'search' ) ) ) : ?>
							<?php esc_html_e( 'Search Results', 'wp-mail-smtp-pro' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'All Emails', 'wp-mail-smtp-pro' ); ?>
						<?php endif; ?>
					</h2>

					<div class="wp-mail-smtp-email-reports__stats">
						<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--total">
							<i class="dashicons dashicons-email"></i> <span></span>
							<?php esc_html_e( 'total', 'wp-mail-smtp-pro' ); ?>
						</div>
						<?php if ( Helpers::mailer_without_send_confirmation() ) : ?>
							<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--sent">
								<i></i> <span></span> <?php esc_html_e( 'sent', 'wp-mail-smtp-pro' ); ?>
							</div>
						<?php else : ?>
							<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--confirmed">
								<i></i> <span></span> <?php esc_html_e( 'confirmed', 'wp-mail-smtp-pro' ); ?>
							</div>
							<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--unconfirmed">
								<i></i> <span></span> <?php esc_html_e( 'unconfirmed', 'wp-mail-smtp-pro' ); ?>
							</div>
						<?php endif; ?>
						<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--unsent">
							<i></i> <span></span> <?php esc_html_e( 'failed', 'wp-mail-smtp-pro' ); ?>
						</div>
						<?php if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) : ?>
							<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--open-count">
								<i></i> <span></span> <?php esc_html_e( 'open count', 'wp-mail-smtp-pro' ); ?>
							</div>
						<?php endif; ?>
						<?php if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) : ?>
							<div class="wp-mail-smtp-email-reports__stats-item wp-mail-smtp-email-reports__stats-item--click-count">
								<i></i> <span></span> <?php esc_html_e( 'click count', 'wp-mail-smtp-pro' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="wp-mail-smtp-email-reports__chart-holder">
					<canvas class="wp-mail-smtp-email-reports__chart" id="wp-mail-smtp-email-reports-chart"></canvas>
				</div>
			</div>

			<?php $this->table->prepare_items(); ?>

			<?php if ( ! empty( $this->report->get_params( 'search' ) ) ) : ?>
				<div id="wp-mail-smtp-reset-filter">
					<?php
					echo wp_kses(
						sprintf( /* translators: %1$d - items count; %2$s - search term. */
							__( 'Found <strong>%1$d items</strong> where the subject contains <i>%2$s</i>', 'wp-mail-smtp-pro' ),
							count( $this->table->items ),
							$this->report->get_params( 'search' )
						),
						[
							'strong' => [],
							'i'      => [],
						]
					);
					?>
					<a href="<?php echo esc_url( remove_query_arg( 's' ) ); ?>">
						<i class="reset dashicons dashicons-dismiss"></i>
					</a>
				</div>
			<?php endif; ?>

			<?php $this->table->display(); ?>
		</form>
		<?php
	}

	/**
	 * Notify user that email reports is disabled.
	 *
	 * @since 3.0.0
	 */
	private function display_reports_disabled() {

		?>
		<div class="wp-mail-smtp-reports-note">
			<h2><?php esc_html_e( 'Email Log Required', 'wp-mail-smtp-pro' ); ?></h2>
			<p>
				<?php
				esc_html_e( 'Email Reports provide valuable insights by aggregating data from the Email Log, allowing you to measure the success of your emails.', 'wp-mail-smtp-pro' );
				?>
			</p>
			<a href="<?php echo esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '&tab=logs' ) ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-orange wp-mail-smtp-btn-md">
				<?php esc_html_e( 'Enable Email Log', 'wp-mail-smtp-pro' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Notify user that email reports is not installed correctly.
	 *
	 * @since 3.0.0
	 */
	private function display_reports_not_installed() {

		$error_message = get_option( Migration::ERROR_OPTION_NAME );
		?>

		<div class="wp-mail-smtp-reports-note errored">
			<h2><?php esc_html_e( 'Email Reports are Not Installed Correctly', 'wp-mail-smtp-pro' ); ?></h2>
			<p>
				<?php
				if ( ! empty( $error_message ) ) {
					esc_html_e( 'The database table was not installed correctly. Please contact plugin support to diagnose and fix the issue. Provide them the error message below:', 'wp-mail-smtp-pro' );
					echo '<br><br>';
					echo '<code>' . esc_html( $error_message ) . '</code>';
				} else {
					esc_html_e( 'For some reason the database table was not installed correctly. Please contact plugin support team to diagnose and fix the issue.', 'wp-mail-smtp-pro' );
				}
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Get single stats by subject ajax handler.
	 *
	 * @since 3.0.0
	 */
	public function get_single_stats_ajax() {

		if ( ! check_ajax_referer( 'wp-mail-smtp-email-reports-nonce', '_wpnonce', false ) ) {
			wp_send_json( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ), 403 );
		}

		if ( ! current_user_can( wp_mail_smtp()->get_admin()->get_logs_access_capability() ) ) {
			wp_send_json( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ), 403 );
		}

		$report = new Report( $this->parse_report_params() );

		wp_send_json(
			[
				'totals'             => $report->get_stats_totals(),
				'by_date_chart_data' => $report->get_stats_by_date_chart_data(),
			]
		);
	}
}
