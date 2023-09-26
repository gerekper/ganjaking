<?php

namespace ACP\Editing\View;

trait PlaceholderTrait {

	public function set_placeholder( $placeholder ) {
		$this->set( 'placeholder', (string) $placeholder );

		return $this;
	}

}