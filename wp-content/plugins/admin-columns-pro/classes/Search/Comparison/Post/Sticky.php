<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Sticky extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			__( 'Not sticky', 'codepress-admin-columns' ),
			__( 'Sticky', 'codepress-admin-columns' ),
		] );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings\Post();

		return $bindings->where( $this->get_where( $value ) );
	}

	/**
	 * @param Value $value
	 *
	 * @return false|string
	 */
	private function get_where( Value $value ) {
		global $wpdb;

		$stickies = get_option( 'sticky_posts' );

		$is_sticky = '1' === $value->get_value();

		if ( ! $stickies && $is_sticky ) {
			return "{$wpdb->posts}.ID = 0"; // Show no results
		}

		if ( ! $stickies ) {
			return false;
		}

		$ids = array_filter( array_map( 'intval', $stickies ) );

		$sql_val = $is_sticky
			? " IN ('" . implode( "','", $ids ) . "')"
			: " NOT IN ('" . implode( "','", $ids ) . "')";

		return sprintf( '%s %s', $wpdb->posts . '.ID', $sql_val );
	}

}