<?php

namespace Smush\Core\Stats;

use Smush\Core\Media\Media_Item_Optimization_Global_Stats;
use Smush\Core\Modules\Background\Mutex;

class Media_Item_Optimization_Global_Stats_Persistable {
	/**
	 * @var Media_Item_Optimization_Global_Stats
	 */
	private $stats;

	private $option_id;

	public function __construct( $option_id, $stats = null ) {
		$this->option_id = $option_id;
		$this->stats     = is_a( $stats, '\Smush\Core\Media\Media_Item_Optimization_Global_Stats' )
			? $stats
			: new Media_Item_Optimization_Global_Stats();

		$this->fetch_from_option( $option_id );
	}

	public function save() {
		update_option( $this->option_id, $this->stats->to_array(), false );
	}

	public function has_attachment_id( $attachment_id ) {
		return $this->stats->has_attachment_id( $attachment_id );
	}

	public function add_item_stats( $attachment_id, $addend ) {
		$this->mutex( function () use ( $attachment_id, $addend ) {
			$this->fetch_from_option( $this->option_id );
			$this->stats->add_item_stats( $attachment_id, $addend );
			$this->save();
		} );
	}

	public function subtract_item_stats( $attachment_id, $subtrahend ) {
		$this->mutex( function () use ( $attachment_id, $subtrahend ) {
			$this->fetch_from_option( $this->option_id );
			$this->stats->subtract_item_stats( $attachment_id, $subtrahend );
			$this->save();
		} );
	}

	public function add( $addend ) {
		$this->mutex( function () use ( $addend ) {
			$this->fetch_from_option( $this->option_id );
			$this->stats->add( $addend );
			$this->save();
		} );
	}

	public function subtract( $subtrahend ) {
		$this->mutex( function () use ( $subtrahend ) {
			$this->fetch_from_option( $this->option_id );
			$this->stats->subtract( $subtrahend );
			$this->save();
		} );
	}

	public function reset() {
		$this->initialize();
	}

	public function initialize() {
		$this->mutex( function () {
			$this->stats->from_array( array() );
			$this->save();
		} );
	}

	/**
	 * @param $option_id
	 *
	 * @return void
	 */
	protected function fetch_from_option( $option_id ) {
		// Two threads may access this at the same time, so cached values can cause reader-writer problem
		wp_cache_delete( $this->option_id, 'options' );
		$this->stats->from_array( get_option( $option_id, array() ) );
	}

	protected function mutex( $operation ) {
		( new Mutex( $this->mutex_key() ) )->execute( $operation );
	}

	private function mutex_key() {
		return 'update_global_stats_mutex_' . $this->option_id;
	}

	public function get_option_id() {
		return $this->option_id;
	}

	public function get_stats() {
		return $this->stats;
	}
}