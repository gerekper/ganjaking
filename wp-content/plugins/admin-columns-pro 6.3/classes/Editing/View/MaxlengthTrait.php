<?php

namespace ACP\Editing\View;

trait MaxlengthTrait {

	public function set_max_length( $max_length ) {
		$this->set( 'maxlength', (int) $max_length );

		return $this;
	}

}