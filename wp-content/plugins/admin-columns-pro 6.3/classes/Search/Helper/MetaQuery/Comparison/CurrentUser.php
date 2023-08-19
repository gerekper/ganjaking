<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Helper\UserValueFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use Exception;

class CurrentUser extends MetaQuery\Comparison {

	/**
	 * @param string $key
	 * @param Value  $value
	 *
	 * @throws Exception
	 */
	public function __construct( $key, Value $value ) {
		$factory = new UserValueFactory();

		parent::__construct( $key, Operators::EQ, $factory->create_current_user() );
	}

}