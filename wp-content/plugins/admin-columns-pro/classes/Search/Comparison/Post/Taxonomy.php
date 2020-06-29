<?php

namespace ACP\Search\Comparison\Post;

use AC;
use AC\MetaType;
use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Helper\TaxQuery\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Taxonomy extends Comparison
	implements Comparison\SearchableValues {

	/**
	 * @var string
	 */
	protected $taxonomy;

	public function __construct( $taxonomy ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->taxonomy = $taxonomy;

		parent::__construct( $operators, Value::INT );
	}

	public function get_meta_type() {
		return MetaType::POST;
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

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		$tax_query = ComparisonFactory::create(
			$this->taxonomy,
			$operator,
			$value
		);

		$bindings = new Bindings\Post();
		$bindings->tax_query( $tax_query->get_expression() );

		return $bindings;
	}

}