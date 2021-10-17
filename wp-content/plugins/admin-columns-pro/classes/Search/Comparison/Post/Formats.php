<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Helper\TaxQuery\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Formats extends Comparison
	implements Comparison\Values {

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

		parent::__construct( $operators );
	}

	public function get_values() {
		$options = [];

		foreach ( get_post_format_strings() as $slug => $label ) {
			$options[ 'post-format-' . $slug ] = $label;
		}

		return AC\Helper\Select\Options::create_from_array( $options );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		if ( 'post-format-standard' === $value->get_value() ) {
			return $this->create_non_existent_post_format_bindings();
		}

		$tax_query = ComparisonFactory::create(
			$this->taxonomy,
			$operator,
			$value,
			'slug'
		);

		$bindings = new Bindings\Post();

		return $bindings->tax_query( $tax_query->get_expression() );
	}

	private function create_non_existent_post_format_bindings() {
		$bindings = new Bindings\Post();

		return $bindings->tax_query( [
			'taxonomy' => $this->taxonomy,
			'operator' => 'NOT EXISTS',
		] );
	}

}