<?php

namespace ACP\Editing\View;

trait MinMaxTrait {

	/**
	 * @param float $min
	 *
	 * @return $this
	 */
	public function set_min( $min ) {
		if ( is_numeric( $min ) ) {
			$this->set( 'range_min', (float) $min );
		}

		return $this;
	}

	/**
	 * @param float $min
	 *
	 * @return $this
	 */
	public function set_max( $max ) {
		if ( is_numeric( $max ) ) {
			$this->set( 'range_max', (float) $max );
		}

		return $this;
	}

}