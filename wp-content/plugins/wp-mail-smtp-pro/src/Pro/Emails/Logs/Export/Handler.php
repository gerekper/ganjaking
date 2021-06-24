<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WP_Error;
use WPMailSMTP\WP;
use WPMailSMTP\Pro\Tasks\Logs\ExportCleanupTask;

/**
 * Export request handler.
 *
 * @since 2.8.0
 */
class Handler {

	/**
	 * Remove export file trait.
	 *
	 * @since 2.9.0
	 */
	use CanRemoveExportFileTrait;

	/**
	 * Export request.
	 *
	 * @since 2.8.0
	 *
	 * @var Request Export request.
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.8.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Initialize.
	 *
	 * @since 2.8.0
	 */
	public function init() {

		$is_valid = $this->check_requirements();

		if ( is_wp_error( $is_valid ) ) {
			if ( WP::is_doing_ajax() ) {
				wp_send_json_error( [ 'error' => $is_valid->get_error_message() ] );
			} else {
				WP::add_admin_notice( $is_valid->get_error_message() );
				return;
			}
		}

		$this->request = new Request( WP::is_doing_ajax() ? 'POST' : 'GET' );
		$action        = $this->request->get_arg( 'action' );

		switch ( $action ) {
			case 'wp_mail_smtp_tools_export_single_email_log':
				$this->single_email_log_export();
				break;

			case 'wp_mail_smtp_tools_export_email_logs':
				add_action( 'wp_ajax_' . $action, [ $this, 'email_logs_export_ajax_handler' ] );
				break;

			case 'wp_mail_smtp_tools_export_download_result':
				$this->download_export_file();
				break;
		}
	}

	/**
	 * Single email log export file download.
	 *
	 * @since 2.8.0
	 *
	 * @throws \Exception Try-catch.
	 */
	public function single_email_log_export() {

		try {

			// Check for email_id.
			if ( empty( $this->request->get_arg( 'email_id' ) ) ) {
				throw new \Exception( Export::get_config( 'errors', 'unknown_email_id' ) );
			}

			// Security check.
			if (
				! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ||
				! wp_verify_nonce( $this->request->get_arg( 'nonce' ), 'wp-mail-smtp-tools-export-single-email-log-nonce' )
			) {
				throw new \Exception( Export::get_config( 'errors', 'security' ) );
			}

			$file        = new File();
			$is_exported = $file->write( $this->request );

			if ( is_wp_error( $is_exported ) ) {
				throw new \Exception( $is_exported->get_error_message() );
			}

			( new ExportCleanupTask() )->schedule(
				$this->request->get_request_id(),
				Export::get_config( 'export', 'request_data_ttl' )
			);

			$file->output_file( $this->request );

		} catch ( \Exception $e ) {
			$this->remove_export_file( $this->request->get_request_id() );

			$error = Export::get_config( 'errors', 'common' ) . '<br>' . $e->getMessage();
			WP::add_admin_notice( $error );
		}
	}

	/**
	 * Bulk email logs ajax export.
	 *
	 * @since 2.8.0
	 *
	 * @throws \Exception Try-catch.
	 */
	public function email_logs_export_ajax_handler() {

		try {

			// Security checks.
			if (
				! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ||
				! check_ajax_referer( 'wp-mail-smtp-tools-export-email-logs-nonce', 'nonce', false )
			) {
				throw new \Exception( Export::get_config( 'errors', 'security' ) );
			}

			// Unlimited execution time.
			WP::set_time_limit();

			$is_written = ( new File() )->write( $this->request );

			if ( is_wp_error( $is_written ) ) {
				throw new \Exception( $is_written->get_error_message() );
			}

			// Store request data.
			$this->request->persist();

			if ( $this->request->get_data( 'total_steps' ) === $this->request->get_arg( 'step' ) ) {
				( new ExportCleanupTask() )->schedule(
					$this->request->get_request_id(),
					Export::get_config( 'export', 'request_data_ttl' )
				);
			}

			wp_send_json_success(
				[
					'request_id'  => $this->request->get_request_id(),
					'count'       => $this->request->get_data( 'count' ),
					'total_steps' => $this->request->get_data( 'total_steps' ),
					'notices'     => $this->request->get_notices(),
				]
			);
		} catch ( \Exception $e ) {
			$this->remove_export_file( $this->request->get_request_id() );

			$error = Export::get_config( 'errors', 'common' ) . '<br>' . $e->getMessage();
			wp_send_json_error( [ 'error' => $error ] );
		}
	}

	/**
	 * Download export file.
	 *
	 * @since 2.8.0
	 *
	 * @throws \Exception Try-catch.
	 */
	public function download_export_file() {

		try {

			// Security check.
			if (
				! wp_verify_nonce( $this->request->get_arg( 'nonce' ), 'wp-mail-smtp-tools-export-email-logs-nonce' ) ||
				! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() )
			) {
				throw new \Exception( Export::get_config( 'errors', 'security' ) );
			}

			// Check for request_id.
			if ( empty( $this->request->get_request_id() ) ) {
				throw new \Exception( Export::get_config( 'errors', 'unknown_request' ) );
			}

			( new File() )->output_file( $this->request );

		} catch ( \Exception $e ) {

			// phpcs:disable
			$error = Export::get_config( 'errors', 'common' ) . '<br>' . $e->getMessage();
			$error = str_replace( "'", '&#039;', $error );

			echo "
			<script>
				( function() {
					var w = window;
					if ( w.frameElement != null &&
						 w.frameElement.nodeName === 'IFRAME' &&
						 w.parent.jQuery )
					{
						w.parent.WPMailSmtpEmailLogsExport.displaySubmitSpinner( true );
						w.parent.jQuery( w.parent.document ).trigger( 'csv_file_error', [ '" . str_replace( "\n", '', $error ) . "' ] );
						w.parent.WPMailSmtpEmailLogsExport.displaySubmitSpinner( false );
					}
				} )();
			</script>
			<pre>" . $error . '</pre>';
			exit;
			// phpcs:enable
		}
	}

	/**
	 * Check export requirements.
	 *
	 * @since 2.8.0
	 *
	 * @return WP_Error|true
	 */
	protected function check_requirements() {

		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_valid_db() ) {
			return new WP_Error( 'empty_email_log', esc_html__( 'No Email Logs found. It looks like the Email Logs database table is missing.', 'wp-mail-smtp-pro' ) );
		}

		return true;
	}

}
