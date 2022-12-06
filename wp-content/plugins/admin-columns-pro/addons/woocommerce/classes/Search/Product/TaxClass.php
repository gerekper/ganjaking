<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class TaxClass extends Comparison\Meta implements Comparison\Values {

	/**
	 * @var array
	 */
	private $tax_classes;

	public function __construct( array $tax_classes ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->tax_classes = $tax_classes;

		parent::__construct( $operators, '_tax_class', MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->tax_classes );
	}

}