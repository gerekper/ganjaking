<?php

namespace Smush\Core\Smush;

use Smush\Core\Media\Media_Item_Optimization_Global_Stats;

class Smush_Optimization_Global_Stats extends Media_Item_Optimization_Global_Stats {
	private $lossy_count = 0;

	public function from_array( $array ) {
		parent::from_array( $array );

		$this->set_lossy_count( (int) $this->get_array_value( $array, 'lossy_count' ) );
	}

	public function to_array() {
		$array = parent::to_array();

		$array['lossy_count'] = $this->get_lossy_count();

		return $array;
	}

	/**
	 * @param $attachment_id int
	 * @param $item_stats Smush_Media_Item_Stats
	 *
	 * @return boolean
	 */
	public function add_item_stats( $attachment_id, $item_stats ) {
		$added = parent::add_item_stats( $attachment_id, $item_stats );

		if ( $added && $item_stats->is_lossy() ) {
			$this->set_lossy_count( $this->get_lossy_count() + 1 );
		}

		return $added;
	}

	/**
	 * @param $attachment_id int
	 * @param $item_stats Smush_Media_Item_Stats
	 *
	 * @return boolean
	 */
	public function subtract_item_stats( $attachment_id, $item_stats ) {
		$subtracted = parent::subtract_item_stats( $attachment_id, $item_stats );

		if ( $subtracted && $item_stats->is_lossy() ) {
			// Assuming that we added to the lossy count
			$this->set_lossy_count( max( $this->get_lossy_count() - 1, 0 ) );
		}

		return $subtracted;
	}

	/**
	 * @param $addend Smush_Optimization_Global_Stats
	 *
	 * @return void
	 */
	public function add( $addend ) {
		parent::add( $addend );

		$this->set_lossy_count( $this->get_lossy_count() + $addend->get_lossy_count() );
	}

	/**
	 * @param $subtrahend Smush_Optimization_Global_Stats
	 *
	 * @return void
	 */
	public function subtract( $subtrahend ) {
		parent::subtract( $subtrahend );

		$this->set_lossy_count( max( $this->get_lossy_count() - $subtrahend->get_lossy_count(), 0 ) );
	}

	/**
	 * @return int
	 */
	public function get_lossy_count() {
		return $this->lossy_count;
	}

	/**
	 * @param int $lossy_count
	 *
	 * @return Smush_Optimization_Global_Stats
	 */
	public function set_lossy_count( $lossy_count ) {
		$this->lossy_count = $lossy_count;

		return $this;
	}
}