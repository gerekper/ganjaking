<?php

namespace Smush\Core\Smush;

use Smush\Core\Media\Media_Item_Stats;

class Smush_Media_Item_Stats extends Media_Item_Stats {
	private $lossy = false;

	/**
	 * @return mixed
	 */
	public function is_lossy() {
		return $this->lossy;
	}

	/**
	 * @param mixed $lossy
	 *
	 * @return Smush_Media_Item_Stats
	 */
	public function set_lossy( $lossy ) {
		$this->lossy = $lossy;

		return $this;
	}

	public function to_array() {
		$array          = parent::to_array();
		$array['lossy'] = $this->is_lossy();

		return $array;
	}

	public function from_array( $array ) {
		parent::from_array( $array );

		$this->set_lossy( ! empty( $array['lossy'] ) );
	}
}