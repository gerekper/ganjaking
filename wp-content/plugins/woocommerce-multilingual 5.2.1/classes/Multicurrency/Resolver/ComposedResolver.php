<?php

namespace WCML\MultiCurrency\Resolver;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Lst;

class ComposedResolver implements Resolver {

	/**
	 * @var callable
	 */
	private $get;

	/**
	 * @param Resolver[] $resolvers
	 */
	public function __construct( array $resolvers ) {
		$this->get = Logic::firstSatisfying(
			Logic::isNotNull(),
			Fns::map( Lst::makePair( Fns::__, 'getClientCurrency' ), $resolvers )
		);
	}

	/**
	 * @return string
	 */
	public function getClientCurrency() {
		return call_user_func( $this->get, null );
	}
}
