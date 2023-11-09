<?php

namespace Smush\Core\Stats;

use Smush\Core\Array_Utils;
use Smush\Core\Attachment_Id_List;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimization_Global_Stats;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Media\Media_Item_Query;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Modules\Background\Mutex;

class Global_Stats {
	const GLOBAL_STATS_OPTION_ID = 'wp_smush_global_stats';
	const OPTIMIZE_LIST_OPTION_ID = 'wp-smush-optimize-list';
	const REOPTIMIZE_LIST_OPTION_ID = 'wp-smush-reoptimize-list';
	const ERROR_LIST_OPTION_ID = 'wp-smush-error-items-list';
	const IGNORE_LIST_OPTION_ID = 'wp-smush-ignored-items-list';
	const ANIMATED_LIST_OPTION_ID = 'wp-smush-animated-items-list';
	/**
	 * @var Global_Stats
	 */
	private static $instance;

	/**
	 * @var Media_Item_Optimization_Global_Stats[]
	 */
	private $optimization_stats;

	/**
	 * @var Attachment_Id_List
	 */
	private $optimize_list;
	/**
	 * @var Attachment_Id_List
	 */
	private $reoptimize_list;
	/**
	 * @var Attachment_Id_List
	 */
	private $error_list;
	/**
	 * @var Attachment_Id_List
	 */
	private $ignore_list;
	/**
	 * @var Attachment_Id_List
	 */
	private $animated_list;
	/**
	 * @var Media_Item_Cache
	 */
	private $media_item_cache;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;
	private $media_item_query;

	public function __construct() {
		$this->optimize_list   = new Attachment_Id_List( self::OPTIMIZE_LIST_OPTION_ID );
		$this->reoptimize_list = new Attachment_Id_List( self::REOPTIMIZE_LIST_OPTION_ID );
		$this->error_list      = new Attachment_Id_List( self::ERROR_LIST_OPTION_ID );
		$this->ignore_list     = new Attachment_Id_List( self::IGNORE_LIST_OPTION_ID );
		$this->animated_list   = new Attachment_Id_List( self::ANIMATED_LIST_OPTION_ID );

		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->array_utils      = new Array_Utils();
		$this->media_item_query = new Media_Item_Query();
	}

	public static function get() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return Media_Item_Optimization_Global_Stats_Persistable[]
	 */
	public function get_persistable_stats_for_optimizations() {
		if ( is_null( $this->optimization_stats ) ) {
			$this->optimization_stats = $this->initialize_stats_for_optimizations();
		}

		return $this->optimization_stats;
	}

	private function initialize_stats_for_optimizations() {
		return apply_filters( 'wp_smush_global_optimization_stats', array() );
	}

	/**
	 * @param $optimization_key
	 *
	 * @return Media_Item_Optimization_Global_Stats
	 */
	public function create_global_stats_object( $optimization_key ) {
		return apply_filters(
			'wp_smush_optimization_global_stats_instance',
			new Media_Item_Optimization_Global_Stats(),
			$optimization_key
		);
	}

	/**
	 * @param $optimization_key
	 *
	 * @return Media_Item_Optimization_Global_Stats_Persistable
	 */
	public function get_persistable_stats_for_optimization( $optimization_key ) {
		return $this->get_array_value(
			$this->get_persistable_stats_for_optimizations(),
			$optimization_key
		);
	}

	private function get_array_value( $array, $key ) {
		return $array && isset( $array[ $key ] )
			? $array[ $key ]
			: null;
	}

	public function delete_global_stats_option() {
		delete_option( self::GLOBAL_STATS_OPTION_ID );
	}

	private function get_global_stats_option_value( $key ) {
		$option = $this->get_global_stats_option();

		return $this->get_array_value( $option, $key );
	}

	private function update_global_stats_option_value( $key, $value ) {
		$option = $this->get_global_stats_option();

		update_option( self::GLOBAL_STATS_OPTION_ID, array_merge( $option, array(
			$key => $value,
		) ), false );
	}

	public function is_outdated() {
		if ( $this->is_media_library_empty() ) {
			return false;
		}

		$stats_updated_timestamp = $this->get_stats_updated_timestamp();
		if ( empty( $stats_updated_timestamp ) ) {
			// The scan has never been run
			return true;
		}

		$rescan_required_timestamp = $this->get_rescan_required_timestamp();

		return $rescan_required_timestamp > $stats_updated_timestamp;
	}

	private function is_media_library_empty() {
		if ( 0 !== $this->get_image_attachment_count() ) {
			// Cached attachment count is not empty, so we definitely have some media items.
			// No need to make a DB call.
			return false;
		}

		return 0 === $this->media_item_query->get_image_attachment_count();
	}

	public function mark_as_outdated() {
		$this->update_rescan_required_timestamp( time() );
	}

	public function get_stats_update_started_timestamp() {
		return (int) $this->get_global_stats_option_value( 'stats_update_started_timestamp' );
	}

	public function update_stats_update_started_timestamp( $timestamp ) {
		$this->update_global_stats_option_value( 'stats_update_started_timestamp', $timestamp );
	}

	public function get_stats_updated_timestamp() {
		return (int) $this->get_global_stats_option_value( 'stats_updated_timestamp' );
	}

	public function update_stats_updated_timestamp( $timestamp ) {
		$this->update_global_stats_option_value( 'stats_updated_timestamp', $timestamp );
	}

	public function get_rescan_required_timestamp() {
		return (int) $this->get_global_stats_option_value( 'rescan_required_timestamp' );
	}

	public function update_rescan_required_timestamp( $timestamp ) {
		$this->update_global_stats_option_value( 'rescan_required_timestamp', $timestamp );
	}

	public function get_image_attachment_count() {
		return (int) $this->get_global_stats_option_value( 'image_attachment_count' );
	}

	public function add_image_attachment_count( $image_attachment_count ) {
		$this->mutex( function () use ( $image_attachment_count ) {
			$old_image_attachment_count = $this->get_image_attachment_count();
			$this->update_global_stats_option_value( 'image_attachment_count', $old_image_attachment_count + $image_attachment_count );
		} );
	}

	public function subtract_image_attachment_count( $image_attachment_count ) {
		$this->mutex( function () use ( $image_attachment_count ) {
			$old_image_attachment_count = $this->get_image_attachment_count();
			$this->update_global_stats_option_value( 'image_attachment_count', max( $old_image_attachment_count - $image_attachment_count, 0 ) );
		} );
	}

	public function get_optimized_images_count() {
		return (int) $this->get_global_stats_option_value( 'optimized_images_count' );
	}

	public function add_optimized_images_count( $optimized_images_count ) {
		$this->mutex( function () use ( $optimized_images_count ) {
			$old_count = $this->get_optimized_images_count();
			$this->update_global_stats_option_value( 'optimized_images_count', $old_count + $optimized_images_count );
		} );
	}

	public function subtract_optimized_images_count( $optimized_images_count ) {
		$this->mutex( function () use ( $optimized_images_count ) {
			$old_count = $this->get_optimized_images_count();
			$this->update_global_stats_option_value( 'optimized_images_count', max( $old_count - $optimized_images_count, 0 ) );
		} );
	}

	public function get_sum_of_optimization_global_stats() {
		$stats = new Media_Item_Stats();

		foreach ( $this->get_persistable_stats_for_optimizations() as $optimization ) {
			$stats->add( $optimization->get_stats() );
		}

		return $stats;
	}

	private function mutex( $operation ) {
		$option_id = self::GLOBAL_STATS_OPTION_ID;
		( new Mutex( "{$option_id}_mutex" ) )->execute( $operation );
	}

	/**
	 * @return Attachment_Id_List
	 */
	public function get_optimize_list() {
		return $this->optimize_list;
	}

	public function get_redo_ids() {
		return array_merge(
			$this->get_reoptimize_list()->get_ids(),
			$this->get_error_list()->get_ids()
		);
	}

	public function get_redo_count() {
		return $this->get_reoptimize_list()->get_count()
		       + $this->get_error_list()->get_count();
	}

	/**
	 * @return Attachment_Id_List
	 */
	public function get_reoptimize_list() {
		return $this->reoptimize_list;
	}

	/**
	 * @return Attachment_Id_List
	 */
	public function get_error_list() {
		return $this->error_list;
	}

	/**
	 * @return Attachment_Id_List
	 */
	public function get_ignore_list() {
		return $this->ignore_list;
	}

	public function get_animated_list() {
		return $this->animated_list;
	}

	public function to_array() {
		$array = array(
			'is_outdated'            => $this->is_outdated(),
			'image_attachment_count' => $this->get_image_attachment_count(),
			'optimized_images_count' => $this->get_optimized_images_count(),
		);

		foreach ( $this->get_persistable_stats_for_optimizations() as $optimization_key => $optimization_stats ) {
			$array[ $optimization_key ] = $optimization_stats->get_stats()->to_array();
		}

		$array['optimize_list']    = $this->optimize_list->get_ids();
		$array['optimize_count']   = $this->optimize_list->get_count();
		$array['reoptimize_list']  = $this->reoptimize_list->get_ids();
		$array['reoptimize_count'] = $this->reoptimize_list->get_count();
		$array['error_list']       = $this->error_list->get_ids();
		$array['error_count']      = $this->error_list->get_count();
		$array['ignore_list']      = $this->ignore_list->get_ids();
		$array['ignore_count']     = $this->ignore_list->get_count();
		$array['animated_list']    = $this->animated_list->get_ids();
		$array['animated_count']   = $this->animated_list->get_count();

		$total_stats              = $this->get_sum_of_optimization_global_stats();
		$array['size_before']     = $total_stats->get_size_before();
		$array['size_after']      = $total_stats->get_size_after();
		$array['savings_percent'] = $total_stats->get_percent();

		$array['remaining_count'] = $this->get_remaining_count();

		$array['percent_optimized'] = $this->get_percent_optimized();
		$array['percent_metric']    = $this->get_percent_metric();
		$array['grade_class']       = $this->get_grade_class();

		$array['total_optimizable_items_count'] = $this->get_total_optimizable_items_count();
		$array['skipped_ids']                   = $this->get_skipped_ids();
		$array['skipped_count']                 = $this->get_skipped_count();

		return $array;
	}

	/**
	 * @return int
	 */
	public function get_remaining_count() {
		return $this->optimize_list->get_count()
		       + $this->reoptimize_list->get_count()
		       + $this->error_list->get_count();
	}

	/**
	 * @return array
	 */
	private function get_global_stats_option() {
		// Cached values are problematic in parallel
		wp_cache_delete( self::GLOBAL_STATS_OPTION_ID, 'options' );
		$option = get_option( self::GLOBAL_STATS_OPTION_ID, array() );

		return empty( $option ) || ! is_array( $option )
			? array()
			: $option;
	}

	public function reset() {
		$this->get_reoptimize_list()->delete_ids();
		$this->get_optimize_list()->delete_ids();
		$this->get_error_list()->delete_ids();
		$this->get_ignore_list()->delete_ids();
		$this->get_animated_list()->delete_ids();

		$this->delete_global_stats_option();
		foreach ( $this->get_persistable_stats_for_optimizations() as $persistable_stats_for_optimization ) {
			$persistable_stats_for_optimization->reset();
		}
	}


	/**
	 * Total number of items that could be optimized, this includes items that have already been optimized.
	 *
	 * For a count that only contains items yet to be optimized/reoptimized {@see self::get_remaining_count()}
	 *
	 * @return int
	 */
	public function get_total_optimizable_items_count() {
		return $this->get_image_attachment_count() - $this->get_skipped_count();
	}

	public function get_skipped_count() {
		return count( $this->get_skipped_ids() );
	}

	public function get_skipped_ids() {
		$skipped_ids = array_merge(
			$this->get_ignore_list()->get_ids(),
			$this->get_animated_list()->get_ids()
		);

		return $this->array_utils->fast_array_unique( $skipped_ids );
	}

	public function get_percent_optimized() {
		$total_optimizable_count = $this->get_total_optimizable_items_count();
		$remaining_count         = $this->get_remaining_count();
		if (
			$total_optimizable_count === 0 ||
			$total_optimizable_count <= $remaining_count
		) {
			return 0;
		}
		$percent_optimized = floor( ( $total_optimizable_count - $remaining_count ) * 100 / $total_optimizable_count );
		if ( $percent_optimized > 100 ) {
			$percent_optimized = 100;
		} elseif ( $percent_optimized < 0 ) {
			$percent_optimized = 0;
		}

		return $percent_optimized;
	}

	public function get_percent_metric() {
		$percent_optimized = $this->get_percent_optimized();

		return 0.0 === (float) $percent_optimized ? 100 : $percent_optimized;
	}

	public function get_grade_class() {
		$total_optimizable_items_count = $this->get_total_optimizable_items_count();
		if ( 0 === $total_optimizable_items_count ) {
			$grade = 'sui-grade-dismissed';
		} else {
			$percent_optimized = $this->get_percent_optimized();

			$grade = 'sui-grade-f';
			if ( $percent_optimized >= 60 && $percent_optimized < 90 ) {
				$grade = 'sui-grade-c';
			} elseif ( $percent_optimized >= 90 ) {
				$grade = 'sui-grade-a';
			}
		}

		return $grade;
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return void
	 */
	public function remove_media_item( $media_item ) {
		$attachment_id = $media_item->get_id();

		// Remove from all the lists
		$this->remove_from_all_lists( $attachment_id );

		// Remove stats
		$this->subtract_item_stats( $media_item );
	}

	public function adjust_for_attachment( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		$this->adjust_for_media_item( $media_item );
	}

	/**
	 * When the status of a media item changes this method can make the necessary changes to the global stats
	 *
	 * @param $media_item Media_Item
	 *
	 * @return void
	 */
	public function adjust_for_media_item( $media_item ) {
		$this->adjust_lists_for_media_item( $media_item );

		$belongs_in_stats = ! $media_item->is_skipped() && ! $media_item->has_errors();
		if ( $belongs_in_stats ) {
			$this->add_item_stats( $media_item );
		} else {
			$this->subtract_item_stats( $media_item );
		}
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return void
	 */
	private function add_item_stats( $media_item ) {
		$optimizer = new Media_Item_Optimizer( $media_item );
		foreach ( $this->get_persistable_stats_for_optimizations() as $optimization_key => $optimization_global_stats ) {
			$optimization = $optimizer->get_optimization( $optimization_key );
			if ( $optimization && $optimization->is_optimized() ) {
				$optimization_global_stats->add_item_stats( $media_item->get_id(), $optimization->get_stats() );
			}
		}
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return void
	 */
	public function subtract_item_stats( $media_item ) {
		$optimizer = new Media_Item_Optimizer( $media_item );
		foreach ( $this->get_persistable_stats_for_optimizations() as $optimization_key => $optimization_global_stats ) {
			$optimization = $optimizer->get_optimization( $optimization_key );
			if ( $optimization && $optimization->is_optimized() ) {
				$optimization_global_stats->subtract_item_stats( $media_item->get_id(), $optimization->get_stats() );
			}
		}
	}

	/**
	 * @param $attachment_id
	 *
	 * @return void
	 */
	private function remove_from_all_lists( $attachment_id ) {
		$this->get_optimize_list()->remove_id( $attachment_id );
		$this->get_reoptimize_list()->remove_id( $attachment_id );
		$this->get_error_list()->remove_id( $attachment_id );
		$this->get_ignore_list()->remove_id( $attachment_id );
		$this->get_animated_list()->remove_id( $attachment_id );
	}

	/**
	 * Also:
	 * @see Global_Stats_Controller::accumulate_attachment_ids()
	 */
	public function adjust_lists_for_media_item( $media_item ) {
		$attachment_id = $media_item->get_id();
		$optimizer     = new Media_Item_Optimizer( $media_item );

		// First remove from all the lists.
		$this->remove_from_all_lists( $attachment_id );

		// Now add only to the lists where it belongs.
		if ( $media_item->is_ignored() ) {
			$this->get_ignore_list()->add_id( $attachment_id );
		} elseif ( $media_item->is_animated() ) {
			$this->get_animated_list()->add_id( $attachment_id );
		} elseif ( $media_item->has_errors() ) {
			$this->get_error_list()->add_id( $attachment_id );
		} else {
			if ( $optimizer->is_optimized() ) {
				if ( $optimizer->should_reoptimize() ) {
					$this->get_reoptimize_list()->add_id( $attachment_id );
				}
			} else {
				if ( $optimizer->should_optimize() ) {
					$this->get_optimize_list()->add_id( $attachment_id );
				}
			}
		}
	}
}