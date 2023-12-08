<?php

namespace Smush\Core\Media;

use Smush\Core\Backups\Backups;
use Smush\Core\Helper;
use Smush\Core\Smush\Smush_Optimization;
use Smush\Core\Stats\Global_Stats;
use WDEV_Logger;
use WP_Error;

class Media_Item_Optimizer {
	const ERROR_META_KEY = 'wp-smush-optimization-errors';

	/**
	 * @var Media_Item_Optimization[]
	 */
	private $optimizations;

	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var Backups
	 */
	private $backups;
	/**
	 * @var WDEV_Logger
	 */
	private $logger;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;

	/**
	 * @var WP_Error
	 */
	private $errors;

	public function __construct( $media_item ) {
		$this->media_item   = $media_item;
		$this->backups      = new Backups();
		$this->logger       = Helper::logger();
		$this->global_stats = Global_Stats::get();
	}

	/**
	 * @return Media_Item_Optimization[]
	 */
	public function get_optimizations() {
		if ( is_null( $this->optimizations ) ) {
			$this->optimizations = $this->initialize_optimizations();
		}

		return $this->optimizations;
	}

	public function set_optimizations( $optimizations ) {
		$this->optimizations = $optimizations;
	}

	private function initialize_optimizations() {
		return apply_filters( 'wp_smush_optimizations', array(), $this->media_item );
	}

	/**
	 * TODO: check the uses for this method to make sure they are prepared to receive null
	 *
	 * @param $key
	 *
	 * @return Media_Item_Optimization|null
	 */
	public function get_optimization( $key ) {
		return $this->get_array_value( $this->get_optimizations(), $key );
	}

	/**
	 * @param $key
	 *
	 * @return Media_Item_Stats
	 */
	public function get_stats( $key ) {
		$optimization = $this->get_optimization( $key );
		if ( $optimization ) {
			return $optimization->get_stats();
		}

		return new Media_Item_Stats();
	}

	public function get_total_stats() {
		$total_stats = new Media_Item_Stats();
		foreach ( $this->get_optimizations() as $optimization ) {
			$total_stats->add( $optimization->get_stats() );
		}

		return $total_stats;
	}

	/**
	 * @param $optimization_key
	 * @param $size_key
	 *
	 * @return Media_Item_Stats
	 */
	public function get_size_stats( $optimization_key, $size_key ) {
		$optimization = $this->get_optimization( $optimization_key );
		if ( $optimization ) {
			return $optimization->get_size_stats( $size_key );
		}

		return new Media_Item_Stats();
	}

	public function get_total_size_stats( $size_key ) {
		$total_stats = new Media_Item_Stats();
		foreach ( $this->get_optimizations() as $optimization ) {
			$total_stats->add( $optimization->get_size_stats( $size_key ) );
		}

		return $total_stats;
	}

	public function get_optimized_sizes_count() {
		$size_count = 0;
		foreach ( $this->get_optimizations() as $optimization ) {
			$optimized_sizes_count = $optimization->get_optimized_sizes_count();
			if ( $optimized_sizes_count > $size_count ) {
				$size_count = $optimized_sizes_count;
			}
		}

		return $size_count;
	}

	/**
	 * Whether the media item was optimized at some point. It may need to be reoptimized.
	 *
	 * @return bool
	 */
	public function is_optimized() {
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->is_optimized() ) {
				return true;
			}
		}

		return false;
	}

	public function should_optimize() {
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->should_optimize() ) {
				return true;
			}
		}

		return false;
	}

	public function should_reoptimize() {
		$should_reoptimize = false;
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->should_reoptimize() ) {
				$should_reoptimize = true;
			}
		}

		return apply_filters( 'wp_smush_should_resmush', $should_reoptimize, $this->media_item->get_id() );
	}

	public function optimize() {
		if ( $this->restore_in_progress() ) {
			$this->logger->log( 'Prevented auto-smush during restore.' );

			return false;
		}

		if ( $this->in_progress() ) {
			$this->handle_error( 'in_progress', 'Smush already in progress' );

			return false;
		}

		$media_item = $this->media_item;
		do_action(
			'wp_smush_before_smush_attempt',
			$media_item->get_id(),
			$media_item->get_wp_metadata()
		);

		if ( $media_item->has_errors() || $media_item->is_skipped() ) {
			$this->adjust_global_stats_lists();

			return false;
		}

		do_action(
			'wp_smush_before_smush_file',
			$media_item->get_id(),
			$media_item->get_wp_metadata()
		);

		$this->set_in_progress_transient();

		$this->backups->maybe_create_backup( $media_item, $this );

		$optimized = $this->run_optimizations();

		do_action(
			'wp_smush_after_smush_file',
			$media_item->get_id(),
			$media_item->get_wp_metadata(),
			$optimized ? array() : $this->get_errors()
		);

		if ( $optimized ) {
			do_action(
				'wp_smush_after_smush_successful',
				$media_item->get_id(),
				$media_item->get_wp_metadata()
			);

			$this->delete_previous_optimization_errors();
		} else {
			$this->handle_optimization_errors();
		}

		$this->delete_in_progress_transient();

		return $optimized;
	}

	public function restore() {
		if ( $this->in_progress() || $this->restore_in_progress() ) {
			return false;
		}

		$this->set_restore_in_progress_transient();

		$restoration_attempted = false;
		$restored              = false;

		// First, allow one of the optimizations to handle the restoration process
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->can_restore() ) {
				$restoration_attempted = true;
				$restored              = $optimization->restore();
				break;
			}
		}

		if ( ! $restoration_attempted ) {
			// Try the standard restoration
			$restored = $this->backups->restore_backup( $this->media_item );
		}

		if ( $restored ) {
			// Before deleting all data subtract the stats
			$this->global_stats->subtract_item_stats( $this->media_item );
			$this->global_stats->subtract_optimized_images_count( $this->get_optimized_sizes_count() );

			// Delete all the optimization data
			$this->delete_data();

			// Delete optimization errors.
			$this->delete_previous_optimization_errors();

			// Once all data has been deleted, adjust the lists
			$this->global_stats->adjust_lists_for_media_item( $this->media_item );
		}

		$this->delete_restore_in_progress_transient();

		return $restored;
	}

	public function save() {
		foreach ( $this->get_optimizations() as $optimization ) {
			$optimization->save();
		}
	}

	private function get_array_value( $array, $key ) {
		return $array && isset( $array[ $key ] )
			? $array[ $key ]
			: null;
	}

	/**
	 * @param Media_Item_Size $full_size
	 *
	 * @return boolean
	 */
	public function should_optimize_size( $full_size ) {
		$should_optimize_size = false;
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->should_optimize_size( $full_size ) ) {
				$should_optimize_size = true;
				break;
			}
		}

		return $should_optimize_size;
	}

	public function delete_data() {
		foreach ( $this->get_optimizations() as $optimization ) {
			$optimization->delete_data();
		}
	}

	/**
	 * @return bool
	 */
	private function run_optimizations() {
		$all_optimized = true;
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->should_optimize() ) {
				$current_optimized = $optimization->optimize();
				$all_optimized     = $all_optimized && $current_optimized;
			}
		}

		return $all_optimized;
	}

	private function adjust_global_stats_lists() {
		$this->global_stats->adjust_lists_for_media_item( $this->media_item );
	}

	private function set_in_progress_transient() {
		set_transient( $this->in_progress_transient_key(), 1, HOUR_IN_SECONDS );
	}

	private function delete_in_progress_transient() {
		delete_transient( $this->in_progress_transient_key() );
	}

	public function in_progress() {
		return (bool) get_transient( $this->in_progress_transient_key() );
	}

	private function in_progress_transient_key() {
		return 'smush-in-progress-' . $this->media_item->get_id();
	}

	private function set_restore_in_progress_transient() {
		set_transient( $this->restore_in_progress_transient_key(), 1, HOUR_IN_SECONDS );
	}

	private function delete_restore_in_progress_transient() {
		delete_transient( $this->restore_in_progress_transient_key() );
	}

	public function restore_in_progress() {
		return (bool) get_transient( $this->restore_in_progress_transient_key() );
	}

	private function restore_in_progress_transient_key() {
		return 'wp-smush-restore-' . $this->media_item->get_id();
	}

	/**
	 * @param $code
	 * @param $error_message
	 *
	 * @return void
	 */
	private function handle_error( $code, $error_message ) {
		$this->logger->error( $error_message );
		$this->set_errors( new WP_Error( $code, $error_message ) );
		$this->update_errors_meta();
	}

	public function get_errors() {
		if ( is_null( $this->errors ) ) {
			$this->errors = $this->fetch_errors_from_meta();
		}

		return $this->errors;
	}

	private function set_errors( $errors ) {
		$this->errors = $errors;
	}

	public function has_errors() {
		return $this->get_errors()->has_errors();
	}

	private function set_optimization_errors() {
		$errors = new WP_Error();
		// Add optimization errors
		foreach ( $this->get_optimizations() as $optimization ) {
			if ( $optimization->has_errors() ) {
				$errors->merge_from( $optimization->get_errors() );
			}
		}

		$this->set_errors( $errors );
	}

	private function fetch_errors_from_meta() {
		$wp_error = new WP_Error();
		$errors   = get_post_meta( $this->media_item->get_id(), self::ERROR_META_KEY, true );

		if ( empty( $errors ) || ! is_array( $errors ) ) {
			return $wp_error;
		}

		foreach ( $errors as $error_code => $error_message ) {
			if ( empty( $error_message ) ) {
				continue;
			}

			if ( is_array( $error_message ) ) {
				foreach ( $error_message as $error ) {
					$wp_error->add( $error_code, $error );
				}
			} else {
				$wp_error->add( $error_code, $error_message );
			}
		}

		return $wp_error;
	}

	private function update_errors_meta() {
		$errors_array = array();
		foreach ( $this->errors->get_error_codes() as $error_code ) {
			$errors_array[ $error_code ] = $this->errors->get_error_messages( $error_code );
		}

		if ( ! empty( $errors_array ) ) {
			update_post_meta( $this->media_item->get_id(), self::ERROR_META_KEY, $errors_array );
		}
	}

	/**
	 * @return void
	 */
	private function handle_optimization_errors() {
		$this->set_optimization_errors();
		$this->update_errors_meta();
	}

	private function delete_previous_optimization_errors() {
		if ( $this->has_errors() ) {
			delete_post_meta( $this->media_item->get_id(), self::ERROR_META_KEY );
			$this->set_errors( null );
		}
	}
}