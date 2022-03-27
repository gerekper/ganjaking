<?php

namespace ACP\Search\Comparison\Post;

use AC;
use AC\MetaType;
use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class LastModifiedAuthor extends Comparison\Meta
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, '_edit_last', MetaType::POST );
	}

	public function get_values( $search, $paged ) {
		$entities = new Select\Entities\User( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

}