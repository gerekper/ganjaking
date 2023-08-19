<?php

namespace ACA\Pods\Search;

use ACP;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickPost extends Meta
	implements SearchableValues {

	/** @var array */
	private $post_type;

	public function __construct( $meta_key, $type, array $post_type ) {
		$this->post_type = $post_type;

		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values( $search, $page ) {
		return new ACP\Helper\Select\Paginated\Posts( $search, $page, [ 'post_type' => $this->post_type ] );
	}

}