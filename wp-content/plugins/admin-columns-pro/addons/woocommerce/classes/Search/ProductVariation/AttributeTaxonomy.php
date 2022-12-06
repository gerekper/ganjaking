<?php

namespace ACA\WC\Search\ProductVariation;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class AttributeTaxonomy extends Comparison\Meta implements Comparison\SearchableValues {

	/**
	 * @var string
	 */
	protected $taxonomy;

	public function __construct( $taxonomy ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
		] );

		$this->taxonomy = $taxonomy;

		parent::__construct( $operators, 'attribute_' . $taxonomy, AC\MetaType::POST );
	}

	public function get_values( $search, $page ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

	public function create_query_bindings( $operator, Value $value ) {
		$term = get_term( $value->get_value() );

		return parent::create_query_bindings( $operator, new Value(
			$term ? $term->slug : '',
			Value::STRING
		) );
	}

}