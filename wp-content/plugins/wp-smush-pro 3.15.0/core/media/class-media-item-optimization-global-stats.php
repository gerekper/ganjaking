<?php

namespace Smush\Core\Media;

use Smush\Core\Array_Utils;

class Media_Item_Optimization_Global_Stats extends Media_Item_Stats {
	/**
	 * @var int How many media *items* are included in this instance.
	 */
	private $count = 0;

	/**
	 * @var int[] Ids of the attachments included in this instance.
	 */
	private $attachment_ids = array();
	private $array_utils;

	public function __construct() {
		$this->array_utils = new Array_Utils();
	}

	public function to_array() {
		$array                   = parent::to_array();
		$array['count']          = $this->get_count();
		$array['attachment_ids'] = join( ',', $this->get_attachment_ids() );

		return $array;
	}

	public function from_array( $array ) {
		parent::from_array( $array );

		$this->set_count( (int) $this->get_array_value( $array, 'count' ) );

		$attachment_ids = $this->get_array_value( $array, 'attachment_ids' );
		$attachment_ids = empty( $attachment_ids ) ? array() : explode( ',', $attachment_ids );
		$this->set_attachment_ids( $attachment_ids );
	}

	/**
	 * @param $attachment_id int
	 * @param $item_stats Media_Item_Stats
	 *
	 * @return boolean
	 */
	public function add_item_stats( $attachment_id, $item_stats ) {
		if ( $this->has_attachment_id( $attachment_id ) ) {
			return false;
		} else {
			parent::add( $item_stats );

			$this->set_count( $this->get_count() + 1 );
			$this->add_attachment_id( $attachment_id );

			return true;
		}
	}

	/**
	 * @param $attachment_id int
	 * @param $item_stats Media_Item_Stats
	 *
	 * @return boolean
	 */
	public function subtract_item_stats( $attachment_id, $item_stats ) {
		if ( $this->has_attachment_id( $attachment_id ) ) {
			parent::subtract( $item_stats );

			$this->set_count( $this->get_count() - 1 );
			$this->remove_attachment_id( $attachment_id );

			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $addend Media_Item_Optimization_Global_Stats
	 *
	 * @return void
	 */
	public function add( $addend ) {
		parent::add( $addend );

		$this->set_count( $this->get_count() + $addend->get_count() );
		$this->set_attachment_ids(
			$this->array_utils->fast_array_unique( array_merge(
				$this->get_attachment_ids(),
				$addend->get_attachment_ids()
			) )
		);
	}

	/**
	 * @param $subtrahend Media_Item_Optimization_Global_Stats
	 *
	 * @return void
	 */
	public function subtract( $subtrahend ) {
		parent::subtract( $subtrahend );

		$this->set_count( max( $this->get_count() - $subtrahend->get_count(), 0 ) );
		$this->set_attachment_ids(
			array_diff(
				$this->get_attachment_ids(),
				$subtrahend->get_attachment_ids()
			)
		);
	}

	/**
	 * @return mixed
	 */
	public function get_count() {
		return $this->count;
	}

	/**
	 * @param mixed $count
	 *
	 * @return Media_Item_Optimization_Global_Stats
	 */
	public function set_count( $count ) {
		$this->count = $count;

		return $this;
	}

	private function add_attachment_id( $attachment_id ) {
		$this->attachment_ids[] = $attachment_id;
	}

	private function remove_attachment_id( $attachment_id ) {
		$attachment_ids = $this->get_attachment_ids();
		$index          = array_search( $attachment_id, $attachment_ids );
		if ( $index !== false ) {
			unset( $attachment_ids[ $index ] );
			$this->set_attachment_ids( $attachment_ids );
		}
	}

	public function has_attachment_id( $attachment_id ) {
		return in_array( $attachment_id, $this->get_attachment_ids() );
	}

	private function get_attachment_ids() {
		$attachment_ids = $this->attachment_ids;

		return empty( $attachment_ids ) || ! is_array( $attachment_ids )
			? array()
			: $attachment_ids;
	}

	private function set_attachment_ids( $attachment_ids ) {
		$this->attachment_ids = empty( $attachment_ids ) || ! is_array( $attachment_ids )
			? array()
			: $attachment_ids;
	}
}