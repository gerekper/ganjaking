<?php

namespace Smush\Core\Stats;

use DateTime;
use DateTimeZone;
use Smush\Core\Controller;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimization_Global_Stats;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Settings;

class Global_Stats_Controller extends Controller {
	const IMAGE_ATTACHMENT_COUNT_KEY = 'image_attachment_count';
	const OPTIMIZED_IMAGES_COUNT_KEY = 'optimized_images_count';
	const OPTIMIZE_IDS_KEY = 'optimize_attachment_ids';
	const REOPTIMIZE_IDS_KEY = 'reoptimize_attachment_ids';
	const ERROR_IDS_KEY = 'error_attachment_ids';
	const IGNORE_IDS_KEY = 'ignore_attachment_ids';
	const ANIMATED_IDS_KEY = 'animated_attachment_ids';
	/**
	 * @var Global_Stats
	 */
	private $global_stats;
	/**
	 * @var Media_Item_Cache
	 */
	private $media_item_cache;
	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct() {
		$this->global_stats     = Global_Stats::get();
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->settings         = Settings::get_instance();

		$this->register_media_library_scan_processes();

		$this->register_action( 'wp_smush_after_attachment_upload', array( $this, 'adjust_on_attachment_upload' ) );
		$this->register_action( 'delete_attachment', array( $this, 'adjust_on_attachment_deletion' ) );
		$this->register_action( 'wp_smush_before_smush_file', array( $this, 'adjust_before_optimization' ), 10, 3 );
		$this->register_action( 'wp_smush_after_smush_file', array( $this, 'adjust_after_optimization' ), 10, 3 );
		$this->register_action( 'wp_smush_plugin_activated', array( $this, 'maybe_mark_as_outdated' ) );
		$this->register_action( 'wp_smush_attachment_ignored_status_changed', array(
			$this,
			'adjust_global_stats_for_attachment',
		) );
		$this->register_action( 'wp_smush_attachment_animated_status_changed', array(
			$this,
			'adjust_global_stats_for_attachment',
		) );
		$this->register_action( 'wp_smush_membership_status_changed', array(
			$this->global_stats,
			'mark_as_outdated',
		), 10, 2 );
		$this->register_action( 'wp_ajax_wp_smush_get_global_stats', array( $this, 'ajax_get_global_stats' ) );
	}

	public function register_scan_process( $before_scan, $handle_attachment, $after_slice ) {
		$this->register_filter( 'wp_smush_before_scan_library', $before_scan );
		$this->register_filter( 'wp_smush_scan_library_slice_handle_attachment', $handle_attachment, 10, 2 );
		$this->register_filter( 'wp_smush_after_scan_library_slice', $after_slice );
	}

	public function reset_global_stats() {
		foreach ( $this->global_stats->get_persistable_stats_for_optimizations() as $optimization_global_stats ) {
			$optimization_global_stats->reset();
		}
	}

	public function accumulate_slice_stats( $slice_data, $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			return $slice_data;
		}

		$optimizer = new Media_Item_Optimizer( $media_item );
		foreach ( $optimizer->get_optimizations() as $optimization ) {
			$key = $this->slice_stats_key( $optimization->get_key() );
			if ( empty( $slice_data[ $key ] ) ) {
				$slice_data[ $key ] = $this->global_stats->create_global_stats_object( $optimization->get_key() );
			}

			$slice_stats = $slice_data[ $key ];
			if ( $optimization->is_optimized() ) {
				$item_stats = $optimization->get_stats();
				$slice_stats->add_item_stats( $attachment_id, $item_stats );
			}
		}

		return $slice_data;
	}

	/**
	 * @param $slice_data Media_Item_Optimization_Global_Stats[]
	 *
	 * @return Media_Item_Optimization_Global_Stats[]
	 */
	public function save_slice_stats( $slice_data ) {
		foreach ( $this->global_stats->get_persistable_stats_for_optimizations() as $optimization_key => $optimization_global_stats ) {
			$key = $this->slice_stats_key( $optimization_key );
			if ( empty( $slice_data[ $key ] ) ) {
				return $slice_data;
			}

			$slice_stats = $slice_data[ $key ];

			$optimization_global_stats->add( $slice_stats );
		}

		return $slice_data;
	}

	/**
	 * @param $optimization_key
	 *
	 * @return string
	 */
	private function slice_stats_key( $optimization_key ) {
		return 'slice_stats_' . $optimization_key;
	}

	public function reset_counts() {
		$this->global_stats->delete_global_stats_option();
	}

	public function accumulate_counts( $slice_data, $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			return $slice_data;
		}

		// Attachment count
		$optimizer  = new Media_Item_Optimizer( $media_item );
		$slice_data = $this->accumulate_count( $slice_data, self::IMAGE_ATTACHMENT_COUNT_KEY, 1 );
		$slice_data = $this->accumulate_count( $slice_data, self::OPTIMIZED_IMAGES_COUNT_KEY, $optimizer->get_optimized_sizes_count() );

		return $slice_data;
	}

	public function save_counts( $slice_data ) {
		$this->global_stats->add_image_attachment_count( (int) $this->get_array_value( $slice_data, self::IMAGE_ATTACHMENT_COUNT_KEY ) );
		$this->global_stats->add_optimized_images_count( (int) $this->get_array_value( $slice_data, self::OPTIMIZED_IMAGES_COUNT_KEY ) );

		return $slice_data;
	}

	private function accumulate_count( $slice_data, $key, $addend ) {
		if ( empty( $slice_data[ $key ] ) ) {
			$slice_data[ $key ] = 0;
		}
		$slice_data[ $key ] += $addend;

		return $slice_data;
	}

	private function get_array_value( $array, $key ) {
		return $array && isset( $array[ $key ] )
			? $array[ $key ]
			: null;
	}

	public function reset_lists() {
		$this->global_stats->get_optimize_list()->delete_ids();
		$this->global_stats->get_reoptimize_list()->delete_ids();
		$this->global_stats->get_error_list()->delete_ids();
		$this->global_stats->get_ignore_list()->delete_ids();
		$this->global_stats->get_animated_list()->delete_ids();
	}

	/**
	 * Also:
	 * @see Global_Stats::adjust_lists_for_media_item()
	 */
	public function accumulate_attachment_ids( $slice_data, $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			return $slice_data;
		}

		if ( $media_item->is_ignored() ) {
			$this->add_to_list( $slice_data, self::IGNORE_IDS_KEY, $attachment_id );
		} elseif ( $media_item->is_animated() ) {
			$this->add_to_list( $slice_data, self::ANIMATED_IDS_KEY, $attachment_id );
		} elseif ( $media_item->has_errors() ) {
			$this->add_to_list( $slice_data, self::ERROR_IDS_KEY, $attachment_id );
		} else {
			$optimizer = new Media_Item_Optimizer( $media_item );
			if ( $optimizer->is_optimized() ) {
				if ( $optimizer->should_reoptimize() ) {
					$this->add_to_list( $slice_data, self::REOPTIMIZE_IDS_KEY, $attachment_id );
				}
			} else {
				if ( $optimizer->should_optimize() ) {
					$this->add_to_list( $slice_data, self::OPTIMIZE_IDS_KEY, $attachment_id );
				}
			}
		}

		return $slice_data;
	}

	private function add_to_list( &$slice_data, $key, $attachment_id ) {
		if ( empty( $slice_data[ $key ] ) ) {
			$slice_data[ $key ] = array();
		}

		$slice_data[ $key ][] = $attachment_id;
	}

	public function save_optimization_lists( $slice_data ) {
		$slice_error_ids = empty( $slice_data[ self::ERROR_IDS_KEY ] ) ? array() : $slice_data[ self::ERROR_IDS_KEY ];
		if ( $slice_error_ids ) {
			$this->global_stats->get_error_list()->add_ids( $slice_error_ids );
		}

		$slice_ignore_ids = empty( $slice_data[ self::IGNORE_IDS_KEY ] ) ? array() : $slice_data[ self::IGNORE_IDS_KEY ];
		if ( $slice_ignore_ids ) {
			$this->global_stats->get_ignore_list()->add_ids( $slice_ignore_ids );
		}

		$slice_animated_ids = empty( $slice_data[ self::ANIMATED_IDS_KEY ] ) ? array() : $slice_data[ self::ANIMATED_IDS_KEY ];
		if ( $slice_animated_ids ) {
			$this->global_stats->get_animated_list()->add_ids( $slice_animated_ids );
		}

		$slice_reoptimize_ids = empty( $slice_data[ self::REOPTIMIZE_IDS_KEY ] ) ? array() : $slice_data[ self::REOPTIMIZE_IDS_KEY ];
		if ( $slice_reoptimize_ids ) {
			$this->global_stats->get_reoptimize_list()->add_ids( $slice_reoptimize_ids );
		}

		$slice_optimize_ids = empty( $slice_data[ self::OPTIMIZE_IDS_KEY ] ) ? array() : $slice_data[ self::OPTIMIZE_IDS_KEY ];
		if ( $slice_optimize_ids ) {
			$this->global_stats->get_optimize_list()->add_ids( $slice_optimize_ids );
		}

		return $slice_data;
	}

	public function adjust_on_attachment_deletion( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() || ! $media_item->is_mime_type_supported() ) {
			return;
		}
		$optimizer = new Media_Item_Optimizer( $media_item );

		// Counts
		$this->global_stats->subtract_image_attachment_count( 1 );
		if ( $optimizer->is_optimized() ) {
			$this->global_stats->subtract_optimized_images_count( $optimizer->get_optimized_sizes_count() );
		}

		$this->global_stats->remove_media_item( $media_item );
	}

	public function adjust_on_attachment_upload( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( $media_item->is_valid() && $media_item->is_mime_type_supported() ) {
			// Counts
			$this->global_stats->add_image_attachment_count( 1 );

			// Lists
			$this->global_stats->adjust_for_attachment( $attachment_id );
		}
	}

	/**
	 * Before optimization, we need to take off the *old* stats so that we can add the new ones afterwards
	 *
	 * @param $attachment_id
	 *
	 * @return void
	 */
	public function adjust_before_optimization( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() || ! $media_item->is_mime_type_supported() ) {
			return;
		}

		$optimizer = new Media_Item_Optimizer( $media_item );
		if ( $optimizer->is_optimized() ) {
			// If the media item is optimized already then this is a reoptimization and we should take off the optimized count we added during the last optimization
			$this->global_stats->subtract_optimized_images_count( $optimizer->get_optimized_sizes_count() );
		}

		// Subtract the old stats, we will add the new stats after reoptimization
		$this->global_stats->subtract_item_stats( $media_item );
	}

	public function adjust_after_optimization( $attachment_id, $metadata, $processing_errors ) {
		$media_item = $this->media_item_cache->get( $attachment_id );

		// We are only handling the success case here because we are relying on the fact that this method will never run when the media item has errors
		if ( ! $media_item->has_errors() && empty( $processing_errors ) ) {
			// Optimization successful
			$optimizer = new Media_Item_Optimizer( $media_item );

			// Counts
			if ( $optimizer->is_optimized() ) {
				$this->global_stats->add_optimized_images_count( $optimizer->get_optimized_sizes_count() );
			}

			$this->global_stats->adjust_for_media_item( $media_item );
		}
	}

	public function update_scan_started_timestamp() {
		$this->global_stats->update_stats_update_started_timestamp( time() );
	}

	public function update_scan_finished_timestamp() {
		$this->global_stats->update_stats_updated_timestamp( time() );
	}

	/**
	 * @return void
	 */
	private function register_media_library_scan_processes() {
		$this->register_action( 'wp_smush_before_scan_library', array( $this, 'update_scan_started_timestamp' ),
			20 // The priority needs to be managed here because reset_counts resets the scan started timestamp as well
		);
		$this->register_action( 'wp_smush_after_scan_library', array( $this, 'update_scan_finished_timestamp' ) );

		// Savings etc.
		$this->register_scan_process(
			array( $this, 'reset_global_stats' ),
			array( $this, 'accumulate_slice_stats' ),
			array( $this, 'save_slice_stats' )
		);

		// Counts
		$this->register_scan_process(
			array( $this, 'reset_counts' ),
			array( $this, 'accumulate_counts' ),
			array( $this, 'save_counts' )
		);

		// Attachment ID lists
		$this->register_scan_process(
			array( $this, 'reset_lists' ),
			array( $this, 'accumulate_attachment_ids' ),
			array( $this, 'save_optimization_lists' )
		);
	}

	public function ajax_get_global_stats() {
		// TODO: check ajax referrer

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		wp_send_json( $this->global_stats->to_array() );
	}

	/**
	 * TODO: add tests for the scenarios where this method is called
	 *
	 * @param $attachment_id
	 *
	 * @return void
	 */
	public function adjust_global_stats_for_attachment( $attachment_id ) {
		$this->global_stats->adjust_for_attachment( $attachment_id );
	}

	public function get_latest_modification_timestamp() {
		global $wpdb;

		$latest_modification_date = $wpdb->get_var( "SELECT post_modified_gmt FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_modified_gmt DESC LIMIT 1;" );
		if ( empty( $latest_modification_date ) ) {
			return false;
		}

		try {
			$date = new DateTime( $latest_modification_date, new DateTimeZone( 'GMT' ) );

			return $date->format( 'U' );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	public function maybe_mark_as_outdated() {
		$stats_updated_timestamp = $this->global_stats->get_stats_updated_timestamp();
		if ( empty( $stats_updated_timestamp ) ) {
			// Already outdated because a scan was never run
			return;
		}

		$latest_modification_timestamp = $this->get_latest_modification_timestamp();
		if ( empty( $latest_modification_timestamp ) ) {
			// Something went wrong
			return;
		}

		if ( $latest_modification_timestamp < $stats_updated_timestamp ) {
			// A scan was done after the latest change in the media library
			return;
		}

		$this->global_stats->mark_as_outdated();
	}
}