<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Posts extends Post {

	public function __construct( $meta_key, $meta_type, $post_type = false, array $terms = [] ) {
		parent::__construct( $meta_key, $meta_type, $post_type, $terms, new Labels( [
			Operators::EQ  => __( 'contains', 'codepress-admin-columns' ),
			Operators::NEQ => __( 'does not contain', 'codepress-admin-columns' ),
		] ) );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

}