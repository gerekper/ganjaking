<?php

namespace ACA\WC\Search\ProductVariation;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ProductTaxonomy extends Comparison
	implements Comparison\SearchableValues {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;

		$operators = new ACP\Search\Operators(
			[
				ACP\Search\Operators::EQ,
			]
		);

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();

		return $bindings->where( $this->get_where( $value->get_value() ) );
	}

	/**
	 * @param int $product_id
	 *
	 * @return string
	 */
	public function get_where( $product_id ) {
		global $wpdb;

		$products = $this->get_product_ids_by_term_id( $product_id );

		if ( empty( $products ) ) {
			$products = [ -1 ];
		}

		return sprintf( "{$wpdb->posts}.post_parent IN( %s )", implode( ',', $products ) );
	}

	public function get_values( $s, $paged ) {
		$entities = new ACP\Helper\Select\Entities\Taxonomy( [
			'search'   => $s,
			'page'     => $paged,
			'taxonomy' => [ $this->taxonomy ],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\TermName( $entities )
		);
	}

	protected function get_product_ids_by_term_id( $term_id ) {
		return get_posts( [
			'post_type'      => 'product',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'tax_query'      => [
				[
					'taxonomy' => $this->taxonomy,
					'terms'    => $term_id,
				],
			],
		] );
	}

}