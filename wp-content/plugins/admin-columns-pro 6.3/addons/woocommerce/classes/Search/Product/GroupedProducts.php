<?php

namespace ACA\WC\Search\Product;

use AC\MetaType;
use ACA\WC\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class GroupedProducts extends Comparison\Meta
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, '_children', MetaType::POST );
	}

	protected function get_meta_query( $operator, Value $value ) {
		switch ( $operator ) {
			case Operators::EQ :
				return [
					'key'     => $this->get_meta_key(),
					'compare' => 'LIKE',
					'value'   => serialize( (int) $value->get_value() ),
				];
			default:
				$comparison = SerializedComparisonFactory::create( $this->meta_key, $operator, $value );

				return $comparison();
		}
	}

	public function get_values( $s, $paged ) {
		return new Select\Paginated\Products( (string) $s, (int) $paged );
	}

}