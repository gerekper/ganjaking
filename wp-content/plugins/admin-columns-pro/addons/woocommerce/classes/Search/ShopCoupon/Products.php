<?php

namespace ACA\WC\Search\ShopCoupon;

use AC;
use AC\MetaType;
use ACA\WC\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Products extends Comparison\Meta
	implements Comparison\SearchableValues {

	public function __construct( $meta_key ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, MetaType::POST );
	}

	public function get_values( $s, $paged ) {
		$entities = new Select\Entities\Product( [
			's'     => $s,
			'paged' => $paged,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\ProductIDTitleAndSKU( $entities )
		);
	}

	protected function get_meta_query( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			return [
				'relation' => 'OR',
				[
					'key'     => $this->get_meta_key(),
					'value'   => '^' . $value->get_value(),
					'compare' => 'REGEXP',
				],
				[
					'key'     => $this->get_meta_key(),
					'value'   => '$' . $value->get_value(),
					'compare' => 'REGEXP',
				],
				[
					'key'     => $this->get_meta_key(),
					'value'   => sprintf( ',%s,', $value->get_value() ),
					'compare' => 'LIKE',
				],
			];
		}

		return parent::get_meta_query( $operator, $value );
	}
}