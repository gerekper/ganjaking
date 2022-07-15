<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Providers\Common;
use WPMailSMTP\Pro\Emails\Logs\Providers\SMTP;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Webhooks;
use WPMailSMTP\Pro\Tasks\EmailLogCleanupTask;
use WPMailSMTP\Pro\Tasks\Logs\Sendlayer\VerifySentStatusTask as SendlayerVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Mailgun\VerifySentStatusTask as MailgunVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Sendinblue\VerifySentStatusTask as SendinblueVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\SMTPcom\VerifySentStatusTask as SMTPcomVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Postmark\VerifySentStatusTask as PostmarkVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\SparkPost\VerifySentStatusTask as SparkPostVerifySentStatusTask;
use WPMailSMTP\Providers\MailerAbstract;
use WPMailSMTP\WP;
use WPMailSMTP\Pro\Emails\Logs\Admin\PrintPreview;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Events as TrackingEvents;
use WPMailSMTP\Pro\Emails\Logs\Reports\Admin as ReportsAdmin;

/**
 * Class Logs.
 *
 * @since 1.5.0
 */
class Logs {

	/**
	 * Used for SMTP, because it has several points of failures
	 * and we need to store email and check its status in different places.
	 * API-based mailers are sent and checked at the same place
	 * and don't need this state.
	 *
	 * @var int
	 */
	private $current_email_id = 0;

	/**
	 * Logs constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize the Logs functionality.
	 *
	 * @since 1.5.0
	 */
	public function init() {

		// Redefine default Lite CSS file.
		add_filter( 'wp_mail_smtp_admin_enqueue_assets_logs_css', function () {

			return wp_mail_smtp()->pro->assets_url . '/css/smtp-pro-logs.min.css';
		} );

		// Redefine default Lite JS file.
		add_filter( 'wp_mail_smtp_admin_enqueue_assets_logs_js', function () {

			return wp_mail_smtp()->pro->assets_url . '/js/smtp-pro-logs' . WP::asset_min() . '.js';
		} );

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', array( $this, 'enqueue_assets' ) );

		// Redefine the Logs page display class.
		add_filter( 'wp_mail_smtp_admin_display_get_logs_fqcn', function () {

			if ( wp_mail_smtp()->pro->get_logs()->is_archive() ) {
				return \WPMailSMTP\Pro\Emails\Logs\Admin\ArchivePage::class;
			} else {
				return \WPMailSMTP\Pro\Emails\Logs\Admin\SinglePage::class;
			}
		} );

		// Add a new Email Log tab under General.
		add_filter( 'wp_mail_smtp_admin_get_pages', function ( $pages ) {

			$misc = $pages['misc'];
			unset( $pages['misc'] );

			$pages['logs'] = new Admin\SettingsTab();
			$pages['misc'] = $misc;

			return $pages;
		}, 0 );

		// Filter admin area options save process.
		add_filter( 'wp_mail_smtp_options_set', [ $this, 'filter_options_set' ] );
		add_filter( 'wp_mail_smtp_options_get_const_value', [ $this, 'filter_options_get_const_value' ], 10, 4 );
		add_filter( 'wp_mail_smtp_options_is_const_defined', [ $this, 'filter_options_is_const_defined' ], 10, 3 );

		// Track single email Preview and Deletion.
		add_action( 'admin_init', [ $this, 'process_email_preview' ] );
		add_action( 'admin_init', [ $this, 'process_email_delete' ] );

		// Display notices.
		add_action( 'admin_init', [ $this, 'display_notices' ] );

		if ( $this->is_enabled() ) {
			// Actually log emails - SMTP.
			add_action( 'wp_mail_smtp_mailcatcher_smtp_pre_send_before', [ $this, 'process_smtp_pre_send_before' ] );
			add_action( 'wp_mail_smtp_mailcatcher_smtp_send_before', [ $this, 'process_smtp_send_before' ] );
			add_action( 'wp_mail_smtp_mailcatcher_smtp_send_after', [ $this, 'process_smtp_send_after' ], 10, 7 );
			add_action( 'wp_mail_failed', [ $this, 'process_smtp_fails' ] );

			// Actually log emails - Catch All.
			add_action( 'wp_mail_smtp_mailcatcher_pre_send_before', [ $this, 'process_pre_send_before' ] );
			add_action( 'wp_mail_smtp_mailcatcher_send_after', [ $this, 'process_log_save' ], 10, 2 );

			// Process AJAX request for deleting all logs.
			add_action( 'wp_ajax_wp_mail_smtp_delete_all_log_entries', [ $this, 'process_ajax_delete_all_log_entries' ] );

			// Initialize email print preview.
			( new PrintPreview() )->hooks();

			// Initialize emails resend.
			( new Resend() )->hooks();
		}

		// Initialize webhooks.
		$this->get_webhooks();

		// Initialize email tracking.
		( new Tracking( $this->is_enabled_tracking() ) )->hooks();

		// Initialize screen options for the logs admin archive page.
		add_action( 'load-wp-mail-smtp_page_wp-mail-smtp-logs', [ $this, 'archive_screen_options' ] );
		add_filter( 'set-screen-option', [ $this, 'set_archive_screen_options' ], 10, 3 );
		add_filter( 'set_screen_option_wp_mail_smtp_log_entries_per_page', [ $this, 'set_archive_screen_options' ], 10, 3 );

		// Set default hidden columns for logs table.
		add_filter( 'default_hidden_columns', [ $this, 'archive_table_default_hidden_columns' ], 10, 2 );

		// Register the sent status verification tasks.
		add_action( 'wp_mail_smtp_providers_mailer_verify_sent_status', [ $this, 'run_sent_status_verification' ], 10, 2 );

		// Detect log retention period constant change.
		if ( Options::init()->is_const_defined( 'logs', 'log_retention_period' ) ) {
			add_action( 'admin_init', [ $this, 'detect_log_retention_period_constant_change' ] );
		}

		// Init logs table.
		if ( $this->is_archive() ) {
			add_action(
				'admin_init',
				function () {
					wp_mail_smtp()->get_admin()->generate_display_logs_object()->get_table();
				}
			);
		}

		// Init reports admin page.
		add_filter(
			'wp_mail_smtp_admin_page_reports_tabs',
			function ( $tabs ) {
				$tabs['reports'] = ReportsAdmin::class;
				return $tabs;
			}
		);

		// Disable summary report email if email log is disabled.
		if ( ! $this->is_enabled() ) {
			add_filter( 'wp_mail_smtp_reports_emails_summary_is_disabled', '__return_true' );
		}
	}

	/**
	 * Register the screen options for the email logs archive page.
	 *
	 * @since 1.9.0
	 */
	public function archive_screen_options() {

		$screen = get_current_screen();

		if (
			! is_object( $screen ) ||
			strpos( $screen->id, 'wp-mail-smtp_page_wp-mail-smtp-logs' ) === false ||
			isset( $_REQUEST['mode'] ) //phpcs:ignore
		) {
			return;
		}

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Number of log entries per page:', 'wp-mail-smtp-pro' ),
				'option'  => 'wp_mail_smtp_log_entries_per_page',
				'default' => EmailsCollection::PER_PAGE,
			)
		);
	}

	/**
	 * Set the screen options for the archive logs page.
	 *
	 * @since 1.9.0
	 *
	 * @param bool   $keep   Whether to save or skip saving the screen option value.
	 * @param string $option The option name.
	 * @param int    $value  The number of items to use.
	 *
	 * @return bool|int
	 */
	public function set_archive_screen_options( $keep, $option, $value ) {

		if ( 'wp_mail_smtp_log_entries_per_page' === $option ) {
			return (int) $value;
		}

		return $keep;
	}

	/**
	 * Set default hidden columns for logs table.
	 * This method is only working for new users,
	 * for existing users a migration is hiding these columns.
	 *
	 * @since 3.1.0
	 *
	 * @param string[]   $hidden Array of IDs of columns hidden by default.
	 * @param \WP_Screen $screen WP_Screen object of the current screen.
	 *
	 * @return array
	 */
	public function archive_table_default_hidden_columns( $hidden, $screen ) {

		if ( $screen->id === 'wp-mail-smtp_page_wp-mail-smtp-logs' ) {
			$hidden[] = 'cc';
			$hidden[] = 'bcc';
		}

		return $hidden;
	}

	/**
	 * Sanitize admin area options.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options Currently processed options passed to a filter hook.
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) {

		if ( ! isset( $options['logs'] ) ) {
			// All options are off by default.
			$options['logs'] = [
				'enabled'              => false,
				'log_email_content'    => false,
				'log_retention_period' => 0,
				'save_attachments'     => false,
				'open_email_tracking'  => false,
				'click_link_tracking'  => false,
			];

			return $options;
		}

		foreach ( $options['logs'] as $key => $value ) {
			if ( $key === 'log_retention_period' ) {
				$options['logs'][ $key ] = intval( $value );

				// If this option has changed, cancel the recurring cleanup task.
				if ( Options::init()->is_option_changed( $options['logs'][ $key ], 'logs', $key ) ) {
					( new EmailLogCleanupTask() )->cancel();
				}
			} else {
				$options['logs'][ $key ] = (bool) $value;
			}
		}

		return $options;
	}

	/**
	 * Process the options values through the constants check.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed  $return Constant value.
	 * @param string $group  The option group.
	 * @param string $key    The option key.
	 * @param mixed  $value  DB option value.
	 *
	 * @return mixed
	 */
	public function filter_options_get_const_value( $return, $group, $key, $value ) {

		$options = Options::init();

		if ( $group === 'logs' ) {
			switch ( $key ) {
				case 'enabled':
					$return = $options->is_const_defined( $group, $key ) ?
						$options->parse_boolean( WPMS_LOGS_ENABLED ) :
						$value;
					break;
				case 'log_email_content':
					$return = $options->is_const_defined( $group, $key ) ?
						$options->parse_boolean( WPMS_LOGS_LOG_EMAIL_CONTENT ) :
						$value;
					break;
				case 'log_retention_period':
					$return = $options->is_const_defined( $group, $key ) ?
						intval( WPMS_LOGS_LOG_RETENTION_PERIOD ) :
						$value;
					break;
				case 'save_attachments':
					$return = $options->is_const_defined( $group, $key ) ?
						$options->parse_boolean( WPMS_LOGS_SAVE_ATTACHMENTS ) :
						$value;
					break;
				case 'open_email_tracking':
					$return = $options->is_const_defined( $group, $key ) ?
						$options->parse_boolean( WPMS_LOGS_OPEN_EMAIL_TRACKING ) :
						$value;
					break;
				case 'click_link_tracking':
					$return = $options->is_const_defined( $group, $key ) ?
						$options->parse_boolean( WPMS_LOGS_CLICK_LINK_TRACKING ) :
						$value;
					break;
			}
		}

		return $return;
	}

	/**
	 * Check is constant defined.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed  $return Constant value.
	 * @param string $group  The option group.
	 * @param string $key    The option key.
	 *
	 * @return bool
	 */
	public function filter_options_is_const_defined( $return, $group, $key ) {

		if ( $group === 'logs' ) {
			switch ( $key ) {
				case 'enabled':
					$return = defined( 'WPMS_LOGS_ENABLED' );
					break;
				case 'log_email_content':
					$return = defined( 'WPMS_LOGS_LOG_EMAIL_CONTENT' );
					break;
				case 'log_retention_period':
					$return = defined( 'WPMS_LOGS_LOG_RETENTION_PERIOD' );
					break;
				case 'save_attachments':
					$return = defined( 'WPMS_LOGS_SAVE_ATTACHMENTS' );
					break;
				case 'open_email_tracking':
					$return = defined( 'WPMS_LOGS_OPEN_EMAIL_TRACKING' );
					break;
				case 'click_link_tracking':
					$return = defined( 'WPMS_LOGS_CLICK_LINK_TRACKING' );
					break;
			}
		}

		return $return;
	}

	/**
	 * Detect log retention period constant change.
	 *
	 * @since 2.8.0
	 */
	public function detect_log_retention_period_constant_change() {

		if ( ! WP::in_wp_admin() ) {
			return;
		}

		if ( Options::init()->is_const_changed( 'logs', 'log_retention_period' ) ) {
			( new EmailLogCleanupTask() )->cancel();
		}
	}

	/**
	 * Get admin area page URL for Logs, regardless of page mode, default is Archive page.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_admin_page_url() {

		return wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' );
	}

	/**
	 * Get plugin settings page URL for Logs.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_settings_url() {

		return add_query_arg( 'tab', 'logs', wp_mail_smtp()->get_admin()->get_admin_page_url() );
	}

	/**
	 * Get email logs page manage capability.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_manage_capability() {

		/**
		 * Filter email logs page manage capability.
		 *
		 * @since 2.8.0
		 *
		 * @param string  $capability Email logs page manage capability.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_logs_get_manage_capability', 'manage_options' );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_assets() {

		if ( ! wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) ) {
			return;
		}

		$settings = [
			'plugin_url'                              => wp_mail_smtp()->plugin_url,
			'text_email_delete_sure'                  => esc_html__( 'Are you sure that you want to delete this email log? This action cannot be undone.', 'wp-mail-smtp-pro' ),
			'ok'                                      => esc_html__( 'OK', 'wp-mail-smtp-pro' ),
			'icon'                                    => esc_html__( 'Icon', 'wp-mail-smtp-pro' ),
			'delete_all_email_logs_confirmation_text' => esc_html__( 'Are you sure you want to permanently delete all email logs?', 'wp-mail-smtp-pro' ),
			'heads_up_title'                          => esc_html__( 'Heads up!', 'wp-mail-smtp-pro' ),
			'yes_text'                                => esc_html__( 'Yes', 'wp-mail-smtp-pro' ),
			'cancel_text'                             => esc_html__( 'Cancel', 'wp-mail-smtp-pro' ),
			'error_occurred'                          => esc_html__( 'An error occurred!', 'wp-mail-smtp-pro' ),
		];

		if ( $this->is_archive() ) {
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

			$settings['lang_code']                           = sanitize_key( WP::get_language_code() );
			$settings['bulk_resend_email_confirmation_text'] = esc_html__( 'Are you sure you want to resend selected emails?', 'wp-mail-smtp-pro' );
			$settings['bulk_resend_email_processing_text']   = esc_html__( 'Queuing emails...', 'wp-mail-smtp-pro' );
		} else {
			$email = new Email( intval( $_GET['email_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

			if ( $email->is_valid() ) {
				$settings['email_id']                                  = $email->get_id();
				$settings['resend_email_confirmation_text']            = Resend::prepare_resend_confirmation_content( $email );
				$settings['resend_email_processing_text']              = esc_html__( 'Sending email...', 'wp-mail-smtp-pro' );
				$settings['resend_email_invalid_recipients_addresses'] = esc_html__( 'Invalid recipients email addresses.', 'wp-mail-smtp-pro' );
			}
		}

		wp_localize_script(
			'wp-mail-smtp-admin-logs',
			'wp_mail_smtp_logs',
			$settings
		);
	}

	/**
	 * Whether the logging to DB functionality is enabled or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_enabled() {

		return (bool) Options::init()->get( 'logs', 'enabled' );
	}

	/**
	 * Whether the DB table exists.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_valid_db() {

		global $wpdb;

		$table = self::get_table_name();

		return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $table ) );
	}

	/**
	 * Whether the email content logging is enabled or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_enabled_content() {

		return (bool) Options::init()->get( 'logs', 'log_email_content' );
	}

	/**
	 * Whether the attachment storing/saving is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_enabled_save_attachments() {

		return $this->is_enabled() && (bool) Options::init()->get( 'logs', 'save_attachments' );
	}

	/**
	 * Whether the open email tracking is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_enabled_open_email_tracking() {

		return $this->is_enabled() && (bool) Options::init()->get( 'logs', 'open_email_tracking' );
	}

	/**
	 * Whether the click link tracking is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_enabled_click_link_tracking() {

		return $this->is_enabled() && (bool) Options::init()->get( 'logs', 'click_link_tracking' );
	}

	/**
	 * Whether the email tracking is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_enabled_tracking() {

		return $this->is_enabled_open_email_tracking() || $this->is_enabled_click_link_tracking();
	}

	/**
	 * Whether we are on a Logs page (archive, list of all emails).
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_archive() {

		return wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) && ! isset( $_GET['mode'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Whether we are previewing the single email HTML.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_preview() {

		// Nonce verification.
		if (
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wp_mail_smtp_pro_logs_log_preview' )
		) {
			return false;
		}

		return wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) &&
			isset( $_GET['mode'] ) &&
			$_GET['mode'] === 'preview' &&
			! empty( $_GET['email_id'] );
	}

	/**
	 * Whether we are deleting email(s) now.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_deleting() {

		$is_nonce_good = false;

		// Nonce verification.
		if (
			isset( $_REQUEST['_wpnonce'] ) &&
			(
				wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'wp_mail_smtp_pro_logs_log_delete' ) ||
				wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'bulk-emails' )
			)
		) {
			$is_nonce_good = true;
		}

		if ( ! $is_nonce_good ) {
			return false;
		}

		return wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) &&
			! empty( $_REQUEST['email_id'] ) &&
			(
				( // Single email deletion.
					isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] === 'delete'
				) ||
				( // Bulk email deletion.
					isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ||
					isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'delete'
				)
			);
	}

	/**
	 * Generate an email preview and display it for users.
	 *
	 * @since 1.5.0
	 */
	public function process_email_preview() {

		if ( ! $this->is_preview() ) {
			return;
		}

		$email = new Email( intval( $_GET['email_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		// It's a raw HTML (with html/body tags), so print as is.
		echo $email->is_html() ?
			$this->prepare_email_preview_html( $email->get_content() ) : // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			nl2br( esc_html( $email->get_content() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		exit;
	}

	/**
	 * Prepare email preview html.
	 *
	 * @since 2.8.0
	 *
	 * @param string $html Email content html.
	 *
	 * @return string
	 */
	public function prepare_email_preview_html( $html ) {

		ob_start();
		?>
		<style type="text/css">
		@media print {
			/* Fix background colors issue when print. */
			body {
				-webkit-print-color-adjust: exact;
			}
		}
		</style>
		<?php
		$print_css = ob_get_clean();

		// Append css in head before closing tag.
		$html = preg_replace( '/<\s*\/\s*head\s*>/', $print_css . '</head>', $html, 1 );

		return $html;
	}

	/**
	 * Delete the email log entry.
	 *
	 * @since 1.5.0
	 */
	public function process_email_delete() {

		if ( ! $this->is_deleting() ) {
			return;
		}

		if ( ! current_user_can( $this->get_manage_capability() ) ) {
			wp_die( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['email_id'] ) ) {
			wp_die( esc_html__( 'Required parameters are missing.', 'wp-mail-smtp-pro' ) );
		}

		$emails = null;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $_REQUEST['email_id'] ) ) {
			$emails = new EmailsCollection( [ 'ids' => array_map( 'intval', array_values( $_REQUEST['email_id'] ) ) ] );
		} elseif ( is_numeric( $_REQUEST['email_id'] ) ) {
			$emails = new EmailsCollection( [ 'id' => intval( $_REQUEST['email_id'] ) ] );
		}
		// phpcs:enable

		$deleted = 0;

		if ( $emails !== null ) {
			$deleted = $emails->delete();
		}

		if ( $deleted === 1 ) {
			$url = add_query_arg( 'message', 'deleted_one', $this->get_admin_page_url() );
		} elseif ( $deleted > 1 ) {
			$url = add_query_arg( 'message', 'deleted_some', $this->get_admin_page_url() );
		} else {
			$url = add_query_arg( 'message', 'deleted_none', $this->get_admin_page_url() );
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Display notices on Logs page when needed.
	 *
	 * @since 1.5.0
	 */
	public function display_notices() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$message = isset( $_GET['message'] ) ? sanitize_key( $_GET['message'] ) : '';

		if (
			empty( $message ) ||
			! current_user_can( $this->get_manage_capability() ) ||
			! wp_mail_smtp()->get_admin()->is_admin_page( 'logs' )
		) {
			return;
		}

		switch ( $message ) {
			case 'deleted_one':
				WP::add_admin_notice(
					esc_html__( 'Email Log entry was successfully deleted.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_SUCCESS
				);
				break;

			case 'deleted_some':
				WP::add_admin_notice(
					esc_html__( 'Email Log entries were successfully deleted.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_SUCCESS
				);
				break;

			case 'deleted_none':
				WP::add_admin_notice(
					esc_html__( 'There was an error while processing your request, and no email log entries were deleted. Please try again.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_WARNING
				);
				break;
		}
	}

	/**
	 * Save the email that is going to be sent right now.
	 *
	 * @since 2.1.2
	 *
	 * @param MailCatcherInterface $mailcatcher The MailCatcher object.
	 */
	public function process_smtp_pre_send_before( $mailcatcher ) {

		if ( ! $this->is_valid_db() ) {
			return;
		}

		$this->set_current_email_id(
			( new SMTP() )->set_source( $mailcatcher )->save_before()
		);
	}

	/**
	 * Update the email that is going to be sent right now.
	 * It was originally saved before in `process_smtp_pre_send_before`.
	 *
	 * @since 1.5.0
	 * @since 2.1.2 Update the Email before it will be sent.
	 *              The saving was already done in `process_smtp_pre_send_before`.
	 *
     * @param MailCatcherInterface $mailcatcher The MailCatcher object.
	 */
	public function process_smtp_send_before( $mailcatcher ) {

		if ( ! $this->is_valid_db() ) {
			return;
		}

		( new SMTP() )->set_source( $mailcatcher )->update_before( $this->get_current_email_id() );
	}

	/**
	 * Save to DB emails sent through both SMTP and mail().
	 *
	 * @since 1.5.0
	 *
	 * @param bool $is_sent
	 * @param array $to
	 * @param array $cc
	 * @param array $bcc
	 * @param string $subject
	 * @param string $body
	 * @param string $from
	 *
	 * @throws \Exception When email saving failed.
	 */
	public function process_smtp_send_after( $is_sent, $to, $cc, $bcc, $subject, $body, $from ) {

		if ( ! $this->is_valid_db() ) {
			return;
		}

		( new SMTP() )->save_after( $this->get_current_email_id(), $is_sent );
	}

	/**
	 * Save the actual email data before email send.
	 *
	 * @since 2.9.0
	 *
	 * @param MailCatcherInterface $mailcatcher The MailCatcher object.
	 */
	public function process_pre_send_before( $mailcatcher ) {

		if ( ! $this->is_valid_db() ) {
			return;
		}

		$this->set_current_email_id(
			( new Common() )->set_source( $mailcatcher )->save_before()
		);
	}

	/**
	 * Save all emails as sent regardless of the actual status. Will be improved in the future.
	 * Supports every mailer, except SMTP, which is handled separately.
	 *
	 * @since 1.5.0
	 *
	 * @param \WPMailSMTP\Providers\MailerAbstract $mailer      The Mailer object.
	 * @param MailCatcherInterface                 $mailcatcher The MailCatcher object.
	 */
	public function process_log_save( MailerAbstract $mailer, MailCatcherInterface $mailcatcher ) {

		if ( ! $this->is_valid_db() ) {
			return;
		}

		$email_id = ( new Common( $mailer ) )->set_source( $mailcatcher )->save( $this->get_current_email_id() );

		$mailer->verify_sent_status( $email_id );
	}

	/**
	 * Get the current email ID.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public function get_current_email_id() {

		return (int) $this->current_email_id;
	}

	/**
	 * Set the email ID that is currently processing.
	 *
	 * @since 1.5.0
	 *
	 * @param int $email_id
	 */
	public function set_current_email_id( $email_id ) {

		$this->current_email_id = (int) $email_id;
	}

	/**
	 * Get the table name.
	 *
	 * @since 1.5.0
	 *
	 * @return string Table name, prefixed.
	 */
	public static function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . 'wpmailsmtp_emails_log';
	}

	/**
	 * Process the failed email sending with SMTP.
	 *
	 * @since 2.1.0
	 *
	 * @param \WP_Error $error The WP Error thrown in WP core: `wp_mail_failed` hook.
	 */
	public function process_smtp_fails( $error ) {

		if ( ! $this->is_valid_db() || ! is_wp_error( $error ) ) {
			return;
		}

		// Process this error only for the Other SMTP, old pepipost SMTP and the default PHP mailer.
		if ( ! in_array( Options::init()->get( 'mail', 'mailer' ), [ 'smtp', 'pepipost', 'mail' ], true ) ) {
			return;
		}

		( new SMTP() )->failed( $this->get_current_email_id(), $error );
	}

	/**
	 * Process AJAX request for deleting all email log entries.
	 *
	 * @since 2.5.0
	 */
	public function process_ajax_delete_all_log_entries() {

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp_mail_smtp_pro_delete_log_entries' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		if ( ! current_user_can( $this->get_manage_capability() ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		global $wpdb;

		$table = self::get_table_name();

		$sql = "TRUNCATE TABLE `$table`;";

		$result = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( $result !== false ) {

			// Delete all attachments if any.
			( new Attachments() )->delete_all_attachments();

			// Delete all tracking events if any.
			( new TrackingEvents() )->delete_all_events();

			wp_send_json_success( esc_html__( 'All email log entries were deleted successfully.', 'wp-mail-smtp-pro' ) );
		}

		wp_send_json_error(
			sprintf( /* translators: %s - WPDB error message. */
				esc_html__( 'There was an issue while trying to delete all email log entries. Error message: %s', 'wp-mail-smtp-pro' ),
				$wpdb->last_error
			)
		);
	}

	/**
	 * Initialize the background tasks for sent status verification, based on the mailer and the email log ID.
	 *
	 * @since 2.5.0
	 *
	 * @param int            $email_log_id The Email log ID.
	 * @param MailerAbstract $mailer       The mailer in use.
	 */
	public function run_sent_status_verification( $email_log_id, MailerAbstract $mailer ) {

		$mailer_name = $mailer->get_mailer_name();

		if ( Webhooks::is_allowed() ) {
			$webhooks_provider = $this->get_webhooks()->get_provider( $mailer_name );

			// Skip AS task verification if webhooks are set up.
			if ( $webhooks_provider !== false && $webhooks_provider->get_setup_status() === Webhooks::SUCCESS_SETUP ) {
				return;
			}
		}

		if ( $mailer_name === 'sendlayer' ) {
			( new SendlayerVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + SendlayerVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		} if ( $mailer_name === 'mailgun' ) {
			( new MailgunVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + MailgunVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		} elseif ( $mailer_name === 'sendinblue' ) {
			( new SendinblueVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + SendinblueVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		} elseif ( $mailer_name === 'smtpcom' ) {
			( new SMTPcomVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + SMTPcomVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		} elseif ( $mailer_name === 'postmark' ) {
			( new PostmarkVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + PostmarkVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		} elseif ( $mailer_name === 'sparkpost' ) {
			( new SparkPostVerifySentStatusTask() )
				->params( $email_log_id, 1 )
				->once( time() + SparkPostVerifySentStatusTask::SCHEDULE_TASK_IN )
				->register();
		}
	}

	/**
	 * Initialize the Webhooks functionality.
	 *
	 * @since 3.3.0
	 *
	 * @return Webhooks
	 */
	public function get_webhooks() {

		static $webhooks;

		if ( ! isset( $webhooks ) ) {
			$webhooks = new Webhooks();
			$webhooks->hooks();
		}

		return $webhooks;
	}
}
