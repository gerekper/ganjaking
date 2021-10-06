<?php

namespace ACP\Search;

use AC\ArrayIterator;

class Labels extends ArrayIterator {

	public function __construct( array $labels = [] ) {
		$labels = array_merge( [
			Operators::EQ           => __( 'is', 'codepress-admin-columns' ),
			Operators::NEQ          => __( 'is not', 'codepress-admin-columns' ),
			Operators::GT           => __( 'is larger than', 'codepress-admin-columns' ),
			Operators::GTE          => __( 'is larger or equal than', 'codepress-admin-columns' ),
			Operators::LT           => __( 'is smaller than', 'codepress-admin-columns' ),
			Operators::LTE          => __( 'is smaller or equal than', 'codepress-admin-columns' ),
			Operators::CONTAINS     => __( 'contains', 'codepress-admin-columns' ),
			Operators::NOT_CONTAINS => __( 'does not contain', 'codepress-admin-columns' ),
			Operators::BEGINS_WITH  => __( 'starts with', 'codepress-admin-columns' ),
			Operators::ENDS_WITH    => __( 'ends with', 'codepress-admin-columns' ),
			Operators::IN           => __( 'in', 'codepress-admin-columns' ),
			Operators::NOT_IN       => __( 'not in', 'codepress-admin-columns' ),
			Operators::BETWEEN      => __( 'between', 'codepress-admin-columns' ),
			Operators::IS_EMPTY     => __( 'is not set', 'codepress-admin-columns' ),
			Operators::NOT_IS_EMPTY => __( 'is set', 'codepress-admin-columns' ),
			Operators::CURRENT_USER => __( 'is current user', 'codepress-admin-columns' ),
		], $labels );

		parent::__construct( $labels );
	}

}