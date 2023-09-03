<?php

namespace WPMailSMTP\Pro\Admin;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\WP;

/**
 * Dashboard Widget shows the email log chart and stats in WP Dashboard.
 *
 * @since 2.7.0
 */
class DashboardWidget {

	/**
	 * Instance slug.
	 *
	 * @since 2.7.0
	 *
	 * @const string
	 */
	const SLUG = 'dash_widget';

	/**
	 * Widget settings.
	 *
	 * @since 2.7.0
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Runtime values.
	 *
	 * @since 2.7.0
	 *
	 * @var array
	 */
	public $runtime_data;

	/**
	 * Constructor.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {

		// Prevent the class initialization, if the dashboard widget hidden setting is enabled.
		if ( Options::init()->get( 'general', 'dashboard_widget_hidden' ) ) {
			return;
		}

		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Init class.
	 *
	 * @since 2.7.0
	 */
	public function init() {

		// This widget should be displayed for certain high-level users only.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * Filters whether the initialization of the dashboard widget should be allowed.
		 *
		 * @since 2.7.0
		 *
		 * @param bool $var If the dashboard widget should be initialized.
		 */
		if ( ! apply_filters( 'wp_mail_smtp_admin_dashboard_widget', '__return_true' ) ) {
			return;
		}

		$this->settings();
		$this->hooks();
	}

	/**
	 * Filterable widget settings.
	 *
	 * @since 2.7.0
	 */
	public function settings() {

		$this->settings = [

			/**
			 * Filters whether the dashboard widget data should be cached.
			 *
			 * Allow results caching to reduce DB load.
			 *
			 * @since 2.7.0
			 *
			 * @param bool $var If the dashboard widget data caching is enabled.
			 */
			'allow_data_caching' => apply_filters( 'wp_mail_smtp_' . static::SLUG . '_allow_data_caching', true ),

			/**
			 * What the end date for the dashboard widget data should be.
			 *
			 * @since 2.7.0
			 *
			 * @param string $var PHP DateTime supported string (http://php.net/manual/en/datetime.formats.php).
			 */
			'date_end_str'       => apply_filters( 'wp_mail_smtp_' . static::SLUG . '_date_end_str', 'yesterday' ),

			/**
			 * Number of seconds for the dashboard widget data to be cached (transient).
			 *
			 * @since 2.7.0
			 *
			 * @param int $var Transient lifetime in seconds. Defaults to the end of a current day.
			 */
			'transient_lifetime' => apply_filters(
				'wp_mail_smtp_' . static::SLUG . '_transient_lifetime',
				strtotime( 'tomorrow' ) - time()
			),
		];
	}

	/**
	 * Widget hooks.
	 *
	 * @since 2.7.0
	 */
	public function hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'wp_dashboard_setup', [ $this, 'widget_register' ] );

		add_action( 'wp_ajax_wp_mail_smtp_' . static::SLUG . '_get_chart_data', [ $this, 'get_chart_data_ajax' ] );
		add_action( 'wp_ajax_wp_mail_smtp_' . static::SLUG . '_get_email_stats', [ $this, 'get_email_stats_ajax' ] );
		add_action( 'wp_ajax_wp_mail_smtp_' . static::SLUG . '_save_widget_meta', [ $this, 'save_widget_meta_ajax' ] );
	}

	/**
	 * Load widget-specific scripts.
	 * Load them only on the admin dashboard page.
	 *
	 * @since 2.7.0
	 */
	public function widget_scripts() {

		$screen = get_current_screen();

		if ( ! isset( $screen->id ) || 'dashboard' !== $screen->id ) {
			return;
		}

		$min = WP::asset_min();

		wp_enqueue_style(
			'wp-mail-smtp-dashboard-widget',
			wp_mail_smtp()->assets_url . '/css/dashboard-widget.min.css',
			[],
			WPMS_PLUGIN_VER
		);

		wp_enqueue_script(
			'wp-mail-smtp-moment',
			wp_mail_smtp()->assets_url . '/js/vendor/moment.min.js',
			[],
			'2.29.4',
			true
		);

		wp_enqueue_script(
			'wp-mail-smtp-chart',
			wp_mail_smtp()->assets_url . '/js/vendor/chart.min.js',
			[ 'wp-mail-smtp-moment' ],
			'2.9.4.1',
			true
		);

		wp_enqueue_script(
			'wp-mail-smtp-dashboard-widget',
			wp_mail_smtp()->pro->assets_url . "/js/smtp-pro-dashboard-widget{$min}.js",
			[ 'jquery', 'wp-mail-smtp-chart' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-dashboard-widget',
			'wp_mail_smtp_dashboard_widget',
			[
				'nonce'                 => wp_create_nonce( 'wp_mail_smtp_' . static::SLUG . '_nonce' ),
				'slug'                  => static::SLUG,
				'empty_chart_html'      => $this->get_empty_chart_html(),
				'chart_data'            => $this->get_email_stats_count_by(
					'date',
					$this->widget_meta( 'get', 'timespan' )
				),
				'texts'                 => [
					'confirmed_emails'   => esc_html__( 'Confirmed sent emails', 'wp-mail-smtp-pro' ),
					'sent_emails'        => esc_html__( 'Sent emails', 'wp-mail-smtp-pro' ),
					'unconfirmed_emails' => esc_html__( 'Unconfirmed sent emails', 'wp-mail-smtp-pro' ),
					'failed_emails'      => esc_html__( 'Failed emails', 'wp-mail-smtp-pro' ),
				],
				'no_send_confirmations' => Helpers::mailer_without_send_confirmation(),
			]
		);
	}

	/**
	 * Register the widget.
	 *
	 * @since 2.7.0
	 */
	public function widget_register() {

		global $wp_meta_boxes;

		$widget_key = 'wp_mail_smtp_reports_widget_pro';

		wp_add_dashboard_widget(
			$widget_key,
			esc_html__( 'WP Mail SMTP', 'wp-mail-smtp-pro' ),
			[ $this, 'widget_content' ]
		);

		// Attempt to place the widget at the top.
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_instance  = [ $widget_key => $normal_dashboard[ $widget_key ] ];
		unset( $normal_dashboard[ $widget_key ] );
		$sorted_dashboard = array_merge( $widget_instance, $normal_dashboard );

		//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Load widget content.
	 *
	 * @since 2.7.0
	 */
	public function widget_content() {

		echo '<div class="wp-mail-smtp-dash-widget wp-mail-smtp-dash-widget--pro">';

		if ( wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
			$this->widget_content_html();
			$this->display_after_widget_content_html();
		} else {
			$this->widget_content_logs_disabled();
		}

		echo '</div><!-- .wp-mail-smtp-dash-widget -->';
	}

	/**
	 * Display the content after the email stats block.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	private function display_after_widget_content_html() {

		if (
			! ( new Alerts() )->is_enabled() &&
			empty( $this->widget_meta( 'get', 'hide_email_alerts_banner' ) )
		) {
			$email_stats = $this->get_email_stats_count_by( 'total', $this->widget_meta( 'get', 'timespan' ) );

			if ( ! empty( $email_stats['unsent'] ) ) {
				$this->show_email_alerts_banner( $email_stats['unsent'] );

				return;
			}
		}

		$plugins          = get_plugins();
		$hide_recommended = $this->widget_meta( 'get', 'hide_recommended_block' );

		if (
			! array_key_exists( 'wpforms-lite/wpforms.php', $plugins ) &&
			! array_key_exists( 'wpforms/wpforms.php', $plugins ) &&
			empty( $hide_recommended )
		) {
			$this->recommended_plugin_block_html();
		}
	}

	/**
	 * Display the email alerts banner.
	 *
	 * @since 3.9.0
	 *
	 * @param int $error_count The number of debug events error.
	 *
	 * @return void
	 */
	private function show_email_alerts_banner( $error_count ) {

		?>
		<div id="wp-mail-smtp-dash-widget-email-alerts-education" class="wp-mail-smtp-dash-widget-block wp-mail-smtp-dash-widget-email-alerts-education">
			<div class="wp-mail-smtp-dash-widget-email-alerts-education-error-icon">
				<?php
				printf(
					'<img src="%s" alt="%s"/>',
					esc_url( wp_mail_smtp()->assets_url . '/images/dash-widget/error-icon.svg' ),
					esc_attr__( 'Error icon', 'wp-mail-smtp-pro' )
				);
				?>
			</div>
			<div class="wp-mail-smtp-dash-widget-email-alerts-education-content">
				<?php
				if ( $error_count === 1 ) {
					$error_title = sprintf(
						/* translators: %d - Timespan. */
						__( 'We detected a failed email in the last %d days.', 'wp-mail-smtp-pro' ),
						$this->widget_meta( 'get', 'timespan' )
					);
				} else {
					$error_title = sprintf(
						/* translators: 1: Number of failed emails, 2: Timespan. */
						__( 'We detected %1$d failed emails in the last %2$d days.', 'wp-mail-smtp-pro' ),
						$error_count,
						$this->widget_meta( 'get', 'timespan' )
					);
				}

				$content = sprintf(
					/* translators: %s - URL to WP Mail SMTP -> Settings -> Alerts Admin page.. */
					__( '<a href="%s">Enable Email Alerts</a> and get instant notifications when they fail.', 'wp-mail-smtp-pro' ),
					esc_url( add_query_arg( 'tab', 'alerts', wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG ) ) )
				);
				?>
				<p>
					<strong><?php echo esc_html( $error_title ); ?></strong><br />
					<?php
					echo wp_kses(
						$content,
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					);
					?>
				</p>
			</div>

			<button type="button" id="wp-mail-smtp-dash-widget-dismiss-email-alert-block" class="wp-mail-smtp-dash-widget-dismiss-email-alert-block" title="<?php esc_attr_e( 'Dismiss email alert block', 'wp-mail-smtp-pro' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Widget content HTML if the Email Logs are not enabled.
	 *
	 * @since 2.7.0
	 */
	public function widget_content_logs_disabled() {

		$enable_logs_url = wp_mail_smtp()->pro->get_logs()->get_settings_url();

		// phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		$learn_more_url = wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/docs/how-to-set-up-email-logging/', [ 'medium' => 'link', 'content' => 'dashboardwidget' ] );

		?>
		<div class="wp-mail-smtp-dash-widget-block wp-mail-smtp-dash-widget-block-logs-disabled">
			<img class="wp-mail-smtp-dash-widget-block-logo" src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/pattie.svg' ); ?>" alt="<?php esc_attr_e( 'Pattie the WP Mail SMTP mascot', 'wp-mail-smtp-pro' ); ?>">
			<h2><?php esc_html_e( 'Enable Email Logs to View Stats', 'wp-mail-smtp-pro' ); ?></h2>
			<p><?php esc_html_e( 'Automatically keep track of every email sent from your WordPress site and view valuable statistics right here in your dashboard.', 'wp-mail-smtp-pro' ); ?></p>
			<a href="<?php echo esc_url( $enable_logs_url ); ?>" class="button button-primary">
				<?php esc_html_e( 'Enable Email Logging', 'wp-mail-smtp-pro' ); ?>
			</a>
			<a href="<?php echo esc_url( $learn_more_url ); ?>" class="button" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Learn More', 'wp-mail-smtp-pro' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Widget content HTML.
	 *
	 * @since 2.7.0
	 */
	public function widget_content_html() {

		$timespan = $this->widget_meta( 'get', 'timespan' );
		?>

		<div class="wp-mail-smtp-dash-widget-chart-block-container">
			<div class="wp-mail-smtp-dash-widget-block wp-mail-smtp-dash-widget-chart-block">
				<canvas id="wp-mail-smtp-dash-widget-chart" width="554" height="291"></canvas>
				<div class="wp-mail-smtp-dash-widget-overlay"></div>
			</div>
		</div>

		<div class="wp-mail-smtp-dash-widget-block wp-mail-smtp-dash-widget-block-settings">
			<div>
				<?php $this->email_types_select_html(); ?>
				<a href="<?php echo esc_url( wp_mail_smtp()->pro->get_logs()->get_admin_page_url() ); ?>">
					<?php esc_html_e( 'View Full Log', 'wp-mail-smtp-pro' ); ?>
				</a>
			</div>
			<div>
				<?php
					$this->timespan_select_html();
					$this->widget_settings_html();
				?>
			</div>
		</div>

		<div id="wp-mail-smtp-dash-widget-email-stats-block" class="wp-mail-smtp-dash-widget-block wp-mail-smtp-dash-widget-email-stats-block">
			<?php $this->email_stats_block( $timespan ); ?>
		</div>
		<?php
	}

	/**
	 * Timespan select HTML.
	 *
	 * @since 2.7.0
	 */
	public function timespan_select_html() {

		$timespan = $this->widget_meta( 'get', 'timespan' );
		?>
		<select id="wp-mail-smtp-dash-widget-timespan" class="wp-mail-smtp-dash-widget-select-timespan" title="<?php esc_attr_e( 'Select timespan', 'wp-mail-smtp-pro' ); ?>">
			<?php foreach ( $this->get_timespan_options() as $option ) : ?>
				<option value="<?php echo absint( $option ); ?>" <?php selected( $timespan, absint( $option ) ); ?>>
					<?php /* translators: %d - Number of days. */ ?>
					<?php echo esc_html( sprintf( _n( 'Last %d day', 'Last %d days', absint( $option ), 'wp-mail-smtp-pro' ), absint( $option ) ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php
	}

	/**
	 * Email types select HTML.
	 *
	 * @since 2.7.0
	 */
	public function email_types_select_html() {

		$email_type = $this->widget_meta( 'get', 'email_type' );
		$options    = [
			'all'       => esc_html__( 'All Emails', 'wp-mail-smtp-pro' ),
			'delivered' => esc_html__( 'Confirmed Emails', 'wp-mail-smtp-pro' ),
			'sent'      => esc_html__( 'Unconfirmed Emails', 'wp-mail-smtp-pro' ),
			'unsent'    => esc_html__( 'Failed Emails', 'wp-mail-smtp-pro' ),
		];

		if ( Helpers::mailer_without_send_confirmation() ) {
			unset( $options['sent'] );
			$options['delivered'] = esc_html__( 'Sent Emails', 'wp-mail-smtp-pro' );
		}

		?>
		<select id="wp-mail-smtp-dash-widget-email-type" class="wp-mail-smtp-dash-widget-select-email-type" title="<?php esc_attr_e( 'Select email type', 'wp-mail-smtp-pro' ); ?>">
			<?php foreach ( $options as $key => $title ) : ?>
				<option value="<?php echo sanitize_key( $key ); ?>" <?php selected( $email_type, sanitize_key( $key ) ); ?>>
					<?php echo esc_html( $title ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php
	}

	/**
	 * Widget settings HTML.
	 *
	 * @since 2.7.0
	 */
	public function widget_settings_html() {

		$graph_style  = $this->widget_meta( 'get', 'graph_style' );
		$color_scheme = $this->widget_meta( 'get', 'color_scheme' );
		?>
		<div class="wp-mail-smtp-dash-widget-settings-container">
			<button id="wp-mail-smtp-dash-widget-settings-button" class="wp-mail-smtp-dash-widget-settings-button button" type="button">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 19">
					<path d="M18,11l-2.18,0c-0.17,0.7 -0.44,1.35 -0.81,1.93l1.54,1.54l-2.1,2.1l-1.54,-1.54c-0.58,0.36 -1.23,0.63 -1.91,0.79l0,2.18l-3,0l0,-2.18c-0.68,-0.16 -1.33,-0.43 -1.91,-0.79l-1.54,1.54l-2.12,-2.12l1.54,-1.54c-0.36,-0.58 -0.63,-1.23 -0.79,-1.91l-2.18,0l0,-2.97l2.17,0c0.16,-0.7 0.44,-1.35 0.8,-1.94l-1.54,-1.54l2.1,-2.1l1.54,1.54c0.58,-0.37 1.24,-0.64 1.93,-0.81l0,-2.18l3,0l0,2.18c0.68,0.16 1.33,0.43 1.91,0.79l1.54,-1.54l2.12,2.12l-1.54,1.54c0.36,0.59 0.64,1.24 0.8,1.94l2.17,0l0,2.97Zm-8.5,1.5c1.66,0 3,-1.34 3,-3c0,-1.66 -1.34,-3 -3,-3c-1.66,0 -3,1.34 -3,3c0,1.66 1.34,3 3,3Z"></path>
				</svg>
			</button>
			<div class="wp-mail-smtp-dash-widget-settings-menu">
				<div class="wp-mail-smtp-dash-widget-settings-menu--style">
					<h4><?php esc_html_e( 'Graph Style', 'wp-mail-smtp-pro' ); ?></h4>
					<div>
						<div class="wp-mail-smtp-dash-widget-settings-menu-item">
							<input type="radio" id="wp-mail-smtp-dash-widget-settings-style-bar" name="style" value="bar" <?php checked( 'bar', $graph_style ); ?>>
							<label for="wp-mail-smtp-dash-widget-settings-style-bar"><?php esc_html_e( 'Bar', 'wp-mail-smtp-pro' ); ?></label>
						</div>
						<div class="wp-mail-smtp-dash-widget-settings-menu-item">
							<input type="radio" id="wp-mail-smtp-dash-widget-settings-style-line" name="style" value="line" <?php checked( 'line', $graph_style ); ?>>
							<label for="wp-mail-smtp-dash-widget-settings-style-line"><?php esc_html_e( 'Line', 'wp-mail-smtp-pro' ); ?></label>
						</div>
					</div>
				</div>
				<div class="wp-mail-smtp-dash-widget-settings-menu--color">
					<h4><?php esc_html_e( 'Color Scheme', 'wp-mail-smtp-pro' ); ?></h4>
					<div>
						<div class="wp-mail-smtp-dash-widget-settings-menu-item">
							<input type="radio" id="wp-mail-smtp-dash-widget-settings-color-smtp" name="color" value="smtp" <?php checked( 'smtp', $color_scheme ); ?>>
							<label for="wp-mail-smtp-dash-widget-settings-color-smtp"><?php esc_html_e( 'WP Mail SMTP', 'wp-mail-smtp-pro' ); ?></label>
						</div>
						<div class="wp-mail-smtp-dash-widget-settings-menu-item">
							<input type="radio" id="wp-mail-smtp-dash-widget-settings-color-wp" name="color" value="wp" <?php checked( 'wp', $color_scheme ); ?>>
							<label for="wp-mail-smtp-dash-widget-settings-color-wp"><?php esc_html_e( 'WordPress', 'wp-mail-smtp-pro' ); ?></label>
						</div>
					</div>
				</div>
				<button type="button" class="button wp-mail-smtp-dash-widget-settings-menu-save"><?php esc_html_e( 'Save Changes', 'wp-mail-smtp-pro' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * Email statistics block.
	 *
	 * @since 2.7.0
	 *
	 * @param int    $days         Timespan (in days) to fetch the data for.
	 * @param string $color_scheme The color scheme to fetch the data for.
	 */
	public function email_stats_block( $days, $color_scheme = false ) {

		$email_stats = $this->get_email_stats_count_by( 'total', $days );

		if ( empty( $email_stats ) ) {
			$this->email_stats_block_empty_html();
		} else {
			$this->email_stats_block_html( $email_stats, $color_scheme );
		}
	}

	/**
	 * Empty email statistics block HTML.
	 *
	 * @since 2.7.0
	 */
	public function email_stats_block_empty_html() {

		?>
		<p class="wp-mail-smtp-error wp-mail-smtp-error-no-data-email-stats">
			<?php esc_html_e( 'No email logs for selected period.', 'wp-mail-smtp-pro' ); ?>
		</p>
		<?php
	}

	/**
	 * Email statistics block HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param array  $email_stats  Stats for total, confirmed, unconfirmed and failed emails.
	 * @param string $color_scheme The color scheme to use for the output.
	 */
	public function email_stats_block_html( $email_stats, $color_scheme = false ) {

		$output_data = $this->get_email_stats_data( $email_stats, $color_scheme );
		?>

		<table id="wp-mail-smtp-dash-widget-email-stats-table" cellspacing="0">
			<tr>
				<?php
				$count   = 0;
				$per_row = 2;

				foreach ( array_values( $output_data ) as $stats ) :
					if ( ! is_array( $stats ) ) {
						continue;
					}

					if ( ! isset( $stats['icon'], $stats['title'] ) ) {
						continue;
					}

					// Make some exceptions for mailers without send confirmation functionality.
					if ( Helpers::mailer_without_send_confirmation() ) {
						$per_row = 3;
					}

					// Create new row after every $per_row cells.
					if ( $count !== 0 && $count % $per_row === 0 ) {
						echo '</tr><tr>';
					}

					$count++;
					?>
					<td class="wp-mail-smtp-dash-widget-email-stats-table-cell wp-mail-smtp-dash-widget-email-stats-table-cell--<?php echo esc_attr( $stats['type'] ); ?> wp-mail-smtp-dash-widget-email-stats-table-cell--<?php echo esc_attr( $per_row ); ?>">
						<div class="wp-mail-smtp-dash-widget-email-stats-table-cell-container">
							<img src="<?php echo esc_url( $stats['icon'] ); ?>" alt="<?php esc_attr_e( 'Table cell icon', 'wp-mail-smtp-pro' ); ?>">
							<span>
								<?php echo esc_html( $stats['title'] ); ?>
							</span>
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
		</table>

		<?php
	}

	/**
	 * Recommended plugin block HTML.
	 *
	 * @since 2.7.0
	 */
	public function recommended_plugin_block_html() {

		$install_wpforms_url = wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=wpforms-lite' ),
			'install-plugin_wpforms-lite'
		);
		?>

		<div class="wp-mail-smtp-dash-widget-recommended-plugin-block">
			<span class="wp-mail-smtp-dash-widget-recommended-plugin">
				<span class="recommended"><?php esc_html_e( 'Recommended Plugin:', 'wp-mail-smtp-pro' ); ?></span>
				<span>
					<b><?php esc_html_e( 'WPForms', 'wp-mail-smtp-pro' ); ?></b> <span class="sep">-</span>
					<?php if ( current_user_can( 'install_plugins' ) ) : ?>
						<a href="<?php echo esc_url( $install_wpforms_url ); ?>"><?php esc_html_e( 'Install', 'wp-mail-smtp-pro' ); ?></a> <span class="sep sep-vertical">&vert;</span>
					<?php endif; ?>
					<a href="https://wpforms.com/?utm_source=wpmailsmtpplugin&utm_medium=link&utm_campaign=wpmailsmtpdashboardwidget" target="_blank"><?php esc_html_e( 'Learn More', 'wp-mail-smtp-pro' ); ?></a>
				</span>
			</span>
			<button type="button" id="wp-mail-smtp-dash-widget-dismiss-recommended-plugin-block" class="wp-mail-smtp-dash-widget-dismiss-recommended-plugin-block" title="<?php esc_attr_e( 'Dismiss recommended plugin block', 'wp-mail-smtp-pro' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Get empty chart HTML.
	 *
	 * @since 2.7.0
	 */
	public function get_empty_chart_html() {

		ob_start();
		?>

		<div class="wp-mail-smtp-error wp-mail-smtp-error-no-data-chart">
			<div class="wp-mail-smtp-dash-widget-modal">
				<h2><?php esc_html_e( 'No email logs yet', 'wp-mail-smtp-pro' ); ?></h2>
				<p><?php esc_html_e( 'Please check back tomorrow.', 'wp-mail-smtp-pro' ); ?></p>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get timespan options (in days).
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function get_timespan_options() {

		$default = [ 7, 14, 30 ];

		/**
		 * Define the timespan options, in days, to be available for the dashboard widget.
		 *
		 * @since 2.7.0
		 *
		 * @param array $default Array of integers -> days.
		 */
		$options = apply_filters( 'wp_mail_smtp_' . static::SLUG . '_timespan_options', $default );

		if ( ! is_array( $options ) ) {
			return $default;
		}

		$options = array_filter( $options, 'is_numeric' );

		return empty( $options ) ? $default : $options;
	}

	/**
	 * Get the default timespan option.
	 *
	 * @since 2.7.0
	 *
	 * @return int|null
	 */
	public function get_timespan_default() {

		$options = $this->get_timespan_options();
		$default = reset( $options );

		if ( ! is_numeric( $default ) ) {
			return null;
		}

		return $default;
	}

	/**
	 * Get/set a widget meta.
	 *
	 * @since 2.7.0
	 *
	 * @param string $action Possible value: 'get' or 'set'.
	 * @param string $meta   Meta name.
	 * @param int    $value  Value to set.
	 *
	 * @return mixed
	 */
	public function widget_meta( $action, $meta, $value = 0 ) {

		$allowed_actions = array( 'get', 'set' );

		if ( ! in_array( $action, $allowed_actions, true ) ) {
			return false;
		}

		if ( 'get' === $action ) {
			return $this->get_widget_meta( $meta );
		}

		$meta_key = $this->get_widget_meta_key( $meta );
		$value    = sanitize_key( $value );

		if ( 'set' === $action && ! empty( $value ) ) {
			return update_user_meta( get_current_user_id(), $meta_key, $value );
		}

		if ( 'set' === $action && empty( $value ) ) {
			return delete_user_meta( get_current_user_id(), $meta_key );
		}

		return false;
	}

	/**
	 * Get the widget meta value.
	 *
	 * @since 3.9.0
	 *
	 * @param string $meta Meta name.
	 *
	 * @return mixed
	 */
	private function get_widget_meta( $meta ) {

		$defaults = [
			'timespan'                 => $this->get_timespan_default(),
			'email_type'               => 'all',
			'graph_style'              => 'line',
			'color_scheme'             => 'smtp',
			'hide_recommended_block'   => 0,
			'hide_email_alerts_banner' => 0,
		];

		$meta_value = get_user_meta( get_current_user_id(), $this->get_widget_meta_key( $meta ), true );

		if ( ! empty( $meta_value ) ) {
			return $meta_value;
		}

		if ( isset( $defaults[ $meta ] ) ) {
			return $defaults[ $meta ];
		}

		return null;
	}

	/**
	 * Retrieve the meta key.
	 *
	 * @since 3.9.0
	 *
	 * @param string $meta Meta name.
	 *
	 * @return string
	 */
	private function get_widget_meta_key( $meta ) {

		return 'wp_mail_smtp_' . static::SLUG . '_' . $meta;
	}

	/**
	 * Converts number of days to day start and day end values.
	 *
	 * @since 2.7.0
	 *
	 * @param integer $days Timespan days.
	 *
	 * @return mixed
	 */
	public function get_days_interval( $days = 0 ) {

		if ( empty( $days ) ) {
			$days = $this->runtime_data['days'];
		} else {
			$this->runtime_data['days'] = $days;
		}

		if ( ! empty( $this->runtime_data['days_interval'][ $days ] ) ) {
			return $this->runtime_data['days_interval'][ $days ];
		}

		// PHP DateTime supported string (http://php.net/manual/en/datetime.formats.php).
		$date_end_str = $this->settings['date_end_str'];

		try {
			$interval['end'] = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return false;
		}

		try {
			$interval['start'] = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return false;
		}

		$interval['end'] = $interval['end']->setTime( 23, 59, 59 );

		$interval['start'] = $interval['start']
			->modify( '-' . ( absint( $days ) - 1 ) . 'days' )
			->setTime( 0, 0 );

		$this->runtime_data['days_interval'][ $days ] = $interval;

		return $interval;
	}


	/**
	 * Get email stats count grouped by $param.
	 * Main point of entry to fetch email stats count data from DB.
	 * Caches the result.
	 *
	 * @since 2.7.0
	 *
	 * @param string $param Possible value: 'date' or 'total'.
	 * @param int    $days  Timespan (in days) to fetch the data for.
	 *
	 * @return array
	 */
	public function get_email_stats_count_by( $param, $days = 0 ) {

		$allowed_params = [ 'date', 'total' ];

		if ( ! in_array( $param, $allowed_params, true ) ) {
			return [];
		}

		$cache = false;

		// Allow results caching to reduce DB load.
		$allow_caching = $this->settings['allow_data_caching'];

		if ( $allow_caching ) {
			$transient_name = 'wp_mail_smtp_' . static::SLUG . '_pro_emails_by_' . $param . '_' . $days;
			$cache          = get_transient( $transient_name );

			/**
			 * Filter the cached email count data, to clear or alter it.
			 *
			 * @since 2.7.0
			 *
			 * @param array  $cache The cached data.
			 * @param string $param The parameter to group the email counts by. Possible value: 'date' or 'total'.
			 * @param int    $days  The number of days in the past to collect the data for.
			 */
			$cache = apply_filters( 'wp_mail_smtp_' . static::SLUG . '_cached_data', $cache, $param, $days );
		}

		// is_array() detects cached empty searches.
		if ( $allow_caching && is_array( $cache ) ) {
			return $cache;
		}

		$result = $this->get_email_stats_results( $param, $days );

		if ( $allow_caching ) {

			// Transient lifetime in seconds. Defaults to the end of a current day.
			$transient_lifetime = $this->settings['transient_lifetime'];
			set_transient( $transient_name, $result, $transient_lifetime );
		}

		return $result;
	}

	/**
	 * Get the email stats results from the DB.
	 *
	 * @since 2.7.0
	 *
	 * @param string $param Possible value: 'date' or 'total'.
	 * @param int    $days  Timespan (in days) to fetch the data for.
	 *
	 * @return array
	 */
	private function get_email_stats_results( $param, $days ) {

		$result = [];
		$dates  = $this->get_days_interval( $days );

		switch ( $param ) {
			case 'date':
				$result = $this->get_email_stats_count_by_date_sql( $dates['start'], $dates['end'] );
				break;
			case 'total':
				$result = $this->get_email_stats_count_by_total_sql( $dates['start'], $dates['end'] );
				break;
		}

		return $result;
	}

	/**
	 * Get email stats count grouped by date.
	 * In most cases it's better to use `get_email_stats_count_by( 'date' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_email_stats_count_by_date_sql( $date_start = null, $date_end = null ) {

		if ( ! current_user_can( 'manage_options' ) || ! wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
			return [];
		}

		global $wpdb;

		$table_name   = Logs::get_table_name();
		$format       = 'Y-m-d H:i:s';
		$placeholders = [];

		$sql = "SELECT CAST(date_sent AS DATE) as day, status, COUNT(status) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $date_start ) ) {
			$sql           .= ' AND date_sent >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql           .= ' AND date_sent <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= ' GROUP BY day, status;';

		if ( ! empty( $placeholders ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $results ) ) {
			return [];
		}

		$results = $this->process_row_data_email_stats_count_by_date( $results );

		$results = $this->fill_chart_empty_days( $results, $date_start, $date_end );

		return (array) $results;
	}

	/**
	 * Get the total email stats count.
	 * In most cases it's better to use `get_email_stats_count_by( 'total' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_email_stats_count_by_total_sql( $date_start = null, $date_end = null ) {

		$data = [
			'unsent'    => 0,
			'sent'      => 0,
			'waiting'   => 0,
			'delivered' => 0,
		];

		if ( ! current_user_can( 'manage_options' ) || ! wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
			return $data;
		}

		global $wpdb;

		$table_name   = Logs::get_table_name();
		$format       = 'Y-m-d H:i:s';
		$placeholders = [];

		$sql = "SELECT status, COUNT(*) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $date_start ) ) {
			$sql           .= ' AND date_sent >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql           .= ' AND date_sent <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= ' GROUP BY status;';

		if ( ! empty( $placeholders ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, \ARRAY_A );

		if ( empty( $results ) ) {
			return $data;
		}

		return $this->filter_total_email_stats_results( $results, $data );
	}

	/**
	 * Fill DB results with empty entries where there's no data.
	 * Needed to correctly distribute labels and data on a chart.
	 *
	 * @since 2.7.0
	 *
	 * @param array     $results    DB results from `$wpdb->get_results()`.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function fill_chart_empty_days( $results, $date_start, $date_end ) {

		if ( ! is_array( $results ) ) {
			return [];
		}

		$period = new \DatePeriod(
			$date_start,
			new \DateInterval( 'P1D' ),
			$date_end
		);

		foreach ( $period as $key => $value ) {
			/** The current DateTime object of the period. @var \DateTime $value */
			$date = $value->format( 'Y-m-d' );
			if ( ! array_key_exists( $date, $results ) ) {
				$results[ $date ] = array(
					'day'       => $date,
					'unsent'    => 0,
					'sent'      => 0,
					'waiting'   => 0,
					'delivered' => 0,
				);
				continue;
			}

			// Mold an object to array to stay uniform.
			$results[ $date ] = (array) $results[ $date ];
		}

		ksort( $results );

		return $results;
	}

	/**
	 * Get the data for a chart using AJAX.
	 *
	 * @since 2.7.0
	 */
	public function get_chart_data_ajax() {

		check_admin_referer( 'wp_mail_smtp_' . static::SLUG . '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$days = ! empty( $_POST['days'] ) ? absint( $_POST['days'] ) : 0;

		$data = $this->get_email_stats_count_by( 'date', $days );

		wp_send_json( $data );
	}

	/**
	 * Get the data for a forms list using AJAX.
	 *
	 * @since 2.7.0
	 */
	public function get_email_stats_ajax() {

		check_admin_referer( 'wp_mail_smtp_' . static::SLUG . '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$days         = ! empty( $_POST['days'] ) ? absint( $_POST['days'] ) : 0;
		$color_scheme = ! empty( $_POST['color'] ) ? sanitize_key( $_POST['color'] ) : false;

		ob_start();
		$this->email_stats_block( $days, $color_scheme );
		wp_send_json( ob_get_clean() );
	}

	/**
	 * Save a widget meta for a current user using AJAX.
	 *
	 * @since 2.7.0
	 */
	public function save_widget_meta_ajax() {

		check_admin_referer( 'wp_mail_smtp_' . static::SLUG . '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$meta  = ! empty( $_POST['meta'] ) ? sanitize_key( $_POST['meta'] ) : '';
		$value = ! empty( $_POST['value'] ) ? sanitize_key( $_POST['value'] ) : 0;

		$this->widget_meta( 'set', $meta, $value );

		exit();
	}

	/**
	 * Prepare the email stats data.
	 * The text and counts of the email stats.
	 *
	 * @since 2.7.0
	 *
	 * @param array  $email_stats  Stats for total, confirmed, unconfirmed and failed emails.
	 * @param string $color_scheme The color scheme to use for the output.
	 *
	 * @return array[]
	 */
	private function get_email_stats_data( $email_stats, $color_scheme = false ) {

		$confirmed_sent = isset( $email_stats['delivered'] ) ? absint( $email_stats['delivered'] ) : 0;
		$sent           = isset( $email_stats['sent'] ) ? absint( $email_stats['sent'] ) : 0;
		$failed_sent    = isset( $email_stats['unsent'] ) ? absint( $email_stats['unsent'] ) : 0;
		$total_sent     = $confirmed_sent + $sent + $failed_sent;

		if ( empty( $color_scheme ) ) {
			$color_scheme = $this->widget_meta( 'get', 'color_scheme' );
		}

		$output_data = [
			'all'       => [
				'type'  => 'all',
				'icon'  => wp_mail_smtp()->assets_url . '/images/dash-widget/' . $color_scheme . '/total.svg',
				/* translators: %d number of total emails sent. */
				'title' => esc_html( sprintf( esc_html__( '%d total', 'wp-mail-smtp-pro' ), $total_sent ) ),
				'count' => $total_sent,
			],
			'delivered' => [
				'type'  => 'delivered',
				'icon'  => wp_mail_smtp()->assets_url . '/images/dash-widget/' . $color_scheme . '/delivered.svg',
				/* translators: %d number of confirmed emails sent. */
				'title' => esc_html( sprintf( esc_html__( '%d confirmed', 'wp-mail-smtp-pro' ), $confirmed_sent ) ),
				'count' => $confirmed_sent,
			],
			'sent'      => [
				'type'  => 'sent',
				'icon'  => wp_mail_smtp()->assets_url . '/images/dash-widget/' . $color_scheme . '/sent.svg',
				/* translators: %d number of unconfirmed emails sent. */
				'title' => esc_html( sprintf( esc_html__( '%d unconfirmed', 'wp-mail-smtp-pro' ), $sent ) ),
				'count' => $sent,
			],
			'unsent'    => [
				'type'  => 'unsent',
				'icon'  => wp_mail_smtp()->assets_url . '/images/dash-widget/' . $color_scheme . '/unsent.svg',
				/* translators: %d number of failed email sent attempts. */
				'title' => esc_html( sprintf( esc_html__( '%d failed', 'wp-mail-smtp-pro' ), $failed_sent ) ),
				'count' => $failed_sent,
			],
		];

		if ( Helpers::mailer_without_send_confirmation() ) {

			// Skip the 'unconfirmed sent' section.
			unset( $output_data['sent'] );

			// Change the 'confirmed sent' section into a general 'sent' section.
			$output_data['delivered']['title'] = esc_html( /* translators: %d number of sent emails. */
				sprintf( esc_html__( '%d sent', 'wp-mail-smtp-pro' ), $sent + $confirmed_sent )
			);
			$output_data['delivered']['count'] = $sent + $confirmed_sent;
		}

		return $output_data;
	}

	/**
	 * Process the DB row count data for email stats by date.
	 *
	 * @since 2.7.0
	 *
	 * @param array $raw_data The data from the DB by rows.
	 *
	 * @return array
	 */
	private function process_row_data_email_stats_count_by_date( $raw_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( empty( $raw_data ) ) {
			return [];
		}

		$results = [];

		foreach ( $raw_data as $row ) {
			if ( ! isset( $results[ $row['day'] ] ) ) {
				$results[ $row['day'] ] = [
					'day'       => $row['day'],
					'unsent'    => 0,
					'sent'      => 0,
					'waiting'   => 0,
					'delivered' => 0,
				];
			}

			switch ( absint( $row['status'] ) ) {
				case Email::STATUS_UNSENT:
					$results[ $row['day'] ]['unsent'] = absint( $row['count'] );
					break;
				case Email::STATUS_SENT:
					$results[ $row['day'] ]['sent'] = absint( $row['count'] );
					break;
				case Email::STATUS_WAITING:
					$results[ $row['day'] ]['waiting'] = absint( $row['count'] );
					break;
				case Email::STATUS_DELIVERED:
					$results[ $row['day'] ]['delivered'] = absint( $row['count'] );
					break;
			}
		}

		return $results;
	}

	/**
	 * Filter the DB results for email stats by total param.
	 *
	 * @since 2.7.0
	 *
	 * @param array $results The DB results for the email stats by total.
	 * @param array $data    The current collected total data.
	 *
	 * @return array
	 */
	private function filter_total_email_stats_results( $results, $data ) {

		foreach ( $results as $row ) {
			switch ( absint( $row['status'] ) ) {
				case Email::STATUS_UNSENT:
					$data['unsent'] = absint( $row['count'] );
					break;
				case Email::STATUS_SENT:
					$data['sent'] = absint( $row['count'] );
					break;
				case Email::STATUS_WAITING:
					$data['waiting'] = absint( $row['count'] );
					break;
				case Email::STATUS_DELIVERED:
					$data['delivered'] = absint( $row['count'] );
					break;
			}
		}

		return $data;
	}
}
