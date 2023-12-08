<?php

namespace ACA\ACF\Search\Comparison;

use ACA\ACF\Search\UnserializedValuesTrait;
use ACP;

class Users extends User {

	use UnserializedValuesTrait;

	protected function get_meta_query( string $operator, ACP\Search\Value $value ): array {
		if ( ACP\Search\Operators::CURRENT_USER === $operator ) {
			$value = ( new ACP\Search\Helper\UserValueFactory() )->create_current_user( ACP\Search\Value::STRING );
		}

		$comparison = ACP\Search\Helper\MetaQuery\SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

	public function get_used_user_ids(): array {
		return $this->get_unserialized_values( $this->query->get() );
	}

}