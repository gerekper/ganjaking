<?php

namespace ACP\Editing\View;

trait MultipleTrait {

	/**
	 * @param bool $multiple
	 *
	 * @return $this
	 */
	public function set_multiple( $multiple ) {
		$this->set( 'multiple', (bool) $multiple );

		return $this;
	}

}