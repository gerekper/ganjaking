<?php

namespace Smush\Core\Resize;

use Smush\Core\Array_Utils;
use Smush\Core\Controller;
use Smush\Core\Stats\Global_Stats;
use Smush\Core\Stats\Media_Item_Optimization_Global_Stats_Persistable;

class Resize_Controller extends Controller {
	const GLOBAL_STATS_OPTION_ID = 'wp-smush-resize-global-stats';
	const RESIZE_OPTIMIZATION_ORDER = 10;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	public function __construct() {
		$this->global_stats = Global_Stats::get();
		$this->array_utils  = new Array_Utils();

		$this->register_filter( 'wp_smush_optimizations', array(
			$this,
			'add_resize_optimization',
		), self::RESIZE_OPTIMIZATION_ORDER, 2 );
		$this->register_filter( 'wp_smush_global_optimization_stats', array( $this, 'add_resize_global_stats' ) );

		$this->register_action( 'wp_smush_settings_updated', array(
			$this,
			'mark_as_outdated_if_resize_turned_on',
		), 10, 2 );

		$this->register_action( 'wp_smush_resize_sizes_updated', array(
			$this,
			'mark_as_outdated_if_resize_settings_changed',
		), 10, 2 );
	}

	public function add_resize_optimization( $optimizations, $media_item ) {
		$resize_optimization                              = new Resize_Optimization( $media_item );
		$optimizations[ $resize_optimization->get_key() ] = $resize_optimization;

		return $optimizations;
	}

	public function add_resize_global_stats( $stats ) {
		$stats[ Resize_Optimization::KEY ] = new Media_Item_Optimization_Global_Stats_Persistable( self::GLOBAL_STATS_OPTION_ID );

		return $stats;
	}

	public function mark_as_outdated_if_resize_turned_on( $old_settings, $settings ) {
		$old_resize_status = ! empty( $old_settings['resize'] );
		$new_resize_status = ! empty( $settings['resize'] );
		if ( $old_resize_status !== $new_resize_status ) {
			$this->mark_global_stats_as_outdated();
		}
	}

	public function mark_as_outdated_if_resize_settings_changed( $old_settings, $settings ) {
		$old_width  = (int) $this->array_utils->get_array_value( $old_settings, 'width' );
		$new_width  = (int) $this->array_utils->get_array_value( $settings, 'width' );
		$old_height = (int) $this->array_utils->get_array_value( $old_settings, 'height' );
		$new_height = (int) $this->array_utils->get_array_value( $settings, 'height' );
		if ( $old_width !== $new_width || $old_height !== $new_height ) {
			$this->mark_global_stats_as_outdated();
		}
	}

	/**
	 * @return void
	 */
	public function mark_global_stats_as_outdated() {
		$this->global_stats->mark_as_outdated();
	}
}