<?php

namespace Smush\Core\Smush;

use Smush\Core\Controller;
use Smush\Core\Media\Media_Item;
use Smush\Core\Stats\Global_Stats;
use Smush\Core\Stats\Media_Item_Optimization_Global_Stats_Persistable;

class Smush_Controller extends Controller {
	const GLOBAL_STATS_OPTION_ID = 'wp-smush-optimization-global-stats';
	const SMUSH_OPTIMIZATION_ORDER = 30;
	private $global_stats;

	public function __construct() {
		$this->global_stats = Global_Stats::get();

		$this->register_filter( 'wp_smush_optimizations', array(
			$this,
			'add_smush_optimization',
		), self::SMUSH_OPTIMIZATION_ORDER, 2 );
		$this->register_filter( 'wp_smush_global_optimization_stats', array( $this, 'add_png2jpg_global_stats' ) );
		$this->register_filter( 'wp_smush_optimization_global_stats_instance', array(
			$this,
			'create_global_stats_instance',
		), 10, 2 );
		$this->register_action( 'wp_smush_settings_updated', array(
			$this,
			'maybe_mark_global_stats_as_outdated',
		), 10, 2 );
	}

	/**
	 * @param $optimizations array
	 * @param $media_item Media_Item
	 *
	 * @return array
	 */
	public function add_smush_optimization( $optimizations, $media_item ) {
		$optimization                              = new Smush_Optimization( $media_item );
		$optimizations[ $optimization->get_key() ] = $optimization;

		return $optimizations;
	}

	public function add_png2jpg_global_stats( $stats ) {
		$stats[ Smush_Optimization::KEY ] = new Media_Item_Optimization_Global_Stats_Persistable(
			self::GLOBAL_STATS_OPTION_ID,
			new Smush_Optimization_Global_Stats()
		);

		return $stats;
	}

	public function create_global_stats_instance( $original, $key ) {
		if ( $key === Smush_Optimization::KEY ) {
			return new Smush_Optimization_Global_Stats();
		}

		return $original;
	}

	public function maybe_mark_global_stats_as_outdated( $old_settings, $settings ) {
		$old_lossy_status     = ! empty( $old_settings['lossy'] ) ? (int) $old_settings['lossy'] : 0;
		$new_lossy_status     = ! empty( $settings['lossy'] ) ? (int) $settings['lossy'] : 0;
		$lossy_status_changed = $old_lossy_status !== $new_lossy_status;

		$old_exif_status     = ! empty( $old_settings['strip_exif'] );
		$new_exif_status     = ! empty( $settings['strip_exif'] );
		$exif_status_changed = $old_exif_status !== $new_exif_status;

		if ( $lossy_status_changed || $exif_status_changed ) {
			$this->global_stats->mark_as_outdated();
		}
	}
}