<?php

namespace ACA\MetaBox\Search\Comparison;

use AC;
use ACP;

class TaxonomyAdvanced extends ACP\Search\Comparison\Meta
	implements ACP\Search\Comparison\SearchableValues {

	/**
	 * @var string
	 */
	protected $taxonomy;

	public function __construct( $taxonomy, $meta_key, $meta_type ) {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::NEQ,
			ACP\Search\Operators::IS_EMPTY,
			ACP\Search\Operators::NOT_IS_EMPTY,
		] );

		$this->taxonomy = $taxonomy;

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values( $search, $page ) {
		$entities = new ACP\Helper\Select\Entities\Taxonomy( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\TermName( $entities )
		);
	}

}