<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use AC\MetaType;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Customer extends Comparison\Meta
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new Operators(
			[
				Operators::EQ,
			]
		);

		parent::__construct( $operators, '_customer_user', MetaType::POST );
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