<?php

namespace WPML\FP\Functor;

class ConstFunctor {
	use Functor;
	use Pointed;

	public function map( $callback ) {
		return $this;
	}
}
