<?php

namespace ACP\Search;

use AC\MetaType;
use LogicException;

final class QueryFactory {

	private static $queries = [
		MetaType::POST    => Query\Post::class,
		MetaType::USER    => Query\User::class,
		MetaType::COMMENT => Query\Comment::class,
		MetaType::TERM    => Query\Term::class,
	];

	/**
	 * @param string $meta_type
	 * @param string $class Query class (FQN)
	 */
	public static function register( $meta_type, $class ) {
		self::$queries[ $meta_type ] = $class;
	}

	/**
	 * @param string $meta_type
	 * @param array  $bindings
	 *
	 * @return Query
	 */
	public static function create( $meta_type, array $bindings ) {
		$class = isset( self::$queries[ $meta_type ] )
			? self::$queries[ $meta_type ]
			: null;

		if ( ! $class ) {
			throw new LogicException( 'Unsupported query meta type.' );
		}

		$query = new $class( $bindings );

		if ( ! $query instanceof Query ) {
			throw new LogicException( sprintf( 'Expected class of type %s.', Query::class ) );
		}

		return $query;
	}

}