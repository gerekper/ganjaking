<?php
/**
 * Error_Handler class.
 *
 * @package Smush\Core
 * @version 3.12.0
 */

namespace Smush\Core;

use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Stats\Global_Stats;
use WP_Error;

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

	public static function get_all_failed_images() {
		$media_item_error_ids   = Global_Stats::get()->get_error_list()->get_ids();
		$optimization_error_ids = self::get_last_optimization_error_ids( PHP_INT_MAX );
		return array_unique( array_merge( $media_item_error_ids, $optimization_error_ids ) );
	}

	/**
	 * Get last optimization errors.
	 *
	 * @param int $limit Query limit.
	 * @return array
	 */
	private static function get_last_optimization_error_ids( $limit ) {
		global $wpdb;
		// phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare(
			"SELECT DISTINCT post_meta_error.post_id FROM $wpdb->postmeta as post_meta_error
			LEFT JOIN $wpdb->postmeta as post_meta_ignore ON post_meta_ignore.post_id = post_meta_error.post_id AND post_meta_ignore.meta_key= %s
			WHERE post_meta_ignore.meta_value IS NULL AND post_meta_error.meta_key = %s
			ORDER BY post_meta_error.post_id DESC LIMIT %d;",
			Media_Item::IGNORED_META_KEY,
			Media_Item_Optimizer::ERROR_META_KEY,
			$limit
		);
		/**
		 * Due to performance, we do not join with table wp_posts to exclude the deleted attachments,
		 * leave a filter for third-party custom it.
		 */
		$query = apply_filters( 'wp_smush_query_get_last_optimize_errors', $query );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_col( $query );
	}

	private static function get_last_media_item_errors( $limit ) {
		$error_list_ids = Global_Stats::get()->get_error_list()->get_ids();
		$last_errors    = array();
		if ( empty( $error_list_ids ) ) {
			return $last_errors;
		}
		foreach( $error_list_ids as $attachment_id ) {
			$media_item        = Media_Item_Cache::get_instance()->get( $attachment_id );
			if ( ! $media_item->has_errors() ) {
				continue;
			}

			$last_errors[ $attachment_id ] = self::get_error( $media_item->get_errors(), $media_item );

			if ( count( $last_errors ) >= $limit ) {
				break;
			}
		}

		return $last_errors;
	}

	private static function get_last_optimize_errors( $limit ) {
		$last_errors_ids = self::get_last_optimization_error_ids( $limit );
		$last_errors = array();
		if ( empty( $last_errors_ids ) ) {
			return $last_errors;
		}

		foreach( $last_errors_ids as $attachment_id ) {
			$media_item = Media_Item_Cache::get_instance()->get( $attachment_id );
			$optimizer  = new Media_Item_Optimizer($media_item);

			if( ! $optimizer->has_errors() ) {
				continue;
			}

			$last_errors[ $attachment_id ] = self::get_error( $optimizer->get_errors(), $media_item );

			if ( count( $last_errors ) >= $limit ) {
				break;
			}
		}

		return $last_errors;
	}

	/**
	 * Get latest errors.
	 *
	 * @param int $limit Limit number of errors to return.
	 * @return array
	 */
	public static function get_last_errors( $limit = 10 ) {
		$last_errors    = self::get_last_media_item_errors( $limit );
		$no_item_errors = count( $last_errors );
		if ( $no_item_errors >= $limit ) {
			return $last_errors;
		}

		$optimize_errors = self::get_last_optimize_errors( $limit - $no_item_errors );

		return $last_errors + $optimize_errors;
	}

	/**
	 * @return array
	 */
	public static function get_error( WP_Error $errors, Media_Item $media_item ) {
		$thumbnail = $media_item->get_size('thumbnail' );
		return array(
			'error_code'    => $errors->get_error_code(),
			'error_message' => $errors->get_error_message(),
			'file_name'     => $media_item->get_scaled_or_full_size()->get_file_name(),
			'thumbnail'     => $thumbnail ? $thumbnail->get_file_url() : false,
		);
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
				'size_pro_limit' => __( 'Skipped (%s), file size limit of 256mb exceeded', 'wp-smushit' ),
				/* translators: %s: Directory path */
				'not_writable'   => __( '%s is not writable', 'wp-smushit' ),
				/* translators: %s: File path */
				'file_not_found' => __( 'Skipped (%s), File not found.', 'wp-smushit' ),
			)
		);
	}
}