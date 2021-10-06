<?php

namespace ACP\Editing\View;

trait MethodTrait {

	public function has_methods( $has_methods ) {
		$this->set( 'has_methods', (bool) $has_methods );

		return $this;
	}

}