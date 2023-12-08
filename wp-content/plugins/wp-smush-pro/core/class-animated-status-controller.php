<?php

namespace Smush\Core;

use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Stats\Global_Stats;

/**
 * The method {@see Media_Item::is_animated} is intentionally naive because we don't want to check file contents on the fly.
 * This controller is responsible for the expensive file content check at the right times.
 */
class Animated_Status_Controller extends Controller {
	private $media_item_cache;
	/**
	 * @var \WDEV_Logger|null
	 */
	private $logger;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;

	public function __construct() {
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->global_stats     = Global_Stats::get();
		$this->logger           = Helper::logger();

		$this->register_filter( 'wp_smush_scan_library_slice_handle_attachment', array(
			$this,
			'maybe_update_animated_status_during_scan',
		), 10, 2 );
		$this->register_action( 'wp_smush_after_attachment_upload', array(
			$this,
			'maybe_update_animated_status_on_upload',
		) );
		$this->register_action( 'wp_smush_before_smush_attempt', array(
			$this,
			'maybe_update_animated_status_before_optimization',
		) );
	}

	public function maybe_update_animated_status_during_scan( $slice_data, $attachment_id ) {
		$this->maybe_update_animated_status( $attachment_id );

		return $slice_data;
	}

	public function maybe_update_animated_status_on_upload( $attachment_id ) {
		$this->maybe_update_animated_status( $attachment_id );
	}

	/**
	 * TODO: add test
	 *
	 * @param $attachment_id
	 *
	 * @return void
	 */
	public function maybe_update_animated_status_before_optimization( $attachment_id ) {
		$this->maybe_update_animated_status( $attachment_id );
	}

	private function maybe_update_animated_status( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			$this->logger->error( 'Tried to check animated value but encountered an problem with the media item' );

			return;
		}

		if ( apply_filters( 'wp_smush_skip_image_animation_check', false, $attachment_id ) ) {
			// The image is explicitly excluded from the animation check
			return;
		}

		if ( $media_item->animated_meta_exists() ) {
			// We already marked this item, no need to check again.
			return;
		}

		if ( ! $media_item->has_animated_mime_type() ) {
			// The media item is not even a GIF so no need to check.
			return;
		}

		$file_path   = $media_item->get_full_or_scaled_size()->get_file_path();
		$is_animated = Helper::check_animated_file_contents( $file_path );

		$this->logger->log( 'Setting animated meta value' );
		$set_animated = $media_item->set_animated( $is_animated );
		if ( $set_animated ) {
			$media_item->save();
		}

		if ( $is_animated ) {
			do_action( 'wp_smush_attachment_animated_status_changed', $attachment_id );
		}
	}
}