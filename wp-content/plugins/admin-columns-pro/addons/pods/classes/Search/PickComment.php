<?php

namespace ACA\Pods\Search;

use AC;
use ACP;
use ACP\Helper\Select;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickComment extends Meta
	implements SearchableValues {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values( $search, $paged ) {
		$entities = new Select\Entities\Comment( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\CommentSummary( $entities )
		);
	}

}