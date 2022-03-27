<?php

namespace WPMailSMTP\Pro\Emails\Logs\Attachments;

use WPMailSMTP\Uploads;

/**
 * Email Log Attachment class.
 *
 * @since 2.9.0
 */
class Attachment {

	/**
	 * The Attachment ID.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The filename of the stored attachment.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $filename;

	/**
	 * The original filename of the stored attachment.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $original_filename;

	/**
	 * The attachment file path.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The attachment file url.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $url;

	/**
	 * The md5 hash of the attachment file.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $hash;

	/**
	 * The folder name of the stored attachment.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $folder_name;

	/**
	 * Attachments constructor.
	 *
	 * @since 2.9.0
	 *
	 * @param array $args The Attachment arguments.
	 */
	public function __construct( $args = [] ) {

		$this->prepare( $args );
	}

	/**
	 * Add the attachment:
	 * - copy file to the plugin's uploads folder, if the attachment file is new/unique,
	 * - save the file details to the DB, if the attachment file is new/unique,
	 * - connect the attachment file to the email log.
	 *
	 * @since 2.9.0
	 *
	 * @param string  $original_attachment      The original attachment file path or content.
	 * @param int     $email_log_id             The email log ID.
	 * @param string  $original_attachment_name The original attachment file name.
	 * @param boolean $is_string_attachment     Whether string attachment or not.
	 *
	 * @return bool
	 */
	public function add( $original_attachment, $email_log_id, $original_attachment_name = '', $is_string_attachment = false ) {

		$original_attachment_content = $this->get_attachment_file_content( $original_attachment, $is_string_attachment );

		if ( $original_attachment_content === false ) {
			return false;
		}

		$this->hash = sanitize_key( md5( $original_attachment_content ) );

		if ( empty( $this->hash ) ) {
			return false;
		}

		if ( ! $is_string_attachment && $original_attachment_name === '' ) {
			$original_attachment_name = wp_basename( $original_attachment );
		}

		$original_attachment_name = sanitize_file_name( $original_attachment_name );
		$this->original_filename  = $original_attachment_name;

		$existing_attachment = $this->attachment_exists( $this->hash );

		if ( empty( $existing_attachment ) ) {
			$this->path = $this->store_file( $original_attachment_content, $original_attachment_name );
			$this->id   = $this->create( $this->path, $this->hash );
		} else {
			$this->path = $existing_attachment['path'];
			$this->id   = $existing_attachment['id'];
		}

		if ( empty( $this->path ) || empty( $this->id ) ) {
			return false;
		}

		return $this->connect_to_email_log( $email_log_id, $this->id, $original_attachment_name );
	}

	/**
	 * Get the attachment ID.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_id() {

		return $this->id;
	}

	/**
	 * Get the attachment URL.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_url() {

		return $this->url;
	}

	/**
	 * Get the attachment file path.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_path() {

		return $this->path;
	}

	/**
	 * Get the attachment filename.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_filename() {

		return ! empty( $this->original_filename ) ? $this->original_filename : $this->filename;
	}

	/**
	 * Get the dashicon icon for the attachment, based on the extension.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_icon() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		if ( empty( $this->path ) ) {
			return 'dashicons-media-default';
		}

		$extension = pathinfo( $this->path, PATHINFO_EXTENSION );

		switch ( $extension ) {
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'gif':
			case 'tiff':
				return 'dashicons-format-image';

			case 'pdf':
				return 'wp-mail-smtp-dashicons-pdf-gray';

			case 'zip':
			case 'tar':
			case 'gz':
			case 'rar':
			case '7z':
				return 'dashicons-media-archive';

			default:
				return 'dashicons-media-default';
		}
	}

	/**
	 * Get the attachment's file content.
	 *
	 * @since 3.1.0
	 *
	 * @param string  $attachment           The attachment file path or content.
	 * @param boolean $is_string_attachment Whether string attachment or not.
	 *
	 * @return string|false
	 */
	protected function get_attachment_file_content( $attachment, $is_string_attachment ) {

		if ( ! $is_string_attachment ) {
			if ( ! file_exists( $attachment ) ) {
				return false;
			}

			$attachment = file_get_contents( $attachment );
		}

		return $attachment;
	}

	/**
	 * Store the original attachment to the plugin's uploads folder.
	 *
	 * @since 2.9.0
	 *
	 * @param string $original_file_content The attachment's original file content.
	 * @param string $original_filename     The attachment's original file name.
	 *
	 * @return false|string
	 */
	protected function store_file( $original_file_content, $original_filename ) {

		$this->folder_name = sanitize_key( uniqid() );

		$upload_folder = Attachments::get_root_uploads_directory() . $this->folder_name;

		if ( ! is_dir( $upload_folder ) ) {
			wp_mkdir_p( $upload_folder );

			// Check if the .htaccess exists in the root upload directory, if not - create it.
			Uploads::create_upload_dir_htaccess_file();

			// Check if the index.html exists in the directories, if not - create them.
			Uploads::create_index_html_file( Uploads::upload_dir()['path'] );
			Uploads::create_index_html_file( Attachments::get_root_uploads_directory() );
		}

		$file_extension = pathinfo( $original_filename, PATHINFO_EXTENSION );
		$this->filename = wp_unique_filename( $upload_folder, wp_generate_password( 32, false, false ) . '.' . $file_extension );
		$upload_path    = trailingslashit( $upload_folder ) . $this->filename;

		if ( file_put_contents( $upload_path, $original_file_content ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			$this->path = $upload_path;

			return $upload_path;
		}

		return false;
	}

	/**
	 * Check if the attachment file already exists.
	 *
	 * @since 2.9.0
	 *
	 * @param string $file_hash The md5 hash of the file content.
	 *
	 * @return array|false
	 */
	protected function attachment_exists( $file_hash ) {

		global $wpdb;

		$file_db_table = Attachments::get_attachment_files_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, folder, filename FROM {$file_db_table} WHERE hash = %s",
				$file_hash
			),
			ARRAY_A
		);
		// phpcs:enable

		if ( empty( $result ) ) {
			return false;
		}

		$result['path'] = $this->build_attachment_path( $result['folder'], $result['filename'] );
		$result['url']  = $this->build_attachment_url( $result['folder'], $result['filename'] );

		return $result;
	}

	/**
	 * Create the attachment file in the DB.
	 *
	 * @since 2.9.0
	 *
	 * @param string $attachment_path The attachment file path.
	 * @param string $file_hash       The md5 hash of the attachment file content.
	 *
	 * @return int|false
	 */
	protected function create( $attachment_path, $file_hash ) {

		global $wpdb;

		$file_db_table = Attachments::get_attachment_files_table_name();

		$result = $wpdb->insert(
			$file_db_table,
			[
				'hash'     => sanitize_key( $file_hash ),
				'folder'   => sanitize_key( $this->folder_name ),
				'filename' => sanitize_file_name( wp_basename( $attachment_path ) ),
			],
			'%s'
		);

		if ( empty( $result ) ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Attach the attachment to the email log.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $email_log_id             The Email Log ID.
	 * @param int    $attachment_id            The Attachment ID.
	 * @param string $original_attachment_name The original attachment file name.
	 *
	 * @return bool
	 */
	protected function connect_to_email_log( $email_log_id, $attachment_id, $original_attachment_name ) {

		global $wpdb;

		$attachments_db_table = Attachments::get_email_attachments_table_name();

		$result = $wpdb->insert(
			$attachments_db_table,
			[
				'email_log_id'  => intval( $email_log_id ),
				'attachment_id' => intval( $attachment_id ),
				'filename'      => sanitize_file_name( $original_attachment_name ),
			],
			[
				'%d',
				'%d',
				'%s',
			]
		);

		return (bool) $result;
	}

	/**
	 * Prepare the Attachment object with passed parameters.
	 *
	 * @since 2.9.0
	 *
	 * @param array $args The array of attachment arguments.
	 */
	protected function prepare( $args ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( ! empty( $args['id'] ) ) {
			$this->id = $args['id'];
		}

		if ( ! empty( $args['hash'] ) ) {
			$this->hash = $args['hash'];
		}

		if ( ! empty( $args['folder'] ) ) {
			$this->folder_name = $args['folder'];
		}

		if ( ! empty( $args['filename'] ) ) {
			$this->filename = $args['filename'];
		}

		if ( ! empty( $args['original_filename'] ) ) {
			$this->original_filename = $args['original_filename'];
		}

		if ( ! empty( $this->folder_name ) && ! empty( $this->filename ) ) {
			$this->path = $this->build_attachment_path( $this->folder_name, $this->filename );
			$this->url  = $this->build_attachment_url( $this->folder_name, $this->filename );
		}
	}

	/**
	 * Build the stored attachment file path from the folder and filename.
	 *
	 * @since 2.9.0
	 *
	 * @param string $folder   The folder where the attachment is stored in.
	 * @param string $filename The filename of the file.
	 *
	 * @return string
	 */
	private function build_attachment_path( $folder, $filename ) {

		$upload_folder = Attachments::get_root_uploads_directory();

		return trailingslashit( $upload_folder . sanitize_key( $folder ) ) . sanitize_file_name( $filename );
	}

	/**
	 * Build the stored attachment file URL from the folder and filename.
	 *
	 * @since 2.9.0
	 *
	 * @param string $folder   The folder where the attachment is stored in.
	 * @param string $filename The filename of the file.
	 *
	 * @return string
	 */
	private function build_attachment_url( $folder, $filename ) {

		$upload_folder_url = Attachments::get_root_uploads_url();

		return trailingslashit( $upload_folder_url . sanitize_key( $folder ) ) . sanitize_file_name( $filename );
	}
}
