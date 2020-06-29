<?php

namespace ACP\Search;

use AC\MetaType;

class QueryFactory {

	/**
	 * @param string $meta_type
	 * @param array  $bindings
	 *
	 * @return Query|false
	 */
	public static function create( $meta_type, array $bindings ) {
		switch ( $meta_type ) {
			case MetaType::POST :
				return new Query\Post( $bindings );

			case MetaType::USER :
				return new Query\User( $bindings );

			case MetaType::COMMENT :
				return new Query\Comment( $bindings );

			case MetaType::TERM :
				return new Query\Term( $bindings );
		}

		return false;
	}

}