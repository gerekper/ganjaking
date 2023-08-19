<?php

namespace ACA\ACF\Search\Comparison;

use ACP;

class Users extends User {

	protected function get_meta_query( $operator, ACP\Search\Value $value ) {
		if( ACP\Search\Operators::CURRENT_USER === $operator ){
			$value = ( new ACP\Search\Helper\UserValueFactory() )->create_current_user( ACP\Search\Value::STRING );
		}

		$comparison = ACP\Search\Helper\MetaQuery\SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}