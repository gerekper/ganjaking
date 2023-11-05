<?php

namespace Smush\Core\Media;

class Media_Item_Stats {
	/**
	 * @var int
	 */
	private $size_before = 0;
	/**
	 * @var int
	 */
	private $size_after = 0;
	/**
	 * @var float
	 */
	private $time = 0.0;

	/**
	 * @return float
	 */
	public function get_percent() {
		return $this->calculate_percentage(
			$this->get_size_before(),
			$this->get_size_after()
		);
	}

	public function get_human_bytes() {
		$bytes = $this->get_bytes();

		return size_format(
			$bytes,
			$bytes >= 1024 ? 1 : 0
		);
	}

	/**
	 * @return int
	 */
	public function get_bytes() {
		$size_before = $this->get_size_before();
		$size_after  = $this->get_size_after();

		return $size_before > $size_after
			? $size_before - $size_after
			: 0;
	}

	/**
	 * @return int
	 */
	public function get_size_before() {
		return $this->size_before;
	}

	/**
	 * @param int $size_before
	 */
	public function set_size_before( $size_before ) {
		$this->size_before = (int) $size_before;
	}

	/**
	 * @return int
	 */
	public function get_size_after() {
		return $this->size_after;
	}

	/**
	 * @param int $size_after
	 */
	public function set_size_after( $size_after ) {
		$this->size_after = (int) $size_after;
	}

	/**
	 * @return float
	 */
	public function get_time() {
		return $this->time;
	}

	/**
	 * @param float $time
	 */
	public function set_time( $time ) {
		$this->time = (float) $time;
	}

	public function from_array( $array ) {
		$this->set_time( (float) $this->get_array_value( $array, 'time' ) );
		$this->set_size_before( (int) $this->get_array_value( $array, 'size_before' ) );
		$this->set_size_after( (int) $this->get_array_value( $array, 'size_after' ) );
	}

	public function is_empty() {
		return empty( $this->get_size_before() ) && empty( $this->get_size_after() );
	}

	public function to_array() {
		return array(
			'time'        => $this->get_time(),
			'bytes'       => $this->get_bytes(),
			'percent'     => $this->get_percent(),
			'size_before' => $this->get_size_before(),
			'size_after'  => $this->get_size_after(),
		);
	}

	protected function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}

	/**
	 * Add values from the passed stats object to the current object
	 *
	 * @param $addend Media_Item_Stats
	 *
	 * @return void
	 */
	public function add( $addend ) {
		$new_size_before = $this->get_size_before() + $addend->get_size_before();
		$new_size_after  = $this->get_size_after() + $addend->get_size_after();
		$new_time        = $this->get_time() + $addend->get_time();

		// Update with new values
		$this->set_time( $new_time );
		$this->set_size_before( $new_size_before );
		$this->set_size_after( $new_size_after );
	}

	/**
	 * @param $subtrahend Media_Item_Stats
	 *
	 * @return void
	 */
	public function subtract( $subtrahend ) {
		$new_size_before = $this->get_size_before() - $subtrahend->get_size_before();
		$new_size_after  = $this->get_size_after() - $subtrahend->get_size_after();
		$new_time        = $this->get_time() - $subtrahend->get_time();

		// Update with new values
		$this->set_time( max( $new_time, 0 ) );
		$this->set_size_before( max( $new_size_before, 0 ) );
		$this->set_size_after( max( $new_size_after, 0 ) );
	}

	/**
	 * @param $to_check Media_Item_Stats
	 *
	 * @return boolean
	 */
	public function equals( $to_check ) {
		return $this->get_size_before() === $to_check->get_size_before()
		       && $this->get_size_after() === $to_check->get_size_after()
		       && $this->get_time() === $to_check->get_time();
	}

	private function calculate_percentage( $size_before, $size_after ) {
		$savings = $size_before - $size_after;
		if ( $savings > 0 && $size_before > 0 ) {
			$percentage = ( $savings / $size_before ) * 100;

			return $percentage > 0
				? round( $percentage, 2 )
				: $percentage;
		}

		return 0;
	}

	/**
	 * @param $to_copy Media_Item_Stats
	 *
	 * @return void
	 */
	public function copy( $to_copy ) {
		$this->from_array( $to_copy->to_array() );
	}
}