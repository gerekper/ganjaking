<?php

namespace ACA\Pods\Search;

use ACP;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickUser extends Meta
	implements SearchableValues {

	/** @var array */
	private $roles;

	public function __construct( $meta_key, $type, $roles ) {
		$this->roles = $roles;

		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::CURRENT_USER,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values( $search, $page ) {
		$args = [];

		if ( ! empty( $this->roles ) ) {
			$args['role__in'] = $this->roles;
		}

		return new ACP\Helper\Select\Paginated\Users( $search, $page, $args );
	}

}