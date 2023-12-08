<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
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

	protected function get_field(): string {
		return 'post_parent';
	}

	public function format_label( $value ): string {
		$post = get_post( $value );

		return $post
			? ( new PostTitle() )->format_label( $post )
			: '';
	}

	public function get_values( string $search, int $page ): Paginated {
		return ( new PaginatedFactory() )->create( [
			's'         => $search,
			'paged'     => $page,
			'post_type' => 'product',
			'tax_query' => [
				[
					'taxonomy' => 'product_type',
					'field'    => 'name',
					'terms'    => [ 'variable' ],
				],
			],
		] );
	}

}