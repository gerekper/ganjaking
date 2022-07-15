<?php

namespace WPMailSMTP\Pro\Providers\Outlook;

use WP_Error;
use WPMailSMTP\WP;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Providers\MailerAbstract;

/**
 * Class AttachmentsUploader implements attachments upload to Outlook.
 *
 * @since 3.4.0
 */
class AttachmentsUploader {

	/**
	 * Large attachment min file size.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	const LARGE_ATTACHMENT_MIN_FILE_SIZE = 1048576 * 2; // 2 MB.

	/**
	 * Large attachment upload chunk size.
	 *
	 * Microsoft states this requirement: Use a fragment size that is a multiple of 320 KiB (320 * 1024 bytes).
	 * Failing to use a fragment size that is a multiple of 320 KiB can result in large file transfers failing after the
	 * last fragment is uploaded (this is a detail imposed by Microsoft's OneDrive server-side implementation).
	 * Optimal chunk size is 5-10MiB.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	const LARGE_ATTACHMENT_CHUNK_SIZE = ( 320 * 1024 * 3 ) * 6; // Approximately 6 MB.

	/**
	 * The Mailer object.
	 *
	 * @since 3.4.0
	 *
	 * @var MailerAbstract
	 */
	private $mailer;

	/**
	 * Large file upload progress.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	private $large_file_upload_progress;

	/**
	 * Attachment file size.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	private $file_size;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 *
	 * @param MailerAbstract $mailer The Mailer object.
	 */
	public function __construct( MailerAbstract $mailer ) {

		$this->mailer = $mailer;
	}

	/**
	 * Upload attachment.
	 *
	 * @since 3.4.0
	 *
	 * @param array  $attachment PHPMailer attachment array.
	 * @param string $message_id Outlook message ID.
	 *
	 * @return true|WP_Error
	 */
	public function upload( $attachment, $message_id ) {

		$this->file_size = $this->mailer->get_attachment_file_size( $attachment );

		if ( $this->file_size === false ) {
			return new WP_Error( 'file-size', __( 'The attachment file size can\'t be calculated.', 'wp-mail-smtp-pro' ) );
		}

		if ( $this->file_size >= self::LARGE_ATTACHMENT_MIN_FILE_SIZE ) {
			return $this->upload_large_attachment( $attachment, $message_id );
		} else {
			return $this->upload_regular_attachment( $attachment, $message_id );
		}
	}

	/**
	 * Upload regular attachment. Less than 2MB.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/message-post-attachments
	 *
	 * @since 3.4.0
	 *
	 * @param array  $attachment PHPMailer attachment array.
	 * @param string $message_id Outlook message ID.
	 *
	 * @return true|WP_Error
	 */
	private function upload_regular_attachment( $attachment, $message_id ) {

		$file = $this->mailer->get_attachment_file_content( $attachment );

		if ( $file === false ) {
			return new WP_Error( 'not-found', __( 'Attachment file not found.', 'wp-mail-smtp-pro' ) );
		}

		$response = $this->mailer->remote_request(
			"https://graph.microsoft.com/v1.0/me/messages/{$message_id}/attachments",
			[
				'headers' => $this->mailer->get_headers(),
				'body'    => wp_json_encode(
					[
						'@odata.type'  => '#microsoft.graph.fileAttachment',
						'name'         => $this->mailer->get_attachment_file_name( $attachment ),
						// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
						'contentBytes' => base64_encode( $file ),
						'contentType'  => $attachment[4],
					]
				),
			]
		);

		if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
			return $this->get_response_error( $response, __( 'Regular attachment upload failed.', 'wp-mail-smtp-pro' ) );
		}

		return true;
	}

	/**
	 * Upload large attachment.
	 * Minimum file size 2MB.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/outlook-large-attachments
	 *
	 * @since 3.4.0
	 *
	 * @param array  $attachment PHPMailer attachment array.
	 * @param string $message_id Outlook message ID.
	 *
	 * @return true|WP_Error
	 */
	private function upload_large_attachment( $attachment, $message_id ) {

		// Reset upload progress.
		$this->large_file_upload_progress = 0;

		// Create upload session.
		$upload_url = $this->create_upload_session( $attachment, $message_id );

		if ( is_wp_error( $upload_url ) ) {
			return $upload_url;
		}

		/*
		 * Upload the various chunks.
		 * $result will be false until the process is complete.
		 */
		$result = false;

		// If there is a string attachment, create a file in a memory from the string for further processing.
		if ( $attachment[5] === true ) {

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
			$handle = fopen( 'php://memory', 'r+b' );

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
			fwrite( $handle, $attachment[0] );
			rewind( $handle );
		} else {

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
			$handle = fopen( $attachment[0], 'rb' );
		}

		while ( $result === false && ! feof( $handle ) ) {

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread
			$chunk  = fread( $handle, self::LARGE_ATTACHMENT_CHUNK_SIZE );
			$result = $this->next_chunk( $upload_url, $chunk );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
		fclose( $handle );

		if ( $result === false ) {
			return new WP_Error( 'unknown', __( 'Large attachment upload failed.', 'wp-mail-smtp-pro' ) );
		}

		return $result;
	}

	/**
	 * Create upload session for upload large attachment.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/attachment-createuploadsession
	 *
	 * @since 3.4.0
	 *
	 * @param array  $attachment PHPMailer attachment array.
	 * @param string $message_id Outlook message ID.
	 */
	private function create_upload_session( $attachment, $message_id ) {

		$response = $this->mailer->remote_request(
			"https://graph.microsoft.com/v1.0/me/messages/{$message_id}/attachments/createUploadSession",
			[
				'headers' => $this->mailer->get_headers(),
				'body'    => wp_json_encode(
					[
						'AttachmentItem' => [
							'attachmentType' => 'file',
							'contentType'    => $attachment[4],
							'name'           => $this->mailer->get_attachment_file_name( $attachment ),
							'size'           => $this->file_size,
						],
					]
				),
			]
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 201 ) {
			return $this->get_response_error( $response, __( 'Upload session creation failed.', 'wp-mail-smtp-pro' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['uploadUrl'] ) ) {
			return $this->get_response_error( $response, __( 'Upload session creation failed. The response is invalid, missing the "uploadUrl" field.', 'wp-mail-smtp-pro' ) );
		}

		return $body['uploadUrl'];
	}

	/**
	 * Upload large file chunk.
	 *
	 * @since 3.4.0
	 *
	 * @param string $upload_url Upload URL.
	 * @param string $chunk      File binary chunk.
	 *
	 * @return bool|WP_Error Returns false when upload not complete yet.
	 */
	private function next_chunk( $upload_url, $chunk ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$chunk_size         = Helpers::strsize( $chunk );
		$last_byte_position = $this->large_file_upload_progress + $chunk_size - 1;
		$timeout            = (int) ini_get( 'max_execution_time' );

		// We shouldn't send the Authorization header here as a temp auth token is included in the upload URL.
		$response = $this->mailer->remote_request(
			$upload_url,
			[
				'method'  => 'PUT',
				'timeout' => $timeout ? $timeout : 30,
				'headers' => [
					'content-range'  => "bytes $this->large_file_upload_progress-$last_byte_position/$this->file_size",
					'content-length' => $chunk_size,
					'content-type'   => 'application/octet-stream',
				],
				'body'    => $chunk,
			]
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		// A 404 code indicates that the upload session no longer exists.
		if ( $response_code === 404 ) {
			return $this->get_response_error( $response, __( 'Upload URL has expired.', 'wp-mail-smtp-pro' ) );
		}

		// If we have uploaded the last chunk, we should receive a 201 Created response code.
		if ( $last_byte_position === ( $this->file_size - 1 ) ) {

			// If we received a 201 Created response, the upload is complete, return true.
			if ( $response_code === 201 ) {
				return true;
			}

			return $this->get_response_error( $response, __( 'Last part of the file upload failed.', 'wp-mail-smtp-pro' ) );
		}

		// If we didn't receive a 200 Accepted response from the Graph API, something has gone wrong.
		if ( $response_code !== 200 ) {
			return $this->get_response_error( $response, __( 'File chunk upload failed.', 'wp-mail-smtp-pro' ) );
		}

		/**
		 * If we received a 200 Accepted response, it will include a nextExpectedRanges key, which will tell
		 * us the next range we'll upload.
		 */
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['nextExpectedRanges'] ) ) {
			return $this->get_response_error( $response, __( 'File chunk upload failed. The response is invalid, missing the "nextExpectedRanges" field.', 'wp-mail-smtp-pro' ) );
		}

		$next_expected_ranges = $body['nextExpectedRanges'];
		$next_range           = $next_expected_ranges[0];

		$this->large_file_upload_progress = $next_range;

		// Upload not complete yet, return false.
		return false;
	}

	/**
	 * Retrieve Outlook response error.
	 *
	 * @since 3.4.0
	 *
	 * @param array  $response Response array.
	 * @param string $message  Context based message.
	 *
	 * @return WP_Error
	 */
	private function get_response_error( $response, $message = false ) {

		$body = wp_remote_retrieve_body( $response );

		if ( $body && WP::is_json( $body ) ) {
			$body = json_decode( $body );
		}

		if ( ! empty( $body->error->message ) ) {
			$message = $body->error->message;
			$code    = ! empty( $body->error->code ) ? $body->error->code : '';

			$error_text = Helpers::format_error_message( $message, $code );
		} else {
			$error_text = WP::wp_remote_get_response_error_message( $response );
		}

		if ( $message ) {
			$error_text = $message . WP::EOL . $error_text;
		}

		return new WP_Error( wp_remote_retrieve_response_code( $response ), $error_text );
	}
}
