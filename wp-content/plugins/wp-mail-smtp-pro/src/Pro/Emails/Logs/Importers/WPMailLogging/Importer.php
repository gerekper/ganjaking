<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers\WPMailLogging;

use Exception;
use WPMailSMTP\Admin\Area;
use WPMailSMTP\Admin\Pages\Tools;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachment;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Importers\ImporterAbstract;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\WP;

/**
 * Class Importer.
 *
 * Handles the importer functionality for WP Mail Logging.
 *
 * @since 3.8.0
 */
class Importer extends ImporterAbstract {

	/**
	 * Importer slug.
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	 const SLUG = 'wp_mail_logging_importer';

	/**
	 * Status: Import logs without attachments.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const STATUS_IMPORT_NO_ATTACHMENTS = 1;

	/**
	 * Status: Import logs with attachments.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const STATUS_IMPORT_ATTACHMENTS = 2;

	/**
	 * Status: Completed.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const STATUS_IMPORT_COMPLETED = 3;

	/**
	 * Default number of logs without attachments to import per AJAX request.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const DEFAULT_NUMBER_OF_LOGS_WITHOUT_ATTACHMENTS_TO_IMPORT_PER_REQUEST = 100;

	/**
	 * Default number of logs with attachments to import per AJAX request.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const DEFAULT_NUMBER_OF_LOGS_WITH_ATTACHMENTS_TO_IMPORT_PER_REQUEST = 10;

	/**
	 * Transient key for logs to import count.
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	const LOGS_TO_IMPORT_COUNT_TRANSIENT_KEY = 'wp_mail_smtp_' . self::SLUG . '_count';

	/**
	 * Number of logs that can be imported.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	public $logs_to_import_count = null;

	/**
	 * WordPress hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function hooks() {

		parent::hooks();

		add_action( 'wp_ajax_wp_mail_smtp_wp_mail_logging_importer_notice_dismiss', [ $this, 'ajax_dismiss_admin_notice' ] );
		add_action( 'admin_notices', [ $this, 'display_admin_notification' ] );
	}

	/**
	 * AJAX function when admin noticed was dismissed.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function ajax_dismiss_admin_notice() {

		check_admin_referer( 'wp-mail-smtp-notice-' . self::get_slug() . '-dismiss', 'nonce' );

		wp_send_json_success(
			$this->update_options( [ 'noticed_dismissed' => true ] )
		);
	}

	/**
	 * Display the admin notification.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function display_admin_notification() {

		// We only display the notification in WP Mail SMTP admin pages except for the actual tab.
		// We also don't display the notification if there are no logs to import.
		if ( ! wp_mail_smtp()->get_admin()->is_admin_page() || $this->is_importer_page() || empty( $this->get_logs_to_import_count() ) ) {
			return;
		}

		$saved_options = $this->get_saved_options();

		// We won't display the notification if import was already finished or the notification was dismissed.
		if (
			! empty( $saved_options ) &&
			( ! empty( $saved_options['last_complete_import_date'] ) || ! empty( $saved_options['noticed_dismissed'] ) )
		) {
			return;
		}
		?>
		<div class="notice <?php echo esc_attr( WP::ADMIN_NOTICE_INFO ); ?> wp-mail-smtp-notice notice-<?php echo esc_attr( self::get_slug() ); ?> is-dismissible"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp-mail-smtp-notice-' . self::get_slug() . '-dismiss' ) ); ?>">
				<p>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %d -Number of logs to be imported; %s the URL link to the importer page. */
							__( 'Do you want to import email logs from WP Mail Logging? You have %1$d email logs available for import to WP Mail SMTP. <a href="%2$s">Get started</a>.', 'wp-mail-smtp-pro' ),
							$this->get_logs_to_import_count(),
							esc_url( $this->get_importer_page_link() )
						),
						[
							'a' => [
								'href' => [],
							],
						]
					);
					?>
				</p>
			</div>
		<?php
	}

	/**
	 * Whether we are currently on the importer page.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	private function is_importer_page() {

		return wp_mail_smtp()->get_admin()->is_admin_page( 'tools' )
			&& ! empty( $_GET['tab'] ) && $_GET['tab'] === self::get_slug(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Returns the importer slug.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public static function get_slug() {

		return self::SLUG;
	}

	/**
	 * Returns the number of logs that can be imported to WP Mail SMTP.
	 *
	 * @since 3.8.0
	 *
	 * @param bool $get_fresh Whether to use the cached data or get the fresh data. Default `false`.
	 *
	 * @return int
	 */
	public function get_logs_to_import_count( $get_fresh = false ) {

		if ( $get_fresh ) {
			return $this->get_and_transient_fresh_logs_to_import_count();
		}

		$this->logs_to_import_count = get_transient( self::LOGS_TO_IMPORT_COUNT_TRANSIENT_KEY );

		if ( $this->logs_to_import_count !== false ) {
			return absint( $this->logs_to_import_count );
		}

		return $this->get_and_transient_fresh_logs_to_import_count();
	}

	/**
	 * Get fresh logs to import count.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_and_transient_fresh_logs_to_import_count() {

		global $wpdb;

		// Check if table exists.
		$result = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$this->get_table_name()
			)
		);

		if ( empty( $result ) ) {
			$this->logs_to_import_count = 0;
		} else {
			$this->logs_to_import_count = absint( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . esc_sql( $this->get_table_name() ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		set_transient( self::LOGS_TO_IMPORT_COUNT_TRANSIENT_KEY, $this->logs_to_import_count, HOUR_IN_SECONDS );

		return $this->logs_to_import_count;
	}

	/**
	 * Returns the Tab class of this importer.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_tab_class() {

		return Tab::class;
	}

	/**
	 * Whether the requirements to initialized/support importer is satisfied.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	public function requirements_satisfied() {

		if ( ! Options::init()->get( 'logs', 'enabled' ) ) {
			return false;
		}

		return ! empty( $this->get_logs_to_import_count() );
	}

	/**
	 * Perform the import.
	 *
	 * @since 3.8.0
	 *
	 * @param array $data Contains data regarding the import action.
	 */
	public function perform_import_process( $data ) {
		/*
		 * 1. First import all logs without attachments.
		 * 2. Then import all logs with attachments.
		 */
		$options = $this->get_saved_options();

		$import_data   = [];
		$import_status = ! empty( $options['status'] ) ? $options['status'] : self::STATUS_IMPORT_NO_ATTACHMENTS;

		switch ( $import_status ) {
			case self::STATUS_IMPORT_COMPLETED:
				$this->save_options(
					[
						'last_complete_import_date' => time(),
					]
				);
				break;

			case self::STATUS_IMPORT_ATTACHMENTS:
				$import_data = $this->start_import_logs_with_attachments();
				break;

			default:
				$import_data = $this->start_import_logs_without_attachments();
				break;
		}

		$this->send_ajax_response( $import_data, $import_status );
	}

	/**
	 * Start importing logs without attachments.
	 *
	 * @since 3.8.0
	 *
	 * @return array Returns an array containing information regarding the imported logs.
	 */
	private function start_import_logs_without_attachments() {

		$logs = $this->get_logs( $this->get_number_of_logs_without_attachments_to_import() );

		if ( empty( $logs ) ) {
			$this->update_import_status( self::STATUS_IMPORT_ATTACHMENTS );

			return [
				'successful_import_count' => 0,
			];
		}

		$import = $this->import_logs_without_attachments( $logs );

		if ( ! empty( $import['error'] ) ) {
			return [
				'error'                   => $import['error'],
				'successful_import_count' => 0,
			];
		}

		if ( $import['number_of_logs_fetched'] < $this->get_number_of_logs_without_attachments_to_import() ) {
			$this->update_import_status( self::STATUS_IMPORT_ATTACHMENTS );

			return [
				'successful_import_count' => $import['successful_import_count'],
			];
		}

		// Update the last imported mail ID.
		$this->save_options(
			[
				'status'                => self::STATUS_IMPORT_NO_ATTACHMENTS,
				'last_imported_mail_id' => $import['last_imported_mail_id'],
			]
		);

		return [
			'successful_import_count' => $import['successful_import_count'],
		];
	}

	/**
	 * Get logs from WP Mail Logging table.
	 *
	 * @since 3.8.0
	 *
	 * @param int  $number_of_logs_to_fetch Number of logs to fetch.
	 * @param bool $with_attachments        Optional. Whether to get logs with attachments or not. Default `false`.
	 *
	 * @return array
	 */
	private function get_logs( $number_of_logs_to_fetch, $with_attachments = false ) {

		global $wpdb;

		if ( $with_attachments ) {
			$where_clause = 'WHERE `attachments` != ""';
		} else {
			$where_clause = 'WHERE `attachments` = ""';
		}

		$options = $this->get_saved_options();

		if ( $options && ! empty( $options['last_imported_mail_id'] ) ) {
			$where_clause .= $wpdb->prepare( ' AND `mail_id` > %d', $options['last_imported_mail_id'] );
		}

		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT * from ' . esc_sql( $this->get_table_name() ) . ' ' . $where_clause . ' ORDER BY mail_id ASC LIMIT %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$number_of_logs_to_fetch
			)
		);
	}

	/**
	 * Update the import status.
	 *
	 * @since 3.8.0
	 *
	 * @param int $status New status.
	 *
	 * @return bool
	 */
	private function update_import_status( $status ) {

		return $this->save_options(
			[
				'status' => $status,
			]
		);
	}

	/**
	 * Get the number of logs without attachments to import per request.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_number_of_logs_without_attachments_to_import() {

		/**
		 * Filters the number of logs without attachments to import per request.
		 *
		 * @since 3.8.0
		 *
		 * @param int $count Number of logs without attachments to import per request.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_emails_logs_importers_wp_mail_logging_importer_count_without_attachments',
			self::DEFAULT_NUMBER_OF_LOGS_WITHOUT_ATTACHMENTS_TO_IMPORT_PER_REQUEST
		);
	}

	/**
	 * Import logs without attachments.
	 *
	 * @since 3.8.0
	 *
	 * @param array $logs Logs without attachments to be imported.
	 *
	 * @return array
	 */
	private function import_logs_without_attachments( $logs ) {

		global $wpdb;

		$insert_schema         = $this->get_insert_schema();
		$insert_schema_keys    = array_keys( $insert_schema );
		$insert_schema_val_str = implode( ',', $insert_schema );

		/*
		 * This line basically does the following:
		 * 1. Get the columns (`$insert_schema_keys`) where new values will be inserted.
		 * 2. Make sure the columns are escaped properly for SQL - `array_map( 'esc_sql', $insert_schema_keys )`.
		 * 3. Convert the escaped columns from array to string using `implode()`.
		 * 4. Create the initial "INSERT" statement.
		 */
		$insert_sql = 'INSERT INTO ' . esc_sql( Logs::get_table_name() ) . ' (' . implode( ',', array_map( 'esc_sql', $insert_schema_keys ) ) . ') VALUES ';

		$logs_counter    = count( $logs );
		$logs_last_index = $logs_counter - 1;

		// Keep track of the last imported WP Mail Logging `mail_id`.
		$last_imported_mail_id = 0;

		for ( $counter = 0; $counter <= $logs_last_index; $counter++ ) {

			$wp_mail_smtp_log_array = $this->convert_to_wp_mail_smtp_log( $logs[ $counter ] );

			if ( empty( $wp_mail_smtp_log_array ) ) {
				continue;
			}

			// Make sure that `$wp_mail_smtp_log_array` has the same keys with the `$insert_schema`.
			if ( $insert_schema_keys !== array_keys( $wp_mail_smtp_log_array ) ) {
				continue;
			}

			$insert_sql .= $wpdb->prepare(
				'(' . $insert_schema_val_str . ')', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				array_values( $wp_mail_smtp_log_array )
			);

			if ( $counter !== $logs_last_index ) {
				$insert_sql .= ',';
			}

			$last_imported_mail_id = $logs[ $counter ]->mail_id;
		}

		$query = $wpdb->query( $insert_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( $query === false ) {
			return [
				'error' => $wpdb->last_error,
			];
		}

		return [
			'successful_import_count' => $query,
			'number_of_logs_fetched'  => $logs_counter,
			'last_imported_mail_id'   => $last_imported_mail_id,
		];
	}

	/**
	 * Returns the insert schema of the import.
	 *
	 * This is basically an array with the `wp_wpmailsmtp_emails_log` columns as keys with the
	 * prepare statement placeholder as the value.
	 *
	 * @since 3.8.0
	 *
	 * @return string[]
	 */
	private function get_insert_schema() {

		return [
			'message_id'     => '%s',
			'subject'        => '%s',
			'people'         => '%s',
			'headers'        => '%s',
			'error_text'     => '%s',
			'content_plain'  => '%s',
			'content_html'   => '%s',
			'status'         => '%s',
			'date_sent'      => '%s',
			'mailer'         => '%s',
			'attachments'    => '%d',
			'initiator_name' => '%s',
			'initiator_file' => '%s',
			'parent_id'      => '%d',
		];
	}

	/**
	 * Returns an array which is converted log from WP Mail Logging to WP Mail SMTP Log.
	 *
	 * @since 3.8.0
	 *
	 * @param object $log Log from WP Mail Logging to import.
	 *
	 * @return array
	 */
	private function convert_to_wp_mail_smtp_log( $log ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		try {
			$email = new Email();

			$email->set_subject( $log->subject );

			if ( ! empty( $log->headers ) ) {
				$email->set_headers( $this->prepare_headers( $log->headers ) );
			}

			if ( ! empty( $log->error ) ) {
				$email->set_error_text( $log->error );
				// If there's an error, we assume that the email was not sucessfully sent.
				$email->set_status( Email::STATUS_UNSENT );
			} else {
				$email->set_status( Email::STATUS_SENT );
			}

			if ( Options::init()->get( 'logs', 'log_email_content' ) && ! empty( $log->message ) ) {
				$email->set_content_html( $log->message );
			}

			if ( ! empty( $log->timestamp ) ) {
				$email->set_date_sent( $log->timestamp );
			}

			$email->set_mailer( $this->get_mailer() );
			$email->set_initiator_name( $this->get_initiator_name() );

			if ( ! empty( $log->receiver ) ) {
				$email->set_people( $this->prepare_people_data( $log->receiver, $email->get_header( 'From' ) ) );
			}

			return [
				'message_id'     => $email->get_message_id(),
				'subject'        => $email->get_subject(),
				'people'         => wp_json_encode( $email->get_people() ),
				'headers'        => $email->get_headers(),
				'error_text'     => $email->get_error_text(),
				'content_plain'  => $email->get_content_plain(),
				'content_html'   => $email->get_content_html(),
				'status'         => $email->get_status(),
				'date_sent'      => $email->get_date_sent()->format( WP::datetime_mysql_format() ),
				'mailer'         => $email->get_mailer(),
				'attachments'    => $email->get_attachments(),
				'initiator_name' => $email->get_initiator_name(),
				'initiator_file' => $email->get_initiator_file(),
				'parent_id'      => $email->get_parent_id(),
			];

		} catch ( Exception $e ) {
			return [];
		}
	}

	/**
	 * Convert the `receiver` data from WP Mail Logging to an array compliant for
	 * import to WP Mail SMTP `people` column.
	 *
	 * @since 3.8.0
	 *
	 * @param string $receiver Data from `receiver` column from WP Mail Logging DB.
	 * @param string $from     From data from the headers.
	 *
	 * @return array
	 */
	private function prepare_people_data( $receiver, $from ) {

		$receiver_arr = explode( ',', $receiver );

		$to = [];

		foreach ( $receiver_arr as $re ) {
			$to[] = trim( str_replace( "\\n", '', $re ) );
		}

		$return = [];

		if ( ! empty( $to ) ) {
			$return['to'] = $to;
		}

		if ( empty( $from ) ) {
			return $return;
		}

		$matches = null;

		if ( preg_match( '/<(.*?)>/', $from, $matches ) ) {
			$from_email = sanitize_email( $matches[1] );

			if ( ! empty( $from_email ) ) {
				$return['from'] = $from_email;
			}
		}

		return $return;
	}

	/**
	 * Convert the `headers` data from WP Mail Logging to an array compliant for
	 * import to WP Mail SMTP `headers` column.
	 *
	 * @since 3.8.0
	 *
	 * @param string $header_to_import Data from `headers` column from WP Mail Logging DB.
	 *
	 * @return false|string[]
	 */
	private function prepare_headers( $header_to_import ) {

		return explode( "\n", $header_to_import );
	}

	/**
	 * Get the mailer to be used in this import.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_mailer() {

		/**
		 * Filters the mailer to be used in WP Mail Logging import.
		 *
		 * @since 3.8.0
		 *
		 * @param string $mailer Mailer to be used in this import.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_emails_logs_importers_wp_mail_logging_importer_mailer',
			'mail'
		);
	}

	/**
	 * Get the initiator name to be used in this import.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_initiator_name() {

		/**
		 * Filters the initiator name to be used in WP Mail Logging import.
		 *
		 * @since 3.8.0
		 *
		 * @param string $initiator_name Initiator name to be used in this import.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_emails_logs_importers_wp_mail_logging_importer_initiator_name',
			'WP Mail Logging'
		);
	}

	/**
	 * Start importing logs with attachments.
	 *
	 * @since 3.8.0
	 *
	 * @return array Array containing import operation information.
	 */
	private function start_import_logs_with_attachments() {

		$logs = $this->get_logs( $this->get_number_of_logs_with_attachments_to_import(), true );

		if ( empty( $logs ) ) {
			$this->update_import_status( self::STATUS_IMPORT_COMPLETED );

			return [
				'successful_import_count' => 0,
			];
		}

		return $this->import_logs_with_attachments( $logs );
	}

	/**
	 * Get the number of logs with attachments to import per request.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_number_of_logs_with_attachments_to_import() {

		/**
		 * Filters the number of logs with attachments to import per request.
		 *
		 * @since 3.8.0
		 *
		 * @param int $count Number of logs with attachments to import per request.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_emails_logs_importers_wp_mail_logging_importer_count_with_attachments',
			self::DEFAULT_NUMBER_OF_LOGS_WITH_ATTACHMENTS_TO_IMPORT_PER_REQUEST
		);
	}

	/**
	 * Import logs with attachments.
	 *
	 * @since 3.8.0
	 *
	 * @param array $logs Logs to import.
	 *
	 * @return array Array containing import information.
	 */
	private function import_logs_with_attachments( $logs ) {

		$successful_import_count = 0;
		$failed_import_count     = 0;
		$failed_attachment_count = 0;

		global $wpdb;

		$insert_schema       = $this->get_insert_schema();
		$insert_placeholders = array_values( $insert_schema );

		foreach ( $logs as $log ) {

			$wp_mail_smtp_log_array = $this->convert_to_wp_mail_smtp_log( $log );

			$insert = $wpdb->insert(
				Logs::get_table_name(),
				$wp_mail_smtp_log_array,
				$insert_placeholders
			);

			// Whether the import succeeds or not, we update the `last_imported_mail_id` to progress the import operation.
			$this->save_options(
				[
					'status'                => self::STATUS_IMPORT_ATTACHMENTS,
					'last_imported_mail_id' => $log->mail_id,
				]
			);

			if ( $insert === false ) {
				$failed_import_count++;

				continue;
			}

			if ( ! empty( $log->attachments ) && wp_mail_smtp()->get_pro()->get_logs()->is_enabled_save_attachments() && ! $this->handle_attachments( $log->attachments, $wpdb->insert_id ) ) {
				$failed_attachment_count++;
			}

			// Update the successful import count.
			$successful_import_count++;
		}

		if ( count( $logs ) < $this->get_number_of_logs_with_attachments_to_import() ) {
			$this->update_import_status( self::STATUS_IMPORT_COMPLETED );
		}

		return [
			'failed_import_count'     => $failed_import_count,
			'failed_attachment_count' => $failed_attachment_count,
			'successful_import_count' => $successful_import_count,
		];
	}

	/**
	 * Handle the attachments of the imported log.
	 *
	 * @since 3.8.0
	 *
	 * @param string $attachments         Path of the attachments separated by comma.
	 * @param int    $wp_mail_smtp_log_id The WP Mail SMTP Log ID.
	 *
	 * @return bool Whether all the attachments are migrated or not.
	 */
	private function handle_attachments( $attachments, $wp_mail_smtp_log_id ) {

		$upload_dir      = wp_upload_dir();
		$attachments_arr = explode( ',', $attachments );

		if ( empty( $attachments_arr ) || empty( $upload_dir['basedir'] ) ) {
			return false;
		}

		$successful_attached_ctr = 0;

		foreach ( $attachments_arr as $attachment ) {

			$attachment_obj  = new Attachment();
			$trim_attachment = trim( $attachment, '\n' );

			if ( $attachment_obj->add( $upload_dir['basedir'] . $this->leadingslashit( $trim_attachment ), $wp_mail_smtp_log_id ) ) {
				$successful_attached_ctr++;
			}
		}

		if ( empty( $successful_attached_ctr ) ) {
			return false;
		}

		global $wpdb;

		$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			Logs::get_table_name(),
			[
				'attachments' => $successful_attached_ctr,
			],
			[
				'id' => $wp_mail_smtp_log_id,
			],
			'%d',
			'%d'
		);

		if ( $successful_attached_ctr !== count( $attachments_arr ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Prepends a slash to a string if it's not present.
	 *
	 * @since 3.8.0
	 *
	 * @param string $string String to prepend slash to.
	 *
	 * @return string
	 */
	private function leadingslashit( $string ) {

		return DIRECTORY_SEPARATOR . ltrim( $string, DIRECTORY_SEPARATOR );
	}

	/**
	 * Sends back JSON response to the import AJAX request.
	 *
	 * @since 3.8.0
	 *
	 * @param array $import_data   Array containing information regarding the import process.
	 * @param int   $import_status Import status.
	 *
	 * @return void
	 */
	private function send_ajax_response( $import_data, $import_status ) {

		if ( ! empty( $import_data['error'] ) ) {
			wp_send_json_error(
				[
					'continue'      => false,
					'error_message' => esc_html( $import_data['error'] ),
				]
			);
		}

		wp_send_json_success(
			[
				'failed_import_count'     => ! empty( $import_data['failed_import_count'] ) ? absint( $import_data['failed_import_count'] ) : 0,
				'failed_attachment_count' => ! empty( $import_data['failed_attachment_count'] ) ? absint( $import_data['failed_attachment_count'] ) : 0,
				'successful_import_count' => ! empty( $import_data['successful_import_count'] ) ? absint( $import_data['successful_import_count'] ) : 0,
				'continue'                => $import_status !== self::STATUS_IMPORT_COMPLETED,
			]
		);
	}

	/**
	 * Database table name where WP Mail Logging logs are stored.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . 'wpml_mails';
	}
}
