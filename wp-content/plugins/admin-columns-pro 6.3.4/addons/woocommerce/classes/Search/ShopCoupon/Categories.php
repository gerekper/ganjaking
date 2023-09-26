<?php

namespace ACA\WC\Search\ShopCoupon;

use AC;
use AC\MetaType;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Categories extends Comparison\Meta
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
		$entities = new ACP\Helper\Select\Entities\Taxonomy( [
			'search'   => $s,
			'page'     => $paged,
			'taxonomy' => 'product_cat',
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\TermName( $entities )
		);
	}

	protected function get_meta_query( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			return [
				'key'     => $this->get_meta_key(),
				'value'   => serialize( absint( $value->get_value() ) ),
				'compare' => 'LIKE',
			];
		}

		return parent::get_meta_query( $operator, $value );
	}

}