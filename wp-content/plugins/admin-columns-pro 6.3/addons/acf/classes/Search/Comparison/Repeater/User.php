<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC;
use ACA\ACF\Search\Comparison;
use ACP;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class User extends Comparison\Repeater implements SearchableValues {

	public function __construct( $meta_type, $parent_key, $sub_key, $multiple ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators, null, $multiple );
	}

	public function get_values( $search, $paged ) {
		$entities = new ACP\Helper\Select\Entities\User( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\UserRole(
				new ACP\Helper\Select\Formatter\UserName( $entities )
			)
		);
	}

}