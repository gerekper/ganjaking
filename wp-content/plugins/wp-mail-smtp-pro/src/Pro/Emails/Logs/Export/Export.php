<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WPMailSMTP\WP;

/**
 * Email logs export.
 *
 * @since 2.8.0
 */
class Export {

	/**
	 * Initialize.
	 *
	 * @since 2.8.0
	 */
	public function init() {

		// Init admin page.
		if ( wp_mail_smtp()->get_admin()->is_admin_page( 'tools' ) ) {
			add_filter(
				'wp_mail_smtp_admin_page_tools_tabs',
				function ( $tabs ) {

					$tabs['export'] = Admin::class;

					return $tabs;
				}
			);
		}

		// Init export request handler.
		if ( $this->is_export_request() ) {
			new Handler();
		}
	}

	/**
	 * Get export config.
	 *
	 * @since 2.8.0
	 *
	 * @param string $group Fields group if we need particular group.
	 * @param string $key   Field key if we need particular option.
	 *
	 * @return mixed
	 */
	public static function get_config( $group = false, $key = false ) {

		$defaults = [
			// General settings.
			'export' => [
				// Export request and a temp file TTL value.
				'request_data_ttl'     => DAY_IN_SECONDS,
				// Number of email logs in a chunk that are retrieved and saved into a temp file per one iteration.
				'email_logs_per_step'  => 5000,
				// Columns separator.
				'csv_export_separator' => ',',
			],
			// Error strings.
			'errors' => [
				'common'            => esc_html__( 'An error occurred while preparing your export file. Please recheck export settings and try again.', 'wp-mail-smtp-pro' ),
				'security'          => esc_html__( 'You don\'t have enough capabilities to complete this request.', 'wp-mail-smtp-pro' ),
				'unknown_request'   => esc_html__( 'Unknown request.', 'wp-mail-smtp-pro' ),
				'unknown_email_id'  => esc_html__( 'Incorrect email ID has been specified.', 'wp-mail-smtp-pro' ),
				'file_not_readable' => esc_html__( 'Export file cannot be retrieved from the file system.', 'wp-mail-smtp-pro' ),
				'file_empty'        => esc_html__( 'Export file is empty.', 'wp-mail-smtp-pro' ),
			],
			// Strings to localize.
			'i18n'   => [
				'error_prefix'        => esc_html__( 'An error occurred while preparing your export file. Please recheck export settings and try again.', 'wp-mail-smtp-pro' ),
				'prc_1_filtering'     => esc_html__( 'Generating a list of email logs according to your filters.', 'wp-mail-smtp-pro' ),
				'prc_1_please_wait'   => esc_html__( 'This can take a while. Please wait.', 'wp-mail-smtp-pro' ),
				'prc_2_no_email_logs' => esc_html__( 'No email logs found after applying your filters.', 'wp-mail-smtp-pro' ),
				'prc_3_done'          => esc_html__( 'The file was generated successfully.', 'wp-mail-smtp-pro' ),
				'prc_3_partially'     => esc_html__( 'The file was generated partially. Please check below notices.', 'wp-mail-smtp-pro' ),
				'prc_3_download'      => esc_html__( 'If the download does not start automatically', 'wp-mail-smtp-pro' ),
				'prc_3_click_here'    => esc_html__( 'click here', 'wp-mail-smtp-pro' ),
			],
		];

		/**
		 * Filters export configuration.
		 *
		 * @since 2.8.0
		 *
		 * @param array $defaults Default configuration values.
		 */
		$config = (array) apply_filters( 'wp_mail_smtp_pro_emails_logs_export_export_get_config', $defaults );

		// Make sure that all defaults are set.
		$config = WP::parse_args_r( $config, $defaults );

		if ( $group !== false && $key === false ) {
			return isset( $config[ $group ] ) ? $config[ $group ] : null;
		} elseif ( $group !== false && $key !== false ) {
			return isset( $config[ $group ][ $key ] ) ? $config[ $group ][ $key ] : null;
		}

		return $config;
	}

	/**
	 * Common information fields for email logs export.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key Field key if we need particular field.
	 *
	 * @return mixed
	 */
	public static function get_common_fields( $key = false ) {

		/**
		 * Filters export common fields.
		 *
		 * @since 2.8.0
		 *
		 * @param array $fields Common fields.
		 */
		$fields = apply_filters(
			'wp_mail_smtp_pro_emails_logs_export_export_get_common_fields',
			[
				'people_to'         => esc_html__( 'To Address', 'wp-mail-smtp-pro' ),
				'people_from'       => esc_html__( 'From Address', 'wp-mail-smtp-pro' ),
				'people_from_name'  => esc_html__( 'From Name', 'wp-mail-smtp-pro' ),
				'subject'           => esc_html__( 'Subject', 'wp-mail-smtp-pro' ),
				'content'           => esc_html__( 'Body', 'wp-mail-smtp-pro' ),
				'date_sent'         => esc_html__( 'Created Date', 'wp-mail-smtp-pro' ),
				'attachments_count' => esc_html__( 'Number of Attachments', 'wp-mail-smtp-pro' ),
				'attachments'       => esc_html__( 'Attachments', 'wp-mail-smtp-pro' ),
			]
		);

		if ( $key !== false ) {
			return isset( $fields[ $key ] ) ? $fields[ $key ] : null;
		}

		return $fields;
	}

	/**
	 * Additional information fields for email logs export.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key Field key if we need particular field.
	 *
	 * @return mixed
	 */
	public static function get_additional_fields( $key = false ) {

		/**
		 * Filters export additional fields.
		 *
		 * @since 2.8.0
		 *
		 * @param array $fields Additional fields.
		 */
		$fields = apply_filters(
			'wp_mail_smtp_pro_emails_logs_export_export_get_additional_fields',
			[
				'status'     => esc_html__( 'Status', 'wp-mail-smtp-pro' ),
				'people_cc'  => esc_html__( 'Carbon Copy (CC)', 'wp-mail-smtp-pro' ),
				'people_bcc' => esc_html__( 'Blind Carbon Copy (BCC)', 'wp-mail-smtp-pro' ),
				'headers'    => esc_html__( 'Headers', 'wp-mail-smtp-pro' ),
				'mailer'     => esc_html__( 'Mailer', 'wp-mail-smtp-pro' ),
				'error_text' => esc_html__( 'Error Details', 'wp-mail-smtp-pro' ),
				'log_id'     => esc_html__( 'Email log ID', 'wp-mail-smtp-pro' ),
				'opened'     => esc_html__( 'Opened', 'wp-mail-smtp-pro' ),
				'clicked'    => esc_html__( 'Clicked', 'wp-mail-smtp-pro' ),
			]
		);

		if ( $key !== false ) {
			return isset( $fields[ $key ] ) ? $fields[ $key ] : null;
		}

		return $fields;
	}

	/**
	 * Export types.
	 *
	 * @since 2.8.0
	 *
	 * @return mixed
	 */
	public static function get_export_types() {

		$types = [];

		$types['csv'] = esc_html__( 'Export in CSV (.csv)', 'wp-mail-smtp-pro' );

		// This option should be available only if zip PHP extension is loaded.
		if ( class_exists( 'ZipArchive' ) ) {
			$types['xlsx'] = esc_html__( 'Export in Microsoft Excel (.xlsx)', 'wp-mail-smtp-pro' );
			$types['eml']  = esc_html__( 'Export in EML (.eml)', 'wp-mail-smtp-pro' );
		}

		/**
		 * Filters export types.
		 *
		 * @since 2.8.0
		 *
		 * @param array $types Export types.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_export_export_get_export_types', $types );
	}

	/**
	 * Check if export request.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_export_request() {

		$req = WP::is_doing_ajax() ? $_POST : $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

		return isset( $req['action'] ) && substr( $req['action'], 0, 25 ) === 'wp_mail_smtp_tools_export';
	}
}
