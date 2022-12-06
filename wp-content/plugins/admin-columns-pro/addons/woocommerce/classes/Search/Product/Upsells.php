<?php

namespace ACA\WC\Search\Product;

use AC\MetaType;
use ACA\WC\Helper\Select;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery;
use ACP\Search\Operators;
use ACP\Search\Value;

class Upsells extends Comparison\Meta
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new ACP\Search\Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, '_upsell_ids', MetaType::POST );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$comparison = MetaQuery\SerializedComparisonFactory::create( $this->meta_key, $operator, $value );

		return $comparison();
	}

	public function get_values( $s, $paged ) {
		return new Select\Paginated\Products( (string) $s, (int) $paged );
	}

}