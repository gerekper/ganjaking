<?php

namespace ACA\ACF\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\LabelFormatter;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Taxonomy extends Meta
	implements SearchableValues {

	private $taxonomy;

	public function __construct( string $meta_key, string $taxonomy ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->taxonomy = $taxonomy;

		parent::__construct( $operators, $meta_key );
	}

	public function format_label( $value ): string {
		$term = get_term( $value );

		return $term
			? $this->formatter()->format_label( $term )
			: '';
	}

	private function formatter(): LabelFormatter\TermName {
		return new LabelFormatter\TermName();
	}

	public function get_values( string $search, int $page ): Paginated {
		return ( new Select\Taxonomy\PaginatedFactory() )->create( [
			'page'     => $page,
			'search'   => $search,
			'taxonomy' => $this->taxonomy,
		], $this->formatter() );
	}

}