<?php

namespace ACP\Editing\View;

trait OptionsTrait {

	/**
	 * @param array $options
	 *
	 * @return $this
	 */
	public function set_options( array $options ) {
		$this->set( 'options', $options );

		return $this;
	}

}