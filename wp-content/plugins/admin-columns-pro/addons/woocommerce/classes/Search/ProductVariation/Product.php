<?php

namespace ACA\WC\Search\ProductVariation;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Product extends Comparison\Post\PostField
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'post_parent';
	}

	public function get_values( $s, $paged ) {
		$entities = new ACP\Helper\Select\Entities\Post( [
			's'         => $s,
			'paged'     => $paged,
			'post_type' => 'product',
			'tax_query' => [
				[
					'taxonomy' => 'product_type',
					'field'    => 'name',
					'terms'    => [ 'variable' ],
				],
			],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\PostTitle( $entities )
		);
	}

}