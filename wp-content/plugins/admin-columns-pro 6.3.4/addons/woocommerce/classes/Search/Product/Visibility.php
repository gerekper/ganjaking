<?php

namespace ACA\WC\Search\Product;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Visibility extends Comparison
	implements Comparison\Values {

	/** @var array */
	private $visibility_options;

	public function __construct( $visibility_options ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->visibility_options = $visibility_options;

		parent::__construct( $operators );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->visibility_options );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings\Post();
		$bindings->tax_query( $this->get_tax_query( $value ) );

		return $bindings;
	}

	public function get_tax_query( Value $value ) {
		switch ( $value->get_value() ) {

			case 'search':
				return [
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => [ 'exclude-from-search' ],
						'operator' => 'NOT IN',
					],
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => [ 'exclude-from-catalog' ],
						'operator' => 'IN',
					],
				];

			case 'catalog':
				return [
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => [ 'exclude-from-catalog' ],
						'operator' => 'NOT IN',
					],
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'slug',
						'terms'    => [ 'exclude-from-search' ],
						'operator' => 'IN',
					],
				];

			case 'visible':
				return [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog', 'exclude-from-search' ],
					'operator' => 'NOT IN',
				];

			case 'hidden':
				return [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog', 'exclude-from-search' ],
					'operator' => 'AND',
				];
			default:
				return [];
		}
	}

}