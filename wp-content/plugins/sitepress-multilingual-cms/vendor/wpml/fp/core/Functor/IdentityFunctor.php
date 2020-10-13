<?php

namespace WPML\FP\Functor;

class IdentityFunctor {
	use Functor;
	use Pointed;

	public function map( $callback ) {
		return new self( $callback( $this->get() ) );
	}
}
