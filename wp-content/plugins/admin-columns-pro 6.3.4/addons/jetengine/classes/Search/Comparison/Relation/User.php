<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;

class User extends Relation {

	public function get_values( $search, $page ) {
		return new Select\Paginated\Users( $search, $page, [] );
	}

}