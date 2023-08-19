<?php

namespace ACP\Search\Labels;

use ACP\Search\Labels;
use ACP\Search\Operators;

class Date extends Labels {

	public function __construct( array $labels = [] ) {
		$labels = array_merge( [
			Operators::GT          => __( 'is after', 'codepress-admin-columns' ),
			Operators::LT          => __( 'is before', 'codepress-admin-columns' ),
			Operators::TODAY       => __( 'is today', 'codepress-admin-columns' ),
			Operators::PAST        => __( 'is in the past', 'codepress-admin-columns' ),
			Operators::FUTURE      => __( 'is in the future', 'codepress-admin-columns' ),
			Operators::LT_DAYS_AGO => __( 'is less than', 'codepress-admin-columns' ),
			Operators::GT_DAYS_AGO => __( 'is more than', 'codepress-admin-columns' ),
			Operators::WITHIN_DAYS => __( 'is within', 'codepress-admin-columns' ),
		], $labels );

		parent::__construct( $labels );
	}

}