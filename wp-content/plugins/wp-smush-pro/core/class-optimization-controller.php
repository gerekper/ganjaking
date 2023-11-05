<?php

namespace Smush\Core;

use Smush\Core\Stats\Global_Stats;

class Optimization_Controller extends Controller {
	/**
	 * @var Global_Stats
	 */
	private $global_stats;

	public function __construct() {
		$this->global_stats = Global_Stats::get();

		$this->register_action( 'wp_smush_image_sizes_changed', array( $this, 'mark_global_stats_as_outdated' ) );
		$this->register_action( 'wp_smush_settings_updated', array(
			$this,
			'maybe_mark_global_stats_as_outdated',
		), 10, 2 );

		// TODO: handle auto optimization when media item is uploaded
		// TODO: handle bulk smush ajax
	}

	public function mark_global_stats_as_outdated() {
		$this->global_stats->mark_as_outdated();
	}

	public function maybe_mark_global_stats_as_outdated( $old_settings, $settings ) {
		$old_original            = ! empty( $old_settings['original'] );
		$new_original            = ! empty( $settings['original'] );
		$original_status_changed = $old_original !== $new_original;
		if ( $original_status_changed ) {
			$this->mark_global_stats_as_outdated();
		}
	}
}