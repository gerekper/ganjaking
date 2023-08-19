<?php

namespace ACP\Editing\View;

trait TagsTrait {

	/**
	 * @param bool $enable_tags
	 *
	 * @return $this
	 */
	public function set_tags( $enable_tags ) {
		$this->set( 'tags', (bool) $enable_tags );

		return $this;
	}

}