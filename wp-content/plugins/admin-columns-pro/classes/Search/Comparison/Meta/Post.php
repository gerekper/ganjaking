<?php

namespace ACP\Search\Comparison\Meta;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

class Post extends Meta
	implements SearchableValues {

	/** @var string|array */
	private $post_type = 'any';

	/** @var WP_Term[] */
	private $terms = [];

	public function __construct( $meta_key, $meta_type, $post_type = false, array $terms = [], $labels = null ) {
		$this->set_post_type( $post_type );
		$this->set_terms( $terms );

		parent::__construct( $this->get_meta_operators(), $meta_key, $meta_type, Value::STRING, $labels );
	}

	protected function get_meta_operators() {
		return new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );
	}

	public function get_values( $search, $page ) {
		$entities = new Select\Entities\Post( [
			's'             => $search,
			'paged'         => $page,
			'post_type'     => $this->post_type,
			'tax_query'     => $this->get_tax_query(),
			'search_fields' => [ 'post_title', 'ID' ],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\PostType(
				new Formatter\PostTitle( $entities )
			)
		);
	}

	/**
	 * @param string $post_type
	 */
	private function set_post_type( $post_type ) {
		if ( $post_type ) {
			$this->post_type = $post_type;
		}
	}

	/**
	 * @param WP_Term[] $terms
	 */
	private function set_terms( array $terms ) {
		$this->terms = $terms;
	}

	/**
	 * @return array
	 */
	protected function get_tax_query() {
		$tax_query = [];

		foreach ( $this->terms as $term ) {
			$tax_query[] = [
				'taxonomy' => $term->taxonomy,
				'field'    => 'slug',
				'terms'    => $term->slug,
			];
		}

		return $tax_query;
	}

}