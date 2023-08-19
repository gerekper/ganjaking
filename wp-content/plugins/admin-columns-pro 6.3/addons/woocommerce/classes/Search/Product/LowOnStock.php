<?php

namespace ACA\WC\Search\Product;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class LowOnStock extends Comparison implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();
		$alias_post = $bindings->get_unique_alias( 'postmeta1' );
		$alias_postmeta1 = $bindings->get_unique_alias( 'postmeta' );
		$alias_postmeta2 = $bindings->get_unique_alias( 'postmeta' );
		$in_type = $value->get_value() === 'true' ? 'IN' : 'NOT IN';

		$sub_query = "
			SELECT {$alias_post}.ID
			FROM {$wpdb->posts} AS {$alias_post}
			INNER JOIN {$wpdb->postmeta} AS {$alias_postmeta1} on {$alias_post}.ID = {$alias_postmeta1}.post_id AND {$alias_postmeta1}.meta_key = '_low_stock_amount'
			INNER JOIN {$wpdb->postmeta} AS {$alias_postmeta2} on {$alias_post}.ID = {$alias_postmeta2}.post_id AND {$alias_postmeta2}.meta_key = '_stock'
			AND {$alias_postmeta2}.meta_value > {$alias_postmeta1}.meta_value
		";

		$bindings->where( " {$wpdb->posts}.ID {$in_type}( {$sub_query})" );

		return $bindings;
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'true'  => __( 'True', 'codepress-admin-columns' ),
			'false' => __( 'False', 'codepress-admin-columns' ),
		] );
	}

}