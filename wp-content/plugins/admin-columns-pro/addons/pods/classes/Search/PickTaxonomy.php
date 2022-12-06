<?php

namespace ACA\Pods\Search;

use ACP\Helper\Select;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickTaxonomy extends Meta
	implements SearchableValues {

	/** @var array */
	private $taxonomy;

	public function __construct( $meta_key, $type, array $taxonomy ) {
		$this->taxonomy = $taxonomy;

		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values( $search, $page ) {
		return new Select\Paginated\Terms( $search, $page, $this->taxonomy );
	}

}