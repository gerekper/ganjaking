<?php

namespace ACA\WC\Search\User;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Country extends Comparison\Meta
	implements Comparison\Values {

	/**
	 * @var array
	 */
	private $countries;

	public function __construct( $meta_key, $countries ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->countries = $countries;

		parent::__construct( $operators, $meta_key, 'post' );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->countries );
	}

}