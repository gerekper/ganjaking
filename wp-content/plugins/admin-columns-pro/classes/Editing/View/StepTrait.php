<?php

namespace ACP\Editing\View;

trait StepTrait {

	/**
	 * @param string $step
	 *
	 * @return $this
	 */
	public function set_step( $step ) {
		$this->set( 'range_step', (string) $step );

		return $this;
	}

}