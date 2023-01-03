<?php
/**
 * Error_Handler class.
 *
 * @package Smush\Core
 * @version 3.12.0
 */

namespace Smush\Core;

use WP_Error;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Error_Handler {
	/**
	 * Ignore meta key.
	 */
	const IGNORE_KEY = 'wp-smush-ignore-bulk';

	/**
	 * Error meta key.
	 */
	const ERROR_KEY = 'wp-smush-error';

	/**
	 * Animated error code.
	 */
	const ANIMATED_ERROR_CODE = 'animated';

	/**
	 * Handled error codes.
	 *
	 * @var array
	 */
	private static $locked_error_codes = array(
		'ignored',
		// 'in_progress',
		// 'animated',
	);

	/**
	 * Skipped error codes.
	 *
	 * @var array
	 */
	private static $skipped_error_codes = array(
		'skipped_filter',
		'ignored',
		'animated',
		'size_limit',
		'size_pro_limit',
	);

	/**
	 * Should regenerate thumbnail error codes.
	 *
	 * @var array
	 */
	private static $regenerate_error_codes = array(
		'no_file_meta',
		'file_not_found',

	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Handle failed items.
		add_action( 'wp_smush_no_smushit', array( $this, 'maybe_flag_failed_item' ), 999, 2 );
		add_action( 'wp_smush_after_smush_file', array( $this, 'maybe_remove_failed_item_flag' ), 999, 3 );
		add_action( 'wp_smush_after_restore_backup', array( $this, 'remove_failed_item_flag_after_restore' ), 10, 3 );

		// When start BO, ignore all animated files of previous session.
		add_action( 'wp_smush_bulk_smush_start', array( $this, 'ignore_all_animate_files_on_start' ) );
	}

	/**
	 * Set a flag for failed item.
	 *
	 * @param int      $attachment_id  Attachment ID.
	 * @param WP_Error $errors         An instance of WP_Error.
	 */
	public function maybe_flag_failed_item( $attachment_id, $errors ) {
		$has_errors = $errors && is_wp_error( $errors ) && $errors->has_errors();
		if ( ! $has_errors ) {
			return;
		}

		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			// Do not flag error for non-image type.
			return;
		}
		self::set_flag_failed_item( $attachment_id, $errors->get_error_code() );
	}

	/**
	 * Maybe remove flag for failed item after compressing image successfully.
	 *
	 * @param int      $attachment_id  Attachment ID.
	 * @param array    $meta           Attachment meta.
	 * @param WP_Error $errors         An instance of WP_Error.
	 */
	public function maybe_remove_failed_item_flag( $attachment_id, $meta, $errors ) {
		$has_errors = $errors && is_wp_error( $errors ) && $errors->has_errors();
		if ( $has_errors ) {
			// Has errors, return.
			return;
		}
		self::remove_flag_failed_item( $attachment_id );
	}

	/**
	 * Remove flag failed item after restoring.
	 *
	 * @param bool   $restored         Is restored or not.
	 * @param string $backup_full_path Backup path.
	 * @param int    $attachment_id    Attachment ID.
	 */
	public function remove_failed_item_flag_after_restore( $restored, $backup_full_path, $attachment_id ) {
		if ( $restored ) {
			delete_post_meta( $attachment_id, self::ERROR_KEY );
		}
	}

	/**
	 * Ignore all animated files when start new background process.
	 *
	 * This will help us:
	 * 1. Do not show animated errors of previous processing after completing the process.
	 * 2. Keep ignore animated files the same behavior with the old version.
	 */
	public function ignore_all_animate_files_on_start() {
		global $wpdb;
		$animated_images = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_meta_error.post_id FROM $wpdb->postmeta as post_meta_error
				LEFT JOIN $wpdb->postmeta as post_meta_ignored ON post_meta_ignored.post_id = post_meta_error.post_id AND post_meta_ignored.meta_key= %s
				WHERE post_meta_ignored.meta_value IS NULL AND post_meta_error.meta_key = %s AND post_meta_error.meta_value = %s",
				self::IGNORE_KEY,
				self::ERROR_KEY,
				self::ANIMATED_ERROR_CODE
			)
		);
		if ( empty( $animated_images ) ) {
			return;
		}

		foreach ( $animated_images as $animated_image_id ) {
			if ( empty( $animated_image_id ) ) {
				continue;
			}
			update_post_meta( $animated_image_id, self::IGNORE_KEY, Core::STATUS_ANIMATED );
		}
	}

	/**
	 * Verify an animated file from error meta value.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return boolean
	 */
	public static function is_animated_file( $attachment_id ) {
		return self::ANIMATED_ERROR_CODE === self::get_error_code( $attachment_id );
	}

	/**
	 * Check if the image is ignored.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return mixed Ignored meta value or false if not found.
	 */
	public static function is_ignored( $attachment_id ) {
		$ignored = get_post_meta( $attachment_id, self::IGNORE_KEY, true );
		return ! empty( $ignored ) ? $ignored : false;
	}

	/**
	 * Check if there is error_code for skipped compression case.
	 *
	 * @param  string $error_code Error code.
	 * @return boolean
	 */
	public static function is_skipped_error_code( $error_code ) {
		return in_array( $error_code, self::$skipped_error_codes, true );
	}

	/**
	 * Check if should regenerate thumbnail.
	 *
	 * @param  string $error_code Error code.
	 * @return boolean
	 */
	public static function should_regenerate_thumbnail( $error_code ) {
		return in_array( $error_code, self::$regenerate_error_codes, true );
	}

	/**
	 * Set ignored flag for failed item.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $error_code Error code.
	 */
	public static function set_flag_failed_item( $attachment_id, $error_code ) {
		if ( in_array( $error_code, self::$locked_error_codes, true ) ) {
			return;
		}
		return update_post_meta( $attachment_id, self::ERROR_KEY, $error_code );
	}

	/**
	 * Get error code.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return null|string
	 */
	public static function get_error_code( $attachment_id ) {
		$error_code = get_post_meta( $attachment_id, self::ERROR_KEY, true );
		if ( 'size_limit' === $error_code && WP_Smush::is_pro() ) {
			return;
		}
		return $error_code;
	}

	/**
	 * Remove ignored flag for failed item.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return bool
	 */
	public static function remove_flag_failed_item( $attachment_id ) {
		if ( ! self::get_error_code( $attachment_id ) ) {
			return false;
		}
		return delete_post_meta( $attachment_id, self::ERROR_KEY );
	}

	/**
	 * Get error message.
	 *
	 * @param string $error_code Error code.
	 * @param int    $image_id   Attachment ID.
	 * @return string
	 */
	public static function get_error_message( $error_code, $image_id = 0 ) {
		$error_messages = self::get_default_error_messages();
		$error_message  = ! empty( $error_messages[ $error_code ] ) ? $error_messages[ $error_code ] : '';
		return self::format_error_message( $error_message, $error_code, $image_id );
	}

	/**
	 * Get sprintf error message.
	 *
	 * @param string $error_message Error message.
	 * @param string $error_code    Error code.
	 * @param int    $image_id      Attachment ID.
	 * @return string
	 */
	private static function format_error_message( $error_message, $error_code, $image_id ) {
		if ( empty( $image_id ) || empty( $error_message ) || false === strpos( $error_message, '%s' ) ) {
			return $error_message;
		}
		switch ( $error_code ) {
			case 'size_limit':
			case 'size_pro_limit':
				$size_exceeded = Helper::size_limit_exceeded( $image_id );
				if ( $size_exceeded ) {
					$error_message = sprintf( $error_message, size_format( $size_exceeded ) );
				} else {
					$error_message = null;
				}
				break;
			case 'not_writable':
				$file_path     = Helper::get_attached_file( $image_id );
				$error_message = sprintf( $error_message, Helper::clean_file_path( dirname( $file_path ) ) );
				break;
			case 'file_not_found':
				$file_path      = Helper::get_attached_file( $image_id );
				$file_not_found = $file_path;
				if ( file_exists( $file_path ) ) {
					// Try go get the not found thumbnail file.
					$all_file_sizes = wp_get_attachment_metadata( $image_id );
					$dir_path       = dirname( $file_path );
					if ( ! empty( $all_file_sizes['sizes'] ) ) {
						foreach ( $all_file_sizes['sizes'] as $size => $size_data ) {
							$size_file = $dir_path . '/' . $size_data['file'];
							if ( ! file_exists( $size_file ) ) {
								$file_not_found = $size_file;
								break;
							}
						}
					}
				}
				$error_message = sprintf( $error_message, basename( $file_not_found ) );
				break;
		}

		return $error_message;
	}

	/**
	 * Get error message on media lib.
	 *
	 * @param string $error_code Error code.
	 * @param int    $image_id   Attachment ID.
	 * @return string
	 */
	public static function get_error_message_for_media_library( $error_code, $image_id = 0 ) {
		$error_messages = self::get_error_messages_for_media_library();
		if ( empty( $error_messages[ $error_code ] ) ) {
			return self::get_error_message( $error_code, $image_id );
		}
		return self::format_error_message( $error_messages[ $error_code ], $error_code, $image_id );
	}

	/**
	 * Get last failed items.
	 *
	 * @param int $limit Query limit.
	 * @return array
	 */
	private static function get_last_failed_items( $limit ) {
		global $wpdb;
		// phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare(
			"SELECT DISTINCT post_meta_error.post_id, post_meta_error.meta_value FROM $wpdb->postmeta as post_meta_error
			LEFT JOIN $wpdb->postmeta as post_meta_ignore ON post_meta_ignore.post_id = post_meta_error.post_id AND post_meta_ignore.meta_key= %s
			WHERE post_meta_ignore.meta_value IS NULL AND post_meta_error.meta_key = %s
			ORDER BY post_meta_error.post_id DESC LIMIT %d;",
			self::IGNORE_KEY,
			self::ERROR_KEY,
			$limit
		);
		/**
		 * Due to performance, we do not join with table wp_posts to exclude the deleted attachments,
		 * leave a filter for third-party custom it.
		 */
		$query = apply_filters( 'wp_smush_query_get_failed_items', $query );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_results( $query );
	}

	/**
	 * Get latest errors.
	 *
	 * @param int $limit Limit number of errors to return.
	 * @return array
	 */
	public static function get_last_errors( $limit = 10 ) {
		$failed_items = self::get_last_failed_items( $limit );
		if ( empty( $failed_items ) ) {
			return array();
		}
		$errors = array();
		foreach ( $failed_items as $failed ) {
			$error_code    = $failed->meta_value;
			$image_id      = (int) $failed->post_id;
			$error_message = self::get_error_message( $error_code, $image_id );
			if ( $error_message ) {
				$error = self::get_error( $image_id, $error_code );
				if ( $error ) {
					// Add error.
					$errors[ $image_id ] = $error;
				}
			}
		}
		return $errors;
	}

	/**
	 * Get error info for log.
	 *
	 * @param int    $image_id   Attachment ID.
	 * @param string $error_code Error code.
	 * @return false|array
	 */
	public static function get_error( $image_id, $error_code ) {
		$error_message = self::get_error_message( $error_code, $image_id );
		if ( empty( $error_message ) ) {
			return false;
		}

		$thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$file_name = 'undefined';
		$file_link = false;
		if ( ! empty( $thumbnail[0] ) ) {
			$file_link = $thumbnail[0];
			$file_name = basename( str_replace( '-150x150.', '.', $file_link ) );
		}
		return array(
			'error_msg'  => $error_message,
			'thumbnail'  => $file_link,
			'file_link'  => Helper::get_image_media_link( $image_id, $file_name ),
			'error_code' => $error_code,
		);
	}

	/**
	 * Get CDN notice base on CDN status.
	 *
	 * @return string
	 */
	private static function get_cdn_notice_with_config_link() {
		$cdn = WP_Smush::get_instance()->core()->mod->cdn;
		if ( $cdn->get_status() ) {
			return '<span class="smush-cdn-notice">' . esc_html__( 'GIFs are serving from global CDN', 'wp-smushit' ) . '</span>';
		}
		$cdn_link = Helper::get_page_url( 'smush-cdn' );
		/* translators: %1$s : Open a link %2$s Close the link */
		return '<span class="smush-cdn-notice">' . sprintf( esc_html__( '%1$sEnable CDN%2$s to serve GIFs closer and faster to visitors', 'wp-smushit' ), '<a href="' . esc_url( $cdn_link ) . '" target="_blank">', '</a>' ) . '</span>';
	}

	/**
	 * Generate utm link base on error code.
	 *
	 * @param string $error_code Error code.
	 *
	 * @return null|string
	 */
	public static function get_utm_msg( $error_code ) {
		if ( WP_Smush::is_pro() ) {
			if ( self::ANIMATED_ERROR_CODE !== $error_code ) {
				return;
			}
			return self::get_cdn_notice_with_config_link();
		}
		$utm_error_msg = array(
			'animated'   => __( 'Try Pro to Serve GIFs with our global CDN.', 'wp-smushit' ),
			'size_limit' => __( 'Try Pro to Smush larger images.', 'wp-smushit' ),
		);
		if ( ! isset( $utm_error_msg[ $error_code ] ) ) {
			return;
		}

		$upgrade_url = 'https://wpmudev.com/project/wp-smush-pro/';
		$args        = array(
			'coupon'     => 'SMUSH30OFF',
			'checkout'   => 0,
			'utm_source' => 'smush',
			'utm_medium' => 'plugin',
		);

		switch ( $error_code ) {
			case 'animated':
				$args['utm_campaign'] = 'smush_bulksmush_library_gif_cdn';
				$utm_link             = add_query_arg( $args, $upgrade_url );
				break;
			case 'size_limit':
				$args['utm_campaign'] = 'smush_bulksmush_library_filesizelimit';
				$utm_link             = add_query_arg( $args, $upgrade_url );
				break;
		}

		if ( ! empty( $utm_link ) ) {
			return sprintf( '<a class="smush-upgrade-link" href="%s" target="_blank">%s</a>', esc_url( $utm_link ), esc_html( $utm_error_msg[ $error_code ] ) );
		}
	}

	/**
	 * Get error messages.
	 *
	 * @return array
	 */
	private static function get_default_error_messages() {
		return apply_filters(
			'wp_smush_error_messages',
			array(
				'missing_id'     => esc_html__( 'No attachment ID was received.', 'wp-smushit' ),
				'ignored'        => esc_html__( 'Skip ignored file.', 'wp-smushit' ),
				'animated'       => esc_html__( 'Skipped animated file.', 'wp-smushit' ),
				'in_progress'    => esc_html__( 'File processing is in progress.', 'wp-smushit' ),
				'no_file_meta'   => esc_html__( 'No file data found in image meta', 'wp-smushit' ),
				'skipped_filter' => esc_html__( 'Skipped with wp_smush_image filter', 'wp-smushit' ),
				'empty_path'     => esc_html__( 'File path is empty', 'wp-smushit' ),
				'empty_response' => esc_html__( 'Webp no response was received.', 'wp-smushit' ),
				'not_processed'  => esc_html__( 'Not processed', 'wp-smushit' ),
				/* translators: %s: image size */
				'size_limit'     => __( 'Skipped (%s), file size limit of 5mb exceeded', 'wp-smushit' ),
				/* translators: %s: image size */
				'size_pro_limit' => __( 'Skipped (%s), file size limit of 32mb exceeded', 'wp-smushit' ),
				/* translators: %s: Directory path */
				'not_writable'   => __( '%s is not writable', 'wp-smushit' ),
				/* translators: %s: File path */
				'file_not_found' => __( 'Skipped (%s), File not found.', 'wp-smushit' ),
			)
		);
	}

	/**
	 * Get error messages.=
	 *
	 * @return array
	 */
	private static function get_error_messages_for_media_library() {
		return apply_filters(
			'wp_smush_error_messages_on_library',
			array(
				'ignored'        => esc_html__( 'Ignored.', 'wp-smushit' ),
				'no_file_meta'   => esc_html__( 'No file data found in image meta. We recommend regenerating the thumbnails.', 'wp-smushit' ),
				/* translators: %s: File path */
				'file_not_found' => __( 'File (%s) not found, we recommend regenerating the thumbnails', 'wp-smushit' ),
			)
		);
	}
}
