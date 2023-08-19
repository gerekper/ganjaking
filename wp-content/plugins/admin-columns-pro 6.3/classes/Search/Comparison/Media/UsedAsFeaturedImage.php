<?php

namespace ACP\Search\Comparison\Media;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class UsedAsFeaturedImage extends Comparison implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$sub_query = "SELECT DISTINCT( meta_value ) as ID
			FROM wp_postmeta
			WHERE meta_key = '_thumbnail_id'";

		$bindings = new Bindings();

		$alias = $bindings->get_unique_alias( 'subquery' );

		switch ( $value->get_value() ) {
			case 'true' :
				$bindings->join( "INNER JOIN ({$sub_query}) as {$alias} ON {$wpdb->posts}.ID = {$alias}.ID" );

				break;
			default :
				$bindings->join( "LEFT JOIN ({$sub_query}) as {$alias} ON {$wpdb->posts}.ID = {$alias}.ID" )
				         ->where( "{$alias}.ID is NULL" );
		}

		return $bindings;
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'true'  => __( 'In use', 'codepress-admin-columns' ),
			'false' => __( 'Not used', 'codepress-admin-columns' ),
		] );
	}

}