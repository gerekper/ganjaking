<?php

namespace ACP\Search\Comparison\User;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Languages extends Comparison\Meta
	implements Comparison\Values {

	/** @var array */
	private $languages;

	public function __construct( $languages ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
		] );

		$this->languages = $languages;

		parent::__construct( $operators, 'locale', MetaType::USER );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->languages );
	}

}